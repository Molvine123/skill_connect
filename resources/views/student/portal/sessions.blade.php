@extends('layouts.app')
@section('title', 'Upcoming Sessions')
@section('page-title', 'Upcoming Sessions')

@section('content')

{{-- Header --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;" class="animate-fade-up">
    <div>
        <h1 style="font-size:1.5rem;font-weight:800;color:#f1f5f9;">📅 Upcoming Training Sessions</h1>
        <p style="color:#6b7280;font-size:0.9rem;margin-top:0.25rem;">Your scheduled sessions from enrolled programs — sorted by nearest date.</p>
    </div>
</div>

{{-- Sessions Timeline --}}
<div style="display:grid;gap:1rem;" class="animate-fade-up">
    @forelse($sessions as $session)
    @php
        $daysUntil = now()->diffInDays($session->start_date, false);
        $daysLabel = $daysUntil === 0 ? '🟢 Today' : ($daysUntil === 1 ? '⏰ Tomorrow' : "In {$daysUntil} days");
        $urgencyColor = $daysUntil <= 1 ? '#34d399' : ($daysUntil <= 3 ? '#fbbf24' : '#818cf8');
    @endphp
    <div class="card" style="overflow:visible;">
        <div style="padding:1.25rem 1.5rem;display:flex;gap:1.5rem;align-items:flex-start;flex-wrap:wrap;">
            
            {{-- Date Panel --}}
            <div style="min-width:80px;text-align:center;padding:0.875rem;background:rgba(99,102,241,0.08);border:1px solid rgba(99,102,241,0.15);border-radius:14px;flex-shrink:0;">
                <div style="font-size:0.75rem;color:#818cf8;text-transform:uppercase;letter-spacing:0.05em;font-weight:600;">{{ $session->start_date->format('M') }}</div>
                <div style="font-size:2rem;font-weight:900;color:#f1f5f9;line-height:1;">{{ $session->start_date->format('d') }}</div>
                <div style="font-size:0.7rem;color:#6b7280;">{{ $session->start_date->format('Y') }}</div>
            </div>

            {{-- Session Info --}}
            <div style="flex:1;min-width:200px;">
                <div style="display:flex;align-items:center;gap:0.625rem;margin-bottom:0.375rem;">
                    <span style="font-size:0.75rem;color:{{ $urgencyColor }};font-weight:600;background:{{ $urgencyColor }}18;padding:0.2rem 0.625rem;border-radius:50px;border:1px solid {{ $urgencyColor }}40;">{{ $daysLabel }}</span>
                </div>
                <h3 style="font-size:1.05rem;font-weight:700;color:#f1f5f9;margin-bottom:0.25rem;">{{ $session->title }}</h3>
                <p style="color:#9ca3af;font-size:0.875rem;margin-bottom:0.75rem;">{{ $session->program?->name }}</p>
                
                @if($session->description)
                <p style="color:#6b7280;font-size:0.8125rem;line-height:1.6;margin-bottom:0.875rem;">{{ Str::limit($session->description, 160) }}</p>
                @endif

                {{-- Meta row --}}
                <div style="display:flex;gap:1.25rem;flex-wrap:wrap;">
                    <div style="display:flex;align-items:center;gap:0.375rem;font-size:0.8125rem;color:#9ca3af;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $session->start_date->format('H:i') }} – {{ $session->end_date?->format('H:i') }}
                    </div>
                    @if($session->venue)
                    <div style="display:flex;align-items:center;gap:0.375rem;font-size:0.8125rem;color:#9ca3af;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ $session->venue }}
                    </div>
                    @endif
                    @if($session->trainer_information)
                    <div style="display:flex;align-items:center;gap:0.375rem;font-size:0.8125rem;color:#9ca3af;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        {{ Str::before($session->trainer_information, ' - ') ?: $session->trainer_information }}
                    </div>
                    @endif
                </div>
            </div>

            {{-- Action Button --}}
            <div style="flex-shrink:0;display:flex;flex-direction:column;gap:0.5rem;align-items:flex-end;">
                @php
                    $vc = $session->virtualClass;
                    $isOnlineMode = in_array($session->program?->mode, ['online', 'hybrid']);
                @endphp

                @if($isOnlineMode && $vc)
                    @if($vc->status === 'active')
                    <a href="{{ route('virtual-class.room', $vc->id) }}" class="btn btn-primary btn-sm"
                       style="font-size:0.8rem;white-space:nowrap;background:linear-gradient(135deg,#0891b2,#06b6d4);animation:pulse 2s infinite;">
                        📹 Join Live Class
                    </a>
                    @else
                    <a href="{{ route('virtual-class.room', $vc->id) }}" class="btn btn-outline btn-sm"
                       style="font-size:0.8rem;white-space:nowrap;border-color:rgba(6,182,212,0.3);color:#22d3ee;">
                        📹 View Classroom
                    </a>
                    @endif
                @elseif($session->meeting_link)
                <a href="{{ $session->meeting_link }}" target="_blank" class="btn btn-primary btn-sm" style="font-size:0.8rem;white-space:nowrap;background:linear-gradient(135deg,#6366f1,#8b5cf6);">
                    🔗 Join Session
                </a>
                @endif
                <span style="font-size:0.75rem;color:#6b7280;">
                    {{ number_format($session->end_date?->diffInMinutes($session->start_date) / 60, 1) }} hrs
                </span>
            </div>
        </div>
    </div>
    @empty
    <div class="coming-soon-card" style="text-align:center;padding:4rem 2rem;">
        <span class="coming-soon-icon">📅</span>
        <h3 style="font-size:1.125rem;font-weight:700;color:#e2e8f0;margin-bottom:0.5rem;">No Upcoming Sessions</h3>
        <p style="color:#6b7280;font-size:0.875rem;max-width:350px;margin:0 auto 1.25rem;">You have no upcoming training sessions. Enroll in more programs to see sessions here.</p>
        <a href="{{ route('student.programs.index') }}" class="btn btn-primary btn-sm" style="background:linear-gradient(135deg,#6366f1,#8b5cf6);">Browse Programs</a>
    </div>
    @endforelse
</div>

@endsection
