@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')

@section('content')

{{-- Welcome Banner --}}
<div style="background:linear-gradient(135deg,#1a1a35 0%,#16213e 50%,#1a1a35 100%);border:1px solid var(--sc-dark-border);border-radius:20px;padding:2rem;margin-bottom:2rem;position:relative;overflow:hidden;" class="animate-fade-up">
    <div style="position:absolute;top:-60px;right:-60px;width:250px;height:250px;background:radial-gradient(circle,rgba(99,102,241,0.2),transparent 70%);pointer-events:none;"></div>
    <div style="position:absolute;bottom:-40px;right:200px;width:180px;height:180px;background:radial-gradient(circle,rgba(139,92,246,0.15),transparent 70%);pointer-events:none;"></div>
    <div style="position:relative;z-index:1;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
        <div>
            <div style="display:flex;align-items:center;gap:0.625rem;margin-bottom:0.5rem;">
                <div style="width:10px;height:10px;background:#10b981;border-radius:50%;box-shadow:0 0 0 3px rgba(16,185,129,0.2);animation:pulse-glow 2s infinite;"></div>
                <span style="font-size:0.8125rem;color:#34d399;font-weight:500;">System Online</span>
            </div>
            <h1 style="font-size:1.75rem;font-weight:800;color:#f1f5f9;margin-bottom:0.375rem;">
                Good {{ now()->hour < 12 ? 'Morning' : (now()->hour < 17 ? 'Afternoon' : 'Evening') }}, {{ explode(' ', auth()->user()->name)[0] }}! 👋
            </h1>
            <p style="color:#94a3b8;font-size:0.9375rem;">Here's what's happening across SkillConnect today.</p>
        </div>
        <div style="font-size:0.875rem;color:#6b7280;text-align:right;">
            <div style="color:#e2e8f0;font-weight:600;">{{ now()->format('l') }}</div>
            <div>{{ now()->format('F j, Y') }}</div>
        </div>
    </div>
</div>

{{-- Stats Grid --}}
<div class="stats-grid animate-fade-up-delay">
    <div class="stat-card indigo">
        <div class="stat-icon indigo">🏫</div>
        <div class="stat-label">Institutions</div>
        <div class="stat-value">{{ $stats['total_institutions'] }}</div>
        <div class="stat-change">Universities, Colleges & TVETs</div>
    </div>
    <div class="stat-card violet">
        <div class="stat-icon violet">🏢</div>
        <div class="stat-label">Organizations</div>
        <div class="stat-value">{{ $stats['total_organizations'] }}</div>
        <div class="stat-change">Training providers</div>
    </div>
    <div class="stat-card cyan">
        <div class="stat-icon cyan">🎓</div>
        <div class="stat-label">Students</div>
        <div class="stat-value">{{ $stats['total_students'] }}</div>
        <div class="stat-change">Enrolled learners</div>
    </div>
    <div class="stat-card emerald">
        <div class="stat-icon emerald">📚</div>
        <div class="stat-label">Programs</div>
        <div class="stat-value">{{ $stats['total_programs'] }}</div>
        <div class="stat-change">Published training programs</div>
    </div>
    <div class="stat-card amber">
        <div class="stat-icon amber">💰</div>
        <div class="stat-label">Revenue</div>
        <div class="stat-value" style="font-size:1.6rem;">KES {{ number_format($stats['system_revenue'], 0) }}</div>
        <div class="stat-change">Total platform revenue</div>
    </div>
</div>

{{-- Tabs --}}
<div class="animate-fade-up-delay-2">
<div style="display:flex;gap:0.5rem;margin-bottom:1.5rem;border-bottom:1px solid var(--sc-dark-border);padding-bottom:0;" id="adminTabs">
    @foreach(['overview'=>'📊 Overview', 'approvals'=>'⏳ Approvals','users'=>'👥 Users','categories'=>'🏷️ Categories','logs'=>'📋 Logs'] as $tab => $label)
    <button onclick="switchTab('{{ $tab }}')" id="tab_{{ $tab }}" class="admin-tab-btn {{ $loop->first ? 'active' : '' }}" style="padding:0.625rem 1.125rem;background:none;border:none;border-bottom:2px solid {{ $loop->first ? 'var(--sc-primary)' : 'transparent' }};color:{{ $loop->first ? 'var(--sc-primary)' : '#6b7280' }};font-size:0.875rem;font-weight:600;cursor:pointer;font-family:inherit;transition:all 0.15s;white-space:nowrap;">{{ $label }}
        @if($tab === 'approvals' && ($pendingInstitutions->count() + $pendingOrganizations->count()) > 0)
        <span style="display:inline-flex;align-items:center;justify-content:center;width:18px;height:18px;background:var(--sc-danger);color:white;border-radius:50%;font-size:0.65rem;margin-left:4px;">{{ $pendingInstitutions->count() + $pendingOrganizations->count() }}</span>
        @endif
    </button>
    @endforeach
</div>

{{-- Tab: Overview Analytics --}}
<div id="panel_overview">
    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(300px, 1fr));gap:1.5rem;margin-bottom:1.5rem;">
        <div class="card">
            <div class="card-header"><span class="card-title">User Growth</span></div>
            <div style="padding:1rem;"><canvas id="userGrowthChart" height="250"></canvas></div>
        </div>
        <div class="card">
            <div class="card-header"><span class="card-title">Revenue Trends (KES)</span></div>
            <div style="padding:1rem;"><canvas id="revenueChart" height="250"></canvas></div>
        </div>
    </div>
</div>

{{-- Tab: Pending Approvals --}}
<div id="panel_approvals" style="display:none;">

    {{-- Pending Institutions --}}
    <div class="card" style="margin-bottom:1.5rem;">
        <div class="card-header">
            <span class="card-title">🏫 Pending Institutions</span>
            <span style="font-size:0.8125rem;color:#f59e0b;">{{ $pendingInstitutions->count() }} awaiting review</span>
        </div>
        <div style="overflow-x:auto;">
            <table class="sc-table">
                <thead><tr><th>Institution</th><th>Reg. Number</th><th>Location</th><th>Contact</th><th>Submitted</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse($pendingInstitutions as $inst)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:0.75rem;">
                                <div style="width:38px;height:38px;border-radius:10px;background:rgba(6,182,212,0.15);display:flex;align-items:center;justify-content:center;font-size:1.25rem;">🏫</div>
                                <div>
                                    <div style="font-weight:600;color:#e2e8f0;">{{ $inst->name }}</div>
                                    <div style="font-size:0.8rem;color:#6b7280;">{{ $inst->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="color:#9ca3af;font-family:monospace;">{{ $inst->registration_number }}</td>
                        <td style="color:#9ca3af;">{{ $inst->location ?? '—' }}</td>
                        <td style="color:#9ca3af;">{{ $inst->phone ?? $inst->user->phone ?? '—' }}</td>
                        <td style="color:#6b7280;font-size:0.8125rem;">{{ $inst->created_at->diffForHumans() }}</td>
                        <td>
                            <div style="display:flex;gap:0.5rem;">
                                <form method="POST" action="{{ route('admin.approve', ['type'=>'institution','id'=>$inst->id]) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm" style="background:rgba(16,185,129,0.15);color:#34d399;border:1px solid rgba(16,185,129,0.3);" onmouseover="this.style.background='rgba(16,185,129,0.3)'" onmouseout="this.style.background='rgba(16,185,129,0.15)'">✓ Approve</button>
                                </form>
                                <form method="POST" action="{{ route('admin.reject', ['type'=>'institution','id'=>$inst->id]) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm" style="background:rgba(239,68,68,0.15);color:#f87171;border:1px solid rgba(239,68,68,0.3);" onclick="return confirm('Reject this institution?')" onmouseover="this.style.background='rgba(239,68,68,0.3)'" onmouseout="this.style.background='rgba(239,68,68,0.15)'">✗ Reject</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center;color:#4b5563;padding:2rem;">No pending institution registrations. 🎉</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pending Organizations --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">🏢 Pending Organizations</span>
            <span style="font-size:0.8125rem;color:#f59e0b;">{{ $pendingOrganizations->count() }} awaiting review</span>
        </div>
        <div style="overflow-x:auto;">
            <table class="sc-table">
                <thead><tr><th>Organization</th><th>Contact Person</th><th>Phone</th><th>Description</th><th>Submitted</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse($pendingOrganizations as $org)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:0.75rem;">
                                <div style="width:38px;height:38px;border-radius:10px;background:rgba(139,92,246,0.15);display:flex;align-items:center;justify-content:center;font-size:1.25rem;">🏢</div>
                                <div>
                                    <div style="font-weight:600;color:#e2e8f0;">{{ $org->name }}</div>
                                    <div style="font-size:0.8rem;color:#6b7280;">{{ $org->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="color:#9ca3af;">{{ $org->contact_person ?? '—' }}</td>
                        <td style="color:#9ca3af;">{{ $org->phone ?? '—' }}</td>
                        <td style="color:#9ca3af;max-width:250px;"><div style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $org->description ?? '—' }}</div></td>
                        <td style="color:#6b7280;font-size:0.8125rem;">{{ $org->created_at->diffForHumans() }}</td>
                        <td>
                            <div style="display:flex;gap:0.5rem;">
                                <form method="POST" action="{{ route('admin.approve', ['type'=>'organization','id'=>$org->id]) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm" style="background:rgba(16,185,129,0.15);color:#34d399;border:1px solid rgba(16,185,129,0.3);" onmouseover="this.style.background='rgba(16,185,129,0.3)'" onmouseout="this.style.background='rgba(16,185,129,0.15)'">✓ Approve</button>
                                </form>
                                <form method="POST" action="{{ route('admin.reject', ['type'=>'organization','id'=>$org->id]) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm" style="background:rgba(239,68,68,0.15);color:#f87171;border:1px solid rgba(239,68,68,0.3);" onclick="return confirm('Reject this organization?')" onmouseover="this.style.background='rgba(239,68,68,0.3)'" onmouseout="this.style.background='rgba(239,68,68,0.15)'">✗ Reject</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center;color:#4b5563;padding:2rem;">No pending organization registrations. 🎉</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Tab: Users --}}
<div id="panel_users" style="display:none;">
    <div class="card">
        <div class="card-header">
            <span class="card-title">All Registered Users</span>
            <span style="font-size:0.8125rem;color:#6b7280;">{{ $users->count() }} total</span>
        </div>
        <div style="overflow-x:auto;">
            <table class="sc-table">
                <thead><tr><th>User</th><th>Email</th><th>Phone</th><th>Role</th><th>Status</th><th>Joined</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:0.75rem;">
                                <img src="{{ $user->getAvatarUrl() }}" style="width:34px;height:34px;border-radius:9px;object-fit:cover;border:1px solid var(--sc-dark-border);">
                                <span style="font-weight:500;color:#e2e8f0;">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td style="color:#9ca3af;">{{ $user->email }}</td>
                        <td style="color:#9ca3af;">{{ $user->phone ?? '—' }}</td>
                        <td>
                            <span class="badge {{ match($user->role?->name) { 'admin'=>'badge-admin','institution'=>'badge-inst','organization'=>'badge-org','student'=>'badge-student',default=>'badge-pending' } }}">
                                {{ $user->getRoleDisplayName() }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $user->status === 'active' ? 'badge-active' : ($user->status === 'pending' ? 'badge-pending' : 'badge-deact') }}">
                                {{ ucfirst($user->status) }}
                            </span>
                        </td>
                        <td style="color:#6b7280;font-size:0.8125rem;">{{ $user->created_at->diffForHumans() }}</td>
                        <td>
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.toggle-user', $user->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm" style="background:rgba(99,102,241,0.1);color:#818cf8;border:1px solid rgba(99,102,241,0.2);" onmouseover="this.style.background='rgba(99,102,241,0.25)'" onmouseout="this.style.background='rgba(99,102,241,0.1)'">
                                    {{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                            @else
                            <span style="color:#4b5563;font-size:0.8rem;">You</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" style="text-align:center;color:#4b5563;padding:2rem;">No users yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Tab: Categories --}}
<div id="panel_categories" style="display:none;">
    <div style="display:grid;grid-template-columns:1fr 2fr;gap:1.5rem;">
        {{-- Add Category --}}
        <div class="card">
            <div class="card-header"><span class="card-title">Add Category</span></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.categories.store') }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Category Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Digital Skills" value="{{ old('name') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Icon (Emoji)</label>
                        <input type="text" name="icon" class="form-control" placeholder="💻" value="{{ old('icon') }}">
                    </div>
                    <button type="submit" class="btn btn-primary btn-full">+ Add Category</button>
                </form>
            </div>
        </div>
        {{-- List Categories --}}
        <div class="card">
            <div class="card-header">
                <span class="card-title">Skill Categories</span>
                <span style="font-size:0.8125rem;color:#6b7280;">{{ $categories->count() }} categories</span>
            </div>
            <div style="overflow-x:auto;">
                <table class="sc-table">
                    <thead><tr><th>Icon</th><th>Category</th><th>Slug</th><th>Programs</th><th>Actions</th></tr></thead>
                    <tbody>
                        @forelse($categories as $cat)
                        <tr>
                            <td style="font-size:1.5rem;">{{ $cat->icon }}</td>
                            <td style="font-weight:600;color:#e2e8f0;">{{ $cat->name }}</td>
                            <td style="color:#6b7280;font-family:monospace;font-size:0.8rem;">{{ $cat->slug }}</td>
                            <td><span class="badge badge-student">{{ $cat->programs_count }} programs</span></td>
                            <td>
                                <form method="POST" action="{{ route('admin.categories.destroy', $cat->id) }}" style="display:inline;" onsubmit="return confirm('Delete this category?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm" style="background:rgba(239,68,68,0.1);color:#f87171;border:1px solid rgba(239,68,68,0.2);" onmouseover="this.style.background='rgba(239,68,68,0.25)'" onmouseout="this.style.background='rgba(239,68,68,0.1)'">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" style="text-align:center;color:#4b5563;padding:2rem;">No categories yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Tab: Audit Logs --}}
<div id="panel_logs" style="display:none;">
    <div class="card">
        <div class="card-header">
            <span class="card-title">📋 System Audit Logs</span>
            <span style="font-size:0.8125rem;color:#6b7280;">Last {{ $auditLogs->count() }} entries</span>
        </div>
        <div style="overflow-x:auto;">
            <table class="sc-table">
                <thead><tr><th>Time</th><th>User</th><th>Action</th><th>Description</th><th>IP Address</th></tr></thead>
                <tbody>
                    @forelse($auditLogs as $log)
                    <tr>
                        <td style="color:#6b7280;font-size:0.8125rem;white-space:nowrap;">{{ $log->created_at->format('M d, H:i') }}</td>
                        <td>
                            @if($log->user)
                            <div style="font-size:0.875rem;color:#e2e8f0;font-weight:500;">{{ $log->user->name }}</div>
                            <div style="font-size:0.75rem;color:#6b7280;">{{ $log->user->email }}</div>
                            @else
                            <span style="color:#4b5563;">System</span>
                            @endif
                        </td>
                        <td><span class="badge badge-inst" style="font-family:monospace;font-size:0.7rem;">{{ $log->action }}</span></td>
                        <td style="color:#9ca3af;max-width:350px;">{{ $log->description }}</td>
                        <td style="color:#6b7280;font-family:monospace;font-size:0.8rem;">{{ $log->ip_address ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="text-align:center;color:#4b5563;padding:2rem;">No audit logs yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function switchTab(tab) {
    ['overview','approvals','users','categories','logs'].forEach(t => {
        const panel = document.getElementById('panel_' + t);
        const btn   = document.getElementById('tab_' + t);
        if(!panel || !btn) return;
        const isActive = t === tab;
        panel.style.display = isActive ? 'block' : 'none';
        btn.style.borderBottomColor = isActive ? 'var(--sc-primary)' : 'transparent';
        btn.style.color = isActive ? 'var(--sc-primary)' : '#6b7280';
    });
}
window.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab');
    if (tab && ['overview','approvals','users','categories','logs'].includes(tab)) {
        switchTab(tab);
    } else {
        switchTab('overview');
    }

    // Initialize Charts
    const ctx1 = document.getElementById('userGrowthChart');
    if(ctx1) {
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: {!! json_encode($userGrowth['labels']) !!},
                datasets: [{
                    label: 'Total Users',
                    data: {!! json_encode($userGrowth['data']) !!},
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
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

    const ctx2 = document.getElementById('revenueChart');
    if(ctx2) {
        new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: {!! json_encode($revenueTrends['labels']) !!},
                datasets: [{
                    label: 'Revenue (KES)',
                    data: {!! json_encode($revenueTrends['data']) !!},
                    backgroundColor: '#8b5cf6',
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
                plugins: {
                    legend: { labels: { color: '#f1f5f9' } }
                }
            }
        });
    }
});
</script>
@endpush
