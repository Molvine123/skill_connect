<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function admin()
    {
        $stats = [
            'total_users'         => \App\Models\User::count(),
            'total_institutions'  => \App\Models\User::whereHas('role', fn($q) => $q->where('name', 'institution'))->count(),
            'total_organizations' => \App\Models\User::whereHas('role', fn($q) => $q->where('name', 'organization'))->count(),
            'total_students'      => \App\Models\User::whereHas('role', fn($q) => $q->where('name', 'student'))->count(),
        ];
        $recentUsers = \App\Models\User::with('role')->latest()->take(8)->get();
        return view('dashboard.admin', compact('stats', 'recentUsers'));
    }

    public function institution()
    {
        return view('dashboard.institution');
    }

    public function organization()
    {
        return view('dashboard.organization');
    }

    public function student()
    {
        $user    = Auth::user();
        $student = $user->student;

        // Ensure student profile exists
        if (!$student) {
            $student = \App\Models\Student::firstOrCreate(
                ['user_id' => $user->id],
                ['phone'   => $user->phone]
            );
        }

        // ── Enrollment stats ─────────────────────────────────────────────────
        $totalEnrollments  = $student->enrollments()->count();
        $activeEnrollments = $student->enrollments()->whereIn('status', ['approved'])->count();

        // ── Approved / completed program IDs for session lookup ──────────────
        $programIds = $student->enrollments()
            ->whereIn('status', ['approved', 'completed'])
            ->pluck('program_id');

        // ── Upcoming sessions in next 7 days ─────────────────────────────────
        $upcomingSessions = \App\Models\TrainingSession::whereIn('program_id', $programIds)
            ->whereBetween('start_date', [now(), now()->addDays(7)])
            ->count();

        // ── Certificates ─────────────────────────────────────────────────────
        $totalCertificates = \App\Models\Enrollment::where('student_id', $student->id)
            ->where('status', 'completed')
            ->whereHas('certificate')
            ->count();

        // ── Hours trained ─────────────────────────────────────────────────────
        $totalHours = $student->getTotalHoursTrained();

        // ── Recent enrollments (sidebar feed) ────────────────────────────────
        $recentEnrollments = $student->enrollments()
            ->with(['program.organization', 'program.category'])
            ->latest()
            ->take(5)
            ->get();

        // ── Next 3 upcoming sessions ──────────────────────────────────────────
        $nextSessions = \App\Models\TrainingSession::with('program')
            ->whereIn('program_id', $programIds)
            ->where('start_date', '>=', now())
            ->orderBy('start_date')
            ->take(3)
            ->get();

        // ── Recent payments ───────────────────────────────────────────────────
        $recentPayments = $student->payments()
            ->with('enrollment.program')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.student', compact(
            'student',
            'totalEnrollments',
            'activeEnrollments',
            'upcomingSessions',
            'totalCertificates',
            'totalHours',
            'recentEnrollments',
            'nextSessions',
            'recentPayments'
        ));
    }
}
