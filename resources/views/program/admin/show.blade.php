@extends('layouts.app')
@section('title', $program->name)
@section('page-title', 'Program Detail')

@section('content')
<div class="animate-fade-up">

{{-- Back Link --}}
<a href="{{ route('admin.programs.index') }}" style="display:inline-flex;align-items:center;gap:0.5rem;color:#6b7280;text-decoration:none;font-size:0.875rem;margin-bottom:1.5rem;transition:color .2s;" onmouseover="this.style.color='#f1f5f9'" onmouseout="this.style.color='#6b7280'">
    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    Back to All Programs
</a>

@php
    $statusCfg = [
        'published' => ['#34d399','rgba(16,185,129,0.15)','Published'],
        'draft'     => ['#fbbf24','rgba(245,158,11,0.15)','Draft'],
        'closed'    => ['#6b7280','rgba(107,114,128,0.15)','Closed'],
    ];
    [$sc,$sbg,$sl] = $statusCfg[$program->status] ?? ['#6b7280','rgba(107,114,128,0.15)',ucfirst($program->status)];
    
    $revenue = $program->enrollments->filter(function($e) {
        return $e->payment && $e->payment->status === 'paid';
    })->sum(function($e) {
        return $e->payment->amount;
    });
@endphp

{{-- Banner --}}
<div style="background:linear-gradient(135deg,#0a1628 0%,#0d1f38 60%,#0a1628 100%);border:1px solid rgba(99,102,241,0.2);border-radius:20px;padding:2rem;margin-bottom:1.5rem;position:relative;overflow:hidden;">
    <div style="position:absolute;top:-50px;right:-30px;width:250px;height:250px;background:radial-gradient(circle,rgba(99,102,241,0.15),transparent 70%);pointer-events:none;"></div>
    
    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:1.5rem;flex-wrap:wrap;position:relative;z-index:1;">
        <div style="flex:1;min-width:300px;">
            <div style="display:flex;gap:0.75rem;align-items:center;margin-bottom:0.75rem;flex-wrap:wrap;">
                <span style="font-size:0.75rem;color:#818cf8;background:rgba(99,102,241,0.1);padding:0.25rem 0.625rem;border-radius:20px;border:1px solid rgba(99,102,241,0.2);">{{ $program->category?->icon ?? '📚' }} {{ $program->category?->name ?? 'Uncategorized' }}</span>
                <span style="font-size:0.75rem;font-weight:600;color:{{ $sc }};background:{{ $sbg }};padding:0.25rem 0.625rem;border-radius:20px;">{{ $sl }}</span>
            </div>
            <h1 style="font-size:2rem;font-weight:800;color:#f1f5f9;margin-bottom:0.25rem;line-height:1.2;">{{ $program->name }}</h1>
            <a href="{{ route('admin.organizations.show', $program->organization->id) }}" style="font-size:1rem;color:#a78bfa;text-decoration:none;">🏢 {{ $program->organization->name }}</a>
        </div>
    </div>
</div>

{{-- Quick Stats --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;margin-bottom:1.5rem;" class="animate-fade-up-delay">
    <div class="card" style="padding:1.25rem;display:flex;align-items:center;gap:1rem;">
        <div style="width:48px;height:48px;border-radius:12px;background:rgba(16,185,129,0.1);display:flex;align-items:center;justify-content:center;font-size:1.5rem;">🎓</div>
        <div>
            <div style="font-size:0.75rem;color:#94a3b8;font-weight:500;">Enrollments</div>
            <div style="font-size:1.5rem;font-weight:800;color:#f1f5f9;">{{ $program->enrollments->count() }} <span style="font-size:0.875rem;font-weight:500;color:#6b7280;">/ {{ $program->capacity }}</span></div>
        </div>
    </div>
    <div class="card" style="padding:1.25rem;display:flex;align-items:center;gap:1rem;">
        <div style="width:48px;height:48px;border-radius:12px;background:rgba(6,182,212,0.1);display:flex;align-items:center;justify-content:center;font-size:1.5rem;">📅</div>
        <div>
            <div style="font-size:0.75rem;color:#94a3b8;font-weight:500;">Sessions</div>
            <div style="font-size:1.5rem;font-weight:800;color:#f1f5f9;">{{ $program->sessions->count() }}</div>
        </div>
    </div>
    <div class="card" style="padding:1.25rem;display:flex;align-items:center;gap:1rem;">
        <div style="width:48px;height:48px;border-radius:12px;background:rgba(245,158,11,0.1);display:flex;align-items:center;justify-content:center;font-size:1.5rem;">💰</div>
        <div>
            <div style="font-size:0.75rem;color:#94a3b8;font-weight:500;">Est. Revenue</div>
            <div style="font-size:1.25rem;font-weight:700;color:#f1f5f9;">KES {{ number_format($revenue) }}</div>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:1.5rem;" class="animate-fade-up-delay-2">
    
    {{-- Left Column --}}
    <div style="display:grid;gap:1.5rem;align-content:start;">
        <div class="card">
            <div class="card-header"><span class="card-title">Description</span></div>
            <div class="card-body">
                <p style="color:#e2e8f0;line-height:1.6;font-size:0.95rem;white-space:pre-wrap;">{{ $program->description }}</p>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <span class="card-title">Training Sessions</span>
            </div>
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;min-width:600px;">
                    <thead>
                        <tr style="border-bottom:1px solid rgba(42,42,74,0.5);background:rgba(255,255,255,0.02);">
                            <th style="padding:0.75rem 1rem;text-align:left;font-size:0.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;">Date</th>
                            <th style="padding:0.75rem 1rem;text-align:left;font-size:0.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;">Title</th>
                            <th style="padding:0.75rem 1rem;text-align:left;font-size:0.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;">Venue/Link</th>
                            <th style="padding:0.75rem 1rem;text-align:left;font-size:0.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;">Trainer</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($program->sessions->sortBy('start_date') as $session)
                        <tr style="border-bottom:1px solid rgba(42,42,74,0.4);">
                            <td style="padding:0.75rem 1rem;font-size:0.875rem;color:#f1f5f9;">
                                <div>{{ $session->start_date->format('M d, Y') }}</div>
                                <div style="font-size:0.7rem;color:#6b7280;">{{ $session->start_date->format('H:i') }} - {{ $session->end_date->format('H:i') }}</div>
                            </td>
                            <td style="padding:0.75rem 1rem;font-size:0.875rem;color:#e2e8f0;font-weight:500;">{{ $session->title }}</td>
                            <td style="padding:0.75rem 1rem;font-size:0.8rem;color:#94a3b8;">{{ $session->venue ?? 'Online' }}</td>
                            <td style="padding:0.75rem 1rem;font-size:0.8rem;color:#94a3b8;">{{ $session->trainer_information ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="padding:1.5rem;text-align:center;color:#6b7280;font-size:0.875rem;">No sessions created yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <span class="card-title">Latest Enrollments</span>
            </div>
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;min-width:600px;">
                    <thead>
                        <tr style="border-bottom:1px solid rgba(42,42,74,0.5);background:rgba(255,255,255,0.02);">
                            <th style="padding:0.75rem 1rem;text-align:left;font-size:0.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;">Student</th>
                            <th style="padding:0.75rem 1rem;text-align:left;font-size:0.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;">Enrolled</th>
                            <th style="padding:0.75rem 1rem;text-align:left;font-size:0.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;">Payment</th>
                            <th style="padding:0.75rem 1rem;text-align:left;font-size:0.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($program->enrollments->sortByDesc('created_at')->take(10) as $enrollment)
                        <tr style="border-bottom:1px solid rgba(42,42,74,0.4);">
                            <td style="padding:0.75rem 1rem;">
                                <div style="display:flex;align-items:center;gap:0.5rem;">
                                    <img src="{{ $enrollment->student->user->getAvatarUrl() }}" style="width:28px;height:28px;border-radius:6px;">
                                    <div>
                                        <div style="font-size:0.875rem;font-weight:600;color:#f1f5f9;">{{ $enrollment->student->user->name }}</div>
                                        <div style="font-size:0.7rem;color:#6b7280;">{{ $enrollment->student->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding:0.75rem 1rem;font-size:0.8rem;color:#94a3b8;">{{ $enrollment->created_at->format('M d, Y') }}</td>
                            <td style="padding:0.75rem 1rem;font-size:0.8rem;">
                                @if($enrollment->payment)
                                    <span style="color:{{ $enrollment->payment->status=='paid' ? '#34d399' : '#fbbf24' }};">{{ ucfirst($enrollment->payment->status) }}</span>
                                    <div style="color:#6b7280;font-size:0.7rem;">KES {{ number_format($enrollment->payment->amount) }}</div>
                                @else
                                    <span style="color:#6b7280;">Free</span>
                                @endif
                            </td>
                            <td style="padding:0.75rem 1rem;">
                                @php
                                    $sCfg = [
                                        'pending'   => ['badge-pending','Pending'],
                                        'approved'  => ['badge-student','Approved'],
                                        'completed' => ['badge-active','Completed'],
                                        'rejected'  => ['badge-deact','Rejected'],
                                    ];
                                    [$bCls, $bLbl] = $sCfg[$enrollment->status] ?? ['badge-pending', ucfirst($enrollment->status)];
                                @endphp
                                <span class="badge {{ $bCls }}">{{ $bLbl }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="padding:1.5rem;text-align:center;color:#6b7280;font-size:0.875rem;">No enrollments yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    {{-- Right Column --}}
    <div style="display:grid;gap:1.5rem;align-content:start;">
        <div class="card">
            <div class="card-header"><span class="card-title">Program Info</span></div>
            <div class="card-body" style="display:grid;gap:0.5rem;padding:1rem;">
                @foreach([
                    ['Mode', ucfirst(str_replace('_','-',$program->mode))],
                    ['Venue', $program->venue ?? 'N/A'],
                    ['Cost', $program->cost > 0 ? 'KES '.number_format($program->cost,0) : 'Free'],
                    ['Duration', $program->duration],
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
            <div class="card-header"><span class="card-title">Organization Contact</span></div>
            <div class="card-body" style="display:grid;gap:0.5rem;padding:1rem;">
                <div style="display:flex;justify-content:space-between;padding:0.375rem 0;border-bottom:1px solid rgba(42,42,74,0.4);">
                    <span style="font-size:0.8rem;color:#94a3b8;">Email</span>
                    <span style="font-size:0.8rem;color:#e2e8f0;font-weight:500;text-align:right;">{{ $program->organization->email ?? $program->organization->user->email }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:0.375rem 0;border-bottom:1px solid rgba(42,42,74,0.4);">
                    <span style="font-size:0.8rem;color:#94a3b8;">Phone</span>
                    <span style="font-size:0.8rem;color:#e2e8f0;font-weight:500;text-align:right;">{{ $program->organization->phone ?? $program->organization->user->phone }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:0.375rem 0;border-bottom:1px solid rgba(42,42,74,0.4);">
                    <span style="font-size:0.8rem;color:#94a3b8;">Contact Person</span>
                    <span style="font-size:0.8rem;color:#e2e8f0;font-weight:500;text-align:right;">{{ $program->organization->contact_person ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
@endsection
