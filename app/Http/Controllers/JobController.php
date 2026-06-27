<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employer;
use App\Models\EmployerJob;
use App\Models\JobApplication;
use App\Models\AuditLog;

class JobController extends Controller
{
    // ── Employer: List Jobs ───────────────────────────────────────────────────

    public function index(Request $request)
    {
        $employer = Auth::user()->employer;

        if (!$employer) {
            return redirect()->route('employer.profile.edit')
                ->with('error', 'Please complete your company profile first.');
        }

        $query = $employer->jobs()->withCount('applications');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $jobs = $query->latest()->paginate(15)->withQueryString();
        return view('employer.jobs.index', compact('jobs', 'employer'));
    }

    // ── Employer: Create Job Form ─────────────────────────────────────────────

    public function create()
    {
        return view('employer.jobs.create');
    }

    // ── Employer: Store Job ───────────────────────────────────────────────────

    public function store(Request $request)
    {
        $employer = Auth::user()->employer;

        if (!$employer) {
            return redirect()->route('employer.profile.edit')
                ->with('error', 'Please complete your company profile first.');
        }

        $request->validate([
            'title'                   => 'required|string|max:255',
            'description'             => 'required|string',
            'type'                    => 'required|in:job,internship',
            'location'                => 'nullable|string|max:255',
            'employment_type'         => 'nullable|string|max:100',
            'salary'                  => 'nullable|string|max:100',
            'duration'                => 'nullable|string|max:100',
            'requirements'            => 'nullable|string',
            'required_skills'         => 'nullable|string',
            'required_qualifications' => 'nullable|string|max:255',
            'experience_level'        => 'nullable|in:Entry,Mid,Senior',
            'deadline'                => 'nullable|date|after:today',
        ]);

        $job = $employer->jobs()->create($request->only([
            'title', 'description', 'type', 'location', 'employment_type',
            'salary', 'duration', 'requirements', 'required_skills',
            'required_qualifications', 'experience_level', 'deadline',
        ]) + ['status' => 'open']);

        AuditLog::log(Auth::id(), 'post_job', "Posted {$job->type}: {$job->title}");

        return redirect()->route('employer.jobs.index')
            ->with('success', ucfirst($job->type) . " \"{$job->title}\" posted successfully.");
    }

    // ── Employer: Edit Job ────────────────────────────────────────────────────

    public function edit($id)
    {
        $employer = Auth::user()->employer;
        $job = EmployerJob::where('employer_id', $employer->id)->findOrFail($id);
        return view('employer.jobs.edit', compact('job'));
    }

    // ── Employer: Update Job ──────────────────────────────────────────────────

    public function update(Request $request, $id)
    {
        $employer = Auth::user()->employer;
        $job = EmployerJob::where('employer_id', $employer->id)->findOrFail($id);

        $request->validate([
            'title'                   => 'required|string|max:255',
            'description'             => 'required|string',
            'type'                    => 'required|in:job,internship',
            'location'                => 'nullable|string|max:255',
            'employment_type'         => 'nullable|string|max:100',
            'salary'                  => 'nullable|string|max:100',
            'duration'                => 'nullable|string|max:100',
            'requirements'            => 'nullable|string',
            'required_skills'         => 'nullable|string',
            'required_qualifications' => 'nullable|string|max:255',
            'experience_level'        => 'nullable|in:Entry,Mid,Senior',
            'deadline'                => 'nullable|date',
            'status'                  => 'required|in:open,closed,draft',
        ]);

        $job->update($request->only([
            'title', 'description', 'type', 'location', 'employment_type',
            'salary', 'duration', 'requirements', 'required_skills',
            'required_qualifications', 'experience_level', 'deadline', 'status',
        ]));

        return redirect()->route('employer.jobs.index')
            ->with('success', "Job \"{$job->title}\" updated successfully.");
    }

    // ── Employer: Delete Job ──────────────────────────────────────────────────

    public function destroy($id)
    {
        $employer = Auth::user()->employer;
        $job = EmployerJob::where('employer_id', $employer->id)->findOrFail($id);
        $title = $job->title;
        $job->delete();
        AuditLog::log(Auth::id(), 'delete_job', "Deleted job: {$title}");
        return back()->with('success', "Job \"{$title}\" deleted successfully.");
    }

    // ── Employer: View Applications for a Job ────────────────────────────────

    public function applications($id)
    {
        $employer = Auth::user()->employer;
        $job = EmployerJob::where('employer_id', $employer->id)
            ->with(['applications.student.user', 'applications.student.enrollments.program', 'applications.interview'])
            ->withCount('applications')
            ->findOrFail($id);

        return view('employer.jobs.applications', compact('job'));
    }

    // ── Student: Browse Jobs ──────────────────────────────────────────────────

    public function browse(Request $request)
    {
        $query = EmployerJob::with('employer')
            ->where('status', 'open')
            ->whereHas('employer', fn($q) => $q->where('status', 'active'));

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('location', 'like', "%{$s}%")
                  ->orWhere('required_skills', 'like', "%{$s}%");
            });
        }

        if ($request->filled('employment_type')) {
            $query->where('employment_type', $request->employment_type);
        }

        $jobs = $query->latest()->paginate(12)->withQueryString();

        // Get the student's applied job IDs
        $student = Auth::user()->student;
        $appliedJobIds = $student
            ? $student->jobApplications()->pluck('employer_job_id')->toArray()
            : [];

        return view('student.jobs.index', compact('jobs', 'appliedJobIds'));
    }

    // ── Student: Show Single Job ──────────────────────────────────────────────

    public function show($id)
    {
        $job = EmployerJob::with('employer')->findOrFail($id);
        $student = Auth::user()->student;

        $alreadyApplied = $student
            ? $student->jobApplications()->where('employer_job_id', $id)->exists()
            : false;

        return view('student.jobs.show', compact('job', 'alreadyApplied'));
    }
}
