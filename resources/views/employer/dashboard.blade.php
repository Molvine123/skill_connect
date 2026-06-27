@extends('layouts.app')

@section('title', 'Employer Dashboard')
@section('page-title', 'Employer Dashboard')

@section('content')
<div class="animate-fade-up">

    @if(!$employer)
    {{-- Setup Prompt --}}
    <div style="background:linear-gradient(135deg,#0d9488,#0891b2);border-radius:16px;padding:2.5rem;text-align:center;color:#fff;margin-bottom:2rem;">
        <div style="font-size:3rem;margin-bottom:1rem;">🏢</div>
        <h2 style="font-size:1.5rem;font-weight:700;margin-bottom:.5rem;">Complete Your Company Profile</h2>
        <p style="opacity:.85;margin-bottom:1.5rem;">Set up your employer profile to start posting jobs and finding talent.</p>
        <a href="{{ route('employer.profile.edit') }}" style="background:#fff;color:#0d9488;padding:.75rem 2rem;border-radius:10px;font-weight:600;text-decoration:none;display:inline-block;">
            Set Up Company Profile →
        </a>
    </div>
    @else

    {{-- Stats Grid --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:1.25rem;margin-bottom:2rem;">
        <div class="stat-card" style="background:linear-gradient(135deg,#0d9488,#0891b2);">
            <div class="stat-value" style="color:#fff;">{{ $stats['active_jobs'] }}</div>
            <div class="stat-label" style="color:rgba(255,255,255,.8);">Active Jobs</div>
        </div>
        <div class="stat-card" style="background:linear-gradient(135deg,#7c3aed,#6d28d9);">
            <div class="stat-value" style="color:#fff;">{{ $stats['active_internships'] }}</div>
            <div class="stat-label" style="color:rgba(255,255,255,.8);">Internships</div>
        </div>
        <div class="stat-card" style="background:linear-gradient(135deg,#2563eb,#1d4ed8);">
            <div class="stat-value" style="color:#fff;">{{ $stats['total_applications'] }}</div>
            <div class="stat-label" style="color:rgba(255,255,255,.8);">Applications</div>
        </div>
        <div class="stat-card" style="background:linear-gradient(135deg,#d97706,#b45309);">
            <div class="stat-value" style="color:#fff;">{{ $stats['shortlisted'] }}</div>
            <div class="stat-label" style="color:rgba(255,255,255,.8);">Shortlisted</div>
        </div>
        <div class="stat-card" style="background:linear-gradient(135deg,#7c3aed,#4f46e5);">
            <div class="stat-value" style="color:#fff;">{{ $stats['interviews_scheduled'] }}</div>
            <div class="stat-label" style="color:rgba(255,255,255,.8);">Interviews</div>
        </div>
        <div class="stat-card" style="background:linear-gradient(135deg,#16a34a,#15803d);">
            <div class="stat-value" style="color:#fff;">{{ $stats['hired'] }}</div>
            <div class="stat-label" style="color:rgba(255,255,255,.8);">Hired</div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div style="display:flex;gap:1rem;flex-wrap:wrap;margin-bottom:2rem;">
        <a href="{{ route('employer.jobs.create') }}" class="btn btn-primary">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-right:.5rem"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Post New Job
        </a>
        <a href="{{ route('employer.search') }}" class="btn btn-secondary">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-right:.5rem"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            Search Candidates
        </a>
        <a href="{{ route('employer.jobs.index') }}" class="btn btn-secondary">My Job Listings</a>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">

        {{-- My Jobs --}}
        <div class="table-container">
            <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--sc-dark-border);display:flex;justify-content:space-between;align-items:center;">
                <h3 style="font-size:1rem;font-weight:600;">My Job Listings</h3>
                <a href="{{ route('employer.jobs.create') }}" style="font-size:.8rem;color:#0d9488;">+ Post New</a>
            </div>
            @forelse($myJobs as $job)
            <div style="padding:1rem 1.5rem;border-bottom:1px solid var(--sc-dark-border);display:flex;justify-content:space-between;align-items:center;">
                <div>
                    <div style="font-weight:600;font-size:.9rem;">{{ $job->title }}</div>
                    <div style="display:flex;gap:.5rem;align-items:center;margin-top:.25rem;">
                        <span style="background:{{ $job->type_badge_color }};color:#fff;padding:.15rem .6rem;border-radius:20px;font-size:.7rem;font-weight:600;">{{ $job->type_label }}</span>
                        <span style="color:#6b7280;font-size:.8rem;">{{ $job->applications_count }} applications</span>
                    </div>
                </div>
                <a href="{{ route('employer.jobs.applications', $job->id) }}" style="color:#0d9488;font-size:.85rem;font-weight:600;">View →</a>
            </div>
            @empty
            <div style="padding:2rem;text-align:center;color:#6b7280;">No jobs posted yet. <a href="{{ route('employer.jobs.create') }}" style="color:#0d9488;">Post your first job →</a></div>
            @endforelse
        </div>

        {{-- Recent Applications --}}
        <div class="table-container">
            <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--sc-dark-border);">
                <h3 style="font-size:1rem;font-weight:600;">Recent Applications</h3>
            </div>
            @forelse($recentApplications as $app)
            <div style="padding:1rem 1.5rem;border-bottom:1px solid var(--sc-dark-border);display:flex;align-items:center;gap:1rem;">
                <img src="{{ $app->student->user->getAvatarUrl() }}" style="width:36px;height:36px;border-radius:50%;object-fit:cover;" alt="">
                <div style="flex:1;min-width:0;">
                    <div style="font-weight:600;font-size:.85rem;">{{ $app->student->user->name }}</div>
                    <div style="color:#6b7280;font-size:.78rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $app->job->title }}</div>
                </div>
                <span style="background:{{ $app->status_color }};color:#fff;padding:.15rem .6rem;border-radius:20px;font-size:.7rem;font-weight:600;white-space:nowrap;">{{ $app->status_label }}</span>
            </div>
            @empty
            <div style="padding:2rem;text-align:center;color:#6b7280;">No applications received yet.</div>
            @endforelse
        </div>

    </div>
    @endif

</div>
@endsection
