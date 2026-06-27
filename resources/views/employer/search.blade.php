@extends('layouts.app')

@section('title', 'Candidate Search')
@section('page-title', 'Search Candidates')

@section('content')
<div class="animate-fade-up">

    {{-- Filters Card --}}
    <div class="table-container" style="padding:1.5rem;margin-bottom:1.5rem;">
        <form method="GET" action="{{ route('employer.search') }}" style="display:grid;grid-template-columns:2fr 1fr 1fr auto;gap:1rem;align-items:end;flex-wrap:wrap;">
            <div>
                <label class="form-label" style="font-size:.8rem;margin-bottom:.4rem;">Search by Name</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="e.g. Jane Wambui">
            </div>
            <div>
                <label class="form-label" style="font-size:.8rem;margin-bottom:.4rem;">Skill Category</label>
                <select name="category_id" class="form-input">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id')==$cat->id?'selected':'' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label" style="font-size:.8rem;margin-bottom:.4rem;">Location</label>
                <input type="text" name="location" value="{{ request('location') }}" class="form-input" placeholder="e.g. Nairobi">
            </div>
            <div style="display:flex;align-items:center;gap:1.5rem;padding-bottom:.5rem;">
                <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;user-select:none;font-size:.85rem;font-weight:600;color:#fff;">
                    <input type="checkbox" name="open_to_work" value="1" {{ request('open_to_work')?'checked':'' }} style="accent-color:#0d9488;width:16px;height:16px;">
                    Open to Work
                </label>
                <button type="submit" class="btn btn-primary" style="padding:.6rem 1.5rem;">Search</button>
            </div>
        </form>
    </div>

    {{-- Results --}}
    @if($students->isEmpty())
    <div class="table-container" style="text-align:center;padding:3rem;">
        <div style="font-size:3rem;margin-bottom:1rem;">🔍</div>
        <h3 style="font-size:1.1rem;font-weight:600;color:#6b7280;">No Candidates Found</h3>
        <p style="color:#9ca3af;font-size:.9rem;margin-top:.25rem;">Try adjusting your search criteria or filters.</p>
    </div>
    @else
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(340px,1fr));gap:1.25rem;">
        @foreach($students as $student)
        <div class="table-container" style="padding:1.5rem;display:flex;flex-direction:column;justify-content:between;height:100%;">
            <div>
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1rem;">
                    <div style="display:flex;gap:1rem;align-items:center;">
                        <img src="{{ $student->user->getAvatarUrl() }}" style="width:48px;height:48px;border-radius:50%;object-fit:cover;" alt="">
                        <div>
                            <h3 style="font-size:.95rem;font-weight:700;">{{ $student->user->name }}</h3>
                            <span style="color:#6b7280;font-size:.78rem;display:block;">
                                🎓 {{ $student->institution ? $student->institution->name : 'Independent Student' }}
                            </span>
                        </div>
                    </div>
                    @if($student->open_to_work)
                    <span style="background:rgba(22,163,74,0.1);color:#16a34a;padding:.15rem .6rem;border-radius:20px;font-size:.65rem;font-weight:700;letter-spacing:0.02em;">
                        OPEN TO WORK
                    </span>
                    @endif
                </div>

                @if($student->bio)
                    <p style="color:#9ca3af;font-size:.82rem;margin-bottom:1rem;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                        {{ $student->bio }}
                    </p>
                @else
                    <p style="color:#6b7280;font-size:.82rem;margin-bottom:1rem;font-style:italic;">
                        No biography provided yet.
                    </p>
                @endif

                {{-- Skills/Completed Programs --}}
                <div style="margin-bottom:1.25rem;">
                    <div style="font-size:.75rem;font-weight:600;color:#6b7280;text-transform:uppercase;margin-bottom:.4rem;letter-spacing:0.02em;">Verified Training:</div>
                    <div style="display:flex;flex-wrap:wrap;gap:.35rem;">
                        @php
                            $approvedPrograms = $student->enrollments->where('status', 'approved');
                            $completedPrograms = $student->enrollments->where('status', 'completed');
                        @endphp
                        @forelse($completedPrograms as $enr)
                            <span style="background:rgba(13,148,136,0.15);color:#0d9488;padding:.15rem .5rem;border-radius:6px;font-size:.7rem;font-weight:600;">
                                ✅ {{ $enr->program->name }}
                            </span>
                        @empty
                            @forelse($approvedPrograms as $enr)
                                <span style="background:rgba(37,99,235,0.1);color:#2563eb;padding:.15rem .5rem;border-radius:6px;font-size:.7rem;font-weight:600;">
                                    ⏳ {{ $enr->program->name }}
                                </span>
                            @empty
                                <span style="color:#6b7280;font-size:.75rem;font-style:italic;">No active programs enrolled</span>
                            @endforelse
                        @endforelse
                    </div>
                </div>
            </div>

            <div style="border-top:1px solid var(--sc-dark-border);padding-top:1rem;display:flex;justify-content:space-between;align-items:center;margin-top:auto;">
                <span style="color:#6b7280;font-size:.78rem;">
                    📍 {{ $student->location ?? 'Not Specified' }}
                </span>
                <a href="{{ route('employer.portfolio', $student->id) }}" class="btn btn-secondary" style="font-size:.75rem;padding:.4rem .9rem;">
                    View Portfolio
                </a>
            </div>
        </div>
        @endforeach
    </div>

    <div style="margin-top:1.5rem;">
        {{ $students->links() }}
    </div>
    @endif

</div>
@endsection
