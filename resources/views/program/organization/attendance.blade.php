@extends('layouts.app')
@section('title', 'Mark Attendance')
@section('page-title', 'Attendance Tracker')

@section('content')
<div class="animate-fade-up" style="max-width:900px;margin:0 auto;">

{{-- Back Link --}}
<a href="{{ route('organization.programs.sessions', $program->id) }}" style="display:inline-flex;align-items:center;gap:0.5rem;color:#6b7280;text-decoration:none;font-size:0.875rem;margin-bottom:1.5rem;transition:color .2s;" onmouseover="this.style.color='#f1f5f9'" onmouseout="this.style.color='#6b7280'">
    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    Back to Sessions
</a>

{{-- Banner --}}
<div style="background:linear-gradient(135deg,#0a1f1a 0%,#0d2b22 60%,#0a1f1a 100%);border:1px solid rgba(16,185,129,0.2);border-radius:20px;padding:2rem;margin-bottom:2rem;position:relative;overflow:hidden;">
    <div style="position:absolute;top:-60px;right:-40px;width:280px;height:280px;background:radial-gradient(circle,rgba(16,185,129,0.18),transparent 70%);pointer-events:none;"></div>
    <div style="position:relative;z-index:1;">
        <div style="font-size:0.8rem;color:#34d399;font-weight:600;margin-bottom:0.5rem;text-transform:uppercase;letter-spacing:0.05em;">{{ $program->name }}</div>
        <h1 style="font-size:1.75rem;font-weight:800;color:#f1f5f9;margin-bottom:0.75rem;">{{ $session->title }}</h1>
        <div style="display:flex;gap:1.5rem;flex-wrap:wrap;font-size:0.875rem;color:#94a3b8;">
            <span>📅 {{ $session->start_date->format('l, M d, Y') }}</span>
            <span>🕒 {{ $session->start_date->format('H:i') }} - {{ $session->end_date->format('H:i') }}</span>
            <span>📍 {{ $session->venue ?? 'Online' }}</span>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Student Roster ({{ $enrollments->count() }})</span>
        <div style="font-size:0.8125rem;color:#6b7280;">Check the box to mark present</div>
    </div>
    
    <form method="POST" action="{{ route('organization.programs.attendance.save', [$program->id, $session->id]) }}">
        @csrf
        
        @if($enrollments->count() > 0)
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;min-width:600px;">
                <thead>
                    <tr style="border-bottom:1px solid rgba(42,42,74,0.5);background:rgba(255,255,255,0.02);">
                        <th style="padding:1rem 1.25rem;text-align:left;font-size:0.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;width:60px;">#</th>
                        <th style="padding:1rem 1.25rem;text-align:left;font-size:0.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;">Student Name</th>
                        <th style="padding:1rem 1.25rem;text-align:left;font-size:0.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;">Reg Number</th>
                        <th style="padding:1rem 1.25rem;text-align:center;font-size:0.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;width:120px;">Present?</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($enrollments as $index => $enrollment)
                    <tr style="border-bottom:1px solid rgba(42,42,74,0.4);transition:background .2s;{{ $index % 2 == 0 ? 'background:rgba(255,255,255,0.01);' : '' }}" onmouseover="this.style.background='rgba(255,255,255,0.03)'" onmouseout="this.style.background='{{ $index % 2 == 0 ? 'rgba(255,255,255,0.01)' : 'transparent' }}'">
                        <td style="padding:1rem 1.25rem;font-size:0.875rem;color:#6b7280;">{{ $index + 1 }}</td>
                        <td style="padding:1rem 1.25rem;">
                            <div style="display:flex;align-items:center;gap:0.75rem;">
                                <img src="{{ $enrollment->student->user->getAvatarUrl() }}" style="width:32px;height:32px;border-radius:8px;">
                                <div style="font-size:0.875rem;font-weight:600;color:#f1f5f9;">{{ $enrollment->student->user->name }}</div>
                            </div>
                        </td>
                        <td style="padding:1rem 1.25rem;font-size:0.875rem;color:#94a3b8;">{{ $enrollment->student->registration_number ?? '-' }}</td>
                        <td style="padding:1rem 1.25rem;text-align:center;">
                            <input type="checkbox" name="present[]" value="{{ $enrollment->student->id }}" {{ ($attendanceMap[$enrollment->student->id] ?? false) ? 'checked' : '' }} style="width:1.25rem;height:1.25rem;cursor:pointer;">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div style="padding:1.25rem;border-top:1px solid rgba(42,42,74,0.5);display:flex;justify-content:flex-end;">
            <button type="submit" class="btn btn-primary" style="background:linear-gradient(135deg,#10b981,#059669);box-shadow:0 4px 15px rgba(16,185,129,0.35);">
                ✓ Save Attendance
            </button>
        </div>
        @else
        <div style="padding:4rem 2rem;text-align:center;">
            <div style="font-size:3rem;margin-bottom:1rem;">👥</div>
            <div style="font-size:1.1rem;font-weight:600;color:#f1f5f9;">No approved students yet</div>
            <div style="font-size:0.85rem;color:#6b7280;margin-top:0.5rem;">Only students with an 'Approved' or 'Completed' enrollment status will appear here.</div>
        </div>
        @endif
    </form>
</div>

</div>
@endsection
