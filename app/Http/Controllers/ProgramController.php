<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\SkillProgram;
use App\Models\SkillCategory;
use App\Models\TrainingSession;
use App\Models\Enrollment;
use App\Models\Attendance;
use App\Models\Certificate;
use App\Models\Payment;
use App\Models\Student;
use App\Models\AuditLog;
use Barryvdh\DomPDF\Facade\Pdf;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class ProgramController extends Controller
{
    // ═════════════════════════════════════════════════════════════════════════
    // ── Organization: My Programs
    // ═════════════════════════════════════════════════════════════════════════

    public function index(Request $request)
    {
        $organization = Auth::user()->organization;

        if (!$organization) {
            return redirect()->route('organization.dashboard')->with('error', 'Organization profile not found.');
        }

        $query = $organization->programs()->with(['category', 'enrollments', 'sessions'])->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $programs   = $query->paginate(12)->withQueryString();
        $categories = SkillCategory::orderBy('name')->get();

        return view('program.organization.index', compact('programs', 'categories', 'organization'));
    }

    public function create()
    {
        $organization = Auth::user()->organization;

        if (!$organization) {
            return redirect()->route('organization.dashboard')->with('error', 'Organization profile not found.');
        }

        $categories = SkillCategory::orderBy('name')->get();

        return view('program.organization.create', compact('categories', 'organization'));
    }

    public function store(Request $request)
    {
        $organization = Auth::user()->organization;

        if (!$organization) {
            return redirect()->route('organization.dashboard')->with('error', 'Organization profile not found.');
        }

        $request->validate([
            'name'             => 'required|string|max:255',
            'category_id'      => 'required|exists:skill_categories,id',
            'description'      => 'required|string|max:3000',
            'duration'         => 'required|string|max:100',
            'cost'             => 'required|numeric|min:0',
            'mode'             => 'required|in:online,in_person,hybrid',
            'venue'            => 'nullable|string|max:300',
            'capacity'         => 'required|integer|min:1',
            'requirements'     => 'nullable|string|max:1500',
            'learning_outcomes'=> 'nullable|string|max:1500',
            'status'           => 'required|in:draft,published,closed',
        ]);

        $program = SkillProgram::create([
            'organization_id'  => $organization->id,
            'category_id'      => $request->category_id,
            'name'             => $request->name,
            'description'      => $request->description,
            'duration'         => $request->duration,
            'cost'             => $request->cost,
            'mode'             => $request->mode,
            'venue'            => $request->venue,
            'capacity'         => $request->capacity,
            'requirements'     => $request->requirements,
            'learning_outcomes'=> $request->learning_outcomes,
            'status'           => $request->status,
        ]);

        AuditLog::log(Auth::id(), 'org_create_program', "Created program: {$program->name} (ID: {$program->id})");

        return redirect()->route('organization.programs.show', $program->id)
            ->with('success', 'Program created successfully!');
    }

    public function show($id)
    {
        $organization = Auth::user()->organization;

        if (!$organization) {
            return redirect()->route('organization.dashboard')->with('error', 'Organization profile not found.');
        }

        $program = SkillProgram::where('organization_id', $organization->id)
            ->with([
                'category',
                'sessions',
                'enrollments.student.user',
                'enrollments.payment',
                'enrollments.certificate',
            ])
            ->findOrFail($id);

        return view('program.organization.show', compact('program', 'organization'));
    }

    public function edit($id)
    {
        $organization = Auth::user()->organization;

        if (!$organization) {
            return redirect()->route('organization.dashboard')->with('error', 'Organization profile not found.');
        }

        $program    = SkillProgram::where('organization_id', $organization->id)->findOrFail($id);
        $categories = SkillCategory::orderBy('name')->get();

        return view('program.organization.edit', compact('program', 'categories', 'organization'));
    }

    public function update(Request $request, $id)
    {
        $organization = Auth::user()->organization;

        if (!$organization) {
            return redirect()->route('organization.dashboard')->with('error', 'Organization profile not found.');
        }

        $program = SkillProgram::where('organization_id', $organization->id)->findOrFail($id);

        $request->validate([
            'name'             => 'required|string|max:255',
            'category_id'      => 'required|exists:skill_categories,id',
            'description'      => 'required|string|max:3000',
            'duration'         => 'required|string|max:100',
            'cost'             => 'required|numeric|min:0',
            'mode'             => 'required|in:online,in_person,hybrid',
            'venue'            => 'nullable|string|max:300',
            'capacity'         => 'required|integer|min:1',
            'requirements'     => 'nullable|string|max:1500',
            'learning_outcomes'=> 'nullable|string|max:1500',
            'status'           => 'required|in:draft,published,closed',
        ]);

        $program->update($request->only([
            'name', 'category_id', 'description', 'duration', 'cost',
            'mode', 'venue', 'capacity', 'requirements', 'learning_outcomes', 'status',
        ]));

        AuditLog::log(Auth::id(), 'org_update_program', "Updated program: {$program->name} (ID: {$program->id})");

        return redirect()->route('organization.programs.show', $program->id)
            ->with('success', 'Program updated successfully.');
    }

    public function destroy($id)
    {
        $organization = Auth::user()->organization;

        if (!$organization) {
            return redirect()->route('organization.dashboard')->with('error', 'Organization profile not found.');
        }

        $program = SkillProgram::where('organization_id', $organization->id)->findOrFail($id);
        $name = $program->name;
        $program->delete();

        AuditLog::log(Auth::id(), 'org_delete_program', "Deleted program: {$name} (ID: {$id})");

        return redirect()->route('organization.programs.index')
            ->with('success', "Program '{$name}' deleted.");
    }

    // ═════════════════════════════════════════════════════════════════════════
    // ── Organization: Training Sessions Management
    // ═════════════════════════════════════════════════════════════════════════

    public function sessions($id)
    {
        $organization = Auth::user()->organization;
        $program = SkillProgram::where('organization_id', $organization->id)
            ->with(['sessions.virtualClass'])
            ->findOrFail($id);

        return view('program.organization.sessions', compact('program', 'organization'));
    }

    public function storeSession(Request $request, $id)
    {
        $organization = Auth::user()->organization;
        $program = SkillProgram::where('organization_id', $organization->id)->findOrFail($id);

        $request->validate([
            'title'               => 'required|string|max:255',
            'description'         => 'nullable|string|max:1000',
            'start_date'          => 'required|date',
            'end_date'            => 'required|date|after:start_date',
            'venue'               => 'nullable|string|max:300',
            'meeting_link'        => 'nullable|url|max:500',
            'max_participants'    => 'nullable|integer|min:1',
            'trainer_information' => 'nullable|string|max:500',
        ]);

        TrainingSession::create([
            'program_id'          => $program->id,
            'title'               => $request->title,
            'description'         => $request->description,
            'start_date'          => $request->start_date,
            'end_date'            => $request->end_date,
            'venue'               => $request->venue,
            'meeting_link'        => $request->meeting_link,
            'max_participants'    => $request->max_participants,
            'trainer_information' => $request->trainer_information,
        ]);

        AuditLog::log(Auth::id(), 'org_add_session', "Added session '{$request->title}' to program ID: {$program->id}");

        return redirect()->route('organization.programs.sessions', $program->id)
            ->with('success', 'Session added successfully.');
    }

    public function destroySession($programId, $sessionId)
    {
        $organization = Auth::user()->organization;
        $program = SkillProgram::where('organization_id', $organization->id)->findOrFail($programId);

        $session = TrainingSession::where('program_id', $program->id)->findOrFail($sessionId);
        $title = $session->title;
        $session->delete();

        AuditLog::log(Auth::id(), 'org_delete_session', "Deleted session '{$title}' from program ID: {$programId}");

        return redirect()->route('organization.programs.sessions', $programId)
            ->with('success', "Session '{$title}' deleted.");
    }

    // ═════════════════════════════════════════════════════════════════════════
    // ── Organization: Enrollment Management (Approve / Reject)
    // ═════════════════════════════════════════════════════════════════════════

    public function enrollments($id)
    {
        $organization = Auth::user()->organization;
        $program = SkillProgram::where('organization_id', $organization->id)
            ->with(['enrollments.student.user', 'enrollments.payment'])
            ->findOrFail($id);

        return view('program.organization.enrollments', compact('program', 'organization'));
    }

    public function approveEnrollment($programId, $enrollmentId)
    {
        $organization = Auth::user()->organization;
        $program = SkillProgram::where('organization_id', $organization->id)->findOrFail($programId);

        $enrollment = Enrollment::where('program_id', $program->id)->findOrFail($enrollmentId);
        $enrollment->update(['status' => 'approved']);

        AuditLog::log(Auth::id(), 'org_approve_enrollment', "Approved enrollment ID: {$enrollmentId} for program: {$program->name}");

        return back()->with('success', "Enrollment approved for {$enrollment->student->user->name}.");
    }

    public function rejectEnrollment($programId, $enrollmentId)
    {
        $organization = Auth::user()->organization;
        $program = SkillProgram::where('organization_id', $organization->id)->findOrFail($programId);

        $enrollment = Enrollment::where('program_id', $program->id)->findOrFail($enrollmentId);
        $enrollment->update(['status' => 'rejected']);

        AuditLog::log(Auth::id(), 'org_reject_enrollment', "Rejected enrollment ID: {$enrollmentId} for program: {$program->name}");

        return back()->with('success', "Enrollment rejected for {$enrollment->student->user->name}.");
    }

    public function completeEnrollment($programId, $enrollmentId)
    {
        $organization = Auth::user()->organization;
        $program = SkillProgram::where('organization_id', $organization->id)->findOrFail($programId);

        $enrollment = Enrollment::where('program_id', $program->id)->findOrFail($enrollmentId);
        $enrollment->update(['status' => 'completed']);

        AuditLog::log(Auth::id(), 'org_complete_enrollment', "Marked enrollment ID: {$enrollmentId} as completed.");

        return back()->with('success', "Enrollment marked as completed for {$enrollment->student->user->name}.");
    }

    // ═════════════════════════════════════════════════════════════════════════
    // ── Organization: Attendance Tracking
    // ═════════════════════════════════════════════════════════════════════════

    public function attendance($programId, $sessionId)
    {
        $organization = Auth::user()->organization;
        $program = SkillProgram::where('organization_id', $organization->id)->findOrFail($programId);
        $session = TrainingSession::where('program_id', $program->id)->findOrFail($sessionId);

        // Get approved/completed students for this program
        $enrollments = Enrollment::where('program_id', $program->id)
            ->whereIn('status', ['approved', 'completed'])
            ->with('student.user')
            ->get();

        // Get existing attendance records
        $attendanceMap = Attendance::where('session_id', $session->id)
            ->pluck('present', 'student_id')
            ->toArray();

        return view('program.organization.attendance', compact('program', 'session', 'enrollments', 'attendanceMap', 'organization'));
    }

    public function saveAttendance(Request $request, $programId, $sessionId)
    {
        $organization = Auth::user()->organization;
        $program = SkillProgram::where('organization_id', $organization->id)->findOrFail($programId);
        $session = TrainingSession::where('program_id', $program->id)->findOrFail($sessionId);

        $enrollments = Enrollment::where('program_id', $program->id)
            ->whereIn('status', ['approved', 'completed'])
            ->pluck('student_id');

        foreach ($enrollments as $studentId) {
            Attendance::updateOrCreate(
                ['session_id' => $session->id, 'student_id' => $studentId],
                ['present' => in_array($studentId, $request->input('present', []))]
            );
        }

        AuditLog::log(Auth::id(), 'org_save_attendance', "Saved attendance for session ID: {$sessionId}, program: {$program->name}");

        return redirect()->route('organization.programs.sessions', $programId)
            ->with('success', 'Attendance recorded successfully.');
    }

    // ═════════════════════════════════════════════════════════════════════════
    // ── Organization: Certificate Issuance
    // ═════════════════════════════════════════════════════════════════════════

    public function certificates($id)
    {
        $organization = Auth::user()->organization;
        $program = SkillProgram::where('organization_id', $organization->id)
            ->with(['enrollments' => function ($q) {
                $q->where('status', 'completed')->with(['student.user', 'certificate']);
            }])
            ->findOrFail($id);

        return view('program.organization.certificates', compact('program', 'organization'));
    }

    public function issueCertificate($programId, $enrollmentId)
    {
        $organization = Auth::user()->organization;
        $program = SkillProgram::where('organization_id', $organization->id)->findOrFail($programId);
        $enrollment = Enrollment::where('program_id', $program->id)
            ->where('status', 'completed')
            ->findOrFail($enrollmentId);

        if ($enrollment->certificate) {
            return back()->with('error', 'Certificate already issued for this enrollment.');
        }

        Certificate::create([
            'enrollment_id'     => $enrollment->id,
            'verification_code' => strtoupper(Str::random(12)),
            'issue_date'        => now()->toDateString(),
        ]);

        AuditLog::log(Auth::id(), 'org_issue_certificate', "Issued certificate for enrollment ID: {$enrollmentId}, program: {$program->name}");

        return back()->with('success', "Certificate issued for {$enrollment->student->user->name}.");
    }

    public function issueAllCertificates($programId)
    {
        $organization = Auth::user()->organization;
        $program = SkillProgram::where('organization_id', $organization->id)->findOrFail($programId);

        $completedEnrollments = Enrollment::where('program_id', $program->id)
            ->where('status', 'completed')
            ->doesntHave('certificate')
            ->get();

        $count = 0;
        foreach ($completedEnrollments as $enrollment) {
            Certificate::create([
                'enrollment_id'     => $enrollment->id,
                'verification_code' => strtoupper(Str::random(12)),
                'issue_date'        => now()->toDateString(),
            ]);
            $count++;
        }

        AuditLog::log(Auth::id(), 'org_issue_all_certificates', "Bulk issued {$count} certificates for program: {$program->name}");

        return back()->with('success', "{$count} certificate(s) issued successfully.");
    }

    // ═════════════════════════════════════════════════════════════════════════
    // ── Admin: All Programs Overview
    // ═════════════════════════════════════════════════════════════════════════

    public function adminIndex(Request $request)
    {
        $query = SkillProgram::with(['organization', 'category'])
            ->withCount(['enrollments', 'sessions']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhereHas('organization', fn($o) => $o->where('name', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $programs   = $query->latest()->paginate(20)->withQueryString();
        $categories = SkillCategory::orderBy('name')->get();

        $totalPublished = SkillProgram::where('status', 'published')->count();
        $totalDraft     = SkillProgram::where('status', 'draft')->count();
        $totalClosed    = SkillProgram::where('status', 'closed')->count();
        $totalEnrolled  = Enrollment::count();

        return view('program.admin.index', compact(
            'programs', 'categories',
            'totalPublished', 'totalDraft', 'totalClosed', 'totalEnrolled'
        ));
    }

    public function adminShow($id)
    {
        $program = SkillProgram::with([
            'organization',
            'category',
            'sessions',
            'enrollments.student.user',
            'enrollments.payment',
            'enrollments.certificate',
        ])->findOrFail($id);

        return view('program.admin.show', compact('program'));
    }

    // ═════════════════════════════════════════════════════════════════════════
    // ── Module 10 & 11: QR Attendance and PDF Certificates
    // ═════════════════════════════════════════════════════════════════════════

    public function showSessionQr($programId, $sessionId)
    {
        $organization = Auth::user()->organization;
        $program = SkillProgram::where('organization_id', $organization->id)->findOrFail($programId);
        $session = TrainingSession::where('program_id', $program->id)->findOrFail($sessionId);

        // This route goes to StudentController to process attendance
        $url = route('student.sessions.attend', $session->id);
        
        $options = new QROptions([
            'outputInterface' => \chillerlan\QRCode\Output\QRMarkupSVG::class,
            'eccLevel'        => \chillerlan\QRCode\Common\EccLevel::L,
        ]);
        $qrCode = (new QRCode($options))->render($url);

        return view('program.organization.session-qr', compact('program', 'session', 'qrCode', 'organization'));
    }

    public function downloadCertificate($programId, $enrollmentId)
    {
        $program = SkillProgram::findOrFail($programId);
        $enrollment = Enrollment::where('program_id', $program->id)
            ->where('status', 'completed')
            ->findOrFail($enrollmentId);

        $user = Auth::user();
        $isOrg = $user->organization && $user->organization->id === $program->organization_id;
        $isStudent = $user->student && $user->student->id === $enrollment->student_id;

        if (!$isOrg && !$isStudent) {
            abort(403, 'Unauthorized action.');
        }

        $certificate = $enrollment->certificate;
        if (!$certificate) {
            abort(404, 'Certificate not issued yet.');
        }

        $verifyUrl = route('certificates.verify', $certificate->verification_code);
        $options = new QROptions([
            'eccLevel'     => \chillerlan\QRCode\Common\EccLevel::L,
            'addQuietzone' => false,
        ]);
        $qrcode = new QRCode($options);
        $qrcode->addSegment(new \chillerlan\QRCode\Data\Byte($verifyUrl));
        $matrix = $qrcode->getQRMatrix();
        $moduleCount = $matrix->moduleCount;
        
        $sizePx = 90;
        $cellSize = $sizePx / $moduleCount;
        
        $qrCode = '<table style="border-collapse: collapse; border: none; padding: 0; margin: 0; line-height: 0; width: ' . $sizePx . 'px; height: ' . $sizePx . 'px; table-layout: fixed; background-color: #ffffff;">';
        for ($y = 0; $y < $moduleCount; $y++) {
            $qrCode .= '<tr style="height: ' . $cellSize . 'px; padding: 0; margin: 0; line-height: 0;">';
            for ($x = 0; $x < $moduleCount; $x++) {
                $isDark = $matrix->isDark($matrix->matrix[$y][$x]);
                $color = $isDark ? '#000000' : '#ffffff';
                $qrCode .= '<td style="width: ' . $cellSize . 'px; height: ' . $cellSize . 'px; background-color: ' . $color . '; padding: 0; margin: 0; border: none; line-height: 0; font-size: 0px;"></td>';
            }
            $qrCode .= '</tr>';
        }
        $qrCode .= '</table>';

        $pdf = Pdf::loadView('pdf.certificate', compact('enrollment', 'certificate', 'program', 'qrCode'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download("Certificate_{$certificate->verification_code}.pdf");
    }

    public function verifyCertificate($code)
    {
        $certificate = Certificate::with(['enrollment.student.user', 'enrollment.program.organization'])
            ->where('verification_code', $code)
            ->first();

        if (!$certificate) {
            return view('certificate.verify', ['valid' => false, 'code' => $code]);
        }

        // Generate QR code pointing to this very verification URL
        $verifyUrl = route('certificates.verify', $certificate->verification_code);
        $options   = new QROptions([
            'outputInterface' => \chillerlan\QRCode\Output\QRMarkupSVG::class,
            'eccLevel'        => \chillerlan\QRCode\Common\EccLevel::H,
            'svgAddXmlHeader' => false,
            'svgUseFillAttributes' => true,
            'outputBase64'    => false,
        ]);
        $qrCode = (new QRCode($options))->render($verifyUrl);
        $qrCode = str_replace('<svg ', '<svg width="88" height="88" ', $qrCode);

        return view('certificate.verify', [
            'valid'       => true,
            'certificate' => $certificate,
            'qrCode'      => $qrCode,
        ]);
    }
}
