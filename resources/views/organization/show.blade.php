@extends('layouts.app')
@section('title', $organization->name . ' — Details')
@section('page-title', 'Organization Details')

@section('content')

{{-- Breadcrumb / Back --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;" class="animate-fade-up">
    <div style="display:flex;align-items:center;gap:0.75rem;">
        <a href="{{ route('admin.organizations.index') }}" style="color:#6b7280;text-decoration:none;font-size:0.875rem;">← Organizations</a>
        <span style="color:#4b5563;">/</span>
        <span style="color:#e2e8f0;font-size:0.875rem;">{{ $organization->name }}</span>
    </div>
    <div style="display:flex;gap:0.75rem;">
        @if($organization->status === 'pending')
        <form method="POST" action="{{ route('admin.approve', ['type'=>'organization','id'=>$organization->id]) }}">
            @csrf
            <button type="submit" class="btn" style="background:rgba(16,185,129,0.15);color:#34d399;border:1px solid rgba(16,185,129,0.3);">✓ Approve Organization</button>
        </form>
        <form method="POST" action="{{ route('admin.reject', ['type'=>'organization','id'=>$organization->id]) }}">
            @csrf
            <button type="submit" class="btn" style="background:rgba(239,68,68,0.15);color:#f87171;border:1px solid rgba(239,68,68,0.3);" onclick="return confirm('Reject this organization?')">✗ Reject</button>
        </form>
        @endif
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 2fr;gap:1.5rem;" class="animate-fade-up">

    {{-- Profile Card --}}
    <div style="display:grid;gap:1.5rem;">
        <div class="card">
            <div class="card-body" style="text-align:center;padding:2rem;">
                @if($organization->logo_path)
                <img src="{{ asset('storage/'.$organization->logo_path) }}" style="width:90px;height:90px;border-radius:18px;object-fit:cover;border:3px solid rgba(139,92,246,0.3);margin-bottom:1rem;">
                @else
                <div style="width:90px;height:90px;border-radius:18px;background:rgba(139,92,246,0.1);border:3px solid rgba(139,92,246,0.2);display:flex;align-items:center;justify-content:center;font-size:3rem;margin:0 auto 1rem;">{{ $organization->type_icon }}</div>
                @endif
                <h2 style="font-size:1.2rem;font-weight:800;color:#f1f5f9;margin-bottom:0.375rem;">{{ $organization->name }}</h2>
                <div style="font-size:0.875rem;color:#a78bfa;margin-bottom:0.75rem;">{{ $organization->type_label }}</div>
                <span class="badge {{ match($organization->status) { 'active'=>'badge-active', 'pending'=>'badge-pending', default=>'badge-deact' } }}" style="font-size:0.875rem;padding:0.375rem 1rem;">{{ ucfirst($organization->status) }}</span>
            </div>
        </div>

        {{-- Stats --}}
        <div class="card">
            <div class="card-header"><span class="card-title">📊 Statistics</span></div>
            <div class="card-body">
                @php
                    $programIds = $organization->programs->pluck('id');
                    $totalRevenue = \App\Models\Payment::whereIn('enrollment_id', \App\Models\Enrollment::whereIn('program_id', $programIds)->pluck('id'))->where('status','paid')->sum('amount');
                    $totalEnrolled = \App\Models\Enrollment::whereIn('program_id', $programIds)->distinct('student_id')->count('student_id');
                @endphp
                @foreach([
                    ['Total Programs',   $organization->programs->count(),          '📚', '#a78bfa'],
                    ['Active Programs',  $organization->programs->where('status','active')->count(), '✅', '#34d399'],
                    ['Total Students',   $totalEnrolled,                            '👥', '#22d3ee'],
                    ['Revenue (KES)',    'KES '.number_format($totalRevenue,0),     '💰', '#fbbf24'],
                    ['Registered',       $organization->created_at->format('M d, Y'), '📅', '#818cf8'],
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
    <div style="display:grid;gap:1.5rem;">

        {{-- Contact Details --}}
        <div class="card">
            <div class="card-header"><span class="card-title">📋 Organization Details</span></div>
            <div class="card-body" style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                @foreach([
                    ['Type',           $organization->type_label, '🏷️'],
                    ['Contact Person', $organization->contact_person ?? '—', '👤'],
                    ['Phone',          $organization->phone ?? '—', '📱'],
                    ['Email',          $organization->email ?? '—', '📧'],
                    ['Address',        $organization->address ?? '—', '📍'],
                    ['County',         $organization->county ?? '—', '🗺️'],
                    ['Website',        $organization->website ?? '—', '🌐'],
                    ['Account Email',  $organization->user?->email ?? '—', '🔑'],
                ] as [$label, $value, $icon])
                <div style="padding:0.875rem;background:rgba(255,255,255,0.02);border:1px solid var(--sc-dark-border);border-radius:10px;">
                    <div style="font-size:0.75rem;color:#6b7280;margin-bottom:0.25rem;">{{ $icon }} {{ $label }}</div>
                    <div style="font-size:0.9rem;color:#e2e8f0;font-weight:500;word-break:break-all;">{{ $value }}</div>
                </div>
                @endforeach
            </div>
            @if($organization->description)
            <div style="margin:0 1.5rem 1.5rem;padding:1rem;background:rgba(139,92,246,0.04);border:1px solid rgba(139,92,246,0.12);border-radius:12px;">
                <div style="font-size:0.75rem;color:#6b7280;margin-bottom:0.5rem;">📝 About</div>
                <p style="color:#94a3b8;font-size:0.9rem;line-height:1.7;margin:0;">{{ $organization->description }}</p>
            </div>
            @endif
        </div>

        {{-- Programs Table --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">📚 Programs Offered</span>
                <span style="font-size:0.8125rem;color:#6b7280;">{{ $organization->programs->count() }} total</span>
            </div>
            <div style="overflow-x:auto;">
                <table class="sc-table">
                    <thead><tr><th>Program</th><th>Category</th><th>Mode</th><th>Students</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse($organization->programs as $program)
                        <tr>
                            <td>
                                <div style="font-weight:500;color:#e2e8f0;">{{ $program->name }}</div>
                                @if($program->cost > 0)
                                <div style="font-size:0.75rem;color:#34d399;">KES {{ number_format($program->cost, 0) }}</div>
                                @else
                                <div style="font-size:0.75rem;color:#6b7280;">Free</div>
                                @endif
                            </td>
                            <td style="color:#9ca3af;font-size:0.8125rem;">{{ $program->category?->name ?? '—' }}</td>
                            <td>
                                <span class="badge badge-inst" style="font-size:0.7rem;">{{ ucfirst($program->mode ?? '—') }}</span>
                            </td>
                            <td><span class="badge badge-student">{{ $program->enrollments?->count() ?? 0 }}</span></td>
                            <td>
                                <span class="badge {{ match($program->status ?? '') { 'active'=>'badge-active', 'draft'=>'badge-pending', 'completed'=>'badge-inst', default=>'badge-deact' } }}">{{ ucfirst($program->status ?? '—') }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" style="text-align:center;color:#4b5563;padding:2rem;">No programs published yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
