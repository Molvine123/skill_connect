@extends('layouts.app')
@section('title', $program->name)
@section('page-title', 'Program Detail')

@section('content')
<div class="animate-fade-up">

{{-- Back Link --}}
<a href="{{ route('organization.programs.index') }}" style="display:inline-flex;align-items:center;gap:0.5rem;color:#6b7280;text-decoration:none;font-size:0.875rem;margin-bottom:1.5rem;transition:color .2s;" onmouseover="this.style.color='#f1f5f9'" onmouseout="this.style.color='#6b7280'">
    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    Back to My Programs
</a>

@php
    $statusCfg = [
        'published' => ['#34d399','rgba(16,185,129,0.15)','Published'],
        'draft'     => ['#fbbf24','rgba(245,158,11,0.15)','Draft'],
        'closed'    => ['#6b7280','rgba(107,114,128,0.15)','Closed'],
    ];
    [$sc,$sbg,$sl] = $statusCfg[$program->status] ?? ['#6b7280','rgba(107,114,128,0.15)',ucfirst($program->status)];
    $modeCfg = ['online'=>['🌐','Online'],'in_person'=>['🏢','In-Person'],'hybrid'=>['🔀','Hybrid']];
    [$mi,$ml] = $modeCfg[$program->mode] ?? ['📍',ucfirst($program->mode)];
    
    $enrolledCount = $program->enrollments->count();
    $sessionsCount = $program->sessions->count();
    $capacityUsed = $program->capacity > 0 ? min(100, round($enrolledCount/$program->capacity*100)) : 0;
@endphp

{{-- Banner --}}
<div style="background:linear-gradient(135deg,#0a1628 0%,#0d1f38 60%,#0a1628 100%);border:1px solid rgba(99,102,241,0.2);border-radius:20px;padding:2rem;margin-bottom:1.5rem;position:relative;overflow:hidden;">
    <div style="position:absolute;top:-50px;right:-30px;width:250px;height:250px;background:radial-gradient(circle,rgba(99,102,241,0.15),transparent 70%);pointer-events:none;"></div>
    
    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:1.5rem;flex-wrap:wrap;position:relative;z-index:1;">
        <div style="flex:1;min-width:300px;">
            <div style="display:flex;gap:0.75rem;align-items:center;margin-bottom:0.75rem;flex-wrap:wrap;">
                <span style="font-size:0.75rem;color:#818cf8;background:rgba(99,102,241,0.1);padding:0.25rem 0.625rem;border-radius:20px;border:1px solid rgba(99,102,241,0.2);">{{ $program->category?->icon ?? '📚' }} {{ $program->category?->name ?? 'Uncategorized' }}</span>
                <span style="font-size:0.75rem;font-weight:600;color:{{ $sc }};background:{{ $sbg }};padding:0.25rem 0.625rem;border-radius:20px;">{{ $sl }}</span>
                <span style="font-size:0.75rem;color:#94a3b8;background:rgba(255,255,255,0.05);padding:0.25rem 0.625rem;border-radius:20px;">{{ $mi }} {{ $ml }}</span>
            </div>
            <h1 style="font-size:2rem;font-weight:800;color:#f1f5f9;margin-bottom:0.5rem;line-height:1.2;">{{ $program->name }}</h1>
            <div style="font-size:1.25rem;font-weight:700;color:{{ $program->cost > 0 ? '#fbbf24' : '#34d399' }};">
                {{ $program->cost > 0 ? 'KES '.number_format($program->cost,0) : 'Free Program' }}
            </div>
        </div>
        
        <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
            <a href="{{ route('organization.programs.edit', $program->id) }}" class="btn btn-outline" style="border-color:rgba(245,158,11,0.4);color:#fbbf24;">✏️ Edit</a>
            <a href="{{ route('organization.programs.sessions', $program->id) }}" class="btn btn-outline" style="border-color:rgba(6,182,212,0.4);color:#22d3ee;">📅 Manage Sessions</a>
            <a href="{{ route('organization.programs.enrollments', $program->id) }}" class="btn btn-outline" style="border-color:rgba(16,185,129,0.4);color:#34d399;">🎓 Enrollments</a>
            <a href="{{ route('organization.programs.certificates', $program->id) }}" class="btn btn-outline" style="border-color:rgba(167,139,250,0.4);color:#a78bfa;">🏆 Certificates</a>
        </div>
    </div>
</div>

{{-- Quick Stats --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;margin-bottom:1.5rem;" class="animate-fade-up-delay">
    <div class="card" style="padding:1.25rem;display:flex;align-items:center;gap:1rem;">
        <div style="width:48px;height:48px;border-radius:12px;background:rgba(16,185,129,0.1);display:flex;align-items:center;justify-content:center;font-size:1.5rem;">🎓</div>
        <div>
            <div style="font-size:0.75rem;color:#94a3b8;font-weight:500;">Enrolled Students</div>
            <div style="font-size:1.5rem;font-weight:800;color:#f1f5f9;">{{ $enrolledCount }} <span style="font-size:0.875rem;font-weight:500;color:#6b7280;">/ {{ $program->capacity }}</span></div>
            <div style="background:rgba(255,255,255,0.06);border-radius:4px;height:4px;overflow:hidden;margin-top:0.25rem;">
                <div style="height:100%;width:{{ $capacityUsed }}%;background:{{ $capacityUsed >= 90 ? '#ef4444' : ($capacityUsed >= 70 ? '#fbbf24' : '#34d399') }};"></div>
            </div>
        </div>
    </div>
    <div class="card" style="padding:1.25rem;display:flex;align-items:center;gap:1rem;">
        <div style="width:48px;height:48px;border-radius:12px;background:rgba(6,182,212,0.1);display:flex;align-items:center;justify-content:center;font-size:1.5rem;">📅</div>
        <div>
            <div style="font-size:0.75rem;color:#94a3b8;font-weight:500;">Training Sessions</div>
            <div style="font-size:1.5rem;font-weight:800;color:#f1f5f9;">{{ $sessionsCount }}</div>
        </div>
    </div>
    <div class="card" style="padding:1.25rem;display:flex;align-items:center;gap:1rem;">
        <div style="width:48px;height:48px;border-radius:12px;background:rgba(99,102,241,0.1);display:flex;align-items:center;justify-content:center;font-size:1.5rem;">⏱️</div>
        <div>
            <div style="font-size:0.75rem;color:#94a3b8;font-weight:500;">Duration</div>
            <div style="font-size:1.25rem;font-weight:700;color:#f1f5f9;">{{ $program->duration }}</div>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:1.5rem;" class="animate-fade-up-delay-2">
    
    {{-- Main Column --}}
    <div style="display:grid;gap:1.5rem;align-content:start;">
        <div class="card">
            <div class="card-header"><span class="card-title">Description</span></div>
            <div class="card-body">
                <p style="color:#e2e8f0;line-height:1.6;font-size:0.95rem;white-space:pre-wrap;">{{ $program->description }}</p>
            </div>
        </div>
        
        @if($program->requirements)
        <div class="card">
            <div class="card-header"><span class="card-title">Requirements / Prerequisites</span></div>
            <div class="card-body">
                <p style="color:#e2e8f0;line-height:1.6;font-size:0.95rem;white-space:pre-wrap;">{{ $program->requirements }}</p>
            </div>
        </div>
        @endif
        
        @if($program->learning_outcomes)
        <div class="card">
            <div class="card-header"><span class="card-title">Learning Outcomes</span></div>
            <div class="card-body">
                <p style="color:#e2e8f0;line-height:1.6;font-size:0.95rem;white-space:pre-wrap;">{{ $program->learning_outcomes }}</p>
            </div>
        </div>
        @endif
        
        {{-- Recent Sessions Preview --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Upcoming Sessions</span>
                <a href="{{ route('organization.programs.sessions', $program->id) }}" style="font-size:0.8125rem;color:#818cf8;text-decoration:none;">Manage all →</a>
            </div>
            @php $upcoming = $program->sessions->where('start_date', '>=', now())->sortBy('start_date')->take(3); @endphp
            @forelse($upcoming as $session)
            <div style="padding:1rem 1.5rem;border-bottom:1px solid rgba(42,42,74,0.5);display:flex;align-items:center;gap:1rem;">
                <div style="width:48px;text-align:center;flex-shrink:0;">
                    <div style="font-size:0.65rem;color:#818cf8;text-transform:uppercase;">{{ $session->start_date->format('M') }}</div>
                    <div style="font-size:1.5rem;font-weight:800;color:#f1f5f9;line-height:1;">{{ $session->start_date->format('d') }}</div>
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:0.9rem;font-weight:600;color:#f1f5f9;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $session->title }}</div>
                    <div style="font-size:0.75rem;color:#6b7280;">{{ $session->start_date->format('H:i') }} • {{ $session->venue ?? 'Online' }}</div>
                </div>
            </div>
            @empty
            <div style="padding:2rem;text-align:center;">
                <div style="color:#6b7280;font-size:0.875rem;">No upcoming sessions.</div>
            </div>
            @endforelse
        </div>
    </div>
    
    {{-- Right Column --}}
    <div style="display:grid;gap:1.5rem;align-content:start;">
        <div class="card">
            <div class="card-header"><span class="card-title">Program Info</span></div>
            <div class="card-body" style="display:grid;gap:0.5rem;padding:1rem;">
                @foreach([
                    ['Mode', $mi.' '.$ml],
                    ['Venue', $program->venue ?? 'N/A'],
                    ['Cost', $program->cost > 0 ? 'KES '.number_format($program->cost,0) : 'Free'],
                    ['Duration', $program->duration],
                    ['Capacity', $program->capacity.' students'],
                    ['Created', $program->created_at->format('M d, Y')],
                ] as [$lbl,$val])
                <div style="display:flex;justify-content:space-between;padding:0.375rem 0;border-bottom:1px solid rgba(42,42,74,0.4);">
                    <span style="font-size:0.8rem;color:#94a3b8;">{{ $lbl }}</span>
                    <span style="font-size:0.8rem;color:#e2e8f0;font-weight:500;text-align:right;">{{ $val }}</span>
                </div>
                @endforeach
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <span class="card-title">Recent Enrollments</span>
                <a href="{{ route('organization.programs.enrollments', $program->id) }}" style="font-size:0.8125rem;color:#34d399;text-decoration:none;">View all →</a>
            </div>
            @forelse($program->enrollments->sortByDesc('created_at')->take(5) as $enrollment)
            <div style="padding:0.75rem 1.25rem;border-bottom:1px solid rgba(42,42,74,0.5);display:flex;align-items:center;gap:0.75rem;">
                <img src="{{ $enrollment->student->user->getAvatarUrl() }}" style="width:32px;height:32px;border-radius:8px;">
                <div style="flex:1;min-width:0;">
                    <div style="font-size:0.8rem;font-weight:600;color:#f1f5f9;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $enrollment->student->user->name }}</div>
                    <div style="font-size:0.7rem;color:#6b7280;">{{ $enrollment->created_at->diffForHumans() }}</div>
                </div>
            </div>
            @empty
            <div style="padding:1.5rem;text-align:center;color:#6b7280;font-size:0.8rem;">No enrollments yet.</div>
            @endforelse
        </div>
    </div>
</div>

</div>
@endsection
