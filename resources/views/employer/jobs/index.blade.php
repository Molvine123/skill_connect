@extends('layouts.app')

@section('title', 'My Job Listings')
@section('page-title', 'My Job Listings')

@section('content')
<div class="animate-fade-up">

    {{-- Header --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
        <div>
            <p style="color:#6b7280;font-size:.9rem;">Manage your job and internship postings</p>
        </div>
        <a href="{{ route('employer.jobs.create') }}" class="btn btn-primary">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-right:.4rem"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Post New Job
        </a>
    </div>

    {{-- Filters --}}
    <form method="GET" style="display:flex;gap:.75rem;flex-wrap:wrap;margin-bottom:1.5rem;">
        <select name="type" class="form-input" style="width:auto;" onchange="this.form.submit()">
            <option value="">All Types</option>
            <option value="job" {{ request('type')=='job'?'selected':'' }}>Jobs</option>
            <option value="internship" {{ request('type')=='internship'?'selected':'' }}>Internships</option>
        </select>
        <select name="status" class="form-input" style="width:auto;" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="open" {{ request('status')=='open'?'selected':'' }}>Open</option>
            <option value="closed" {{ request('status')=='closed'?'selected':'' }}>Closed</option>
            <option value="draft" {{ request('status')=='draft'?'selected':'' }}>Draft</option>
        </select>
    </form>

    @if($jobs->isEmpty())
    <div class="table-container" style="text-align:center;padding:3rem;">
        <div style="font-size:3rem;margin-bottom:1rem;">📋</div>
        <h3 style="font-size:1.1rem;font-weight:600;margin-bottom:.5rem;">No Job Listings Yet</h3>
        <p style="color:#6b7280;margin-bottom:1.5rem;">Start attracting skilled talent by posting your first vacancy.</p>
        <a href="{{ route('employer.jobs.create') }}" class="btn btn-primary">Post Your First Job</a>
    </div>
    @else
    <div style="display:grid;gap:1rem;">
        @foreach($jobs as $job)
        <div class="table-container" style="padding:1.5rem;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;flex-wrap:wrap;">
                <div style="flex:1;">
                    <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:.5rem;flex-wrap:wrap;">
                        <h3 style="font-size:1rem;font-weight:700;">{{ $job->title }}</h3>
                        <span style="background:{{ $job->type_badge_color }};color:#fff;padding:.2rem .7rem;border-radius:20px;font-size:.75rem;font-weight:600;">{{ $job->type_label }}</span>
                        <span style="background:{{ $job->status==='open' ? '#16a34a' : ($job->status==='draft'?'#d97706':'#dc2626') }};color:#fff;padding:.2rem .7rem;border-radius:20px;font-size:.75rem;font-weight:600;">{{ ucfirst($job->status) }}</span>
                    </div>
                    <div style="display:flex;gap:1.5rem;color:#6b7280;font-size:.85rem;flex-wrap:wrap;">
                        @if($job->location)<span>📍 {{ $job->location }}</span>@endif
                        @if($job->employment_type)<span>💼 {{ $job->employment_type }}</span>@endif
                        @if($job->deadline)<span>📅 Deadline: {{ $job->deadline->format('d M Y') }}</span>@endif
                        <span>👥 {{ $job->applications_count }} {{ Str::plural('application', $job->applications_count) }}</span>
                    </div>
                </div>
                <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
                    <a href="{{ route('employer.jobs.applications', $job->id) }}" class="btn btn-secondary" style="font-size:.82rem;padding:.5rem 1rem;">View Applications</a>
                    <a href="{{ route('employer.jobs.edit', $job->id) }}" class="btn btn-secondary" style="font-size:.82rem;padding:.5rem 1rem;">Edit</a>
                    <form method="POST" action="{{ route('employer.jobs.destroy', $job->id) }}" onsubmit="return confirm('Delete this job posting?')">
                        @csrf @method('DELETE')
                        <button type="submit" style="background:#dc2626;color:#fff;border:none;padding:.5rem 1rem;border-radius:8px;cursor:pointer;font-size:.82rem;">Delete</button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    {{ $jobs->links() }}
    @endif
</div>
@endsection
