<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Employer;
use App\Models\EmployerJob;
use App\Models\JobApplication;
use App\Models\EmploymentRecord;
use App\Models\Student;
use App\Models\SkillCategory;
use App\Models\AuditLog;

class EmployerController extends Controller
{
    // ── Employer Dashboard ────────────────────────────────────────────────────

    public function dashboard()
    {
        $user     = Auth::user();
        $employer = $user->employer;

        $stats = [
            'active_jobs'          => 0,
            'active_internships'   => 0,
            'total_applications'   => 0,
            'shortlisted'          => 0,
            'interviews_scheduled' => 0,
            'hired'                => 0,
        ];

        $recentApplications = collect();
        $myJobs = collect();

        if ($employer) {
            $jobIds = $employer->jobs()->pluck('id');

            $stats = [
                'active_jobs'          => $employer->jobs()->where('type', 'job')->where('status', 'open')->count(),
                'active_internships'   => $employer->jobs()->where('type', 'internship')->where('status', 'open')->count(),
                'total_applications'   => JobApplication::whereIn('employer_job_id', $jobIds)->count(),
                'shortlisted'          => JobApplication::whereIn('employer_job_id', $jobIds)->where('status', 'shortlisted')->count(),
                'interviews_scheduled' => JobApplication::whereIn('employer_job_id', $jobIds)->where('status', 'interview_scheduled')->count(),
                'hired'                => JobApplication::whereIn('employer_job_id', $jobIds)->where('status', 'hired')->count(),
            ];

            $recentApplications = JobApplication::whereIn('employer_job_id', $jobIds)
                ->with(['student.user', 'job'])
                ->latest()
                ->take(8)
                ->get();

            $myJobs = $employer->jobs()->withCount('applications')->latest()->take(6)->get();
        }

        return view('employer.dashboard', compact('employer', 'stats', 'recentApplications', 'myJobs'));
    }

    // ── Profile Edit ──────────────────────────────────────────────────────────

    public function edit()
    {
        $user     = Auth::user();
        $employer = $user->employer;
        return view('employer.profile.edit', compact('employer'));
    }

    public function updateProfile(Request $request)
    {
        $user     = Auth::user();
        $employer = $user->employer;

        $request->validate([
            'company_name'        => 'required|string|max:255',
            'registration_number' => 'nullable|string|max:100',
            'industry'            => 'nullable|string|max:255',
            'phone'               => 'nullable|string|max:20',
            'email'               => 'nullable|email|max:255',
            'website'             => 'nullable|url|max:255',
            'address'             => 'nullable|string|max:500',
            'description'         => 'nullable|string|max:2000',
            'logo'                => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $logoPath = $employer?->logo;

        if ($request->hasFile('logo')) {
            if ($employer && $employer->logo) {
                Storage::disk('public')->delete($employer->logo);
            }
            $logoPath = $request->file('logo')->store('logos/employers', 'public');
        }

        $user->update([
            'name'  => $request->company_name,
            'phone' => $request->phone,
        ]);

        $data = [
            'company_name'        => $request->company_name,
            'registration_number' => $request->registration_number,
            'industry'            => $request->industry,
            'phone'               => $request->phone,
            'email'               => $request->email,
            'website'             => $request->website,
            'address'             => $request->address,
            'description'         => $request->description,
            'logo'                => $logoPath,
        ];

        if ($employer) {
            $employer->update($data);
        } else {
            Employer::create(array_merge($data, ['user_id' => $user->id, 'status' => 'pending']));
        }

        AuditLog::log($user->id, 'update_employer_profile', "Updated employer profile for {$user->name}");

        return redirect()->route('employer.dashboard')->with('success', 'Company profile updated successfully.');
    }

    // ── Candidate Search ──────────────────────────────────────────────────────

    public function search(Request $request)
    {
        $query = Student::with(['user', 'institution', 'enrollments.program.category', 'jobApplications'])
            ->whereHas('user', fn($q) => $q->where('status', 'active'));

        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('user', function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%");
            });
        }

        if ($request->filled('open_to_work')) {
            $query->where('open_to_work', true);
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        if ($request->filled('category_id')) {
            $catId = $request->category_id;
            $query->whereHas('enrollments.program', function ($q) use ($catId) {
                $q->where('category_id', $catId);
            });
        }

        $students   = $query->paginate(15)->withQueryString();
        $categories = SkillCategory::orderBy('name')->get();

        return view('employer.search', compact('students', 'categories'));
    }

    // ── View Student Portfolio ────────────────────────────────────────────────

    public function viewPortfolio($studentId)
    {
        $student = Student::with([
            'user',
            'institution',
            'enrollments.program.category',
            'enrollments.program.sessions',
            'jobApplications',
            'employmentRecords',
        ])->findOrFail($studentId);

        return view('employer.portfolio', compact('student'));
    }

    // ── Admin: Employer Management ────────────────────────────────────────────

    public function adminIndex(Request $request)
    {
        $query = Employer::with('user')->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('company_name', 'like', "%{$s}%")
                  ->orWhere('industry', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $employers = $query->paginate(15)->withQueryString();
        return view('employer.admin_index', compact('employers'));
    }

    public function adminShow($id)
    {
        $employer = Employer::with(['user', 'jobs.applications'])->findOrFail($id);
        return view('employer.admin_show', compact('employer'));
    }
}
