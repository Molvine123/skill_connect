@extends('layouts.app')
@section('title', 'Organizations Management')
@section('page-title', 'Organizations Management')

@section('content')

{{-- Header --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;" class="animate-fade-up">
    <div>
        <h1 style="font-size:1.5rem;font-weight:800;color:#f1f5f9;">🏢 All Organizations</h1>
        <p style="color:#6b7280;font-size:0.9rem;margin-top:0.25rem;">Manage NGOs, Private Companies, Ajira Programs, and Professional Trainers</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline" style="font-size:0.875rem;">← Admin Dashboard</a>
</div>

{{-- Filters --}}
<div class="card animate-fade-up" style="margin-bottom:1.5rem;">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.organizations.index') }}" style="display:flex;gap:1rem;flex-wrap:wrap;align-items:flex-end;">
            <div class="form-group" style="flex:1;min-width:200px;margin:0;">
                <label class="form-label" style="font-size:0.8rem;">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Name, contact person, county…" value="{{ request('search') }}" style="height:40px;">
            </div>
            <div class="form-group" style="min-width:180px;margin:0;">
                <label class="form-label" style="font-size:0.8rem;">Organization Type</label>
                <select name="org_type" class="form-control" style="height:40px;">
                    <option value="">All Types</option>
                    @foreach($typeLabels as $value => $label)
                    <option value="{{ $value }}" {{ request('org_type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="min-width:140px;margin:0;">
                <label class="form-label" style="font-size:0.8rem;">Status</label>
                <select name="status" class="form-control" style="height:40px;">
                    <option value="">All Status</option>
                    <option value="pending"     {{ request('status') === 'pending'     ? 'selected' : '' }}>Pending</option>
                    <option value="active"      {{ request('status') === 'active'      ? 'selected' : '' }}>Active</option>
                    <option value="rejected"    {{ request('status') === 'rejected'    ? 'selected' : '' }}>Rejected</option>
                    <option value="deactivated" {{ request('status') === 'deactivated' ? 'selected' : '' }}>Deactivated</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="height:40px;padding:0 1.25rem;font-size:0.875rem;">🔍 Filter</button>
            @if(request()->hasAny(['search','org_type','status']))
            <a href="{{ route('admin.organizations.index') }}" class="btn btn-outline" style="height:40px;padding:0 1rem;font-size:0.875rem;">✕ Clear</a>
            @endif
        </form>
    </div>
</div>

{{-- Stats Row --}}
<div style="display:grid;grid-template-columns:repeat(5,1fr);gap:1rem;margin-bottom:1.5rem;" class="animate-fade-up-delay">
    @php
        $allOrg = \App\Models\Organization::selectRaw("status, count(*) as c")->groupBy('status')->pluck('c','status');
        $byType = \App\Models\Organization::selectRaw("org_type, count(*) as c")->groupBy('org_type')->pluck('c','org_type');
    @endphp
    @foreach([
        ['Total',   $organizations->total(),          '#6366f1','rgba(99,102,241,0.1)'],
        ['Active',  $allOrg['active']    ?? 0,        '#10b981','rgba(16,185,129,0.1)'],
        ['Pending', $allOrg['pending']   ?? 0,        '#f59e0b','rgba(245,158,11,0.1)'],
        ['NGOs',    $byType['ngo']       ?? 0,        '#06b6d4','rgba(6,182,212,0.1)'],
        ['Ajira',   $byType['ajira']     ?? 0,        '#8b5cf6','rgba(139,92,246,0.1)'],
    ] as [$label, $count, $color, $bg])
    <div style="padding:1rem 1.25rem;background:{{ $bg }};border:1px solid {{ $color }}22;border-radius:14px;text-align:center;">
        <div style="font-size:1.75rem;font-weight:800;color:{{ $color }};">{{ $count }}</div>
        <div style="font-size:0.8rem;color:#9ca3af;margin-top:0.25rem;">{{ $label }}</div>
    </div>
    @endforeach
</div>

{{-- Table --}}
<div class="card animate-fade-up-delay-2">
    <div class="card-header">
        <span class="card-title">Registered Organizations</span>
        <span style="font-size:0.8125rem;color:#6b7280;">{{ $organizations->total() }} total</span>
    </div>
    <div style="overflow-x:auto;">
        <table class="sc-table">
            <thead>
                <tr>
                    <th>Organization</th>
                    <th>Type</th>
                    <th>Contact Person</th>
                    <th>Programs</th>
                    <th>County</th>
                    <th>Status</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($organizations as $org)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:0.75rem;">
                            @if($org->logo_path)
                            <img src="{{ asset('storage/'.$org->logo_path) }}" style="width:40px;height:40px;border-radius:10px;object-fit:cover;border:1px solid var(--sc-dark-border);">
                            @else
                            <div style="width:40px;height:40px;border-radius:10px;background:rgba(139,92,246,0.1);display:flex;align-items:center;justify-content:center;font-size:1.25rem;flex-shrink:0;">{{ $org->type_icon }}</div>
                            @endif
                            <div>
                                <div style="font-weight:600;color:#e2e8f0;">{{ $org->name }}</div>
                                <div style="font-size:0.775rem;color:#6b7280;">{{ $org->user?->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-org">{{ $org->type_label }}</span>
                    </td>
                    <td style="color:#9ca3af;">{{ $org->contact_person ?? '—' }}</td>
                    <td>
                        <span class="badge badge-student">{{ $org->programs_count }} programs</span>
                    </td>
                    <td style="color:#9ca3af;">{{ $org->county ?? '—' }}</td>
                    <td>
                        <span class="badge {{ match($org->status) {
                            'active'      => 'badge-active',
                            'pending'     => 'badge-pending',
                            'rejected'    => 'badge-deact',
                            'deactivated' => 'badge-deact',
                            default       => 'badge-pending',
                        } }}">{{ ucfirst($org->status) }}</span>
                    </td>
                    <td style="color:#6b7280;font-size:0.8125rem;">{{ $org->created_at->format('M d, Y') }}</td>
                    <td>
                        <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
                            <a href="{{ route('admin.organizations.show', $org->id) }}" class="btn btn-sm" style="background:rgba(139,92,246,0.1);color:#a78bfa;border:1px solid rgba(139,92,246,0.2);font-size:0.75rem;" onmouseover="this.style.background='rgba(139,92,246,0.25)'" onmouseout="this.style.background='rgba(139,92,246,0.1)'">👁 View</a>
                            @if($org->status === 'pending')
                            <form method="POST" action="{{ route('admin.approve', ['type'=>'organization','id'=>$org->id]) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm" style="background:rgba(16,185,129,0.1);color:#34d399;border:1px solid rgba(16,185,129,0.2);font-size:0.75rem;" onmouseover="this.style.background='rgba(16,185,129,0.25)'" onmouseout="this.style.background='rgba(16,185,129,0.1)'">✓ Approve</button>
                            </form>
                            <form method="POST" action="{{ route('admin.reject', ['type'=>'organization','id'=>$org->id]) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm" style="background:rgba(239,68,68,0.1);color:#f87171;border:1px solid rgba(239,68,68,0.2);font-size:0.75rem;" onclick="return confirm('Reject this organization?')" onmouseover="this.style.background='rgba(239,68,68,0.25)'" onmouseout="this.style.background='rgba(239,68,68,0.1)'">✗ Reject</button>
                            </form>
                            @elseif($org->status === 'active')
                            <form method="POST" action="{{ route('admin.toggle-user', $org->user_id) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm" style="background:rgba(245,158,11,0.1);color:#fbbf24;border:1px solid rgba(245,158,11,0.2);font-size:0.75rem;" onclick="return confirm('Deactivate this organization?')" onmouseover="this.style.background='rgba(245,158,11,0.25)'" onmouseout="this.style.background='rgba(245,158,11,0.1)'">⊘ Deactivate</button>
                            </form>
                            @else
                            <form method="POST" action="{{ route('admin.toggle-user', $org->user_id) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm" style="background:rgba(16,185,129,0.1);color:#34d399;border:1px solid rgba(16,185,129,0.2);font-size:0.75rem;" onmouseover="this.style.background='rgba(16,185,129,0.25)'" onmouseout="this.style.background='rgba(16,185,129,0.1)'">↩ Reactivate</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;color:#4b5563;padding:3rem;">
                        <div style="font-size:2.5rem;margin-bottom:0.75rem;">🏢</div>
                        <div>No organizations found matching your filters.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($organizations->hasPages())
    <div style="padding:1rem 1.5rem;border-top:1px solid var(--sc-dark-border);">
        {{ $organizations->links() }}
    </div>
    @endif
</div>

@endsection
