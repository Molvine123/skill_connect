@extends('layouts.app')

@section('title', 'Manage Portfolio')
@section('page-title', 'My Professional Portfolio')

@section('content')
<div class="animate-fade-up" style="max-width:800px;margin:0 auto;">

    <div class="table-container" style="padding:2rem;">
        <div style="margin-bottom:2.5rem;display:flex;justify-content:space-between;align-items:start;flex-wrap:wrap;gap:1rem;">
            <div>
                <h2 style="font-size:1.25rem;font-weight:700;">Professional Portfolio</h2>
                <p style="color:#6b7280;font-size:.9rem;margin-top:.25rem;">Present your verified skills, education, and credentials to potential employers.</p>
            </div>
            @if($student->open_to_work)
                <span style="background:rgba(22,163,74,0.15);color:#16a34a;padding:.25rem .75rem;border-radius:20px;font-size:.72rem;font-weight:700;letter-spacing:0.02em;">
                    🟢 OPEN TO WORK
                </span>
            @endif
        </div>

        <form method="POST" action="{{ route('student.portfolio.update') }}" enctype="multipart/form-data">
            @csrf

            {{-- Open to Work Checkbox --}}
            <div style="margin-bottom:1.5rem;background:rgba(255,255,255,0.01);border:1px solid var(--sc-dark-border);border-radius:10px;padding:1rem;">
                <label style="display:flex;align-items:start;gap:.75rem;cursor:pointer;user-select:none;">
                    <input type="checkbox" name="open_to_work" value="1" {{ old('open_to_work', $student->open_to_work)?'checked':'' }} style="accent-color:#0d9488;width:18px;height:18px;margin-top:.15rem;">
                    <div>
                        <div style="font-weight:700;color:#fff;font-size:.9rem;">Actively Seeking Opportunities</div>
                        <div style="font-size:.8rem;color:#6b7280;margin-top:.15rem;">Make your profile searchable by approved employers looking for candidates with your verified skillsets.</div>
                    </div>
                </label>
                @error('open_to_work')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Biography --}}
            <div style="margin-bottom:1.5rem;">
                <label class="form-label">Professional Summary / Biography</label>
                <textarea name="bio" rows="5" class="form-input" placeholder="Briefly introduce yourself, your experience, career goals, and what you specialize in...">{{ old('bio', $student->bio) }}</textarea>
                <div style="font-size:.78rem;color:#6b7280;margin-top:.25rem;">Max 1000 characters. Showcase your personality and work ethic.</div>
                @error('bio')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.5rem;">
                {{-- LinkedIn --}}
                <div>
                    <label class="form-label">LinkedIn URL</label>
                    <input type="url" name="linkedin_url" value="{{ old('linkedin_url', $student->linkedin_url) }}" class="form-input" placeholder="https://www.linkedin.com/in/username">
                    @error('linkedin_url')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                {{-- Personal Website --}}
                <div>
                    <label class="form-label">Personal Portfolio Website</label>
                    <input type="url" name="portfolio_url" value="{{ old('portfolio_url', $student->portfolio_url) }}" class="form-input" placeholder="https://myportfolio.com">
                    @error('portfolio_url')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.5rem;">
                {{-- Location --}}
                <div>
                    <label class="form-label">Physical Location</label>
                    <input type="text" name="location" value="{{ old('location', $student->location) }}" class="form-input" placeholder="e.g. Nairobi, Kenya">
                    @error('location')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                {{-- CV Upload --}}
                <div>
                    <label class="form-label">Upload Curriculum Vitae (CV) / Resume</label>
                    <input type="file" name="cv_file" class="form-input" style="padding:.4rem;" accept=".pdf,.doc,.docx">
                    @if($student->cv_file)
                        <div style="margin-top:.4rem;display:flex;align-items:center;gap:.5rem;font-size:.8rem;">
                            <span style="color:#16a34a;font-weight:600;">✓ CV uploaded</span>
                            <a href="{{ asset('storage/' . $student->cv_file) }}" target="_blank" style="color:#0d9488;text-decoration:none;">View current document</a>
                        </div>
                    @endif
                    @error('cv_file')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div style="display:flex;gap:1rem;margin-top:2rem;">
                <button type="submit" class="btn btn-primary">Update Portfolio</button>
                <a href="{{ route('student.dashboard') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
