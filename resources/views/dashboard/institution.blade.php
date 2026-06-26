@extends('layouts.app')
@section('title', 'Institution Dashboard')
@section('page-title', 'Institution Dashboard')

@section('content')

{{-- Welcome Banner --}}
<div style="background:linear-gradient(135deg,#0d1b2a 0%,#1a2744 60%,#0d1b2a 100%);border:1px solid rgba(6,182,212,0.2);border-radius:20px;padding:2rem;margin-bottom:2rem;position:relative;overflow:hidden;" class="animate-fade-up">
    <div style="position:absolute;top:-80px;right:-40px;width:280px;height:280px;background:radial-gradient(circle,rgba(6,182,212,0.15),transparent 70%);pointer-events:none;"></div>
    <div style="position:relative;z-index:1;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
        <div>
            <div style="display:flex;align-items:center;gap:0.625rem;margin-bottom:0.5rem;">
                <div style="width:10px;height:10px;background:#22d3ee;border-radius:50%;box-shadow:0 0 0 3px rgba(6,182,212,0.25);"></div>
                <span style="font-size:0.8125rem;color:#22d3ee;font-weight:500;">Institution Portal</span>
            </div>
            <h1 style="font-size:1.75rem;font-weight:800;color:#f1f5f9;margin-bottom:0.375rem;">
                Welcome, {{ explode(' ', auth()->user()->name)[0] }}! 🏫
            </h1>
            <p style="color:#94a3b8;font-size:0.9375rem;">Monitor your students' skill development and training participation.</p>
        </div>
        @if($institution)
        <div style="display:flex;align-items:center;gap:1rem;">
            @if($institution->logo_path)
            <img src="{{ asset('storage/'.$institution->logo_path) }}" style="width:64px;height:64px;border-radius:14px;object-fit:cover;border:2px solid rgba(6,182,212,0.4);">
            @else
            <div style="width:64px;height:64px;border-radius:14px;background:rgba(6,182,212,0.1);border:2px solid rgba(6,182,212,0.3);display:flex;align-items:center;justify-content:center;font-size:2rem;">🏫</div>
            @endif
            <div>
                <div style="font-weight:700;color:#e2e8f0;">{{ $institution->name }}</div>
                <div style="font-size:0.8rem;color:#22d3ee;">{{ $institution->type_label }}</div>
                <span class="badge {{ $institution->status === 'active' ? 'badge-active' : 'badge-pending' }}" style="margin-top:0.25rem;display:inline-block;">{{ ucfirst($institution->status) }}</span>
            </div>
        </div>
        @endif
    </div>
</div>

@if($institution && $institution->status === 'pending')
<div class="alert alert-warning animate-fade-up" style="margin-bottom:1.5rem;">
    ⏳ Your institution is <strong>pending admin approval</strong>. Some features are limited until approved.
</div>
@endif

{{-- Stats --}}
<div class="stats-grid animate-fade-up-delay">
    <div class="stat-card cyan">
        <div class="stat-icon cyan">👥</div>
        <div class="stat-label">Registered Students</div>
        <div class="stat-value">{{ $stats['total_students'] }}</div>
        <div class="stat-change" style="color:#22d3ee;">Under your institution</div>
    </div>
    <div class="stat-card indigo">
        <div class="stat-icon indigo">📚</div>
        <div class="stat-label">Active Enrollments</div>
        <div class="stat-value">{{ $stats['active_enrollments'] }}</div>
        <div class="stat-change">In training programs</div>
    </div>
    <div class="stat-card emerald">
        <div class="stat-icon emerald">🏆</div>
        <div class="stat-label">Certificates Earned</div>
        <div class="stat-value">{{ $stats['certificates'] }}</div>
        <div class="stat-change" style="color:#34d399;">By your students</div>
    </div>
    <div class="stat-card amber">
        <div class="stat-icon amber">📈</div>
        <div class="stat-label">Completion Rate</div>
        <div class="stat-value">{{ $stats['completion_rate'] }}%</div>
        <div class="stat-change" style="color:#fbbf24;">Program completion</div>
    </div>
</div>

{{-- Content Grid --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;" class="animate-fade-up-delay-2">

    {{-- Charts --}}
    <div class="card">
        <div class="card-header"><span class="card-title">Student Growth</span></div>
        <div class="card-body" style="padding:1rem;">
            <canvas id="studentGrowthChart" height="200"></canvas>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header"><span class="card-title">Certificates Earned</span></div>
        <div class="card-body" style="padding:1rem;">
            <canvas id="certificateTrendsChart" height="200"></canvas>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="card">
        <div class="card-header"><span class="card-title">Quick Actions</span></div>
        <div class="card-body" style="display:grid;gap:0.75rem;">
            @foreach([
                ['👥','View My Students','See all students under your institution', route('institution.students.index')],
                ['➕','Add New Student','Register a student to your institution', route('institution.students.add')],
                ['📋','Browse Programs','Browse available skill programs', route('institution.programs.index')],
                ['⚙️','Edit Institution Profile','Update your institution details', route('institution.profile.edit')],
            ] as [$icon, $title, $desc, $link])
            <a href="{{ $link }}" style="display:flex;align-items:center;gap:1rem;padding:0.875rem 1rem;background:rgba(255,255,255,0.02);border:1px solid var(--sc-dark-border);border-radius:12px;text-decoration:none;transition:all 0.15s;" onmouseover="this.style.background='rgba(6,182,212,0.06)';this.style.borderColor='rgba(6,182,212,0.25)'" onmouseout="this.style.background='rgba(255,255,255,0.02)';this.style.borderColor='var(--sc-dark-border)'">
                <div style="font-size:1.5rem;width:42px;height:42px;display:flex;align-items:center;justify-content:center;background:rgba(6,182,212,0.1);border-radius:10px;flex-shrink:0;">{{ $icon }}</div>
                <div>
                    <div style="font-size:0.9rem;font-weight:600;color:#e2e8f0;">{{ $title }}</div>
                    <div style="font-size:0.8rem;color:#6b7280;margin-top:0.125rem;">{{ $desc }}</div>
                </div>
                <svg style="margin-left:auto;" width="16" height="16" fill="none" stroke="#4b5563" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            @endforeach
        </div>
    </div>

    {{-- Institution Profile Card --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">Institution Profile</span>
            <a href="{{ route('institution.profile.edit') }}" class="btn btn-sm" style="background:rgba(6,182,212,0.1);color:#22d3ee;border:1px solid rgba(6,182,212,0.25);font-size:0.8rem;">✏️ Edit</a>
        </div>
        <div class="card-body">
            <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.5rem;padding:1rem;background:rgba(6,182,212,0.06);border:1px solid rgba(6,182,212,0.15);border-radius:12px;">
                @if($institution && $institution->logo_path)
                <img src="{{ asset('storage/'.$institution->logo_path) }}" style="width:56px;height:56px;border-radius:12px;object-fit:cover;border:2px solid rgba(6,182,212,0.3);">
                @else
                <div style="width:56px;height:56px;border-radius:12px;background:rgba(6,182,212,0.15);display:flex;align-items:center;justify-content:center;font-size:1.75rem;flex-shrink:0;">🏫</div>
                @endif
                <div>
                    <div style="font-weight:700;color:#e2e8f0;font-size:1rem;">{{ auth()->user()->name }}</div>
                    <div style="font-size:0.8125rem;color:#22d3ee;margin-top:0.25rem;">{{ $institution?->type_label ?? 'Institution' }}</div>
                    <div style="font-size:0.8125rem;color:#6b7280;margin-top:0.125rem;">{{ auth()->user()->email }}</div>
                </div>
            </div>
            <div style="display:grid;gap:0.75rem;">
                @foreach([
                    ['Registration No.', $institution?->registration_number ?? 'Not set', '🔖'],
                    ['Location', $institution?->location ?? 'Not set', '📍'],
                    ['County', $institution?->county ?? 'Not set', '🗺️'],
                    ['Phone', $institution?->phone ?? auth()->user()->phone ?? 'Not set', '📱'],
                    ['Email', $institution?->email ?? auth()->user()->email, '📧'],
                    ['Website', $institution?->website ?? 'Not set', '🌐'],
                    ['Status', ucfirst(auth()->user()->status), '✅'],
                ] as [$label, $value, $icon])
                <div style="display:flex;justify-content:space-between;align-items:center;padding:0.5rem 0;border-bottom:1px solid rgba(42,42,74,0.4);">
                    <span style="font-size:0.8125rem;color:#6b7280;">{{ $icon }} {{ $label }}</span>
                    <span style="font-size:0.8125rem;color:#e2e8f0;font-weight:500;max-width:200px;text-align:right;word-break:break-all;">{{ $value }}</span>
                </div>
                @endforeach
            </div>
            @if($institution?->description)
            <div style="margin-top:1rem;padding:0.875rem;background:rgba(255,255,255,0.02);border-radius:10px;border:1px solid var(--sc-dark-border);">
                <div style="font-size:0.75rem;color:#6b7280;margin-bottom:0.375rem;">About</div>
                <div style="font-size:0.875rem;color:#9ca3af;line-height:1.6;">{{ $institution->description }}</div>
            </div>
            @endif
            <a href="{{ route('institution.profile.edit') }}" class="btn btn-outline btn-full" style="margin-top:1.25rem;font-size:0.875rem;">⚙️ Edit Institution Profile</a>
        </div>
    </div>

    {{-- Students Table --}}
    @if($students->count())
    <div class="card" style="grid-column:1/-1;">
        <div class="card-header">
            <span class="card-title">👥 My Students</span>
            <span style="font-size:0.8125rem;color:#6b7280;">{{ $students->count() }} students</span>
        </div>
        <div style="overflow-x:auto;">
            <table class="sc-table">
                <thead><tr><th>Student</th><th>Email</th><th>Phone</th><th>Enrollments</th><th>Certificates</th><th>Status</th></tr></thead>
                <tbody>
                    @foreach($students as $student)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:0.75rem;">
                                <img src="{{ $student->user?->getAvatarUrl() }}" style="width:34px;height:34px;border-radius:9px;object-fit:cover;">
                                <span style="font-weight:500;color:#e2e8f0;">{{ $student->user?->name ?? '—' }}</span>
                            </div>
                        </td>
                        <td style="color:#9ca3af;">{{ $student->user?->email ?? '—' }}</td>
                        <td style="color:#9ca3af;">{{ $student->user?->phone ?? '—' }}</td>
                        <td><span class="badge badge-inst">{{ $student->enrollments?->count() ?? 0 }}</span></td>
                        <td><span class="badge badge-active">{{ $student->enrollments?->filter(fn($e) => $e->certificate)->count() ?? 0 }}</span></td>
                        <td><span class="badge {{ $student->user?->status === 'active' ? 'badge-active' : 'badge-pending' }}">{{ ucfirst($student->user?->status ?? 'active') }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="card" style="grid-column:1/-1;">
        <div class="card-header"><span class="card-title">👥 Student Participation Overview</span></div>
        <div class="coming-soon-card" style="margin:1.5rem;">
            <span class="coming-soon-icon">🎓</span>
            <h3 style="font-size:1.25rem;font-weight:700;color:#e2e8f0;margin-bottom:0.5rem;">No Students Yet</h3>
            <p style="color:#6b7280;font-size:0.9375rem;max-width:420px;margin:0 auto;">Students who register under your institution will appear here with their enrollment and participation data.</p>
        </div>
    </div>
    @endif

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
window.addEventListener('DOMContentLoaded', () => {
    const ctx1 = document.getElementById('studentGrowthChart');
    if(ctx1) {
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: {!! json_encode($studentGrowth['labels']) !!},
                datasets: [{
                    label: 'Registered Students',
                    data: {!! json_encode($studentGrowth['data']) !!},
                    borderColor: '#22d3ee',
                    backgroundColor: 'rgba(34, 211, 238, 0.1)',
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
                plugins: { legend: { labels: { color: '#f1f5f9' } } }
            }
        });
    }

    const ctx2 = document.getElementById('certificateTrendsChart');
    if(ctx2) {
        new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: {!! json_encode($certificateTrends['labels']) !!},
                datasets: [{
                    label: 'Certificates Earned',
                    data: {!! json_encode($certificateTrends['data']) !!},
                    backgroundColor: '#10b981',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' } },
                    x: { grid: { color: 'rgba(255,255,255,0.05)' } }
                },
                plugins: { legend: { labels: { color: '#f1f5f9' } } }
            }
        });
    }
});
</script>
@endpush
