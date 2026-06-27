@extends('layouts.app')

@section('title', 'My Applications')
@section('page-title', 'My Job Applications')

@section('content')
<div class="animate-fade-up">

    <div style="margin-bottom:1.5rem;">
        <p style="color:#6b7280;font-size:.9rem;">Track all the jobs and internships you have applied for.</p>
    </div>

    @if($applications->isEmpty())
    <div class="table-container" style="text-align:center;padding:3rem;">
        <div style="font-size:3rem;margin-bottom:1rem;">💼</div>
        <h3 style="font-size:1.1rem;font-weight:600;color:#6b7280;">No Applications Yet</h3>
        <p style="color:#9ca3af;font-size:.9rem;margin-top:.25rem;margin-bottom:1.5rem;">Browse available jobs and submit your first application.</p>
        <a href="{{ route('student.jobs.index') }}" class="btn btn-primary">Browse Opportunities</a>
    </div>
    @else
    <div style="display:grid;gap:1rem;">
        @foreach($applications as $app)
        <div class="table-container" style="padding:1.5rem;">
            <div style="display:flex;justify-content:space-between;align-items:center;gap:1.5rem;flex-wrap:wrap;">
                
                {{-- Left details --}}
                <div style="display:flex;gap:1rem;align-items:center;flex:1;">
                    <img src="{{ $app->job->employer->logo_url }}" style="width:48px;height:48px;border-radius:8px;object-fit:cover;border:1px solid var(--sc-dark-border);" alt="">
                    <div>
                        <h4 style="font-size:.82rem;font-weight:600;color:#6b7280;margin-bottom:.15rem;">
                            {{ $app->job->employer->company_name }}
                        </h4>
                        <h3 style="font-size:.95rem;font-weight:700;color:#fff;">
                            {{ $app->job->title }}
                        </h3>
                        <div style="display:flex;gap:1rem;color:#6b7280;font-size:.78rem;margin-top:.25rem;flex-wrap:wrap;">
                            <span>Applied {{ $app->applied_at ? $app->applied_at->format('d M Y') : $app->created_at->format('d M Y') }}</span>
                            @if($app->job->location)<span>📍 {{ $app->job->location }}</span>@endif
                        </div>
                    </div>
                </div>

                {{-- Status & Info --}}
                <div style="display:flex;align-items:center;gap:1.5rem;flex-wrap:wrap;">
                    
                    {{-- Interview Schedule Info --}}
                    @if($app->status === 'interview_scheduled' && $app->interview)
                        <div style="background:rgba(124,58,237,0.05);border:1px solid rgba(124,58,237,0.2);border-radius:8px;padding:.5rem 1rem;font-size:.8rem;color:#c084fc;">
                            📅 <strong>Interview:</strong> {{ $app->interview->interview_date->format('d M Y') }} at {{ $app->interview->interview_time }}
                            @if($app->interview->meeting_link)
                                <a href="{{ $app->interview->meeting_link }}" target="_blank" style="margin-left:.5rem;color:#a78bfa;text-decoration:underline;font-weight:600;">Join Meeting</a>
                            @elseif($app->interview->venue)
                                <span style="margin-left:.5rem;">(at {{ $app->interview->venue }})</span>
                            @endif
                        </div>
                    @endif

                    <div style="text-align:right;">
                        <span style="background:{{ $app->status_color }};color:#fff;padding:.2rem .75rem;border-radius:20px;font-size:.75rem;font-weight:600;display:inline-block;">
                            {{ $app->status_label }}
                        </span>
                    </div>

                    {{-- Withdraw Application Button --}}
                    @if($app->status === 'submitted')
                        <form method="POST" action="{{ route('student.applications.withdraw', $app->id) }}" onsubmit="return confirm('Withdraw application for this vacancy?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background:none;border:none;color:#dc2626;cursor:pointer;font-size:.8rem;font-weight:600;">
                                Withdraw
                            </button>
                        </form>
                    @endif
                </div>

            </div>
        </div>
        @endforeach
    </div>

    <div style="margin-top:1.5rem;">
        {{ $applications->links() }}
    </div>
    @endif

</div>
@endsection
