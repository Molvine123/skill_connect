<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Student;
use App\Models\Institution;
use App\Models\SkillCategory;
use App\Models\SkillProgram;
use App\Models\Enrollment;
use App\Models\TrainingSession;
use App\Models\Attendance;
use App\Models\Payment;
use App\Models\Certificate;
use App\Models\AuditLog;
use App\Models\Role;

class StudentController extends Controller
{
    // ═════════════════════════════════════════════════════════════════════════
    // ── Admin: Student Management Actions
    // ═════════════════════════════════════════════════════════════════════════

    public function index(Request $request)
    {
        $query = Student::with(['user', 'institution'])->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('user', function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%");
            })->orWhere('registration_number', 'like', "%{$s}%");
        }

        if ($request->filled('institution_id')) {
            $query->where('institution_id', $request->institution_id);
        }

        $students = $query->paginate(15)->withQueryString();
        $institutions = Institution::where('status', 'active')->orderBy('name')->get();

        return view('student.admin.index', compact('students', 'institutions'));
    }

    public function show($id)
    {
        $student = Student::with([
            'user', 
            'institution', 
            'enrollments.program.organization', 
            'enrollments.payment', 
            'enrollments.certificate',
            'payments',
            'attendances.session'
        ])->findOrFail($id);

        $institutions = Institution::where('status', 'active')->orderBy('name')->get();

        return view('student.admin.show', compact('student', 'institutions'));
    }

    public function edit($id)
    {
        $student = Student::with('user')->findOrFail($id);
        return view('student.admin.edit', compact('student'));
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        $user = $student->user;

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'registration_number' => 'nullable|string|max:100',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        $student->update([
            'registration_number' => $request->registration_number,
            'phone' => $request->phone,
        ]);

        AuditLog::log(Auth::id(), 'admin_update_student', "Updated student user: {$user->name} (ID: {$student->id})");

        return redirect()->route('admin.students.show', $student->id)->with('success', 'Student profile updated successfully.');
    }

    public function assignInstitution(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $request->validate([
            'institution_id' => 'nullable|exists:institutions,id',
        ]);

        $student->update([
            'institution_id' => $request->institution_id,
        ]);

        $instName = $request->institution_id ? Institution::find($request->institution_id)->name : 'None (Unassigned)';
        AuditLog::log(Auth::id(), 'admin_assign_institution', "Assigned student ID {$student->id} to institution: {$instName}");

        return back()->with('success', "Student's institution assignment updated to: {$instName}.");
    }

    // ═════════════════════════════════════════════════════════════════════════
    // ── Institution: Student Management Actions
    // ═════════════════════════════════════════════════════════════════════════

    public function institutionIndex(Request $request)
    {
        $institution = Auth::user()->institution;

        if (!$institution) {
            return redirect()->route('institution.dashboard')->with('error', 'Institution profile not found.');
        }

        $query = Student::with(['user', 'enrollments.program'])
            ->where('institution_id', $institution->id)
            ->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($sub) use ($s) {
                $sub->whereHas('user', function ($q) use ($s) {
                    $q->where('name', 'like', "%{$s}%")
                      ->orWhere('email', 'like', "%{$s}%");
                })->orWhere('registration_number', 'like', "%{$s}%");
            });
        }

        $students = $query->paginate(15)->withQueryString();

        return view('student.institution.index', compact('students', 'institution'));
    }

    public function institutionShow($id)
    {
        $institution = Auth::user()->institution;

        if (!$institution) {
            return redirect()->route('institution.dashboard')->with('error', 'Institution profile not found.');
        }

        $student = Student::where('institution_id', $institution->id)
            ->with([
                'user',
                'enrollments.program.organization',
                'enrollments.payment',
                'enrollments.certificate',
                'payments',
                'attendances.session'
            ])
            ->findOrFail($id);

        return view('student.institution.show', compact('student', 'institution'));
    }

    public function institutionAddForm(Request $request)
    {
        $institution = Auth::user()->institution;

        if (!$institution) {
            return redirect()->route('institution.dashboard')->with('error', 'Institution profile not found.');
        }

        // Fetch unassigned students
        $query = Student::with('user')->whereNull('institution_id');

        if ($request->filled('search_unassigned')) {
            $s = $request->search_unassigned;
            $query->whereHas('user', function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        $unassignedStudents = $query->paginate(10, ['*'], 'unassigned_page')->withQueryString();

        return view('student.institution.add', compact('unassignedStudents', 'institution'));
    }

    public function institutionStoreStudent(Request $request)
    {
        $institution = Auth::user()->institution;

        if (!$institution) {
            return redirect()->route('institution.dashboard')->with('error', 'Institution profile not found.');
        }

        $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone'     => ['nullable', 'string', 'max:20'],
            'registration_number' => ['nullable', 'string', 'max:100'],
            'password'  => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $role = Role::where('name', 'student')->firstOrFail();

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'role_id'  => $role->id,
            'status'   => 'active',
        ]);

        $student = Student::create([
            'user_id'             => $user->id,
            'institution_id'      => $institution->id,
            'registration_number' => $request->registration_number,
            'phone'               => $request->phone,
        ]);

        AuditLog::log(Auth::id(), 'institution_register_student', "Institution registered and assigned student: {$user->name} (ID: {$student->id})");

        return redirect()->route('institution.students.index')->with('success', 'Student registered and added to your institution successfully.');
    }

    public function institutionAssignStudent(Request $request)
    {
        $institution = Auth::user()->institution;

        if (!$institution) {
            return redirect()->route('institution.dashboard')->with('error', 'Institution profile not found.');
        }

        $request->validate([
            'student_id' => 'required|exists:students,id',
        ]);

        $student = Student::findOrFail($request->student_id);

        if ($student->institution_id !== null) {
            return back()->with('error', 'Student is already assigned to an institution.');
        }

        $student->update([
            'institution_id' => $institution->id,
        ]);

        AuditLog::log(Auth::id(), 'institution_claim_student', "Institution claimed student: {$student->user->name} (ID: {$student->id})");

        return redirect()->route('institution.students.index')->with('success', "Student {$student->user->name} added to your institution successfully.");
    }

    // ═════════════════════════════════════════════════════════════════════════
    // ── Student: Portal Actions
    // ═════════════════════════════════════════════════════════════════════════

    public function editProfile()
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            // Safe fallback: create student profile if somehow deleted
            $role = Role::where('name', 'student')->first();
            $student = Student::firstOrCreate(
                ['user_id' => $user->id],
                ['phone' => $user->phone]
            );
        }

        return view('student.portal.edit_profile', compact('user', 'student'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $student = $user->student;

        $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'registration_number' => 'nullable|string|max:100',
            'password' => 'nullable|confirmed|min:8',
        ]);

        $user->update([
            'name'  => $request->name,
            'phone' => $request->phone,
        ]);

        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        if ($student) {
            $student->update([
                'registration_number' => $request->registration_number,
                'phone' => $request->phone,
            ]);
        }

        AuditLog::log($user->id, 'student_update_profile', "Student self-updated profile.");

        return redirect()->route('student.dashboard')->with('success', 'Profile updated successfully.');
    }

    public function browsePrograms(Request $request)
    {
        $categories = SkillCategory::withCount('programs')->get();
        
        $query = SkillProgram::with(['organization', 'category'])
            ->where('status', 'published');

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%");
            });
        }

        $programs = $query->paginate(12)->withQueryString();

        $user = Auth::user();
        $student = $user->student;
        $myEnrollments = $student ? $student->enrollments()->pluck('status', 'program_id')->toArray() : [];

        $eligibleStudents = collect();
        if ($user->isInstitution() && $user->institution) {
            $eligibleStudents = $user->institution->students()->with('user')->get();
        } elseif ($user->isOrganization()) {
            $eligibleStudents = \App\Models\Student::with('user')->get();
        }

        return view('student.portal.programs', compact('programs', 'categories', 'myEnrollments', 'eligibleStudents'));
    }

    public function enrollInProgram(Request $request, $id)
    {
        $user = Auth::user();
        
        if ($user->isStudent()) {
            $student = $user->student;
            if (!$student) {
                return back()->with('error', 'Student profile not found. Cannot enroll.');
            }
        } else {
            // For Institutions and Organizations
            $request->validate(['student_id' => 'required|exists:students,id']);
            $student = \App\Models\Student::findOrFail($request->student_id);
            
            // If institution, ensure student belongs to them
            if ($user->isInstitution() && $student->institution_id !== $user->institution?->id) {
                return back()->with('error', 'You can only enroll students registered under your institution.');
            }
        }

        $program = SkillProgram::findOrFail($id);

        // Check if already enrolled
        $exists = Enrollment::where('student_id', $student->id)
            ->where('program_id', $program->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'You are already enrolled in this program.');
        }

        // Auto approve if program is free
        $isFree = $program->cost <= 0;
        $status = $isFree ? 'approved' : 'pending';

        DB::beginTransaction();
        try {
            $enrollment = Enrollment::create([
                'student_id' => $student->id,
                'program_id' => $program->id,
                'status'     => $status,
            ]);

            Payment::create([
                'enrollment_id'         => $enrollment->id,
                'student_id'            => $student->id,
                'amount'                => $program->cost,
                'payment_method'        => $isFree ? 'Free' : 'M-Pesa',
                'transaction_reference' => $isFree ? 'FREE-' . strtoupper(Str::random(8)) : 'PEND-' . strtoupper(Str::random(8)),
                'status'                => $isFree ? 'paid' : 'pending',
            ]);

            DB::commit();

            AuditLog::log(Auth::id(), 'student_enroll_program', "Student enrolled in program: {$program->name} (Enrollment Status: {$status})");

            if (!$user->isStudent()) {
                $msg = "Successfully enrolled student {$student->user->name} in program {$program->name}.";
                return back()->with('success', $msg);
            }

            if ($isFree) {
                return redirect()->route('student.enrollments.index')
                    ->with('success', 'Enrolled successfully! Since this is a free course, your enrollment is automatically approved.');
            }

            // Redirect to M-Pesa checkout for paid programs
            return redirect()->route('student.payment.checkout', $enrollment->id)
                ->with('info', 'Please complete your M-Pesa payment to activate your enrollment.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred during enrollment: ' . $e->getMessage());
        }
    }

    public function cancelEnrollment($id)
    {
        $student = Auth::user()->student;
        if (!$student) {
            return back()->with('error', 'Student profile not found.');
        }

        $enrollment = Enrollment::where('student_id', $student->id)
            ->where('program_id', $id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if (!$enrollment) {
            return back()->with('error', 'Enrollment not found or cannot be cancelled.');
        }

        DB::beginTransaction();
        try {
            // Delete associated payment if pending
            if ($enrollment->payment && $enrollment->payment->status === 'pending') {
                $enrollment->payment->delete();
            }

            $enrollment->delete();
            
            DB::commit();

            AuditLog::log(Auth::id(), 'student_cancel_enrollment', "Student cancelled enrollment for program ID: {$id}");

            return back()->with('success', 'Your enrollment has been successfully cancelled.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to cancel enrollment: ' . $e->getMessage());
        }
    }

    public function payForEnrollment($id)
    {
        $student = Auth::user()->student;
        if (!$student) {
            return back()->with('error', 'Student profile not found.');
        }

        $enrollment = Enrollment::with('payment')
            ->where('student_id', $student->id)
            ->where('id', $id)
            ->firstOrFail();

        if ($enrollment->status !== 'pending' || !$enrollment->payment || $enrollment->payment->status === 'paid') {
            return back()->with('error', 'Enrollment is already paid or cannot be processed.');
        }

        DB::beginTransaction();
        try {
            $enrollment->payment->update([
                'status' => 'paid',
                'transaction_reference' => 'SIM-PAY-' . strtoupper(\Illuminate\Support\Str::random(8))
            ]);

            $enrollment->update([
                'status' => 'approved'
            ]);

            DB::commit();

            AuditLog::log(Auth::id(), 'student_payment_success', "Student paid for enrollment ID: {$enrollment->id}");

            return back()->with('success', 'Payment successful! You are now fully enrolled.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Payment failed: ' . $e->getMessage());
        }
    }

    public function myEnrollments()
    {
        $student = Auth::user()->student;
        
        $enrollments = $student 
            ? $student->enrollments()->with(['program.organization', 'payment', 'certificate'])->latest()->get()
            : collect();

        return view('student.portal.enrollments', compact('enrollments'));
    }

    public function upcomingSessions()
    {
        $student = Auth::user()->student;

        if (!$student) {
            $sessions = collect();
            return view('student.portal.sessions', compact('sessions'));
        }

        $programIds = Enrollment::where('student_id', $student->id)
            ->whereIn('status', ['approved', 'completed'])
            ->pluck('program_id');

        $sessions = TrainingSession::with(['program', 'virtualClass'])
            ->whereIn('program_id', $programIds)
            ->where('start_date', '>=', now())
            ->orderBy('start_date')
            ->get();

        return view('student.portal.sessions', compact('sessions'));
    }

    public function myCertificates()
    {
        $student = Auth::user()->student;

        $certificates = $student 
            ? $student->user->student->enrollments()
                ->where('status', 'completed')
                ->whereHas('certificate')
                ->with(['program.organization', 'certificate'])
                ->get()
                ->pluck('certificate')
            : collect();

        return view('student.portal.certificates', compact('certificates'));
    }

    public function myPayments()
    {
        $student = Auth::user()->student;

        $payments = $student 
            ? $student->payments()->with('enrollment.program')->latest()->get()
            : collect();

        return view('student.portal.payments', compact('payments'));
    }

    // ═════════════════════════════════════════════════════════════════════════
    // ── Module 10 & 11: QR Attendance and Certificate Download
    // ═════════════════════════════════════════════════════════════════════════

    public function markAttendanceViaQr($id)
    {
        $student = Auth::user()->student;
        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }

        $session = TrainingSession::findOrFail($id);
        
        // Ensure student is enrolled in the program and approved
        $isEnrolled = Enrollment::where('student_id', $student->id)
            ->where('program_id', $session->program_id)
            ->whereIn('status', ['approved', 'completed'])
            ->exists();

        if (!$isEnrolled) {
            return redirect()->route('student.dashboard')->with('error', 'You are not enrolled in this program.');
        }

        Attendance::updateOrCreate(
            ['session_id' => $session->id, 'student_id' => $student->id],
            ['present' => true]
        );

        AuditLog::log(Auth::id(), 'student_mark_attendance', "Student marked attendance via QR for session ID: {$session->id}");

        return redirect()->route('student.sessions.index')->with('success', "Attendance marked successfully for session: {$session->title}");
    }

    public function downloadCertificate($id)
    {
        $student = Auth::user()->student;
        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }
        
        $certificate = Certificate::whereHas('enrollment', function($q) use ($student) {
            $q->where('student_id', $student->id);
        })->findOrFail($id);

        $enrollment = $certificate->enrollment;
        $program = $enrollment->program;

        $verifyUrl = route('certificates.verify', $certificate->verification_code);
        $options = new \chillerlan\QRCode\QROptions([
            'eccLevel'     => \chillerlan\QRCode\Common\EccLevel::L,
            'addQuietzone' => false,
        ]);
        $qrcode = new \chillerlan\QRCode\QRCode($options);
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

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.certificate', compact('enrollment', 'certificate', 'program', 'qrCode'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download("Certificate_{$certificate->verification_code}.pdf");
    }

    // ═════════════════════════════════════════════════════════════════════════
    // ── Student: Portfolio Management
    // ═════════════════════════════════════════════════════════════════════════

    public function editPortfolio()
    {
        $user    = Auth::user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }

        return view('student.portal.portfolio', compact('user', 'student'));
    }

    public function updatePortfolio(Request $request)
    {
        $user    = Auth::user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }

        $request->validate([
            'bio'           => 'nullable|string|max:1000',
            'linkedin_url'  => 'nullable|url|max:255',
            'portfolio_url' => 'nullable|url|max:255',
            'location'      => 'nullable|string|max:255',
            'open_to_work'  => 'nullable|boolean',
            'cv_file'       => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $cvPath = $student->cv_file;
        if ($request->hasFile('cv_file')) {
            if ($cvPath) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($cvPath);
            }
            $cvPath = $request->file('cv_file')->store('cvs', 'public');
        }

        $student->update([
            'bio'           => $request->bio,
            'linkedin_url'  => $request->linkedin_url,
            'portfolio_url' => $request->portfolio_url,
            'location'      => $request->location,
            'open_to_work'  => $request->boolean('open_to_work'),
            'cv_file'       => $cvPath,
        ]);

        AuditLog::log($user->id, 'student_update_portfolio', 'Student updated professional portfolio.');

        return redirect()->route('student.portfolio.edit')->with('success', 'Portfolio updated successfully!');
    }
}

