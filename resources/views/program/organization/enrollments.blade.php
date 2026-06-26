@extends('layouts.app')
@section('title', 'Enrollments')
@section('page-title', 'Program Enrollments')

@section('content')
<div class="animate-fade-up">

{{-- Breadcrumb --}}
<div style="margin-bottom:1.5rem;font-size:0.875rem;color:#94a3b8;display:flex;align-items:center;gap:0.5rem;">
    <a href="{{ route('organization.programs.index') }}" style="color:#6b7280;text-decoration:none;">My Programs</a>
    <span style="color:#475569;">/</span>
    <a href="{{ route('organization.programs.show', $program->id) }}" style="color:#6b7280;text-decoration:none;">{{ Str::limit($program->name, 30) }}</a>
    <span style="color:#475569;">/</span>
    <span style="color:#f1f5f9;font-weight:500;">Enrollments</span>
</div>

@php
    $enrollments = $program->enrollments;
    $total = $enrollments->count();
    $pending = $enrollments->where('status', 'pending')->count();
    $approved = $enrollments->where('status', 'approved')->count();
    $completed = $enrollments->where('status', 'completed')->count();
    $rejected = $enrollments->where('status', 'rejected')->count();
@endphp

{{-- Stats Row --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:1rem;margin-bottom:1.5rem;">
    <div class="card" style="padding:1rem;text-align:center;">
        <div style="font-size:1.5rem;font-weight:800;color:#f1f5f9;">{{ $total }}</div>
        <div style="font-size:0.75rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;margin-top:0.25rem;">Total</div>
    </div>
    <div class="card" style="padding:1rem;text-align:center;">
        <div style="font-size:1.5rem;font-weight:800;color:#fbbf24;">{{ $pending }}</div>
        <div style="font-size:0.75rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;margin-top:0.25rem;">Pending</div>
    </div>
    <div class="card" style="padding:1rem;text-align:center;">
        <div style="font-size:1.5rem;font-weight:800;color:#34d399;">{{ $approved }}</div>
        <div style="font-size:0.75rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;margin-top:0.25rem;">Approved</div>
    </div>
    <div class="card" style="padding:1rem;text-align:center;">
        <div style="font-size:1.5rem;font-weight:800;color:#818cf8;">{{ $completed }}</div>
        <div style="font-size:0.75rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;margin-top:0.25rem;">Completed</div>
    </div>
    <div class="card" style="padding:1rem;text-align:center;">
        <div style="font-size:1.5rem;font-weight:800;color:#ef4444;">{{ $rejected }}</div>
        <div style="font-size:0.75rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;margin-top:0.25rem;">Rejected</div>
    </div>
</div>

<div class="card">
    <div class="card-header"><span class="card-title">Student Enrollments</span></div>
    
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;min-width:800px;">
            <thead>
                <tr style="border-bottom:1px solid rgba(42,42,74,0.5);background:rgba(255,255,255,0.02);">
                    <th style="padding:1rem 1.25rem;text-align:left;font-size:0.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;">Student</th>
                    <th style="padding:1rem 1.25rem;text-align:left;font-size:0.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;">Reg No</th>
                    <th style="padding:1rem 1.25rem;text-align:left;font-size:0.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;">Enrolled</th>
                    <th style="padding:1rem 1.25rem;text-align:left;font-size:0.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;">Payment</th>
                    <th style="padding:1rem 1.25rem;text-align:left;font-size:0.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;">Status</th>
                    <th style="padding:1rem 1.25rem;text-align:right;font-size:0.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($enrollments->sortByDesc('created_at') as $enrollment)
                <tr style="border-bottom:1px solid rgba(42,42,74,0.4);transition:background .2s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                    <td style="padding:1rem 1.25rem;">
                        <div style="display:flex;align-items:center;gap:0.75rem;">
                            <img src="{{ $enrollment->student->user->getAvatarUrl() }}" style="width:36px;height:36px;border-radius:8px;">
                            <div>
                                <div style="font-size:0.875rem;font-weight:600;color:#f1f5f9;">{{ $enrollment->student->user->name }}</div>
                                <div style="font-size:0.75rem;color:#6b7280;">{{ $enrollment->student->user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:1rem 1.25rem;font-size:0.875rem;color:#e2e8f0;">{{ $enrollment->student->registration_number ?? '-' }}</td>
                    <td style="padding:1rem 1.25rem;font-size:0.875rem;color:#e2e8f0;">
                        <div>{{ $enrollment->created_at->format('M d, Y') }}</div>
                        <div style="font-size:0.7rem;color:#6b7280;">{{ $enrollment->created_at->format('H:i') }}</div>
                    </td>
                    <td style="padding:1rem 1.25rem;">
                        @if($enrollment->payment)
                            @php
                                $pStat = $enrollment->payment->status;
                                $pColor = $pStat=='paid' ? '#34d399' : ($pStat=='pending' ? '#fbbf24' : '#ef4444');
                            @endphp
                            <div style="font-size:0.875rem;font-weight:600;color:#f1f5f9;">KES {{ number_format($enrollment->payment->amount) }}</div>
                            <div style="font-size:0.7rem;color:{{ $pColor }};">{{ ucfirst($pStat) }}</div>
                        @else
                            <span style="font-size:0.75rem;color:#6b7280;">Free / N/A</span>
                        @endif
                    </td>
                    <td style="padding:1rem 1.25rem;">
                        @php
                            $sCfg = [
                                'pending'   => ['badge-pending','Pending'],
                                'approved'  => ['badge-student','Approved'],
                                'completed' => ['badge-active','Completed'],
                                'rejected'  => ['badge-deact','Rejected'],
                            ];
                            [$badgeCls, $badgeLbl] = $sCfg[$enrollment->status] ?? ['badge-pending', ucfirst($enrollment->status)];
                        @endphp
                        <span class="badge {{ $badgeCls }}">{{ $badgeLbl }}</span>
                    </td>
                    <td style="padding:1rem 1.25rem;text-align:right;">
                        <div style="display:flex;gap:0.5rem;justify-content:flex-end;">
                            @if($enrollment->status === 'pending')
                                <form method="POST" action="{{ route('organization.programs.enrollments.approve', [$program->id, $enrollment->id]) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-outline" style="padding:0.35rem 0.75rem;font-size:0.75rem;border-color:rgba(16,185,129,0.3);color:#34d399;">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('organization.programs.enrollments.reject', [$program->id, $enrollment->id]) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-outline" style="padding:0.35rem 0.75rem;font-size:0.75rem;border-color:rgba(239,68,68,0.3);color:#ef4444;">Reject</button>
                                </form>
                            @elseif($enrollment->status === 'approved')
                                <form method="POST" action="{{ route('organization.programs.enrollments.complete', [$program->id, $enrollment->id]) }}" style="display:inline;" onsubmit="return confirm('Mark this student as having completed the program?');">
                                    @csrf
                                    <button type="submit" class="btn btn-outline" style="padding:0.35rem 0.75rem;font-size:0.75rem;border-color:rgba(99,102,241,0.3);color:#818cf8;">Mark Complete</button>
                                </form>
                            @elseif($enrollment->status === 'completed')
                                <span style="font-size:0.75rem;color:#818cf8;padding:0.35rem 0;">🎉 Done</span>
                            @else
                                <span style="font-size:0.75rem;color:#6b7280;padding:0.35rem 0;">-</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding:3rem;text-align:center;">
                        <div style="font-size:3rem;margin-bottom:1rem;">📝</div>
                        <div style="font-size:1.1rem;font-weight:600;color:#f1f5f9;">No enrollments yet</div>
                        <div style="font-size:0.85rem;color:#6b7280;margin-top:0.5rem;">Students will appear here once they enroll.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</div>
@endsection
