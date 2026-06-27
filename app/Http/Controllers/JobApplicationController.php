<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\EmployerJob;
use App\Models\JobApplication;
use App\Models\Interview;
use App\Models\EmploymentRecord;
use App\Models\AuditLog;

class JobApplicationController extends Controller
{
    // ── Student: Submit Application ───────────────────────────────────────────

    public function apply(Request $request, $jobId)
    {
        $student = Auth::user()->student;

        if (!$student) {
            return back()->with('error', 'You must have a student profile to apply for jobs.');
        }

        $job = EmployerJob::where('status', 'open')->findOrFail($jobId);

        // Prevent duplicate application
        $existing = JobApplication::where('employer_job_id', $jobId)
            ->where('student_id', $student->id)
            ->first();

        if ($existing) {
            return back()->with('error', 'You have already applied for this position.');
        }

        $request->validate([
            'cover_letter' => 'nullable|string|max:3000',
            'cv_file'      => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $cvPath = null;
        if ($request->hasFile('cv_file')) {
            $cvPath = $request->file('cv_file')->store('cvs', 'public');
            // Also save to student's profile if they don't have one
            if (!$student->cv_file) {
                $student->update(['cv_file' => $cvPath]);
            }
        } elseif ($student->cv_file) {
            $cvPath = $student->cv_file;
        }

        JobApplication::create([
            'employer_job_id' => $jobId,
            'student_id'      => $student->id,
            'cover_letter'    => $request->cover_letter,
            'cv_file'         => $cvPath,
            'status'          => 'submitted',
        ]);

        AuditLog::log(Auth::id(), 'apply_job', "Applied for job: {$job->title}");

        return redirect()->route('student.jobs.index')
            ->with('success', "Application submitted for \"{$job->title}\" successfully!");
    }

    // ── Student: My Applications ──────────────────────────────────────────────

    public function myApplications()
    {
        $student = Auth::user()->student;

        $applications = $student
            ? $student->jobApplications()
                ->with(['job.employer', 'interview'])
                ->latest()
                ->paginate(12)
            : collect();

        return view('student.applications', compact('applications'));
    }

    // ── Student: Withdraw Application ─────────────────────────────────────────

    public function withdraw($id)
    {
        $student = Auth::user()->student;
        $application = JobApplication::where('student_id', $student->id)
            ->where('status', 'submitted')
            ->findOrFail($id);

        $application->delete();

        return back()->with('success', 'Application withdrawn successfully.');
    }

    // ── Employer: Update Application Status ──────────────────────────────────

    public function updateStatus(Request $request, $id)
    {
        $employer = Auth::user()->employer;

        $application = JobApplication::whereHas('job', fn($q) => $q->where('employer_id', $employer->id))
            ->findOrFail($id);

        $request->validate([
            'status' => 'required|in:submitted,under_review,shortlisted,interview_scheduled,hired,rejected',
        ]);

        $application->update(['status' => $request->status]);

        // If hired, create an employment record
        if ($request->status === 'hired') {
            EmploymentRecord::firstOrCreate(
                ['student_id' => $application->student_id, 'employer_job_id' => $application->employer_job_id],
                [
                    'employer_id'       => $employer->id,
                    'employment_date'   => now()->toDateString(),
                    'employment_status' => 'hired',
                ]
            );
        }

        AuditLog::log(Auth::id(), 'update_application_status', "Updated application #{$id} to {$request->status}");

        return back()->with('success', 'Application status updated successfully.');
    }

    // ── Employer: Schedule Interview ──────────────────────────────────────────

    public function scheduleInterview(Request $request, $applicationId)
    {
        $employer = Auth::user()->employer;

        $application = JobApplication::whereHas('job', fn($q) => $q->where('employer_id', $employer->id))
            ->findOrFail($applicationId);

        $request->validate([
            'interview_date' => 'required|date|after_or_equal:today',
            'interview_time' => 'required',
            'venue'          => 'nullable|string|max:255',
            'meeting_link'   => 'nullable|url|max:255',
            'remarks'        => 'nullable|string|max:1000',
        ]);

        // Update or create interview
        Interview::updateOrCreate(
            ['job_application_id' => $applicationId],
            $request->only(['interview_date', 'interview_time', 'venue', 'meeting_link', 'remarks'])
        );

        // Update application status
        $application->update(['status' => 'interview_scheduled']);

        return back()->with('success', 'Interview scheduled successfully. The candidate has been notified.');
    }

    // ── Employer: Update Employment Record ────────────────────────────────────

    public function updateEmploymentStatus(Request $request, $id)
    {
        $employer = Auth::user()->employer;

        $record = EmploymentRecord::where('employer_id', $employer->id)->findOrFail($id);

        $request->validate([
            'employment_status' => 'required|in:hired,internship_placement,contract_completed,offer_declined',
        ]);

        $record->update(['employment_status' => $request->employment_status]);

        return back()->with('success', 'Employment record updated successfully.');
    }
}
