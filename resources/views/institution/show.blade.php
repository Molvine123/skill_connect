@extends('layouts.app')
@section('title', $institution->name . ' — Details')
@section('page-title', 'Institution Details')

@section('content')

{{-- Breadcrumb / Back --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;" class="animate-fade-up">
    <div style="display:flex;align-items:center;gap:0.75rem;">
        <a href="{{ route('admin.institutions.index') }}" style="color:#6b7280;text-decoration:none;font-size:0.875rem;">← Institutions</a>
        <span style="color:#4b5563;">/</span>
        <span style="color:#e2e8f0;font-size:0.875rem;">{{ $institution->name }}</span>
    </div>
    <div style="display:flex;gap:0.75rem;">
        @if($institution->status === 'pending')
        <form method="POST" action="{{ route('admin.approve', ['type'=>'institution','id'=>$institution->id]) }}">
            @csrf
            <button type="submit" class="btn" style="background:rgba(16,185,129,0.15);color:#34d399;border:1px solid rgba(16,185,129,0.3);">✓ Approve Institution</button>
        </form>
        <form method="POST" action="{{ route('admin.reject', ['type'=>'institution','id'=>$institution->id]) }}">
            @csrf
            <button type="submit" class="btn" style="background:rgba(239,68,68,0.15);color:#f87171;border:1px solid rgba(239,68,68,0.3);" onclick="return confirm('Reject this institution?')">✗ Reject</button>
        </form>
        @endif
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 2fr;gap:1.5rem;" class="animate-fade-up">

    {{-- Profile Card --}}
    <div style="display:grid;gap:1.5rem;">
        <div class="card">
            <div class="card-body" style="text-align:center;padding:2rem;">
                @if($institution->logo_path)
                <img src="{{ asset('storage/'.$institution->logo_path) }}" style="width:90px;height:90px;border-radius:18px;object-fit:cover;border:3px solid rgba(6,182,212,0.3);margin-bottom:1rem;">
                @else
                <div style="width:90px;height:90px;border-radius:18px;background:rgba(6,182,212,0.1);border:3px solid rgba(6,182,212,0.2);display:flex;align-items:center;justify-content:center;font-size:3rem;margin:0 auto 1rem;">{{ $institution->type_icon }}</div>
                @endif
                <h2 style="font-size:1.2rem;font-weight:800;color:#f1f5f9;margin-bottom:0.375rem;">{{ $institution->name }}</h2>
                <div style="font-size:0.875rem;color:#22d3ee;margin-bottom:0.75rem;">{{ $institution->type_label }}</div>
                <span class="badge {{ match($institution->status) { 'active'=>'badge-active', 'pending'=>'badge-pending', default=>'badge-deact' } }}" style="font-size:0.875rem;padding:0.375rem 1rem;">{{ ucfirst($institution->status) }}</span>
            </div>
        </div>

        {{-- Stats --}}
        <div class="card">
            <div class="card-header"><span class="card-title">📊 Statistics</span></div>
            <div class="card-body">
                @php $studentIds = $institution->students->pluck('id'); @endphp
                @foreach([
                    ['Total Students',  $institution->students->count(), '👥', '#22d3ee'],
                    ['Active Enrollments', \App\Models\Enrollment::whereIn('student_id',$studentIds)->where('status','approved')->count(), '📚', '#818cf8'],
                    ['Certificates',    \App\Models\Certificate::whereIn('student_id',$studentIds)->count(), '🏆', '#34d399'],
                    ['Registered',      $institution->created_at->format('M d, Y'), '📅', '#fbbf24'],
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
            <div class="card-header"><span class="card-title">📋 Institution Details</span></div>
            <div class="card-body" style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                @foreach([
                    ['Registration Number', $institution->registration_number ?? '—', '🔖'],
                    ['Type',                $institution->type_label, '🏛️'],
                    ['Location',            $institution->location ?? '—', '📍'],
                    ['County',              $institution->county ?? '—', '🗺️'],
                    ['Phone',               $institution->phone ?? '—', '📱'],
                    ['Email',               $institution->email ?? '—', '📧'],
                    ['Website',             $institution->website ?? '—', '🌐'],
                    ['Account Email',       $institution->user?->email ?? '—', '👤'],
                ] as [$label, $value, $icon])
                <div style="padding:0.875rem;background:rgba(255,255,255,0.02);border:1px solid var(--sc-dark-border);border-radius:10px;">
                    <div style="font-size:0.75rem;color:#6b7280;margin-bottom:0.25rem;">{{ $icon }} {{ $label }}</div>
                    <div style="font-size:0.9rem;color:#e2e8f0;font-weight:500;word-break:break-all;">{{ $value }}</div>
                </div>
                @endforeach
            </div>
            @if($institution->description)
            <div style="margin:0 1.5rem 1.5rem;padding:1rem;background:rgba(6,182,212,0.04);border:1px solid rgba(6,182,212,0.12);border-radius:12px;">
                <div style="font-size:0.75rem;color:#6b7280;margin-bottom:0.5rem;">📝 About</div>
                <p style="color:#94a3b8;font-size:0.9rem;line-height:1.7;margin:0;">{{ $institution->description }}</p>
            </div>
            @endif
        </div>

        {{-- Students Table --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">👥 Students</span>
                <span style="font-size:0.8125rem;color:#6b7280;">{{ $institution->students->count() }} registered</span>
            </div>
            <div style="overflow-x:auto;">
                <table class="sc-table">
                    <thead><tr><th>Student</th><th>Email</th><th>Enrollments</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse($institution->students as $student)
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:0.75rem;">
                                    <img src="{{ $student->user?->getAvatarUrl() }}" style="width:32px;height:32px;border-radius:8px;">
                                    <span style="font-weight:500;color:#e2e8f0;">{{ $student->user?->name ?? '—' }}</span>
                                </div>
                            </td>
                            <td style="color:#9ca3af;font-size:0.8125rem;">{{ $student->user?->email ?? '—' }}</td>
                            <td><span class="badge badge-inst">{{ $student->enrollments?->count() ?? 0 }}</span></td>
                            <td>
                                <span class="badge {{ $student->user?->status === 'active' ? 'badge-active' : 'badge-pending' }}">{{ ucfirst($student->user?->status ?? '—') }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" style="text-align:center;color:#4b5563;padding:2rem;">No students linked yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
