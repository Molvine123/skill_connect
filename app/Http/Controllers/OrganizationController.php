<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Organization;
use App\Models\SkillProgram;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\AuditLog;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class OrganizationController extends Controller
{
    // ── Organization Dashboard (for organization-role users) ─────────────────

    public function dashboard()
    {
        $user         = Auth::user();
        $organization = $user->organization;

        $stats = [
            'published_programs'   => 0,
            'enrolled_students'    => 0,
            'revenue'              => 0,
            'upcoming_sessions'    => 0,
        ];

        $programs      = collect();
        $recentEnrolls = collect();

        if ($organization) {
            $programIds = $organization->programs()->pluck('id');

            $stats = [
                'published_programs'  => $organization->programs()->where('status', 'active')->count(),
                'enrolled_students'   => Enrollment::whereIn('program_id', $programIds)->distinct('student_id')->count('student_id'),
                'revenue'             => Payment::whereIn('enrollment_id', Enrollment::whereIn('program_id', $programIds)->pluck('id'))->where('status', 'paid')->sum('amount'),
                'upcoming_sessions'   => \App\Models\TrainingSession::whereIn('program_id', $programIds)->where('start_date', '>=', now())->count(),
            ];

            $programs = $organization->programs()->with(['category', 'sessions', 'enrollments'])->latest()->take(6)->get();
            $recentEnrolls = Enrollment::whereIn('program_id', $programIds)
                ->with(['student.user', 'program'])
                ->latest()
                ->take(5)
                ->get();

            // Mock Data for Charts
            $enrollmentTrends = [
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                'data'   => [0, 10, 25, 45, 60, $stats['enrolled_students']],
            ];
        }

        return view('dashboard.organization', compact('stats', 'programs', 'recentEnrolls', 'organization', 'enrollmentTrends'));
    }

    // ── Admin: List All Organizations ─────────────────────────────────────────

    public function index(Request $request)
    {
        $query = Organization::with('user')->withCount('programs')->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('contact_person', 'like', "%{$s}%")
                  ->orWhere('county', 'like', "%{$s}%");
            });
        }

        if ($request->filled('org_type')) {
            $query->where('org_type', $request->org_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $organizations = $query->paginate(15)->withQueryString();
        $typeLabels    = Organization::typeLabels();

        return view('organization.index', compact('organizations', 'typeLabels'));
    }

    // ── Admin/Self: View Single Organization ──────────────────────────────────

    public function show($id)
    {
        $organization = Organization::with(['user', 'programs.category', 'programs.enrollments'])->findOrFail($id);
        return view('organization.show', compact('organization'));
    }

    // ── Organization: Edit Profile Page ──────────────────────────────────────

    public function edit()
    {
        $user         = Auth::user();
        $organization = $user->organization;
        $typeLabels   = Organization::typeLabels();
        return view('organization.edit', compact('organization', 'typeLabels'));
    }

    // ── Organization: Update Profile ──────────────────────────────────────────

    public function updateProfile(Request $request)
    {
        $user         = Auth::user();
        $organization = $user->organization;

        $request->validate([
            'org_name'       => 'required|string|max:255',
            'org_type'       => 'required|in:ngo,private_company,ajira,trainer',
            'contact_person' => 'nullable|string|max:255',
            'phone'          => 'nullable|string|max:20',
            'email'          => 'nullable|email|max:255',
            'website'        => 'nullable|url|max:255',
            'address'        => 'nullable|string|max:500',
            'county'         => 'nullable|string|max:100',
            'description'    => 'nullable|string|max:1500',
            'logo'           => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $logoPath = $organization?->logo_path;

        if ($request->hasFile('logo')) {
            if ($organization && $organization->logo_path) {
                Storage::disk('public')->delete($organization->logo_path);
            }
            $logoPath = $request->file('logo')->store('logos/organizations', 'public');
        }

        $user->update([
            'name'  => $request->org_name,
            'phone' => $request->phone,
        ]);

        if ($organization) {
            $organization->update([
                'name'           => $request->org_name,
                'org_type'       => $request->org_type,
                'contact_person' => $request->contact_person,
                'phone'          => $request->phone,
                'email'          => $request->email,
                'website'        => $request->website,
                'address'        => $request->address,
                'county'         => $request->county,
                'description'    => $request->description,
                'logo_path'      => $logoPath,
            ]);
        } else {
            Organization::create([
                'user_id'        => $user->id,
                'name'           => $request->org_name,
                'org_type'       => $request->org_type,
                'contact_person' => $request->contact_person,
                'phone'          => $request->phone,
                'email'          => $request->email,
                'website'        => $request->website,
                'address'        => $request->address,
                'county'         => $request->county,
                'description'    => $request->description,
                'logo_path'      => $logoPath,
                'status'         => 'pending',
            ]);
        }

        AuditLog::log($user->id, 'update_organization_profile', "Updated profile for organization: {$user->name}");

        return redirect()->route('organization.dashboard')->with('success', 'Organization profile updated successfully.');
    }
    // Show attendance for a specific session
    public function attendance($programId, $sessionId)
    {
        $session = \App\Models\TrainingSession::with('virtualClass')
            ->findOrFail($sessionId);

        $virtualClass = $session->virtualClass;

        $attendance = \App\Models\ClassAttendance::where('virtual_class_id', $virtualClass->id)
            ->with(['student.user'])
            ->orderBy('join_time', 'asc')
            ->get();

        return view('organization.attendance', compact('session', 'virtualClass', 'attendance'));
    }

    // Show organization-wide attendance records for all sessions
    public function attendanceIndex()
    {
        $user = Auth::user();
        $organization = $user->organization;

        if (!$organization) {
            return redirect()->route('organization.dashboard')->with('error', 'Organization profile not found.');
        }

        $programIds = $organization->programs()->pluck('id');

        // Fetch sessions with their programs, virtual class, and both types of attendance
        $sessions = \App\Models\TrainingSession::whereIn('program_id', $programIds)
            ->with([
                'program',
                'attendances.student.user',
                'virtualClass.attendances.student.user'
            ])
            ->orderBy('start_date', 'desc')
            ->paginate(15);

        return view('organization.attendance_index', compact('sessions', 'organization'));
    }

    // Show organization-wide certificates index
    public function certificatesIndex()
    {
        $user = Auth::user();
        $organization = $user->organization;

        if (!$organization) {
            return redirect()->route('organization.dashboard')->with('error', 'Organization profile not found.');
        }

        // Get all programs for this organization with completed enrollments count
        $programs = $organization->programs()
            ->withCount(['enrollments' => function($q) {
                $q->where('status', 'completed');
            }])
            ->get();

        // Load all issued certificates for the organization's programs
        $programIds = $programs->pluck('id');
        $recentCertificates = \App\Models\Certificate::whereIn('enrollment_id', function($q) use ($programIds) {
                $q->select('id')->from('enrollments')->whereIn('program_id', $programIds);
            })
            ->with(['student.user', 'enrollment.program'])
            ->orderBy('issue_date', 'desc')
            ->paginate(15);

        // Build a QR code map: verification_code => SVG string
        $qrOptions = new QROptions([
            'outputInterface' => \chillerlan\QRCode\Output\QRMarkupSVG::class,
            'eccLevel'        => \chillerlan\QRCode\Common\EccLevel::H,
            'svgAddXmlHeader' => false,
            'svgUseFillAttributes' => true,
            'outputBase64'    => false,
        ]);
        $qrCodes = [];
        foreach ($recentCertificates as $cert) {
            $url = route('certificates.verify', $cert->verification_code);
            $svg = (new QRCode($qrOptions))->render($url);
            $qrCodes[$cert->verification_code] = str_replace('<svg ', '<svg width="60" height="60" ', $svg);
        }

        return view('organization.certificates_index', compact('programs', 'recentCertificates', 'organization', 'qrCodes'));
    }
}


