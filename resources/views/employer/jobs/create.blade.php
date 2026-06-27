@extends('layouts.app')

@section('title', 'Post a New Job')
@section('page-title', 'Post a New Vacancy')

@section('content')
<div class="animate-fade-up" style="max-width:860px;margin:0 auto;">

    <div class="table-container" style="padding:2rem;">
        <div style="margin-bottom:2rem;">
            <h2 style="font-size:1.25rem;font-weight:700;">Post a New Vacancy</h2>
            <p style="color:#6b7280;font-size:.9rem;margin-top:.25rem;">Fill in the details to attract the right candidates for your position.</p>
        </div>

        <form method="POST" action="{{ route('employer.jobs.store') }}">
            @csrf

            {{-- Type Selector --}}
            <div style="margin-bottom:1.5rem;">
                <label class="form-label">Vacancy Type *</label>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-top:.5rem;">
                    <label style="display:flex;align-items:center;gap:.75rem;padding:1rem;border:2px solid {{ old('type','job')==='job'?'#0d9488':'var(--sc-dark-border)' }};border-radius:10px;cursor:pointer;transition:.2s;" id="typeJobCard">
                        <input type="radio" name="type" value="job" {{ old('type','job')==='job'?'checked':'' }} style="accent-color:#0d9488;" onchange="updateTypeCard()">
                        <div>
                            <div style="font-weight:700;">💼 Job</div>
                            <div style="font-size:.8rem;color:#6b7280;">Permanent or contract employment</div>
                        </div>
                    </label>
                    <label style="display:flex;align-items:center;gap:.75rem;padding:1rem;border:2px solid {{ old('type')==='internship'?'#7c3aed':'var(--sc-dark-border)' }};border-radius:10px;cursor:pointer;transition:.2s;" id="typeInternCard">
                        <input type="radio" name="type" value="internship" {{ old('type')==='internship'?'checked':'' }} style="accent-color:#7c3aed;" onchange="updateTypeCard()">
                        <div>
                            <div style="font-weight:700;">🎓 Internship</div>
                            <div style="font-size:.8rem;color:#6b7280;">Short-term learning opportunity</div>
                        </div>
                    </label>
                </div>
                @error('type')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Title --}}
            <div style="margin-bottom:1.25rem;">
                <label class="form-label">Job Title *</label>
                <input type="text" name="title" value="{{ old('title') }}" class="form-input" placeholder="e.g. Junior Software Developer" required>
                @error('title')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Description --}}
            <div style="margin-bottom:1.25rem;">
                <label class="form-label">Job Description *</label>
                <textarea name="description" rows="5" class="form-input" placeholder="Describe the role, responsibilities, and what makes this opportunity exciting..." required>{{ old('description') }}</textarea>
                @error('description')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.25rem;">
                {{-- Location --}}
                <div>
                    <label class="form-label">Location</label>
                    <input type="text" name="location" value="{{ old('location') }}" class="form-input" placeholder="e.g. Nairobi, Kenya (Hybrid)">
                </div>
                {{-- Employment Type --}}
                <div>
                    <label class="form-label">Employment Type</label>
                    <select name="employment_type" class="form-input">
                        <option value="">Select...</option>
                        @foreach(['Full-time','Part-time','Contract','Remote','Internship'] as $t)
                        <option value="{{ $t }}" {{ old('employment_type')===$t?'selected':'' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.25rem;">
                {{-- Salary --}}
                <div>
                    <label class="form-label">Salary / Stipend (Optional)</label>
                    <input type="text" name="salary" value="{{ old('salary') }}" class="form-input" placeholder="e.g. KES 80,000 - 120,000">
                </div>
                {{-- Duration (internship) --}}
                <div>
                    <label class="form-label">Duration (for Internships)</label>
                    <input type="text" name="duration" value="{{ old('duration') }}" class="form-input" placeholder="e.g. 3 Months">
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.25rem;">
                {{-- Experience Level --}}
                <div>
                    <label class="form-label">Experience Level</label>
                    <select name="experience_level" class="form-input">
                        <option value="">Select...</option>
                        @foreach(['Entry','Mid','Senior'] as $lvl)
                        <option value="{{ $lvl }}" {{ old('experience_level')===$lvl?'selected':'' }}>{{ $lvl }} Level</option>
                        @endforeach
                    </select>
                </div>
                {{-- Deadline --}}
                <div>
                    <label class="form-label">Application Deadline</label>
                    <input type="date" name="deadline" value="{{ old('deadline') }}" class="form-input">
                    @error('deadline')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Required Skills --}}
            <div style="margin-bottom:1.25rem;">
                <label class="form-label">Required Skills</label>
                <input type="text" name="required_skills" value="{{ old('required_skills') }}" class="form-input" placeholder="e.g. JavaScript, React, SQL, Git">
                <div style="font-size:.78rem;color:#6b7280;margin-top:.25rem;">Comma-separated list of skills</div>
            </div>

            {{-- Required Qualifications --}}
            <div style="margin-bottom:1.25rem;">
                <label class="form-label">Required Qualifications</label>
                <input type="text" name="required_qualifications" value="{{ old('required_qualifications') }}" class="form-input" placeholder="e.g. Diploma or Degree in Computer Science">
            </div>

            {{-- Requirements --}}
            <div style="margin-bottom:2rem;">
                <label class="form-label">Additional Requirements</label>
                <textarea name="requirements" rows="3" class="form-input" placeholder="Any additional requirements or notes for applicants...">{{ old('requirements') }}</textarea>
            </div>

            <div style="display:flex;gap:1rem;">
                <button type="submit" class="btn btn-primary">🚀 Post Vacancy</button>
                <a href="{{ route('employer.jobs.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function updateTypeCard() {
    const isJob = document.querySelector('input[name="type"][value="job"]').checked;
    document.getElementById('typeJobCard').style.borderColor = isJob ? '#0d9488' : 'var(--sc-dark-border)';
    document.getElementById('typeInternCard').style.borderColor = !isJob ? '#7c3aed' : 'var(--sc-dark-border)';
}
</script>
@endpush
@endsection
