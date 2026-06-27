@extends('layouts.app')
@section('title', 'Attendance Dashboard')
@section('page-title', 'Attendance Dashboard')

@section('content')
<div class="animate-fade-up">

    {{-- Banner --}}
    <div style="background:linear-gradient(135deg,#0a1f1a 0%,#0d2b22 60%,#0a1f1a 100%);border:1px solid rgba(16,185,129,0.2);border-radius:20px;padding:2rem;margin-bottom:2rem;position:relative;overflow:hidden;">
        <div style="position:absolute;top:-60px;right:-40px;width:280px;height:280px;background:radial-gradient(circle,rgba(16,185,129,0.18),transparent 70%);pointer-events:none;"></div>
        <div style="position:relative;z-index:1;">
            <h1 style="font-size:1.75rem;font-weight:800;color:#f1f5f9;margin-bottom:0.5rem;">⏱️ Attendance Records</h1>
            <p style="font-size:0.875rem;color:#94a3b8;margin:0;">
                Track student attendance in real time. Expand each session to view the complete roster of attendees, join/leave times, and durations.
            </p>
        </div>
    </div>

    {{-- Sessions list --}}
    <div class="card">
        <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
            <span class="card-title">Training Sessions ({{ $sessions->total() }})</span>
            <div style="font-size:0.8rem;color:#6b7280;">Click a session to toggle attendance list</div>
        </div>
        
        @if($sessions->count() > 0)
            <div style="display:grid;gap:1px;background:rgba(42,42,74,0.3);">
                @foreach($sessions as $session)
                    @php
                        $isPast = $session->end_date < now();
                        $isOngoing = $session->start_date <= now() && $session->end_date >= now();
                        $statusColor = $isOngoing ? '#34d399' : ($isPast ? '#6b7280' : '#818cf8');
                        $statusText = $isOngoing ? 'Ongoing' : ($isPast ? 'Completed' : 'Upcoming');
                        $isOnline = $session->isOnline();
                        
                        // Collect all attendees
                        $attendeesList = collect();
                        if ($isOnline && $session->virtualClass) {
                            $attendeesList = $session->virtualClass->attendances;
                        } else {
                            $attendeesList = $session->attendances;
                        }
                        
                        $count = $attendeesList->count();
                    @endphp
                    
                    {{-- Session Header Row (Interactive Accordion Trigger) --}}
                    <div class="session-accordion-trigger" data-session-id="{{ $session->id }}" style="padding:1.25rem;background:#0d1127;cursor:pointer;display:flex;align-items:center;justify-content:space-between;transition:all 0.2s;border-bottom:1px solid rgba(42,42,74,0.3);" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='#0d1127'">
                        <div style="display:flex;align-items:center;gap:1.25rem;flex:1;min-width:0;">
                            {{-- Date block --}}
                            <div style="width:54px;height:54px;border-radius:10px;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.06);display:flex;flex-direction:column;align-items:center;justify-content:center;flex-shrink:0;">
                                <div style="font-size:0.7rem;color:{{ $statusColor }};text-transform:uppercase;font-weight:600;">{{ $session->start_date->format('M') }}</div>
                                <div style="font-size:1.25rem;font-weight:800;color:#f1f5f9;line-height:1.1;">{{ $session->start_date->format('d') }}</div>
                            </div>
                            
                            {{-- Main info --}}
                            <div style="flex:1;min-width:0;">
                                <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.25rem;flex-wrap:wrap;">
                                    <span style="font-size:0.8rem;color:#34d399;font-weight:600;background:rgba(16,185,129,0.08);padding:0.1rem 0.5rem;border-radius:6px;border:1px solid rgba(16,185,129,0.15);">
                                        {{ $session->program->name }}
                                    </span>
                                    <div style="font-size:1.05rem;font-weight:700;color:#f1f5f9;">{{ $session->title }}</div>
                                    
                                    @if($isOnline)
                                        <span style="font-size:0.7rem;color:#06b6d4;background:rgba(6,182,212,0.08);border:1px solid rgba(6,182,212,0.25);padding:0.1rem 0.4rem;border-radius:8px;">🌐 Online Class</span>
                                    @else
                                        <span style="font-size:0.7rem;color:#f59e0b;background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.25);padding:0.1rem 0.4rem;border-radius:8px;">📍 In Person</span>
                                    @endif
                                </div>
                                <div style="display:flex;gap:1.5rem;font-size:0.8rem;color:#6b7280;flex-wrap:wrap;">
                                    <span>🕒 {{ $session->start_date->format('H:i') }} - {{ $session->end_date->format('H:i') }}</span>
                                    <span>📍 {{ $session->venue ?? 'Online' }}</span>
                                    <span>👨‍🏫 {{ $session->trainer_information ?? 'No Trainer' }}</span>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Roster Status Count & Chevron --}}
                        <div style="display:flex;align-items:center;gap:1.5rem;margin-left:1rem;flex-shrink:0;">
                            <div style="text-align:right;">
                                <div style="font-size:0.875rem;font-weight:700;color:#f1f5f9;">{{ $count }}</div>
                                <div style="font-size:0.75rem;color:#6b7280;">Attendees</div>
                            </div>
                            <div class="accordion-chevron" style="transition:transform 0.2s;color:#94a3b8;">
                                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Accordion Content (Roster Table) --}}
                    <div id="session-roster-{{ $session->id }}" class="session-roster-container" style="display:none;background:rgba(255,255,255,0.01);border-bottom:1px solid rgba(42,42,74,0.3);overflow:hidden;">
                        <div style="padding:1.5rem;border-top:1px solid rgba(42,42,74,0.2);">
                            @if($count > 0)
                                <div style="overflow-x:auto;">
                                    <table style="width:100%;border-collapse:collapse;font-size:0.875rem;min-width:650px;">
                                        <thead>
                                            <tr style="border-bottom:1px solid rgba(255,255,255,0.05);color:#94a3b8;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.05em;text-align:left;">
                                                <th style="padding:0.75rem 1rem;">Student</th>
                                                <th style="padding:0.75rem 1rem;">Registration No</th>
                                                @if($isOnline)
                                                    <th style="padding:0.75rem 1rem;">Join Time</th>
                                                    <th style="padding:0.75rem 1rem;">Leave Time</th>
                                                    <th style="padding:0.75rem 1rem;text-align:center;">Duration (Mins)</th>
                                                @else
                                                    <th style="padding:0.75rem 1rem;">Method</th>
                                                    <th style="padding:0.75rem 1rem;">Marked Time</th>
                                                @endif
                                                <th style="padding:0.75rem 1rem;text-align:right;">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($attendeesList as $record)
                                                @php
                                                    $student = $record->student;
                                                    $user = $student?->user;
                                                @endphp
                                                <tr style="border-bottom:1px solid rgba(255,255,255,0.02);color:#e2e8f0;">
                                                    <td style="padding:0.75rem 1rem;display:flex;align-items:center;gap:0.75rem;">
                                                        @if($user)
                                                            <img src="{{ $user->getAvatarUrl() }}" style="width:28px;height:28px;border-radius:6px;background:rgba(255,255,255,0.05);">
                                                            <div>
                                                                <div style="font-weight:600;">{{ $user->name }}</div>
                                                                <div style="font-size:0.75rem;color:#6b7280;">{{ $user->email }}</div>
                                                            </div>
                                                        @else
                                                            <div style="font-weight:600;color:#6b7280;">Unknown Student</div>
                                                        @endif
                                                    </td>
                                                    <td style="padding:0.75rem 1rem;color:#94a3b8;">
                                                        {{ $student->registration_number ?? '-' }}
                                                    </td>
                                                    @if($isOnline)
                                                        <td style="padding:0.75rem 1rem;color:#94a3b8;font-size:0.8rem;">
                                                            {{ $record->join_time ? $record->join_time->format('M d, H:i:s') : '-' }}
                                                        </td>
                                                        <td style="padding:0.75rem 1rem;color:#94a3b8;font-size:0.8rem;">
                                                            {{ $record->leave_time ? $record->leave_time->format('M d, H:i:s') : '-' }}
                                                        </td>
                                                        <td style="padding:0.75rem 1rem;text-align:center;font-weight:700;color:#38bdf8;">
                                                            {{ $record->duration ?? 0 }} min
                                                        </td>
                                                    @else
                                                        <td style="padding:0.75rem 1rem;color:#94a3b8;">
                                                            {{ ucfirst(str_replace('_', ' ', $record->verification_method ?? 'Manual')) }}
                                                        </td>
                                                        <td style="padding:0.75rem 1rem;color:#94a3b8;font-size:0.8rem;">
                                                            {{ $record->marked_at ? $record->marked_at->format('M d, H:i') : ($record->created_at ? $record->created_at->format('M d, H:i') : '-') }}
                                                        </td>
                                                    @endif
                                                    <td style="padding:0.75rem 1rem;text-align:right;">
                                                        @php
                                                            $status = $record->status ?? ($record->present ? 'present' : 'absent');
                                                            $statusText = ucfirst($status);
                                                            $bgColor = $status === 'present' ? 'rgba(16,185,129,0.1)' : 'rgba(239,68,68,0.1)';
                                                            $borderColor = $status === 'present' ? 'rgba(16,185,129,0.3)' : 'rgba(239,68,68,0.3)';
                                                            $textColor = $status === 'present' ? '#34d399' : '#f87171';
                                                        @endphp
                                                        <span style="font-size:0.75rem;padding:0.2rem 0.5rem;border-radius:6px;background:{{ $bgColor }};border:1px solid {{ $borderColor }};color:{{ $textColor }};font-weight:600;">
                                                            {{ $statusText }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div style="text-align:center;padding:2rem 1rem;color:#6b7280;">
                                    <div style="font-size:2rem;margin-bottom:0.5rem;">👥</div>
                                    <div style="font-size:0.9rem;font-weight:600;color:#94a3b8;">No attendance records found yet</div>
                                    @if($isOnline)
                                        <div style="font-size:0.8rem;margin-top:0.25rem;">Students will be listed automatically when they join the virtual classroom.</div>
                                    @else
                                        <div style="font-size:0.8rem;margin-top:0.25rem;">Use "Take Attendance" in the sessions manager to manually record or show a QR code.</div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div style="padding:1.25rem;border-top:1px solid rgba(42,42,74,0.5);">
                {{ $sessions->links() }}
            </div>
        @else
            <div style="padding:5rem 2rem;text-align:center;">
                <div style="font-size:3.5rem;margin-bottom:1.5rem;">⏱️</div>
                <h3 style="font-size:1.2rem;font-weight:700;color:#f1f5f9;margin:0 0 0.5rem 0;">No sessions found</h3>
                <p style="font-size:0.875rem;color:#6b7280;max-width:400px;margin:0 auto 1.5rem auto;">
                    You haven't scheduled any training sessions or programs yet. Build a program to start tracking attendance.
                </p>
                <a href="{{ route('organization.programs.create') }}" class="btn btn-primary">Create Program</a>
            </div>
        @endif
    </div>

</div>

{{-- Inline JS for Collapsible Accordions --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const triggers = document.querySelectorAll('.session-accordion-trigger');
    
    triggers.forEach(trigger => {
        trigger.addEventListener('click', function() {
            const sessionId = this.getAttribute('data-session-id');
            const target = document.getElementById('session-roster-' + sessionId);
            const chevron = this.querySelector('.accordion-chevron');
            
            if (target.style.display === 'none' || !target.style.display) {
                // Smooth transition
                target.style.display = 'block';
                chevron.style.transform = 'rotate(180deg)';
                this.style.background = 'rgba(255, 255, 255, 0.03)';
            } else {
                target.style.display = 'none';
                chevron.style.transform = 'rotate(0deg)';
                this.style.background = '#0d1127';
            }
        });
    });
});
</script>
@endsection
