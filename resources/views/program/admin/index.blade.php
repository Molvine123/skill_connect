@extends('layouts.app')
@section('title', 'All Programs')
@section('page-title', 'Program Overview')

@section('content')
<div class="animate-fade-up">

{{-- Banner --}}
<div style="background:linear-gradient(135deg,#0a1628 0%,#0d1f38 60%,#0a1628 100%);border:1px solid rgba(99,102,241,0.2);border-radius:20px;padding:2rem;margin-bottom:2rem;position:relative;overflow:hidden;">
    <div style="position:absolute;top:-50px;right:-30px;width:250px;height:250px;background:radial-gradient(circle,rgba(99,102,241,0.15),transparent 70%);pointer-events:none;"></div>
    <div style="position:relative;z-index:1;display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
        <div>
            <h1 style="font-size:1.75rem;font-weight:800;color:#f1f5f9;margin-bottom:0.375rem;">Platform Programs</h1>
            <p style="color:#94a3b8;font-size:0.9rem;">Overview of all training programs across the platform.</p>
        </div>
    </div>
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-top:1.5rem;">
        @foreach([
            ['🟢','Published', $totalPublished, '#34d399'],
            ['📝','Draft', $totalDraft, '#fbbf24'],
            ['🔒','Closed', $totalClosed, '#6b7280'],
            ['🎓','Total Enrollments', $totalEnrolled, '#22d3ee'],
        ] as [$icon,$label,$val,$color])
        <div style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.06);border-radius:12px;padding:1rem;text-align:center;">
            <div style="font-size:1.5rem;margin-bottom:0.25rem;">{{ $icon }}</div>
            <div style="font-size:1.5rem;font-weight:800;color:{{ $color }};">{{ $val }}</div>
            <div style="font-size:0.75rem;color:#6b7280;">{{ $label }}</div>
        </div>
        @endforeach
    </div>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('admin.programs.index') }}" style="margin-bottom:1.5rem;" class="animate-fade-up-delay">
    <div style="display:flex;gap:0.75rem;flex-wrap:wrap;align-items:center;">
        <div style="flex:1;min-width:200px;position:relative;">
            <svg style="position:absolute;left:0.875rem;top:50%;transform:translateY(-50%);color:#6b7280;" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search program or organization..." style="background:rgba(255,255,255,0.04);border:1px solid rgba(42,42,74,0.6);border-radius:10px;padding:0.65rem 1rem 0.65rem 2.5rem;color:#f1f5f9;width:100%;font-size:0.875rem;">
        </div>
        <select name="status" style="background:rgba(255,255,255,0.04);border:1px solid rgba(42,42,74,0.6);border-radius:10px;padding:0.65rem 1rem;color:#f1f5f9;font-size:0.875rem;min-width:140px;">
            <option value="">All Statuses</option>
            <option value="draft" {{ request('status')=='draft'?'selected':'' }}>Draft</option>
            <option value="published" {{ request('status')=='published'?'selected':'' }}>Published</option>
            <option value="closed" {{ request('status')=='closed'?'selected':'' }}>Closed</option>
        </select>
        <select name="category" style="background:rgba(255,255,255,0.04);border:1px solid rgba(42,42,74,0.6);border-radius:10px;padding:0.65rem 1rem;color:#f1f5f9;font-size:0.875rem;min-width:160px;">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ request('category')==$cat->id?'selected':'' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-primary" style="padding:0.65rem 1.25rem;">Filter</button>
        @if(request()->hasAny(['search','status','category']))
        <a href="{{ route('admin.programs.index') }}" class="btn btn-outline" style="padding:0.65rem 1rem;">Clear</a>
        @endif
    </div>
</form>

<div class="card animate-fade-up-delay-2">
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;min-width:900px;">
            <thead>
                <tr style="border-bottom:1px solid rgba(42,42,74,0.5);background:rgba(255,255,255,0.02);">
                    <th style="padding:1rem 1.25rem;text-align:left;font-size:0.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;">Program & Org</th>
                    <th style="padding:1rem 1.25rem;text-align:left;font-size:0.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;">Category</th>
                    <th style="padding:1rem 1.25rem;text-align:left;font-size:0.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;">Mode & Cost</th>
                    <th style="padding:1rem 1.25rem;text-align:left;font-size:0.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;">Status</th>
                    <th style="padding:1rem 1.25rem;text-align:center;font-size:0.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;">Metrics</th>
                    <th style="padding:1rem 1.25rem;text-align:right;font-size:0.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($programs as $program)
                @php
                    $statusCfg = [
                        'published' => ['#34d399','rgba(16,185,129,0.15)','Published'],
                        'draft'     => ['#fbbf24','rgba(245,158,11,0.15)','Draft'],
                        'closed'    => ['#6b7280','rgba(107,114,128,0.15)','Closed'],
                    ];
                    [$sc,$sbg,$sl] = $statusCfg[$program->status] ?? ['#6b7280','rgba(107,114,128,0.15)',ucfirst($program->status)];
                @endphp
                <tr style="border-bottom:1px solid rgba(42,42,74,0.4);transition:background .2s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                    <td style="padding:1rem 1.25rem;">
                        <div style="font-size:0.9rem;font-weight:600;color:#f1f5f9;">{{ Str::limit($program->name, 40) }}</div>
                        <div style="font-size:0.75rem;color:#818cf8;margin-top:0.125rem;">🏢 {{ Str::limit($program->organization->name, 30) }}</div>
                    </td>
                    <td style="padding:1rem 1.25rem;">
                        <div style="font-size:0.8rem;color:#e2e8f0;">{{ $program->category?->icon ?? '📚' }} {{ $program->category?->name ?? 'None' }}</div>
                    </td>
                    <td style="padding:1rem 1.25rem;">
                        <div style="font-size:0.8rem;color:#e2e8f0;">{{ ucfirst(str_replace('_', '-', $program->mode)) }}</div>
                        <div style="font-size:0.75rem;color:{{ $program->cost > 0 ? '#fbbf24' : '#34d399' }};">{{ $program->cost > 0 ? 'KES '.number_format($program->cost) : 'Free' }}</div>
                    </td>
                    <td style="padding:1rem 1.25rem;">
                        <span style="font-size:0.7rem;font-weight:600;color:{{ $sc }};background:{{ $sbg }};padding:0.25rem 0.625rem;border-radius:20px;">{{ $sl }}</span>
                    </td>
                    <td style="padding:1rem 1.25rem;text-align:center;">
                        <div style="display:flex;gap:0.75rem;justify-content:center;">
                            <span title="Enrollments" style="font-size:0.75rem;color:#94a3b8;"><span style="color:#e2e8f0;font-weight:600;">{{ $program->enrollments_count }}</span> E</span>
                            <span title="Sessions" style="font-size:0.75rem;color:#94a3b8;"><span style="color:#e2e8f0;font-weight:600;">{{ $program->sessions_count }}</span> S</span>
                        </div>
                    </td>
                    <td style="padding:1rem 1.25rem;text-align:right;">
                        <a href="{{ route('admin.programs.show', $program->id) }}" class="btn btn-outline" style="padding:0.35rem 0.75rem;font-size:0.75rem;">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding:3rem;text-align:center;">
                        <div style="font-size:3rem;margin-bottom:1rem;">📋</div>
                        <div style="font-size:1.1rem;font-weight:600;color:#f1f5f9;">No programs found</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
@if($programs->hasPages())
<div style="margin-top:1.5rem;display:flex;justify-content:center;">
    {{ $programs->links() }}
</div>
@endif

</div>
@endsection
