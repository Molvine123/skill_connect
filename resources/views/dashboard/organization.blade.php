@extends('layouts.app')
@section('title', 'Organization Dashboard')
@section('page-title', 'Organization Dashboard')

@section('content')

{{-- Welcome Banner --}}
<div style="background:linear-gradient(135deg,#160d2a 0%,#1e1244 60%,#160d2a 100%);border:1px solid rgba(139,92,246,0.2);border-radius:20px;padding:2rem;margin-bottom:2rem;position:relative;overflow:hidden;" class="animate-fade-up">
    <div style="position:absolute;top:-60px;right:-60px;width:280px;height:280px;background:radial-gradient(circle,rgba(139,92,246,0.2),transparent 70%);pointer-events:none;"></div>
    <div style="position:absolute;bottom:-40px;left:200px;width:200px;height:200px;background:radial-gradient(circle,rgba(99,102,241,0.12),transparent 70%);pointer-events:none;"></div>
    <div style="position:relative;z-index:1;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
        <div>
            <div style="display:flex;align-items:center;gap:0.625rem;margin-bottom:0.5rem;">
                <div style="width:10px;height:10px;background:#a78bfa;border-radius:50%;box-shadow:0 0 0 3px rgba(139,92,246,0.25);"></div>
                <span style="font-size:0.8125rem;color:#a78bfa;font-weight:500;">Organization Portal</span>
            </div>
            <h1 style="font-size:1.75rem;font-weight:800;color:#f1f5f9;margin-bottom:0.375rem;">
                Welcome, {{ explode(' ', auth()->user()->name)[0] }}! 🏢
            </h1>
            <p style="color:#94a3b8;font-size:0.9375rem;">Manage your training programs and track student enrollments.</p>
        </div>
        @if($organization)
        <div style="display:flex;align-items:center;gap:1rem;">
            @if($organization->logo_path)
            <img src="{{ asset('storage/'.$organization->logo_path) }}" style="width:64px;height:64px;border-radius:14px;object-fit:cover;border:2px solid rgba(139,92,246,0.4);">
            @else
            <div style="width:64px;height:64px;border-radius:14px;background:rgba(139,92,246,0.1);border:2px solid rgba(139,92,246,0.3);display:flex;align-items:center;justify-content:center;font-size:2rem;">{{ $organization->type_icon }}</div>
            @endif
            <div>
                <div style="font-weight:700;color:#e2e8f0;">{{ $organization->name }}</div>
                <div style="font-size:0.8rem;color:#a78bfa;">{{ $organization->type_label }}</div>
                <span class="badge {{ $organization->status === 'active' ? 'badge-active' : 'badge-pending' }}" style="margin-top:0.25rem;display:inline-block;">{{ ucfirst($organization->status) }}</span>
            </div>
        </div>
        @endif
    </div>
</div>

@if($organization && $organization->status === 'pending')
<div class="alert alert-warning animate-fade-up" style="margin-bottom:1.5rem;">
    ⏳ Your organization is <strong>pending admin approval</strong>. Publishing programs requires approval first.
</div>
@endif

{{-- Stats --}}
<div class="stats-grid animate-fade-up-delay">
    <div class="stat-card violet">
        <div class="stat-icon violet">📚</div>
        <div class="stat-label">Published Programs</div>
        <div class="stat-value">{{ $stats['published_programs'] }}</div>
        <div class="stat-change" style="color:#a78bfa;">Active programs</div>
    </div>
    <div class="stat-card indigo">
        <div class="stat-icon indigo">🎓</div>
        <div class="stat-label">Enrolled Students</div>
        <div class="stat-value">{{ $stats['enrolled_students'] }}</div>
        <div class="stat-change">Across all programs</div>
    </div>
    <div class="stat-card emerald">
        <div class="stat-icon emerald">💰</div>
        <div class="stat-label">Revenue (KES)</div>
        <div class="stat-value" style="font-size:1.5rem;">{{ number_format($stats['revenue'], 0) }}</div>
        <div class="stat-change" style="color:#34d399;">Total collected</div>
    </div>
    <div class="stat-card amber">
        <div class="stat-icon amber">📅</div>
        <div class="stat-label">Upcoming Sessions</div>
        <div class="stat-value">{{ $stats['upcoming_sessions'] }}</div>
        <div class="stat-change" style="color:#fbbf24;">Scheduled ahead</div>
    </div>
</div>

{{-- Grid --}}
<div style="display:grid;grid-template-columns:2fr 1fr;gap:1.5rem;" class="animate-fade-up-delay-2">

    {{-- Quick Actions --}}
    <div class="card">
        <div class="card-header"><span class="card-title">Quick Actions</span></div>
        <div class="card-body" style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
            @foreach([
                ['➕','Create Program','Publish a new training program', route('organization.programs.create')],
                ['📋','My Programs','View and manage your programs', route('organization.programs.index')],
                ['📅','Schedule Session','Manage your training sessions', route('organization.sessions.index')],
                ['👥','Manage Enrollments','Review student enrollments', route('organization.enrollments.index')],
                ['✅','Record Attendance','Mark session attendance', route('organization.attendance')],
                ['🏆','Issue Certificates','Generate completion certificates', route('organization.certificates.index')],
                ['⚙️','Edit Profile','Update your organization profile', route('organization.profile.edit')],
                ['🌐','Browse Platform Programs','Discover & enroll students', route('organization.programs.browse')],
            ] as [$icon, $title, $desc, $link])
            <a href="{{ $link }}" style="display:flex;align-items:center;gap:0.875rem;padding:0.875rem 1rem;background:rgba(255,255,255,0.02);border:1px solid var(--sc-dark-border);border-radius:12px;text-decoration:none;transition:all 0.15s;" onmouseover="this.style.background='rgba(139,92,246,0.06)';this.style.borderColor='rgba(139,92,246,0.25)'" onmouseout="this.style.background='rgba(255,255,255,0.02)';this.style.borderColor='var(--sc-dark-border)'">
                <div style="font-size:1.25rem;width:38px;height:38px;display:flex;align-items:center;justify-content:center;background:rgba(139,92,246,0.1);border-radius:10px;flex-shrink:0;">{{ $icon }}</div>
                <div>
                    <div style="font-size:0.85rem;font-weight:600;color:#e2e8f0;">{{ $title }}</div>
                    <div style="font-size:0.75rem;color:#6b7280;margin-top:0.1rem;">{{ $desc }}</div>
                </div>
            </a>
            @endforeach
        </div>
    </div>

    {{-- Right column --}}
    <div style="display:grid;gap:1.5rem;">

        {{-- Org Profile Card --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Organization Profile</span>
                <a href="{{ route('organization.profile.edit') }}" class="btn btn-sm" style="background:rgba(139,92,246,0.1);color:#a78bfa;border:1px solid rgba(139,92,246,0.25);font-size:0.8rem;">✏️ Edit</a>
            </div>
            <div class="card-body">
                <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.25rem;padding:1rem;background:rgba(139,92,246,0.06);border:1px solid rgba(139,92,246,0.15);border-radius:12px;">
                    @if($organization && $organization->logo_path)
                    <img src="{{ asset('storage/'.$organization->logo_path) }}" style="width:50px;height:50px;border-radius:10px;object-fit:cover;border:2px solid rgba(139,92,246,0.3);">
                    @else
                    <div style="width:50px;height:50px;border-radius:10px;background:rgba(139,92,246,0.15);display:flex;align-items:center;justify-content:center;font-size:1.5rem;flex-shrink:0;">{{ $organization?->type_icon ?? '🏢' }}</div>
                    @endif
                    <div>
                        <div style="font-weight:700;color:#e2e8f0;">{{ auth()->user()->name }}</div>
                        <div style="font-size:0.8125rem;color:#a78bfa;margin-top:0.25rem;">{{ $organization?->type_label ?? 'Organization' }}</div>
                    </div>
                </div>
                <div style="display:grid;gap:0.625rem;">
                    @foreach([
                        ['Contact', $organization?->contact_person ?? 'Not set', '👤'],
                        ['Phone', $organization?->phone ?? 'Not set', '📱'],
                        ['County', $organization?->county ?? 'Not set', '🗺️'],
                        ['Status', ucfirst(auth()->user()->status), '🔖'],
                    ] as [$label, $value, $icon])
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:0.4rem 0;border-bottom:1px solid rgba(42,42,74,0.4);">
                        <span style="font-size:0.8rem;color:#6b7280;">{{ $icon }} {{ $label }}</span>
                        <span style="font-size:0.8rem;color:#e2e8f0;font-weight:500;">{{ $value }}</span>
                    </div>
                    @endforeach
                </div>
                <a href="{{ route('organization.profile.edit') }}" class="btn btn-outline btn-full" style="margin-top:1rem;font-size:0.875rem;">⚙️ Manage Profile</a>
            </div>
        </div>

        {{-- Enrollment Trends Chart --}}
        <div class="card">
            <div class="card-header"><span class="card-title">Enrollment Trends</span></div>
            <div class="card-body" style="padding:1rem;">
                <canvas id="enrollmentTrendsChart" height="200"></canvas>
            </div>
        </div>

        {{-- Program Status --}}
        <div class="card">
            <div class="card-header"><span class="card-title">Program Status</span></div>
            <div class="card-body">
                @if($programs->count())
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
                    @php
                        $active    = $programs->where('status','active')->count();
                        $draft     = $programs->where('status','draft')->count();
                        $completed = $programs->where('status','completed')->count();
                        $cancelled = $programs->where('status','cancelled')->count();
                    @endphp
                    @foreach([
                        ['Active',    $active,    '#10b981','rgba(16,185,129,0.1)'],
                        ['Draft',     $draft,     '#f59e0b','rgba(245,158,11,0.1)'],
                        ['Completed', $completed, '#6366f1','rgba(99,102,241,0.1)'],
                        ['Cancelled', $cancelled, '#ef4444','rgba(239,68,68,0.1)'],
                    ] as [$label, $count, $color, $bg])
                    <div style="text-align:center;padding:1rem;background:{{ $bg }};border-radius:12px;border:1px solid {{ $color }}22;">
                        <div style="font-size:1.75rem;font-weight:800;color:{{ $color }};">{{ $count }}</div>
                        <div style="font-size:0.75rem;color:#9ca3af;margin-top:0.25rem;font-weight:500;">{{ $label }}</div>
                    </div>
                    @endforeach
                </div>
                @else
                <div style="text-align:center;padding:1.5rem;">
                    <div style="font-size:2rem;margin-bottom:0.5rem;">📋</div>
                    <p style="color:#6b7280;font-size:0.875rem;">No programs yet. Create your first training program!</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Recent Enrollments --}}
    @if($recentEnrolls->count())
    <div class="card" style="grid-column:1/-1;">
        <div class="card-header">
            <span class="card-title">📋 Recent Enrollments</span>
            <span style="font-size:0.8125rem;color:#6b7280;">{{ $recentEnrolls->count() }} recent</span>
        </div>
        <div style="overflow-x:auto;">
            <table class="sc-table">
                <thead><tr><th>Student</th><th>Program</th><th>Status</th><th>Date</th></tr></thead>
                <tbody>
                    @foreach($recentEnrolls as $enroll)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:0.75rem;">
                                <img src="{{ $enroll->student?->user?->getAvatarUrl() }}" style="width:32px;height:32px;border-radius:8px;">
                                <span style="font-weight:500;color:#e2e8f0;">{{ $enroll->student?->user?->name ?? '—' }}</span>
                            </div>
                        </td>
                        <td style="color:#9ca3af;">{{ $enroll->program?->name ?? '—' }}</td>
                        <td>
                            <span class="badge {{ match($enroll->status) { 'approved'=>'badge-active', 'pending'=>'badge-pending', 'completed'=>'badge-inst', default=>'badge-deact' } }}">
                                {{ ucfirst($enroll->status) }}
                            </span>
                        </td>
                        <td style="color:#6b7280;font-size:0.8125rem;">{{ $enroll->created_at->diffForHumans() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
window.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('enrollmentTrendsChart');
    if(ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($enrollmentTrends['labels']) !!},
                datasets: [{
                    label: 'Enrollments',
                    data: {!! json_encode($enrollmentTrends['data']) !!},
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' } },
                    x: { grid: { color: 'rgba(255,255,255,0.05)' } }
                },
                plugins: {
                    legend: { labels: { color: '#f1f5f9' } }
                }
            }
        });
    }
});
</script>
@endpush
