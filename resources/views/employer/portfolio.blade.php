@extends('layouts.app')

@section('title', $student->user->name . '\'s Portfolio')
@section('page-title', 'Student Portfolio')

@section('content')
<div class="animate-fade-up">

    <div style="margin-bottom:1.5rem;">
        <a href="{{ route('employer.search') }}" style="color:#0d9488;text-decoration:none;font-weight:600;font-size:.9rem;">← Back to Candidate Search</a>
    </div>

    {{-- Portfolio Main Layout --}}
    <div style="display:grid;grid-template-columns:1fr 2fr;gap:1.75rem;align-items:start;flex-wrap:wrap;">

        {{-- Left Sidebar Profile Card --}}
        <div>
            <div class="table-container" style="padding:2rem;text-align:center;">
                <img src="{{ $student->user->getAvatarUrl() }}" style="width:110px;height:110px;border-radius:50%;object-fit:cover;border:3px solid #0d9488;margin-bottom:1rem;" alt="">
                <h2 style="font-size:1.2rem;font-weight:700;margin-bottom:.25rem;color:#fff;">{{ $student->user->name }}</h2>
                
                @if($student->open_to_work)
                    <span style="background:rgba(22,163,74,0.15);color:#16a34a;padding:.2rem .75rem;border-radius:20px;font-size:.7rem;font-weight:700;letter-spacing:0.03em;display:inline-block;margin-bottom:1rem;">
                        OPEN TO WORK
                    </span>
                @endif

                <p style="color:#9ca3af;font-size:.85rem;margin-bottom:1.5rem;">
                    🎓 {{ $student->institution ? $student->institution->name : 'Independent Student' }}
                </p>

                <div style="border-top:1px solid var(--sc-dark-border);padding-top:1.25rem;text-align:left;font-size:.85rem;display:grid;gap:.75rem;">
                    @if($student->location)
                        <div style="color:#9ca3af;">📍 <strong>Location:</strong> {{ $student->location }}</div>
                    @endif
                    <div style="color:#9ca3af;">📧 <strong>Email:</strong> {{ $student->user->email }}</div>
                    @if($student->phone)
                        <div style="color:#9ca3af;">📞 <strong>Phone:</strong> {{ $student->phone }}</div>
                    @endif
                </div>

                {{-- External Links --}}
                @if($student->linkedin_url || $student->portfolio_url || $student->cv_file)
                <div style="border-top:1px solid var(--sc-dark-border);padding-top:1.25rem;margin-top:1.25rem;display:flex;flex-direction:column;gap:.5rem;">
                    @if($student->linkedin_url)
                        <a href="{{ $student->linkedin_url }}" target="_blank" class="btn btn-secondary" style="font-size:.8rem;padding:.5rem;text-align:center;">
                            🔗 LinkedIn Profile
                        </a>
                    @endif
                    @if($student->portfolio_url)
                        <a href="{{ $student->portfolio_url }}" target="_blank" class="btn btn-secondary" style="font-size:.8rem;padding:.5rem;text-align:center;">
                            💻 Personal Website
                        </a>
                    @endif
                    @if($student->cv_file)
                        <a href="{{ asset('storage/' . $student->cv_file) }}" target="_blank" class="btn btn-primary" style="font-size:.8rem;padding:.5rem;text-align:center;background:#0d9488;">
                            📄 Download CV / Resume
                        </a>
                    @endif
                </div>
                @endif
            </div>
        </div>

        {{-- Right Section Detail Tabs --}}
        <div style="display:grid;gap:1.5rem;">

            {{-- About section --}}
            <div class="table-container" style="padding:1.75rem;">
                <h3 style="font-size:1.05rem;font-weight:700;margin-bottom:1rem;color:#fff;border-bottom:1px solid var(--sc-dark-border);padding-bottom:.5rem;">
                    Professional Summary
                </h3>
                @if($student->bio)
                    <p style="color:#d1d5db;font-size:.9rem;line-height:1.6;white-space:pre-line;">{{ $student->bio }}</p>
                @else
                    <p style="color:#6b7280;font-size:.9rem;font-style:italic;">No bio has been written yet.</p>
                @endif
            </div>

            {{-- Training and Skills --}}
            <div class="table-container" style="padding:1.75rem;">
                <h3 style="font-size:1.05rem;font-weight:700;margin-bottom:1rem;color:#fff;border-bottom:1px solid var(--sc-dark-border);padding-bottom:.5rem;">
                    Verified Training Programs
                </h3>
                @php
                    $completed = $student->enrollments->where('status', 'completed');
                    $active = $student->enrollments->where('status', 'approved');
                @endphp

                @if($completed->isEmpty() && $active->isEmpty())
                    <p style="color:#6b7280;font-size:.9rem;font-style:italic;">No training history recorded yet.</p>
                @else
                    <div style="display:grid;gap:1.25rem;">
                        {{-- Completed Programs --}}
                        @if($completed->isNotEmpty())
                            <div>
                                <h4 style="font-size:.85rem;font-weight:600;color:#0d9488;margin-bottom:.5rem;text-transform:uppercase;">Completed Programs:</h4>
                                <div style="display:grid;gap:.75rem;">
                                    @foreach($completed as $enr)
                                        <div style="background:rgba(13,148,136,0.03);border:1px solid rgba(13,148,136,0.15);border-radius:8px;padding:1rem;display:flex;justify-content:space-between;align-items:center;">
                                            <div>
                                                <h5 style="font-weight:700;font-size:.9rem;color:#fff;">{{ $enr->program->name }}</h5>
                                                <p style="font-size:.8rem;color:#6b7280;margin-top:.2rem;">
                                                    Offered by: {{ $enr->program->organization->name }} | Duration: {{ $enr->program->duration }}
                                                </p>
                                            </div>
                                            @if($enr->certificate)
                                                <span style="background:rgba(13,148,136,0.15);color:#0d9488;padding:.2rem .6rem;border-radius:20px;font-size:.7rem;font-weight:700;">
                                                    🎓 Verified Certificate
                                                </span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Active/Ongoing --}}
                        @if($active->isNotEmpty())
                            <div>
                                <h4 style="font-size:.85rem;font-weight:600;color:#2563eb;margin-bottom:.5rem;text-transform:uppercase;">Ongoing Programs:</h4>
                                <div style="display:grid;gap:.75rem;">
                                    @foreach($active as $enr)
                                        <div style="background:rgba(37,99,235,0.03);border:1px solid rgba(37,99,235,0.15);border-radius:8px;padding:1rem;">
                                            <h5 style="font-weight:700;font-size:.9rem;color:#fff;">{{ $enr->program->name }}</h5>
                                            <p style="font-size:.8rem;color:#6b7280;margin-top:.2rem;">
                                                Offered by: {{ $enr->program->organization->name }} | Mode: {{ ucfirst($enr->program->mode) }}
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Attendance & Participation metrics --}}
            <div class="table-container" style="padding:1.75rem;">
                <h3 style="font-size:1.05rem;font-weight:700;margin-bottom:1rem;color:#fff;border-bottom:1px solid var(--sc-dark-border);padding-bottom:.5rem;">
                    Platform Participation Summary
                </h3>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;text-align:center;">
                    <div style="background:rgba(255,255,255,0.01);border:1px solid var(--sc-dark-border);border-radius:10px;padding:1rem;">
                        <div style="font-size:1.75rem;font-weight:700;color:#0d9488;">{{ $student->getTotalHoursTrained() }}</div>
                        <div style="font-size:.78rem;color:#6b7280;text-transform:uppercase;margin-top:.25rem;font-weight:600;">Total Training Hours</div>
                    </div>
                    <div style="background:rgba(255,255,255,0.01);border:1px solid var(--sc-dark-border);border-radius:10px;padding:1rem;">
                        <div style="font-size:1.75rem;font-weight:700;color:#7c3aed;">{{ $student->attendances()->where('status', 'present')->count() }}</div>
                        <div style="font-size:.78rem;color:#6b7280;text-transform:uppercase;margin-top:.25rem;font-weight:600;">Sessions Attended</div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
