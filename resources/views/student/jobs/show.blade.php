@extends('layouts.app')

@section('title', $job->title)
@section('page-title', 'Vacancy Details')

@section('content')
<div class="animate-fade-up" style="max-width:960px;margin:0 auto;">

    <div style="margin-bottom:1.5rem;">
        <a href="{{ route('student.jobs.index') }}" style="color:#0d9488;text-decoration:none;font-weight:600;font-size:.9rem;">← Back to Opportunity Board</a>
    </div>

    {{-- Details Layout --}}
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:1.75rem;align-items:start;flex-wrap:wrap;">

        {{-- Main Job info --}}
        <div>
            <div class="table-container" style="padding:2rem;margin-bottom:1.5rem;">
                <div style="display:flex;gap:1.25rem;align-items:start;border-bottom:1px solid var(--sc-dark-border);padding-bottom:1.5rem;margin-bottom:1.5rem;flex-wrap:wrap;">
                    <img src="{{ $job->employer->logo_url }}" style="width:64px;height:64px;border-radius:10px;object-fit:cover;border:1px solid var(--sc-dark-border);" alt="">
                    <div>
                        <span style="background:{{ $job->type_badge_color }};color:#fff;padding:.15rem .6rem;border-radius:20px;font-size:.7rem;font-weight:600;display:inline-block;margin-bottom:.5rem;">
                            {{ $job->type_label }}
                        </span>
                        <h2 style="font-size:1.3rem;font-weight:700;color:#fff;">{{ $job->title }}</h2>
                        <p style="color:#9ca3af;font-size:.9rem;margin-top:.15rem;">{{ $job->employer->company_name }}</p>
                    </div>
                </div>

                {{-- Specs --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.5rem;font-size:.88rem;">
                    @if($job->location)
                        <div style="color:#9ca3af;">📍 <strong>Location:</strong> {{ $job->location }}</div>
                    @endif
                    @if($job->employment_type)
                        <div style="color:#9ca3af;">💼 <strong>Employment Type:</strong> {{ $job->employment_type }}</div>
                    @endif
                    @if($job->salary)
                        <div style="color:#9ca3af;">💰 <strong>Salary / Stipend:</strong> {{ $job->salary }}</div>
                    @endif
                    @if($job->duration)
                        <div style="color:#9ca3af;">⏱️ <strong>Duration:</strong> {{ $job->duration }}</div>
                    @endif
                    @if($job->experience_level)
                        <div style="color:#9ca3af;">📈 <strong>Experience:</strong> {{ $job->experience_level }} Level</div>
                    @endif
                    @if($job->deadline)
                        <div style="color:#9ca3af;">📅 <strong>Apply Before:</strong> {{ $job->deadline->format('d M Y') }}</div>
                    @endif
                </div>

                {{-- Description --}}
                <div style="margin-bottom:1.5rem;">
                    <h3 style="font-weight:700;font-size:1rem;margin-bottom:.5rem;color:#fff;">Role Description</h3>
                    <p style="color:#d1d5db;font-size:.9rem;line-height:1.6;white-space:pre-line;">{{ $job->description }}</p>
                </div>

                {{-- Requirements --}}
                @if($job->requirements)
                <div style="margin-bottom:1.5rem;">
                    <h3 style="font-weight:700;font-size:1rem;margin-bottom:.5rem;color:#fff;">Role Requirements</h3>
                    <p style="color:#d1d5db;font-size:.9rem;line-height:1.6;white-space:pre-line;">{{ $job->requirements }}</p>
                </div>
                @endif

                {{-- Skills --}}
                @if($job->required_skills)
                <div style="margin-bottom:1.5rem;">
                    <h3 style="font-weight:700;font-size:1rem;margin-bottom:.5rem;color:#fff;">Required Skills</h3>
                    <div style="display:flex;flex-wrap:wrap;gap:.4rem;">
                        @foreach(explode(',', $job->required_skills) as $skill)
                            <span style="background:rgba(13,148,136,0.1);color:#0d9488;padding:.2rem .6rem;border-radius:6px;font-size:.75rem;font-weight:600;">
                                {{ trim($skill) }}
                            </span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Sidebar Action Card --}}
        <div>
            @if($alreadyApplied)
                <div class="table-container" style="padding:1.5rem;text-align:center;background:rgba(22,163,74,0.02);border:1px solid rgba(22,163,74,0.2);">
                    <div style="font-size:2rem;margin-bottom:.5rem;">✅</div>
                    <h3 style="font-weight:700;color:#16a34a;font-size:1rem;margin-bottom:.25rem;">Application Submitted</h3>
                    <p style="color:#6b7280;font-size:.8rem;margin-bottom:1rem;">You have already applied for this position. Keep track of it in your dashboard.</p>
                    <a href="{{ route('student.applications.index') }}" class="btn btn-secondary" style="width:100%;font-size:.8rem;">
                        Track Application
                    </a>
                </div>
            @else
                <div class="table-container" style="padding:1.5rem;">
                    <h3 style="font-weight:700;font-size:.95rem;margin-bottom:1rem;color:#fff;border-bottom:1px solid var(--sc-dark-border);padding-bottom:.5rem;">
                        Apply Now
                    </h3>
                    
                    <form method="POST" action="{{ route('student.jobs.apply', $job->id) }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div style="margin-bottom:1rem;">
                            <label class="form-label" style="font-size:.78rem;">Cover Letter / Pitch</label>
                            <textarea name="cover_letter" rows="4" class="form-input" style="font-size:.82rem;padding:.5rem;" placeholder="Introduce yourself and explain why you're a great fit..."></textarea>
                            @error('cover_letter')<div class="form-error">{{ $message }}</div>@enderror
                        </div>

                        <div style="margin-bottom:1.5rem;">
                            <label class="form-label" style="font-size:.78rem;">Upload Resume / CV</label>
                            <input type="file" name="cv_file" class="form-input" style="font-size:.82rem;padding:.4rem;" accept=".pdf,.doc,.docx">
                            @if(Auth::user()->student && Auth::user()->student->cv_file)
                                <div style="font-size:.75rem;color:#16a34a;margin-top:.4rem;font-weight:600;">
                                    ✓ Profile CV will be used if you leave this empty.
                                </div>
                            @endif
                            @error('cv_file')<div class="form-error">{{ $message }}</div>@enderror
                        </div>

                        <button type="submit" class="btn btn-primary" style="width:100%;padding:.65rem;font-size:.85rem;background:#0d9488;">
                            Submit Application
                        </button>
                    </form>
                </div>
            @endif

            {{-- Company details card --}}
            <div class="table-container" style="padding:1.5rem;margin-top:1.25rem;">
                <h3 style="font-weight:700;font-size:.9rem;margin-bottom:.75rem;color:#fff;">About the Employer</h3>
                <p style="color:#d1d5db;font-size:.8rem;line-height:1.5;margin-bottom:1rem;">
                    {{ $job->employer->description ?? 'No description provided.' }}
                </p>
                @if($job->employer->website)
                    <a href="{{ $job->employer->website }}" target="_blank" style="color:#0d9488;font-size:.8rem;text-decoration:none;font-weight:600;">
                        Visit Website ↗
                    </a>
                @endif
            </div>
        </div>

    </div>

</div>
@endsection
