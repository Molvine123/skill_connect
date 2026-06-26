<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Institution;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\Certificate;
use App\Models\AuditLog;

class InstitutionController extends Controller
{
    // ── Institution Dashboard (for institution-role users) ────────────────────

    public function dashboard()
    {
        $user        = Auth::user();
        $institution = $user->institution;

        $stats = [
            'total_students'     => 0,
            'active_enrollments' => 0,
            'certificates'       => 0,
            'completion_rate'    => 0,
        ];

        $students = collect();

        if ($institution) {
            $studentIds = $institution->students()->pluck('id');

            $activeEnrollments   = Enrollment::whereIn('student_id', $studentIds)->where('status', 'approved')->count();
            $completedEnrollments = Enrollment::whereIn('student_id', $studentIds)->where('status', 'completed')->count();
            $totalEnrollments    = Enrollment::whereIn('student_id', $studentIds)->count();
            $certificates        = Certificate::whereIn('student_id', $studentIds)->count();

            $stats = [
                'total_students'     => $institution->students()->count(),
                'active_enrollments' => $activeEnrollments,
                'certificates'       => $certificates,
                'completion_rate'    => $totalEnrollments > 0 ? round(($completedEnrollments / $totalEnrollments) * 100) : 0,
            ];

            $students = $institution->students()->with([
                'user',
                'enrollments.program.organization',
                'enrollments.certificate',
            ])->get();

            // Mock Data for Charts
            $studentGrowth = [
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                'data'   => [0, 5, 12, 25, 40, $stats['total_students']],
            ];
            $certificateTrends = [
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                'data'   => [0, 2, 8, 15, 20, $stats['certificates']],
            ];
        } else {
            $studentGrowth = ['labels' => [], 'data' => []];
            $certificateTrends = ['labels' => [], 'data' => []];
        }

        return view('dashboard.institution', compact('stats', 'students', 'institution', 'studentGrowth', 'certificateTrends'));
    }

    // ── Admin: List All Institutions ──────────────────────────────────────────

    public function index(Request $request)
    {
        $query = Institution::with('user')->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('registration_number', 'like', "%{$s}%")
                  ->orWhere('location', 'like', "%{$s}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $institutions = $query->paginate(15)->withQueryString();
        $typeLabels   = Institution::typeLabels();

        return view('institution.index', compact('institutions', 'typeLabels'));
    }

    // ── Admin/Self: View Single Institution ───────────────────────────────────

    public function show($id)
    {
        $institution = Institution::with(['user', 'students.user', 'students.enrollments.program'])->findOrFail($id);
        return view('institution.show', compact('institution'));
    }

    // ── Institution: Edit Profile Page ────────────────────────────────────────

    public function edit()
    {
        $user        = Auth::user();
        $institution = $user->institution;
        $typeLabels  = Institution::typeLabels();
        return view('institution.edit', compact('institution', 'typeLabels'));
    }

    // ── Institution: Update Profile ───────────────────────────────────────────

    public function updateProfile(Request $request)
    {
        $user        = Auth::user();
        $institution = $user->institution;

        $request->validate([
            'inst_name'           => 'required|string|max:255',
            'type'                => 'required|in:university,college,tvet',
            'registration_number' => 'required|string|max:100',
            'location'            => 'nullable|string|max:255',
            'county'              => 'nullable|string|max:100',
            'phone'               => 'nullable|string|max:20',
            'email'               => 'nullable|email|max:255',
            'website'             => 'nullable|url|max:255',
            'description'         => 'nullable|string|max:1000',
            'logo'                => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $logoPath = $institution?->logo_path;

        if ($request->hasFile('logo')) {
            if ($institution && $institution->logo_path) {
                Storage::disk('public')->delete($institution->logo_path);
            }
            $logoPath = $request->file('logo')->store('logos/institutions', 'public');
        }

        $user->update([
            'name'  => $request->inst_name,
            'phone' => $request->phone,
        ]);

        if ($institution) {
            $institution->update([
                'name'                => $request->inst_name,
                'type'                => $request->type,
                'registration_number' => $request->registration_number,
                'location'            => $request->location,
                'county'              => $request->county,
                'phone'               => $request->phone,
                'email'               => $request->email,
                'website'             => $request->website,
                'description'         => $request->description,
                'logo_path'           => $logoPath,
            ]);
        } else {
            // Create institution record if it doesn't exist yet
            Institution::create([
                'user_id'             => $user->id,
                'name'                => $request->inst_name,
                'type'                => $request->type,
                'registration_number' => $request->registration_number,
                'location'            => $request->location,
                'county'              => $request->county,
                'phone'               => $request->phone,
                'email'               => $request->email,
                'website'             => $request->website,
                'description'         => $request->description,
                'logo_path'           => $logoPath,
                'status'              => 'pending',
            ]);
        }

        AuditLog::log($user->id, 'update_institution_profile', "Updated profile for institution: {$user->name}");

        return redirect()->route('institution.dashboard')->with('success', 'Institution profile updated successfully.');
    }
}
