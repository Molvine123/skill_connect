@extends('layouts.app')
@section('title', 'Browse Programs')
@section('page-title', 'Browse Programs')

@section('content')

{{-- Header --}}
<div style="background:linear-gradient(135deg,#0f0f1a 0%,#1a1a35 60%,#16213e 100%);border:1px solid rgba(99,102,241,0.25);border-radius:20px;padding:2rem;margin-bottom:2rem;position:relative;overflow:hidden;" class="animate-fade-up">
    <div style="position:absolute;top:-60px;right:-60px;width:280px;height:280px;background:radial-gradient(circle,rgba(99,102,241,0.2),transparent 70%);pointer-events:none;"></div>
    <div style="position:relative;z-index:1;">
        <h1 style="font-size:1.75rem;font-weight:800;color:#f1f5f9;margin-bottom:0.375rem;">🔍 Browse Skill Programs</h1>
        <p style="color:#94a3b8;font-size:0.9375rem;">Discover training programs across Digital Skills, Vocational Trades, Soft Skills, and Entrepreneurship.</p>
    </div>
</div>

{{-- Category Filters --}}
<div style="display:flex;gap:0.75rem;flex-wrap:wrap;margin-bottom:1.75rem;" class="animate-fade-up">
    <a href="{{ route('student.programs.index') }}" style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.5rem 1.125rem;background:{{ !request('category') ? 'rgba(99,102,241,0.25)' : 'rgba(255,255,255,0.04)' }};border:1px solid {{ !request('category') ? 'var(--sc-primary)' : 'var(--sc-dark-border)' }};border-radius:50px;font-size:0.875rem;color:{{ !request('category') ? '#818cf8' : '#9ca3af' }};text-decoration:none;font-weight:500;transition:all 0.15s;">
        🌐 All Categories <span style="background:rgba(99,102,241,0.2);color:#818cf8;padding:0.1rem 0.5rem;border-radius:20px;font-size:0.75rem;">{{ $programs->total() }}</span>
    </a>
    @foreach($categories as $cat)
    <a href="{{ route('student.programs.index', ['category' => $cat->slug, 'search' => request('search')]) }}" style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.5rem 1.125rem;background:{{ request('category') === $cat->slug ? 'rgba(99,102,241,0.25)' : 'rgba(255,255,255,0.04)' }};border:1px solid {{ request('category') === $cat->slug ? 'var(--sc-primary)' : 'var(--sc-dark-border)' }};border-radius:50px;font-size:0.875rem;color:{{ request('category') === $cat->slug ? '#818cf8' : '#9ca3af' }};text-decoration:none;font-weight:500;transition:all 0.15s;">
        {{ $cat->icon }} {{ $cat->name }} <span style="background:rgba(255,255,255,0.06);color:#6b7280;padding:0.1rem 0.5rem;border-radius:20px;font-size:0.75rem;">{{ $cat->programs_count }}</span>
    </a>
    @endforeach
</div>

{{-- Search --}}
<div class="card animate-fade-up" style="margin-bottom:1.5rem;">
    <div class="card-body" style="padding:1rem 1.5rem;">
        <form method="GET" action="{{ route('student.programs.index') }}" style="display:flex;gap:0.75rem;align-items:center;">
            @if(request('category'))
            <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            <div style="flex:1;position:relative;">
                <svg width="16" height="16" fill="none" stroke="#6b7280" viewBox="0 0 24 24" style="position:absolute;left:0.875rem;top:50%;transform:translateY(-50%);pointer-events:none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" class="form-control" placeholder="Search programs by name or description…" value="{{ request('search') }}" style="padding-left:2.75rem;height:40px;">
            </div>
            <button type="submit" class="btn btn-primary" style="height:40px;padding:0 1.25rem;font-size:0.875rem;">Search</button>
            @if(request('search'))
            <a href="{{ route('student.programs.index', ['category' => request('category')]) }}" class="btn btn-outline" style="height:40px;padding:0 1rem;font-size:0.875rem;">✕ Clear</a>
            @endif
        </form>
    </div>
</div>

{{-- Programs Grid --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:1.5rem;" class="animate-fade-up-delay">
    @forelse($programs as $program)
    @php
        $userStatus = $myEnrollments[$program->id] ?? null;
        $modeColors = [
            'online'    => ['#6366f1','rgba(99,102,241,0.12)'],
            'in_person' => ['#10b981','rgba(16,185,129,0.12)'],
            'hybrid'    => ['#f59e0b','rgba(245,158,11,0.12)'],
        ];
        [$modeColor, $modeBg] = $modeColors[$program->mode] ?? ['#9ca3af', 'rgba(255,255,255,0.05)'];
        $modeLabel = ['online'=>'🌐 Online','in_person'=>'📍 In-Person','hybrid'=>'⚡ Hybrid'][$program->mode] ?? $program->mode;
        
        $rolePrefix = auth()->user()->role?->name ?? 'student';
        $enrollRoute = route("{$rolePrefix}.programs.enroll", $program->id);
    @endphp
    <div class="card" style="display:flex;flex-direction:column;overflow:visible;transition:transform 0.2s,box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 20px 40px rgba(0,0,0,0.4)'" onmouseout="this.style.transform='';this.style.boxShadow=''">
        {{-- Card Header Band --}}
        <div style="padding:1.25rem 1.5rem;border-bottom:1px solid var(--sc-dark-border);display:flex;gap:0.75rem;align-items:flex-start;">
            <div style="width:48px;height:48px;border-radius:13px;background:rgba(99,102,241,0.12);border:1px solid rgba(99,102,241,0.2);display:flex;align-items:center;justify-content:center;font-size:1.5rem;flex-shrink:0;">
                {{ $program->category?->icon ?? '📚' }}
            </div>
            <div style="flex:1;min-width:0;">
                <h3 style="font-size:1rem;font-weight:700;color:#f1f5f9;margin-bottom:0.25rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $program->name }}</h3>
                <div style="font-size:0.8rem;color:#6b7280;">by <span style="color:#a78bfa;">{{ $program->organization?->name }}</span></div>
            </div>
        </div>

        {{-- Card Body --}}
        <div style="padding:1.25rem 1.5rem;flex:1;display:flex;flex-direction:column;gap:1rem;">
            <p style="font-size:0.875rem;color:#9ca3af;line-height:1.6;margin:0;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $program->description }}</p>

            {{-- Meta pills --}}
            <div style="display:flex;flex-wrap:wrap;gap:0.5rem;">
                <span style="display:inline-flex;align-items:center;gap:0.35rem;padding:0.3rem 0.65rem;background:{{ $modeBg }};border:1px solid {{ $modeColor }}30;border-radius:6px;font-size:0.75rem;color:{{ $modeColor }};">{{ $modeLabel }}</span>
                <span style="display:inline-flex;align-items:center;gap:0.35rem;padding:0.3rem 0.65rem;background:rgba(255,255,255,0.04);border:1px solid var(--sc-dark-border);border-radius:6px;font-size:0.75rem;color:#9ca3af;">⏱️ {{ $program->duration }}</span>
                <span style="display:inline-flex;align-items:center;gap:0.35rem;padding:0.3rem 0.65rem;background:rgba(255,255,255,0.04);border:1px solid var(--sc-dark-border);border-radius:6px;font-size:0.75rem;color:#9ca3af;">👥 {{ $program->capacity }} seats</span>
            </div>

            {{-- Cost --}}
            <div style="display:flex;align-items:center;justify-content:space-between;padding:0.875rem 1rem;background:rgba(255,255,255,0.02);border:1px solid var(--sc-dark-border);border-radius:10px;">
                <div>
                    <div style="font-size:0.75rem;color:#6b7280;">Program Fee</div>
                    <div style="font-size:1.25rem;font-weight:800;color:{{ $program->cost > 0 ? '#f1f5f9' : '#34d399' }};">
                        {{ $program->cost > 0 ? 'KES ' . number_format($program->cost, 0) : '🎁 Free' }}
                    </div>
                </div>
                @if($program->venue)
                <div style="text-align:right;">
                    <div style="font-size:0.75rem;color:#6b7280;">Venue</div>
                    <div style="font-size:0.8rem;color:#9ca3af;max-width:140px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $program->venue }}</div>
                </div>
                @endif
            </div>
        </div>

        {{-- Card Footer --}}
        <div style="padding:1rem 1.5rem;border-top:1px solid var(--sc-dark-border);">
            @if(auth()->user()->isStudent())
                @if($userStatus === 'approved' || $userStatus === 'completed')
                    <div style="display:flex;align-items:center;gap:0.5rem;padding:0.625rem 1rem;background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.25);border-radius:8px;font-size:0.875rem;color:#34d399;font-weight:600;">
                        ✓ {{ $userStatus === 'completed' ? 'Completed' : 'Enrolled & Approved' }}
                    </div>
                @elseif($userStatus === 'pending')
                    <div style="display:flex;align-items:center;gap:0.5rem;padding:0.625rem 1rem;background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.25);border-radius:8px;font-size:0.875rem;color:#fbbf24;font-weight:600;">
                        ⏳ Enrollment Pending Approval
                    </div>
                @else
                    <form method="POST" action="{{ $enrollRoute }}">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-full" style="background:linear-gradient(135deg,var(--sc-primary),var(--sc-secondary));box-shadow:0 4px 15px rgba(99,102,241,0.35);" onclick="return confirm('Enroll in \'{{ addslashes($program->name) }}\'?')">
                            Enroll Now →
                        </button>
                    </form>
                @endif
            @else
                <form method="POST" action="{{ $enrollRoute }}">
                    @csrf
                    <div style="display:flex; flex-direction:column; gap:0.5rem;">
                        <select name="student_id" class="form-control" required style="font-size:0.85rem; padding:0.5rem; background:rgba(255,255,255,0.05); border:1px solid var(--sc-dark-border); border-radius:6px; color:#e2e8f0; width:100%;">
                            <option value="">-- Select Student to Enroll --</option>
                            @foreach($eligibleStudents as $el_student)
                                <option value="{{ $el_student->id }}">{{ $el_student->user->name ?? 'Unknown' }} ({{ $el_student->registration_number ?? $el_student->user->email ?? 'No email' }})</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary btn-full" style="background:linear-gradient(135deg,var(--sc-primary),var(--sc-secondary));box-shadow:0 4px 15px rgba(99,102,241,0.35);" onclick="return confirm('Enroll selected student in \'{{ addslashes($program->name) }}\'?')">
                            Enroll Student →
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
    @empty
    <div style="grid-column:1/-1;text-align:center;padding:4rem 2rem;" class="coming-soon-card">
        <span class="coming-soon-icon">📭</span>
        <h3 style="font-size:1.125rem;font-weight:700;color:#e2e8f0;margin-bottom:0.5rem;">No Programs Found</h3>
        <p style="color:#6b7280;font-size:0.875rem;">Try adjusting your search filters or browsing another category.</p>
    </div>
    @endforelse
</div>

{{-- Pagination --}}
@if($programs->hasPages())
<div style="margin-top:2rem;display:flex;justify-content:center;" class="animate-fade-up-delay">
    {{ $programs->links() }}
</div>
@endif

@endsection
