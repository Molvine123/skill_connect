@extends('layouts.app')
@section('title', $student->user?->name . ' — Student Details')
@section('page-title', 'Student Details')

@section('content')

{{-- Breadcrumbs / Back --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;" class="animate-fade-up">
    <div style="display:flex;align-items:center;gap:0.75rem;">
        <a href="{{ route('institution.students.index') }}" style="color:#6b7280;text-decoration:none;font-size:0.875rem;">My Students</a>
        <span style="color:#4b5563;">/</span>
        <span style="color:#e2e8f0;font-size:0.875rem;">{{ $student->user?->name }}</span>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 2fr;gap:1.5rem;" class="animate-fade-up">

    {{-- Left side --}}
    <div style="display:grid;gap:1.5rem;align-content:start;">
        
        {{-- Profile card --}}
        <div class="card">
            <div class="card-body" style="text-align:center;padding:2rem;">
                <img src="{{ $student->user?->getAvatarUrl() }}" style="width:90px;height:90px;border-radius:18px;object-fit:cover;border:3px solid rgba(16,185,129,0.3);margin:0 auto 1rem;display:block;">
                <h2 style="font-size:1.2rem;font-weight:800;color:#f1f5f9;margin-bottom:0.375rem;">{{ $student->user?->name }}</h2>
                <div style="font-size:0.875rem;color:#34d399;margin-bottom:0.75rem;">Institution Student</div>
                <span class="badge badge-inst" style="font-size:0.8125rem;padding:0.375rem 0.75rem;">🏫 {{ $institution->name }}</span>
            </div>
        </div>

        {{-- Stats --}}
        <div class="card">
            <div class="card-header"><span class="card-title">📈 Statistics</span></div>
            <div class="card-body">
                @php
                    $enrCount = $student->enrollments()->count();
                    $completedCount = $student->enrollments()->where('status', 'completed')->count();
                    $totalSpent = $student->payments()->where('status', 'paid')->sum('amount');
                @endphp
                @foreach([
                    ['Hours Trained',  $student->getTotalHoursTrained() . ' hrs', '⏱️', '#34d399'],
                    ['Skill Programs',       $enrCount, '📚', '#818cf8'],
                    ['Completed Courses',    $completedCount, '🏆', '#f59e0b'],
                    ['Paid Fees',            'KES ' . number_format($totalSpent, 0), '💰', '#06b6d4'],
                ] as [$label, $value, $icon, $color])
                <div style="display:flex;justify-content:space-between;align-items:center;padding:0.625rem 0;border-bottom:1px solid rgba(42,42,74,0.4);">
                    <span style="font-size:0.875rem;color:#6b7280;">{{ $icon }} {{ $label }}</span>
                    <span style="font-size:0.9rem;color:{{ $color }};font-weight:700;">{{ $value }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Right side --}}
    <div style="display:grid;gap:1.5rem;align-content:start;">

        {{-- Profile Details --}}
        <div class="card">
            <div class="card-header"><span class="card-title">📋 Student Information</span></div>
            <div class="card-body" style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                @foreach([
                    ['Registration Number', $student->registration_number ?? '—', '🔖'],
                    ['Contact Phone',       $student->phone ?? $student->user?->phone ?? '—', '📱'],
                    ['Email Address',       $student->user?->email ?? '—', '📧'],
                    ['Student Status',      ucfirst($student->user?->status ?? '—'), '✅'],
                    ['Joined Institution',  $student->created_at->format('M d, Y'), '📅'],
                ] as [$label, $value, $icon])
                <div style="padding:0.875rem;background:rgba(255,255,255,0.02);border:1px solid var(--sc-dark-border);border-radius:10px;">
                    <div style="font-size:0.75rem;color:#6b7280;margin-bottom:0.25rem;">{{ $icon }} {{ $label }}</div>
                    <div style="font-size:0.9rem;color:#e2e8f0;font-weight:500;word-break:break-all;">{{ $value }}</div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Enrollments --}}
        <div class="card">
            <div class="card-header"><span class="card-title">📚 Enrolled Programs & Achievements</span></div>
            <div style="overflow-x:auto;">
                <table class="sc-table">
                    <thead>
                        <tr>
                            <th>Program</th>
                            <th>Provider</th>
                            <th>Cost</th>
                            <th>Status</th>
                            <th>Certificate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($student->enrollments as $enr)
                        <tr>
                            <td>
                                <span style="font-weight:600;color:#e2e8f0;">{{ $enr->program?->name }}</span>
                            </td>
                            <td>
                                <span style="font-size:0.85rem;color:#9ca3af;">{{ $enr->program?->organization?->name }}</span>
                            </td>
                            <td>
                                <span style="font-size:0.85rem;color:#cbd5e1;">KES {{ number_format($enr->program?->cost ?? 0, 0) }}</span>
                            </td>
                            <td>
                                <span class="badge {{ match($enr->status) {
                                    'completed' => 'badge-active',
                                    'approved'  => 'badge-student',
                                    'pending'   => 'badge-pending',
                                    default     => 'badge-deact',
                                } }}">{{ ucfirst($enr->status) }}</span>
                            </td>
                            <td>
                                @if($enr->certificate)
                                <span class="badge badge-student" style="font-family:monospace;font-size:0.75rem;">🏆 {{ $enr->certificate->certificate_number }}</span>
                                @else
                                <span style="color:#6b7280;font-size:0.8rem;">None</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align:center;color:#4b5563;padding:2rem;">Not enrolled in any program yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Attendances --}}
        <div class="card">
            <div class="card-header"><span class="card-title">⏱️ Lecture Attendance logs</span></div>
            <div style="overflow-x:auto;">
                <table class="sc-table">
                    <thead>
                        <tr>
                            <th>Session / Lecture</th>
                            <th>Program</th>
                            <th>Status</th>
                            <th>Marked At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($student->attendances as $att)
                        <tr>
                            <td>
                                <div style="font-weight:500;color:#e2e8f0;">{{ $att->session?->title }}</div>
                                <div style="font-size:0.75rem;color:#6b7280;">{{ $att->session?->start_date?->format('M d, H:i') }} ({{ $att->verification_method }})</div>
                            </td>
                            <td>
                                <span style="font-size:0.85rem;color:#9ca3af;">{{ $att->session?->program?->name }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $att->status === 'present' ? 'badge-active' : 'badge-deact' }}">{{ ucfirst($att->status) }}</span>
                            </td>
                            <td style="color:#6b7280;font-size:0.8125rem;">{{ $att->marked_at?->format('M d, H:i') ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align:center;color:#4b5563;padding:2rem;">No attendance logs found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
