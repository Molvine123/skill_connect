@extends('layouts.app')
@section('title', 'My Students — ' . $institution->name)
@section('page-title', 'My Students')

@section('content')

{{-- Header --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;" class="animate-fade-up">
    <div>
        <h1 style="font-size:1.5rem;font-weight:800;color:#f1f5f9;">🏫 My Students</h1>
        <p style="color:#6b7280;font-size:0.9rem;margin-top:0.25rem;">View and manage student records linked to {{ $institution->name }}.</p>
    </div>
    <a href="{{ route('institution.students.add') }}" class="btn btn-primary" style="font-size:0.875rem;">+ Add / Link Student</a>
</div>

{{-- Filters --}}
<div class="card animate-fade-up" style="margin-bottom:1.5rem;">
    <div class="card-body">
        <form method="GET" action="{{ route('institution.students.index') }}" style="display:flex;gap:1rem;align-items:flex-end;">
            <div class="form-group" style="flex:1;margin:0;">
                <label class="form-label" style="font-size:0.8rem;">Search Students</label>
                <input type="text" name="search" class="form-control" placeholder="Search by name, email, registration number…" value="{{ request('search') }}" style="height:40px;">
            </div>
            <button type="submit" class="btn btn-primary" style="height:40px;padding:0 1.25rem;font-size:0.875rem;">🔍 Search</button>
            @if(request()->has('search'))
            <a href="{{ route('institution.students.index') }}" class="btn btn-outline" style="height:40px;padding:0 1rem;font-size:0.875rem;">✕ Clear</a>
            @endif
        </form>
    </div>
</div>

{{-- Stats Row --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.5rem;" class="animate-fade-up-delay">
    @php
        $studentIds = $students->pluck('id');
        $activeCount = \App\Models\Enrollment::whereIn('student_id', $studentIds)->where('status', 'approved')->count();
        $certCount = \App\Models\Certificate::whereIn('student_id', $studentIds)->count();
    @endphp
    @foreach([
        ['Linked Students', $students->total(), '#6366f1', 'rgba(99,102,241,0.1)'],
        ['Active Enrollments', $activeCount, '#10b981', 'rgba(16,185,129,0.1)'],
        ['Certificates Earned', $certCount, '#f59e0b', 'rgba(245,158,11,0.1)'],
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
        <span class="card-title">Enrolled Students</span>
        <span style="font-size:0.8125rem;color:#6b7280;">{{ $students->total() }} total</span>
    </div>
    <div style="overflow-x:auto;">
        <table class="sc-table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Registration Number</th>
                    <th>Programs Enrolled</th>
                    <th>Linked Date</th>
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
                    <td>
                        @php $programNames = $st->enrollments->map(fn($e) => $e->program?->name)->filter()->unique(); @endphp
                        @forelse($programNames as $name)
                        <span class="badge badge-student" style="margin-bottom:2px;display:inline-block;">{{ $name }}</span>
                        @empty
                        <span style="color:#6b7280;font-size:0.85rem;">None</span>
                        @endforelse
                    </td>
                    <td style="color:#6b7280;font-size:0.8125rem;">{{ $st->created_at->format('M d, Y') }}</td>
                    <td>
                        <a href="{{ route('institution.students.show', $st->id) }}" class="btn btn-sm" style="background:rgba(99,102,241,0.1);color:#818cf8;border:1px solid rgba(99,102,241,0.2);font-size:0.75rem;" onmouseover="this.style.background='rgba(99,102,241,0.25)'" onmouseout="this.style.background='rgba(99,102,241,0.1)'">👁 View History</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;color:#4b5563;padding:3rem;">
                        <div style="font-size:2.5rem;margin-bottom:0.75rem;">🎓</div>
                        <div>No students linked to your institution yet.</div>
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
