@extends('layouts.app')
@section('title', 'Student Dashboard')
@section('page-title', 'Student Dashboard')

@section('content')

{{-- Success / Error alerts --}}
@if(session('success'))
<div class="alert alert-success animate-fade-up">✅ {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-error animate-fade-up">⚠️ {{ session('error') }}</div>
@endif

{{-- Welcome Banner --}}
<div style="background:linear-gradient(135deg,#0a1f1a 0%,#0d2b22 60%,#0a1f1a 100%);border:1px solid rgba(16,185,129,0.2);border-radius:20px;padding:2rem;margin-bottom:2rem;position:relative;overflow:hidden;" class="animate-fade-up">
    <div style="position:absolute;top:-60px;right:-40px;width:280px;height:280px;background:radial-gradient(circle,rgba(16,185,129,0.18),transparent 70%);pointer-events:none;"></div>
    <div style="position:absolute;bottom:-30px;left:180px;width:200px;height:200px;background:radial-gradient(circle,rgba(6,182,212,0.1),transparent 70%);pointer-events:none;"></div>
    <div style="position:relative;z-index:1;display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
        <div>
            <div style="display:flex;align-items:center;gap:0.625rem;margin-bottom:0.5rem;">
                <div style="width:10px;height:10px;background:#34d399;border-radius:50%;box-shadow:0 0 0 3px rgba(16,185,129,0.25);"></div>
                <span style="font-size:0.8125rem;color:#34d399;font-weight:500;">Student Portal</span>
            </div>
            <h1 style="font-size:1.75rem;font-weight:800;color:#f1f5f9;margin-bottom:0.375rem;">
                Hello, {{ explode(' ', auth()->user()->name)[0] }}! 🎓
            </h1>
            <p style="color:#94a3b8;font-size:0.9375rem;">Your skill development journey starts here. Explore programs and grow your career.</p>
        </div>
        <a href="{{ route('student.programs.index') }}" class="btn btn-primary" style="background:linear-gradient(135deg,#10b981,#059669);box-shadow:0 4px 15px rgba(16,185,129,0.35);flex-shrink:0;">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            Browse Programs
        </a>
    </div>
</div>

{{-- Stats --}}
<div class="stats-grid animate-fade-up-delay">
    <div class="stat-card emerald">
        <div class="stat-icon emerald">📚</div>
        <div class="stat-label">My Enrollments</div>
        <div class="stat-value">{{ $totalEnrollments ?? 0 }}</div>
        <div class="stat-change" style="color:#34d399;">{{ $activeEnrollments ?? 0 }} active</div>
    </div>
    <div class="stat-card cyan">
        <div class="stat-icon cyan">📅</div>
        <div class="stat-label">Upcoming Sessions</div>
        <div class="stat-value">{{ $upcomingSessions ?? 0 }}</div>
        <div class="stat-change" style="color:#22d3ee;">Next 7 days</div>
    </div>
    <div class="stat-card amber">
        <div class="stat-icon amber">🏆</div>
        <div class="stat-label">Certificates</div>
        <div class="stat-value">{{ $totalCertificates ?? 0 }}</div>
        <div class="stat-change" style="color:#fbbf24;">Earned so far</div>
    </div>
    <div class="stat-card indigo">
        <div class="stat-icon indigo">⏱️</div>
        <div class="stat-label">Hours Trained</div>
        <div class="stat-value">{{ $totalHours ?? 0 }}</div>
        <div class="stat-change">Total learning hours</div>
    </div>
</div>

{{-- Main Grid --}}
<div style="display:grid;grid-template-columns:2fr 1fr;gap:1.5rem;" class="animate-fade-up-delay-2">

    {{-- Recent Enrollments --}}
    <div style="display:grid;gap:1.5rem;">
        <div class="card">
            <div class="card-header">
                <span class="card-title">Recent Enrollments</span>
                <a href="{{ route('student.enrollments.index') }}" style="font-size:0.8125rem;color:var(--sc-primary);text-decoration:none;font-weight:500;">View all →</a>
            </div>

            @forelse($recentEnrollments ?? [] as $enr)
            @php
                $statusConfig = [
                    'approved'  => ['badge-student',  '✅ Active'],
                    'completed' => ['badge-active',   '🏆 Done'],
                    'pending'   => ['badge-pending',  '⏳ Pending'],
                    'rejected'  => ['badge-deact',    '✗ Rejected'],
                ];
                [$bc, $sl] = $statusConfig[$enr->status] ?? ['badge-pending', ucfirst($enr->status)];
            @endphp
            <div style="padding:1rem 1.5rem;border-bottom:1px solid rgba(42,42,74,0.5);display:flex;align-items:center;gap:1rem;">
                <div style="width:40px;height:40px;border-radius:10px;background:rgba(99,102,241,0.1);border:1px solid rgba(99,102,241,0.15);display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0;">
                    {{ $enr->program?->category?->icon ?? '📚' }}
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:0.875rem;font-weight:600;color:#f1f5f9;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $enr->program?->name }}</div>
                    <div style="font-size:0.75rem;color:#6b7280;">{{ $enr->program?->organization?->name }} · {{ $enr->created_at->diffForHumans() }}</div>
                </div>
                <span class="badge {{ $bc }}">{{ $sl }}</span>
            </div>
            @empty
            <div style="padding:2.5rem;text-align:center;">
                <div style="font-size:2rem;margin-bottom:0.5rem;">📚</div>
                <div style="color:#6b7280;font-size:0.875rem;">No enrollments yet. <a href="{{ route('student.programs.index') }}" style="color:var(--sc-primary);">Browse programs →</a></div>
            </div>
            @endforelse
        </div>

        {{-- Upcoming Sessions Preview --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Upcoming Sessions</span>
                <a href="{{ route('student.sessions.index') }}" style="font-size:0.8125rem;color:var(--sc-primary);text-decoration:none;font-weight:500;">View all →</a>
            </div>

            @forelse($nextSessions ?? [] as $session)
            @php
                $daysUntil = now()->diffInDays($session->start_date, false);
                $urgencyColor = $daysUntil <= 1 ? '#34d399' : ($daysUntil <= 3 ? '#fbbf24' : '#818cf8');
            @endphp
            <div style="padding:1rem 1.5rem;border-bottom:1px solid rgba(42,42,74,0.5);display:flex;align-items:center;gap:1rem;">
                <div style="width:48px;text-align:center;flex-shrink:0;">
                    <div style="font-size:0.65rem;color:#818cf8;text-transform:uppercase;letter-spacing:0.05em;">{{ $session->start_date->format('M') }}</div>
                    <div style="font-size:1.5rem;font-weight:800;color:#f1f5f9;line-height:1;">{{ $session->start_date->format('d') }}</div>
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:0.875rem;font-weight:600;color:#f1f5f9;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $session->title }}</div>
                    <div style="font-size:0.75rem;color:#6b7280;">{{ $session->start_date->format('H:i') }}{{ $session->venue ? ' · ' . $session->venue : '' }}</div>
                </div>
                <span style="font-size:0.7rem;color:{{ $urgencyColor }};background:{{ $urgencyColor }}18;padding:0.2rem 0.5rem;border-radius:20px;white-space:nowrap;">
                    {{ $daysUntil === 0 ? 'Today' : ($daysUntil === 1 ? 'Tomorrow' : "In {$daysUntil}d") }}
                </span>
            </div>
            @empty
            <div style="padding:2.5rem;text-align:center;">
                <div style="font-size:2rem;margin-bottom:0.5rem;">📅</div>
                <div style="color:#6b7280;font-size:0.875rem;">No upcoming sessions.</div>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Right sidebar --}}
    <div style="display:grid;gap:1.5rem;align-content:start;">

        {{-- Student Profile --}}
        <div class="card">
            <div class="card-header"><span class="card-title">My Profile</span></div>
            <div class="card-body">
                <div style="text-align:center;margin-bottom:1.25rem;">
                    <img src="{{ auth()->user()->getAvatarUrl() }}" style="width:72px;height:72px;border-radius:16px;border:3px solid rgba(16,185,129,0.3);margin:0 auto 0.875rem;display:block;">
                    <div style="font-weight:700;color:#e2e8f0;font-size:1rem;">{{ auth()->user()->name }}</div>
                    <div style="font-size:0.8125rem;color:#34d399;margin-top:0.25rem;font-weight:500;">Student</div>
                    <div style="font-size:0.8125rem;color:#6b7280;margin-top:0.25rem;">{{ auth()->user()->email }}</div>
                    @if(auth()->user()->student?->institution)
                    <div style="margin-top:0.5rem;font-size:0.75rem;color:#a78bfa;font-weight:500;">🏫 {{ auth()->user()->student->institution->name }}</div>
                    @endif
                </div>
                <div style="display:grid;gap:0.5rem;">
                    @foreach([
                        ['📱','Phone', auth()->user()->phone ?? 'Not set'],
                        ['🆔','Reg. No', auth()->user()->student?->registration_number ?? 'Not assigned'],
                        ['📅','Member Since', auth()->user()->created_at->format('M d, Y')],
                        ['✅','Status', ucfirst(auth()->user()->status)],
                    ] as [$icon, $label, $value])
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:0.45rem 0;border-bottom:1px solid rgba(42,42,74,0.4);">
                        <span style="font-size:0.8rem;color:#6b7280;">{{ $icon }} {{ $label }}</span>
                        <span style="font-size:0.8rem;color:#e2e8f0;font-weight:500;max-width:140px;text-align:right;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $value }}</span>
                    </div>
                    @endforeach
                </div>
                <a href="{{ route('student.profile.edit') }}" class="btn btn-outline btn-full" style="margin-top:1.25rem;font-size:0.875rem;">✏️ Edit Profile</a>
            </div>
        </div>

        {{-- Payment Summary --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Payment Summary</span>
                <a href="{{ route('student.payments.index') }}" style="font-size:0.8125rem;color:var(--sc-primary);text-decoration:none;font-weight:500;">View all →</a>
            </div>
            <div class="card-body">
                @php
                    $totalPaid    = ($recentPayments ?? collect())->where('status','paid')->sum('amount');
                    $totalPending = ($recentPayments ?? collect())->where('status','pending')->sum('amount');
                @endphp
                <div style="display:grid;gap:0.75rem;">
                    <div style="padding:0.875rem;background:rgba(16,185,129,0.07);border:1px solid rgba(16,185,129,0.15);border-radius:10px;display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-size:0.8125rem;color:#34d399;">✅ Total Paid</span>
                        <span style="font-size:1rem;font-weight:700;color:#34d399;">KES {{ number_format($totalPaid, 0) }}</span>
                    </div>
                    @if($totalPending > 0)
                    <div style="padding:0.875rem;background:rgba(245,158,11,0.07);border:1px solid rgba(245,158,11,0.2);border-radius:10px;display:flex;justify-content:space-between;align-items:center;">
                        <span style="font-size:0.8125rem;color:#fbbf24;">⏳ Outstanding</span>
                        <span style="font-size:1rem;font-weight:700;color:#fbbf24;">KES {{ number_format($totalPending, 0) }}</span>
                    </div>
                    @else
                    <div style="padding:0.75rem;text-align:center;color:#6b7280;font-size:0.8125rem;">No outstanding payments 🎉</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Certificates earned --}}
        @if(($totalCertificates ?? 0) > 0)
        <div class="card">
            <div class="card-header">
                <span class="card-title">🏆 Certificates</span>
                <a href="{{ route('student.certificates.index') }}" style="font-size:0.8125rem;color:var(--sc-primary);text-decoration:none;font-weight:500;">View all →</a>
            </div>
            <div class="card-body" style="text-align:center;padding:1.5rem;">
                <div style="font-size:3rem;margin-bottom:0.5rem;">🥇</div>
                <div style="font-size:1.5rem;font-weight:800;color:#fbbf24;">{{ $totalCertificates }}</div>
                <div style="font-size:0.8125rem;color:#9ca3af;margin-top:0.25rem;">{{ $totalCertificates === 1 ? 'Certificate' : 'Certificates' }} Earned</div>
            </div>
        </div>
        @endif
    </div>
</div>

@endsection
