@extends('layouts.app')
@section('title', 'My Enrollments')
@section('page-title', 'My Enrollments')

@section('content')

{{-- Header --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;" class="animate-fade-up">
    <div>
        <h1 style="font-size:1.5rem;font-weight:800;color:#f1f5f9;">📚 My Enrollments</h1>
        <p style="color:#6b7280;font-size:0.9rem;margin-top:0.25rem;">All training programs you have enrolled in on SkillConnect.</p>
    </div>
    <a href="{{ route('student.programs.index') }}" class="btn btn-primary" style="font-size:0.875rem;">🔍 Browse More Programs</a>
</div>

{{-- Stats --}}
@php
    $approved  = $enrollments->where('status', 'approved')->count();
    $completed = $enrollments->where('status', 'completed')->count();
    $pending   = $enrollments->where('status', 'pending')->count();
    $rejected  = $enrollments->where('status', 'rejected')->count();
@endphp
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.75rem;" class="animate-fade-up">
    @foreach([
        ['Total', $enrollments->count(), '#6366f1', '📚'],
        ['Active / Approved', $approved, '#10b981', '✅'],
        ['Completed', $completed, '#f59e0b', '🏆'],
        ['Pending', $pending, '#8b5cf6', '⏳'],
    ] as [$label, $count, $color, $icon])
    <div style="padding:1rem 1.25rem;background:rgba(255,255,255,0.02);border:1px solid var(--sc-dark-border);border-radius:14px;text-align:center;">
        <div style="font-size:1.5rem;margin-bottom:0.25rem;">{{ $icon }}</div>
        <div style="font-size:1.75rem;font-weight:800;color:{{ $color }};">{{ $count }}</div>
        <div style="font-size:0.8rem;color:#9ca3af;margin-top:0.25rem;">{{ $label }}</div>
    </div>
    @endforeach
</div>

{{-- Enrollments Table --}}
<div class="card animate-fade-up-delay">
    <div class="card-header">
        <span class="card-title">All Enrollments</span>
        <span style="font-size:0.8125rem;color:#6b7280;">{{ $enrollments->count() }} total</span>
    </div>

    @forelse($enrollments as $enr)
    @php
        $statusConfig = [
            'approved'  => ['badge-student', '✅ Approved & Active'],
            'completed' => ['badge-active',  '🏆 Completed'],
            'pending'   => ['badge-pending', '⏳ Pending Approval'],
            'rejected'  => ['badge-deact',   '✗ Rejected'],
            'cancelled' => ['badge-deact',   '⊘ Cancelled'],
        ];
        [$badgeClass, $statusLabel] = $statusConfig[$enr->status] ?? ['badge-pending', ucfirst($enr->status)];
    @endphp
    <div style="padding:1.25rem 1.5rem;border-bottom:1px solid rgba(42,42,74,0.5);display:flex;gap:1.25rem;align-items:flex-start;flex-wrap:wrap;">
        {{-- Program Icon --}}
        <div style="width:52px;height:52px;border-radius:13px;background:rgba(99,102,241,0.1);border:1px solid rgba(99,102,241,0.15);display:flex;align-items:center;justify-content:center;font-size:1.6rem;flex-shrink:0;">
            {{ $enr->program?->category?->icon ?? '📚' }}
        </div>
        
        {{-- Program Info --}}
        <div style="flex:1;min-width:200px;">
            <div style="font-size:1rem;font-weight:700;color:#f1f5f9;margin-bottom:0.2rem;">{{ $enr->program?->name }}</div>
            <div style="font-size:0.8125rem;color:#6b7280;">by <span style="color:#a78bfa;">{{ $enr->program?->organization?->name }}</span> · {{ $enr->program?->duration }}</div>

            {{-- Badges --}}
            <div style="display:flex;gap:0.5rem;flex-wrap:wrap;margin-top:0.625rem;">
                <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                @if($enr->certificate)
                <span class="badge badge-student" style="font-family:monospace;font-size:0.7rem;">🏆 {{ $enr->certificate->certificate_number }}</span>
                @endif
                @if($enr->payment)
                <span class="badge" style="background:rgba(6,182,212,0.12);color:#22d3ee;">
                    {{ $enr->payment->status === 'paid' ? '💳 Paid' : '⚠️ Payment Pending' }}
                </span>
                @endif
            </div>
        </div>

        {{-- Cost, Date & Actions --}}
        <div style="text-align:right;flex-shrink:0;">
            <div style="font-size:1.1rem;font-weight:700;color:{{ ($enr->program?->cost ?? 0) > 0 ? '#f1f5f9' : '#34d399' }};">
                {{ ($enr->program?->cost ?? 0) > 0 ? 'KES ' . number_format($enr->program->cost, 0) : 'Free' }}
            </div>
            <div style="font-size:0.775rem;color:#6b7280;margin-top:0.25rem;">Enrolled {{ $enr->created_at->diffForHumans() }}</div>
            
            @if(in_array($enr->status, ['pending', 'approved']))
            <div style="display:flex; flex-direction:column; gap:0.5rem; margin-top: 0.75rem; align-items:flex-end;">
                @if($enr->status === 'pending' && $enr->payment && $enr->payment->status === 'pending')
                <form method="POST" action="{{ route('student.enrollments.pay', $enr->id) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary" style="padding:0.35rem 1rem;font-size:0.8rem;background:#10b981;border-color:#10b981;">
                        💳 Pay Now
                    </button>
                </form>
                @endif
                <form method="POST" action="{{ route('student.programs.cancel-enrollment', $enr->program_id) }}" onsubmit="return confirm('Are you sure you want to cancel your enrollment?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline" style="padding:0.25rem 0.5rem;font-size:0.75rem;border-color:rgba(239,68,68,0.3);color:#ef4444;">
                        Cancel Enrollment
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
    @empty
    <div style="padding:4rem 2rem;text-align:center;">
        <div style="font-size:3rem;margin-bottom:1rem;">📭</div>
        <h3 style="color:#e2e8f0;font-size:1rem;font-weight:700;margin-bottom:0.5rem;">No Enrollments Yet</h3>
        <p style="color:#6b7280;font-size:0.875rem;margin-bottom:1.25rem;">Start your skill development journey by browsing available programs.</p>
        <a href="{{ route('student.programs.index') }}" class="btn btn-primary" style="font-size:0.875rem;">Browse Programs</a>
    </div>
    @endforelse
</div>

@endsection
