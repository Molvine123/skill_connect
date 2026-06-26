@extends('layouts.app')
@section('title', 'Students Management')
@section('page-title', 'Students Management')

@section('content')

{{-- Header --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;" class="animate-fade-up">
    <div>
        <h1 style="font-size:1.5rem;font-weight:800;color:#f1f5f9;">🎓 All Students</h1>
        <p style="color:#6b7280;font-size:0.9rem;margin-top:0.25rem;">Monitor and manage student profiles and institution assignments across the platform.</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline" style="font-size:0.875rem;">← Admin Dashboard</a>
</div>

{{-- Filters --}}
<div class="card animate-fade-up" style="margin-bottom:1.5rem;">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.students.index') }}" style="display:flex;gap:1rem;flex-wrap:wrap;align-items:flex-end;">
            <div class="form-group" style="flex:1;min-width:200px;margin:0;">
                <label class="form-label" style="font-size:0.8rem;">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Name, email, registration number…" value="{{ request('search') }}" style="height:40px;">
            </div>
            <div class="form-group" style="min-width:220px;margin:0;">
                <label class="form-label" style="font-size:0.8rem;">Institution</label>
                <select name="institution_id" class="form-control" style="height:40px;">
                    <option value="">All Institutions</option>
                    @foreach($institutions as $inst)
                    <option value="{{ $inst->id }}" {{ request('institution_id') == $inst->id ? 'selected' : '' }}>{{ $inst->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="height:40px;padding:0 1.25rem;font-size:0.875rem;">🔍 Filter</button>
            @if(request()->hasAny(['search','institution_id']))
            <a href="{{ route('admin.students.index') }}" class="btn btn-outline" style="height:40px;padding:0 1rem;font-size:0.875rem;">✕ Clear</a>
            @endif
        </form>
    </div>
</div>

{{-- Stats Row --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.5rem;" class="animate-fade-up-delay">
    @php
        $totalStudentsCount = \App\Models\Student::count();
        $unassignedCount = \App\Models\Student::whereNull('institution_id')->count();
        $enrolledCount = \App\Models\Student::whereHas('enrollments')->count();
    @endphp
    @foreach([
        ['Total Students', $totalStudentsCount, '#6366f1', 'rgba(99,102,241,0.1)'],
        ['Enrolled Learners', $enrolledCount, '#10b981', 'rgba(16,185,129,0.1)'],
        ['Unassigned Students', $unassignedCount, '#f59e0b', 'rgba(245,158,11,0.1)'],
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
        <span class="card-title">Registered Students</span>
        <span style="font-size:0.8125rem;color:#6b7280;">{{ $students->total() }} total</span>
    </div>
    <div style="overflow-x:auto;">
        <table class="sc-table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Reg. Number</th>
                    <th>Phone</th>
                    <th>Institution</th>
                    <th>Training Status</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $st)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:0.75rem;">
                            <img src="{{ $st->user?->getAvatarUrl() }}" style="width:40px;height:40px;border-radius:10px;object-fit:cover;border:1px solid var(--sc-dark-border);">
                            <div>
                                <div style="font-weight:600;color:#e2e8f0;">{{ $st->user?->name }}</div>
                                <div style="font-size:0.775rem;color:#6b7280;">{{ $st->user?->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="color:#9ca3af;font-family:monospace;font-size:0.85rem;">{{ $st->registration_number ?? '—' }}</td>
                    <td style="color:#9ca3af;">{{ $st->phone ?? $st->user?->phone ?? '—' }}</td>
                    <td>
                        @if($st->institution)
                        <span class="badge badge-inst">🏫 {{ $st->institution->name }}</span>
                        @else
                        <span class="badge badge-pending">⚠️ Unassigned</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $enr = $st->enrollments()->count();
                            $comp = $st->enrollments()->where('status', 'completed')->count();
                        @endphp
                        @if($enr > 0)
                        <span class="badge badge-student">{{ $enr }} Enrolled ({{ $comp }} Done)</span>
                        @else
                        <span style="color:#4b5563;font-size:0.85rem;">No courses</span>
                        @endif
                    </td>
                    <td style="color:#6b7280;font-size:0.8125rem;">{{ $st->created_at->format('M d, Y') }}</td>
                    <td>
                        <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
                            <a href="{{ route('admin.students.show', $st->id) }}" class="btn btn-sm" style="background:rgba(99,102,241,0.1);color:#818cf8;border:1px solid rgba(99,102,241,0.2);font-size:0.75rem;" onmouseover="this.style.background='rgba(99,102,241,0.25)'" onmouseout="this.style.background='rgba(99,102,241,0.1)'">👁 View Details</a>
                            <a href="{{ route('admin.students.edit', $st->id) }}" class="btn btn-sm" style="background:rgba(255,255,255,0.05);color:#cbd5e1;border:1px solid var(--sc-dark-border);font-size:0.75rem;" onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">✏️ Edit</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;color:#4b5563;padding:3rem;">
                        <div style="font-size:2.5rem;margin-bottom:0.75rem;">🎓</div>
                        <div>No students found matching your filters.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($students->hasPages())
    <div style="padding:1rem 1.5rem;border-top:1px solid var(--sc-dark-border);">
        {{ $students->links() }}
    </div>
    @endif
</div>

@endsection
