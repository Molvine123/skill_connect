@extends('layouts.app')

@section('title', 'Job Board')
@section('page-title', 'Available Opportunities')

@section('content')
<div class="animate-fade-up">

    {{-- Filter Search Header --}}
    <div class="table-container" style="padding:1.5rem;margin-bottom:1.5rem;">
        <form method="GET" action="{{ route('student.jobs.index') }}" style="display:grid;grid-template-columns:2fr 1fr 1fr auto;gap:1rem;align-items:end;flex-wrap:wrap;">
            <div>
                <label class="form-label" style="font-size:.8rem;margin-bottom:.4rem;">Search Jobs & Internships</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="e.g. Developer, Intern, Python">
            </div>
            <div>
                <label class="form-label" style="font-size:.8rem;margin-bottom:.4rem;">Opportunity Type</label>
                <select name="type" class="form-input">
                    <option value="">All Types</option>
                    <option value="job" {{ request('type')==='job'?'selected':'' }}>Jobs</option>
                    <option value="internship" {{ request('type')==='internship'?'selected':'' }}>Internships</option>
                </select>
            </div>
            <div>
                <label class="form-label" style="font-size:.8rem;margin-bottom:.4rem;">Employment Type</label>
                <select name="employment_type" class="form-input">
                    <option value="">All Arrangements</option>
                    @foreach(['Full-time','Part-time','Contract','Remote','Internship'] as $t)
                        <option value="{{ $t }}" {{ request('employment_type')===$t?'selected':'' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div style="padding-bottom:.1rem;">
                <button type="submit" class="btn btn-primary" style="padding:.6rem 1.5rem;">Filter Opportunities</button>
            </div>
        </form>
    </div>

    @if($jobs->isEmpty())
    <div class="table-container" style="text-align:center;padding:3rem;">
        <div style="font-size:3rem;margin-bottom:1rem;">💼</div>
        <h3 style="font-size:1.1rem;font-weight:600;color:#6b7280;">No Opportunities Found</h3>
        <p style="color:#9ca3af;font-size:.9rem;margin-top:.25rem;">Try adjusting your filters or search terms.</p>
    </div>
    @else
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:1.25rem;">
        @foreach($jobs as $job)
        <div class="table-container" style="padding:1.5rem;display:flex;flex-direction:column;justify-content:between;height:100%;transition: transform .2s, box-shadow .2s;" 
             onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 10px 15px -3px rgba(0, 0, 0, 0.1)';"
             onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
            
            <div>
                {{-- Header --}}
                <div style="display:flex;gap:1rem;align-items:start;margin-bottom:1rem;">
                    <img src="{{ $job->employer->logo_url }}" style="width:44px;height:44px;border-radius:8px;object-fit:cover;border:1px solid var(--sc-dark-border);" alt="">
                    <div style="flex:1;min-width:0;">
                        <h4 style="font-size:.82rem;font-weight:600;color:#6b7280;margin-bottom:.15rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            {{ $job->employer->company_name }}
                        </h4>
                        <h3 style="font-size:.95rem;font-weight:700;color:#fff;line-height:1.2;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            {{ $job->title }}
                        </h3>
                    </div>
                </div>

                {{-- Badges --}}
                <div style="display:flex;flex-wrap:wrap;gap:.4rem;margin-bottom:1rem;">
                    <span style="background:{{ $job->type_badge_color }};color:#fff;padding:.15rem .5rem;border-radius:20px;font-size:.68rem;font-weight:600;">
                        {{ $job->type_label }}
                    </span>
                    @if($job->employment_type)
                    <span style="background:rgba(255,255,255,0.05);border:1px solid var(--sc-dark-border);color:#d1d5db;padding:.15rem .5rem;border-radius:20px;font-size:.68rem;font-weight:600;">
                        {{ $job->employment_type }}
                    </span>
                    @endif
                    @if($job->location)
                    <span style="background:rgba(255,255,255,0.05);border:1px solid var(--sc-dark-border);color:#d1d5db;padding:.15rem .5rem;border-radius:20px;font-size:.68rem;font-weight:600;">
                        📍 {{ Str::limit($job->location, 16) }}
                    </span>
                    @endif
                </div>

                <p style="color:#9ca3af;font-size:.82rem;margin-bottom:1.25rem;line-height:1.4;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;">
                    {{ $job->description }}
                </p>
            </div>

            <div style="border-top:1px solid var(--sc-dark-border);padding-top:1rem;display:flex;justify-content:space-between;align-items:center;margin-top:auto;">
                <span style="color:#6b7280;font-size:.78rem;">
                    @if($job->deadline)
                        Ends: {{ $job->deadline->format('d M Y') }}
                    @else
                        Ongoing
                    @endif
                </span>
                
                @if(in_array($job->id, $appliedJobIds))
                    <span style="color:#16a34a;font-size:.8rem;font-weight:700;display:inline-flex;align-items:center;gap:.25rem;">
                        ✓ Applied
                    </span>
                @else
                    <a href="{{ route('student.jobs.show', $job->id) }}" class="btn btn-secondary" style="font-size:.75rem;padding:.4rem .9rem;">
                        View details
                    </a>
                @endif
            </div>

        </div>
        @endforeach
    </div>

    <div style="margin-top:1.5rem;">
        {{ $jobs->links() }}
    </div>
    @endif

</div>
@endsection
