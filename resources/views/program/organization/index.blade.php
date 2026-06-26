@extends('layouts.app')
@section('title', 'My Programs')
@section('page-title', 'My Programs')

@section('content')

@php
    $publishedCount = $programs->getCollection()->where('status','published')->count();
    $totalEnrolled  = $programs->getCollection()->sum(fn($p) => $p->enrollments->count());
@endphp

{{-- Banner --}}
<div style="background:linear-gradient(135deg,#0a1628 0%,#0d1f38 60%,#0a1628 100%);border:1px solid rgba(99,102,241,0.2);border-radius:20px;padding:2rem;margin-bottom:2rem;position:relative;overflow:hidden;" class="animate-fade-up">
    <div style="position:absolute;top:-50px;right:-30px;width:250px;height:250px;background:radial-gradient(circle,rgba(99,102,241,0.15),transparent 70%);pointer-events:none;"></div>
    <div style="position:relative;z-index:1;display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
        <div>
            <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.5rem;">
                <div style="width:8px;height:8px;background:#818cf8;border-radius:50%;box-shadow:0 0 0 3px rgba(99,102,241,0.25);"></div>
                <span style="font-size:0.8rem;color:#818cf8;font-weight:500;">{{ $organization->name }}</span>
            </div>
            <h1 style="font-size:1.75rem;font-weight:800;color:#f1f5f9;margin-bottom:0.375rem;">Training Programs</h1>
            <p style="color:#94a3b8;font-size:0.9rem;">Create and manage your skill training programs.</p>
        </div>
        <a href="{{ route('organization.programs.create') }}" class="btn btn-primary" style="background:linear-gradient(135deg,#6366f1,#4f46e5);box-shadow:0 4px 15px rgba(99,102,241,0.35);flex-shrink:0;">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Create Program
        </a>
    </div>
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-top:1.5rem;">
        @foreach([
            ['📋','Total Programs', $programs->total(), '#818cf8'],
            ['🟢','Published', $programs->getCollection()->where('status','published')->count(), '#34d399'],
            ['🎓','Total Enrolled', $programs->getCollection()->sum(fn($p)=>$p->enrollments->count()), '#22d3ee'],
            ['📝','Draft', $programs->getCollection()->where('status','draft')->count(), '#fbbf24'],
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
<form method="GET" action="{{ route('organization.programs.index') }}" style="margin-bottom:1.5rem;" class="animate-fade-up-delay">
    <div style="display:flex;gap:0.75rem;flex-wrap:wrap;align-items:center;">
        <div style="flex:1;min-width:200px;position:relative;">
            <svg style="position:absolute;left:0.875rem;top:50%;transform:translateY(-50%);color:#6b7280;" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search programs..." style="background:rgba(255,255,255,0.04);border:1px solid rgba(42,42,74,0.6);border-radius:10px;padding:0.65rem 1rem 0.65rem 2.5rem;color:#f1f5f9;width:100%;font-size:0.875rem;">
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
        <a href="{{ route('organization.programs.index') }}" class="btn btn-outline" style="padding:0.65rem 1rem;">Clear</a>
        @endif
    </div>
</form>

{{-- Programs Grid --}}
@forelse($programs as $program)
@php
    $statusCfg = [
        'published' => ['#34d399','rgba(16,185,129,0.15)','Published'],
        'draft'     => ['#fbbf24','rgba(245,158,11,0.15)','Draft'],
        'closed'    => ['#6b7280','rgba(107,114,128,0.15)','Closed'],
    ];
    [$sc,$sbg,$sl] = $statusCfg[$program->status] ?? ['#6b7280','rgba(107,114,128,0.15)',ucfirst($program->status)];
    $modeCfg = ['online'=>['🌐','Online'],'in_person'=>['🏢','In-Person'],'hybrid'=>['🔀','Hybrid']];
    [$mi,$ml] = $modeCfg[$program->mode] ?? ['📍',ucfirst($program->mode)];
    $enrolled  = $program->enrollments->count();
    $sessions  = $program->sessions->count();
    $pct = $program->capacity > 0 ? min(100, round($enrolled/$program->capacity*100)) : 0;
@endphp
@if($loop->first)<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:1.25rem;" class="animate-fade-up-delay-2">@endif
<div style="background:#0d1117;border:1px solid rgba(42,42,74,0.5);border-radius:16px;overflow:hidden;display:flex;flex-direction:column;transition:border-color .2s,transform .2s;" onmouseover="this.style.borderColor='rgba(99,102,241,0.4)';this.style.transform='translateY(-2px)'" onmouseout="this.style.borderColor='rgba(42,42,74,0.5)';this.style.transform='translateY(0)'">
    {{-- Card Top --}}
    <div style="padding:1.25rem;border-bottom:1px solid rgba(42,42,74,0.4);display:flex;justify-content:space-between;align-items:flex-start;gap:0.75rem;">
        <div style="flex:1;min-width:0;">
            <div style="font-size:0.75rem;color:#6b7280;margin-bottom:0.375rem;">
                {{ $program->category?->icon ?? '📚' }} {{ $program->category?->name ?? 'Uncategorized' }}
            </div>
            <div style="font-size:1rem;font-weight:700;color:#f1f5f9;line-height:1.3;margin-bottom:0.5rem;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $program->name }}</div>
            <div style="font-size:0.8rem;color:#6b7280;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $program->description }}</div>
        </div>
        <span style="flex-shrink:0;font-size:0.7rem;font-weight:600;color:{{ $sc }};background:{{ $sbg }};padding:0.25rem 0.625rem;border-radius:20px;white-space:nowrap;">{{ $sl }}</span>
    </div>
    {{-- Stats Row --}}
    <div style="padding:0.875rem 1.25rem;display:grid;grid-template-columns:repeat(3,1fr);gap:0.5rem;border-bottom:1px solid rgba(42,42,74,0.4);">
        @foreach([['🎓',$enrolled,'Enrolled'],['📅',$sessions,'Sessions'],['👥',$program->capacity,'Capacity']] as [$ico,$val,$lbl])
        <div style="text-align:center;">
            <div style="font-size:0.95rem;font-weight:700;color:#f1f5f9;">{{ $val }}</div>
            <div style="font-size:0.7rem;color:#6b7280;">{{ $ico }} {{ $lbl }}</div>
        </div>
        @endforeach
    </div>
    {{-- Meta --}}
    <div style="padding:0.875rem 1.25rem;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid rgba(42,42,74,0.4);">
        <span style="font-size:0.75rem;color:#94a3b8;">{{ $mi }} {{ $ml }}</span>
        <span style="font-size:0.875rem;font-weight:700;color:{{ $program->cost > 0 ? '#fbbf24' : '#34d399' }};">
            {{ $program->cost > 0 ? 'KES '.number_format($program->cost,0) : 'Free' }}
        </span>
    </div>
    {{-- Capacity Bar --}}
    <div style="padding:0.5rem 1.25rem;border-bottom:1px solid rgba(42,42,74,0.4);">
        <div style="display:flex;justify-content:space-between;margin-bottom:0.25rem;">
            <span style="font-size:0.7rem;color:#6b7280;">Enrollment capacity</span>
            <span style="font-size:0.7rem;color:#94a3b8;">{{ $pct }}%</span>
        </div>
        <div style="background:rgba(255,255,255,0.06);border-radius:4px;height:4px;overflow:hidden;">
            <div style="height:100%;width:{{ $pct }}%;background:{{ $pct >= 90 ? '#ef4444' : ($pct >= 70 ? '#fbbf24' : '#6366f1') }};border-radius:4px;transition:width 0.5s;"></div>
        </div>
    </div>
    {{-- Actions --}}
    <div style="padding:0.875rem 1.25rem;display:grid;grid-template-columns:repeat(4,1fr);gap:0.5rem;">
        <a href="{{ route('organization.programs.show', $program->id) }}" style="text-align:center;font-size:0.7rem;font-weight:600;color:#818cf8;background:rgba(99,102,241,0.08);border:1px solid rgba(99,102,241,0.15);border-radius:8px;padding:0.45rem 0.25rem;text-decoration:none;transition:background .2s;" onmouseover="this.style.background='rgba(99,102,241,0.18)'" onmouseout="this.style.background='rgba(99,102,241,0.08)'">👁 View</a>
        <a href="{{ route('organization.programs.edit', $program->id) }}" style="text-align:center;font-size:0.7rem;font-weight:600;color:#fbbf24;background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.15);border-radius:8px;padding:0.45rem 0.25rem;text-decoration:none;transition:background .2s;" onmouseover="this.style.background='rgba(245,158,11,0.18)'" onmouseout="this.style.background='rgba(245,158,11,0.08)'">✏️ Edit</a>
        <a href="{{ route('organization.programs.sessions', $program->id) }}" style="text-align:center;font-size:0.7rem;font-weight:600;color:#22d3ee;background:rgba(6,182,212,0.08);border:1px solid rgba(6,182,212,0.15);border-radius:8px;padding:0.45rem 0.25rem;text-decoration:none;transition:background .2s;" onmouseover="this.style.background='rgba(6,182,212,0.18)'" onmouseout="this.style.background='rgba(6,182,212,0.08)'">📅 Sessions</a>
        <a href="{{ route('organization.programs.enrollments', $program->id) }}" style="text-align:center;font-size:0.7rem;font-weight:600;color:#34d399;background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.15);border-radius:8px;padding:0.45rem 0.25rem;text-decoration:none;transition:background .2s;" onmouseover="this.style.background='rgba(16,185,129,0.18)'" onmouseout="this.style.background='rgba(16,185,129,0.08)'">🎓 Enrols</a>
    </div>
</div>
@if($loop->last)</div>@endif
@empty
<div style="text-align:center;padding:4rem 2rem;background:#0d1117;border:1px solid rgba(42,42,74,0.5);border-radius:16px;" class="animate-fade-up-delay">
    <div style="font-size:4rem;margin-bottom:1rem;">📋</div>
    <div style="font-size:1.25rem;font-weight:700;color:#f1f5f9;margin-bottom:0.5rem;">No Programs Yet</div>
    <div style="color:#6b7280;font-size:0.9rem;margin-bottom:1.5rem;">Create your first training program to start enrolling students.</div>
    <a href="{{ route('organization.programs.create') }}" class="btn btn-primary">+ Create Your First Program</a>
</div>
@endforelse

{{-- Pagination --}}
@if($programs->hasPages())
<div style="margin-top:1.5rem;display:flex;justify-content:center;">
    {{ $programs->links() }}
</div>
@endif

@endsection
