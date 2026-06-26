@extends('layouts.app')
@section('title', 'Institutions Management')
@section('page-title', 'Institutions Management')

@section('content')

{{-- Header --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;" class="animate-fade-up">
    <div>
        <h1 style="font-size:1.5rem;font-weight:800;color:#f1f5f9;">🏫 All Institutions</h1>
        <p style="color:#6b7280;font-size:0.9rem;margin-top:0.25rem;">Manage Universities, Colleges, and TVETs registered on SkillConnect</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline" style="font-size:0.875rem;">← Admin Dashboard</a>
</div>

{{-- Filters --}}
<div class="card animate-fade-up" style="margin-bottom:1.5rem;">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.institutions.index') }}" style="display:flex;gap:1rem;flex-wrap:wrap;align-items:flex-end;">
            <div class="form-group" style="flex:1;min-width:200px;margin:0;">
                <label class="form-label" style="font-size:0.8rem;">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Name, reg. number, location…" value="{{ request('search') }}" style="height:40px;">
            </div>
            <div class="form-group" style="min-width:160px;margin:0;">
                <label class="form-label" style="font-size:0.8rem;">Type</label>
                <select name="type" class="form-control" style="height:40px;">
                    <option value="">All Types</option>
                    @foreach($typeLabels as $value => $label)
                    <option value="{{ $value }}" {{ request('type') === $value ? 'selected' : '' }}>{{ $label }}</option>
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
            @if(request()->hasAny(['search','type','status']))
            <a href="{{ route('admin.institutions.index') }}" class="btn btn-outline" style="height:40px;padding:0 1rem;font-size:0.875rem;">✕ Clear</a>
            @endif
        </form>
    </div>
</div>

{{-- Stats Row --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.5rem;" class="animate-fade-up-delay">
    @php
        $allInst = \App\Models\Institution::selectRaw("status, count(*) as c")->groupBy('status')->pluck('c','status');
    @endphp
    @foreach([
        ['Total',       $institutions->total(), '#6366f1', 'rgba(99,102,241,0.1)'],
        ['Active',      $allInst['active']     ?? 0, '#10b981', 'rgba(16,185,129,0.1)'],
        ['Pending',     $allInst['pending']    ?? 0, '#f59e0b', 'rgba(245,158,11,0.1)'],
        ['Rejected',    ($allInst['rejected'] ?? 0) + ($allInst['deactivated'] ?? 0), '#ef4444', 'rgba(239,68,68,0.1)'],
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
        <span class="card-title">Registered Institutions</span>
        <span style="font-size:0.8125rem;color:#6b7280;">{{ $institutions->total() }} total</span>
    </div>
    <div style="overflow-x:auto;">
        <table class="sc-table">
            <thead>
                <tr>
                    <th>Institution</th>
                    <th>Type</th>
                    <th>Reg. Number</th>
                    <th>Location</th>
                    <th>Students</th>
                    <th>Status</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($institutions as $inst)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:0.75rem;">
                            @if($inst->logo_path)
                            <img src="{{ asset('storage/'.$inst->logo_path) }}" style="width:40px;height:40px;border-radius:10px;object-fit:cover;border:1px solid var(--sc-dark-border);">
                            @else
                            <div style="width:40px;height:40px;border-radius:10px;background:rgba(6,182,212,0.1);display:flex;align-items:center;justify-content:center;font-size:1.25rem;flex-shrink:0;">{{ $inst->type_icon }}</div>
                            @endif
                            <div>
                                <div style="font-weight:600;color:#e2e8f0;">{{ $inst->name }}</div>
                                <div style="font-size:0.775rem;color:#6b7280;">{{ $inst->user?->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-inst">{{ $inst->type_label }}</span>
                    </td>
                    <td style="color:#9ca3af;font-family:monospace;font-size:0.8rem;">{{ $inst->registration_number }}</td>
                    <td style="color:#9ca3af;">
                        {{ $inst->location ?? '—' }}
                        @if($inst->county) <span style="color:#6b7280;">({{ $inst->county }})</span> @endif
                    </td>
                    <td>
                        <span class="badge badge-student">{{ $inst->students()->count() }}</span>
                    </td>
                    <td>
                        <span class="badge {{ match($inst->status) {
                            'active'      => 'badge-active',
                            'pending'     => 'badge-pending',
                            'rejected'    => 'badge-deact',
                            'deactivated' => 'badge-deact',
                            default       => 'badge-pending',
                        } }}">{{ ucfirst($inst->status) }}</span>
                    </td>
                    <td style="color:#6b7280;font-size:0.8125rem;">{{ $inst->created_at->format('M d, Y') }}</td>
                    <td>
                        <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
                            <a href="{{ route('admin.institutions.show', $inst->id) }}" class="btn btn-sm" style="background:rgba(99,102,241,0.1);color:#818cf8;border:1px solid rgba(99,102,241,0.2);font-size:0.75rem;" onmouseover="this.style.background='rgba(99,102,241,0.25)'" onmouseout="this.style.background='rgba(99,102,241,0.1)'">👁 View</a>
                            @if($inst->status === 'pending')
                            <form method="POST" action="{{ route('admin.approve', ['type'=>'institution','id'=>$inst->id]) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm" style="background:rgba(16,185,129,0.1);color:#34d399;border:1px solid rgba(16,185,129,0.2);font-size:0.75rem;" onmouseover="this.style.background='rgba(16,185,129,0.25)'" onmouseout="this.style.background='rgba(16,185,129,0.1)'">✓ Approve</button>
                            </form>
                            <form method="POST" action="{{ route('admin.reject', ['type'=>'institution','id'=>$inst->id]) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm" style="background:rgba(239,68,68,0.1);color:#f87171;border:1px solid rgba(239,68,68,0.2);font-size:0.75rem;" onclick="return confirm('Reject this institution?')" onmouseover="this.style.background='rgba(239,68,68,0.25)'" onmouseout="this.style.background='rgba(239,68,68,0.1)'">✗ Reject</button>
                            </form>
                            @elseif($inst->status === 'active')
                            <form method="POST" action="{{ route('admin.toggle-user', $inst->user_id) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm" style="background:rgba(245,158,11,0.1);color:#fbbf24;border:1px solid rgba(245,158,11,0.2);font-size:0.75rem;" onclick="return confirm('Deactivate this institution?')" onmouseover="this.style.background='rgba(245,158,11,0.25)'" onmouseout="this.style.background='rgba(245,158,11,0.1)'">⊘ Deactivate</button>
                            </form>
                            @else
                            <form method="POST" action="{{ route('admin.toggle-user', $inst->user_id) }}">
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
                        <div style="font-size:2.5rem;margin-bottom:0.75rem;">🏫</div>
                        <div>No institutions found matching your filters.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($institutions->hasPages())
    <div style="padding:1rem 1.5rem;border-top:1px solid var(--sc-dark-border);">
        {{ $institutions->links() }}
    </div>
    @endif
</div>

@endsection
