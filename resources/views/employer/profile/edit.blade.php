@extends('layouts.app')

@section('title', 'Edit Company Profile')
@section('page-title', 'Company Profile Setup')

@section('content')
<div class="animate-fade-up" style="max-width:800px;margin:0 auto;">

    <div class="table-container" style="padding:2rem;">
        <div style="margin-bottom:2rem;">
            <h2 style="font-size:1.25rem;font-weight:700;">Company Profile</h2>
            <p style="color:#6b7280;font-size:.9rem;margin-top:.25rem;">Provide information about your business to showcase to graduates.</p>
        </div>

        <form method="POST" action="{{ route('employer.profile.update') }}" enctype="multipart/form-data">
            @csrf

            <div style="display:flex;gap:2rem;align-items:center;margin-bottom:2rem;flex-wrap:wrap;">
                <img src="{{ $employer ? $employer->logo_url : 'https://ui-avatars.com/api/?name=Company&background=0d9488&color=fff&size=128&bold=true' }}" 
                     style="width:96px;height:96px;border-radius:12px;object-fit:cover;border:1px solid var(--sc-dark-border);" alt="Company Logo" id="logoPreview">
                <div>
                    <label class="form-label">Company Logo</label>
                    <input type="file" name="logo" class="form-input" style="padding:.4rem;" accept="image/*" onchange="previewImage(this)">
                    <div style="font-size:.78rem;color:#6b7280;margin-top:.25rem;">Supported formats: JPG, PNG, GIF. Max 2MB.</div>
                </div>
            </div>

            {{-- Company Name --}}
            <div style="margin-bottom:1.25rem;">
                <label class="form-label">Company Name *</label>
                <input type="text" name="company_name" value="{{ old('company_name', $employer ? $employer->company_name : Auth::user()->name) }}" class="form-input" required>
                @error('company_name')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.25rem;">
                {{-- Registration Number --}}
                <div>
                    <label class="form-label">Business Registration Number</label>
                    <input type="text" name="registration_number" value="{{ old('registration_number', $employer ? $employer->registration_number : '') }}" class="form-input" placeholder="e.g. CPR/2026/0000">
                    @error('registration_number')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                {{-- Industry --}}
                <div>
                    <label class="form-label">Industry</label>
                    <input type="text" name="industry" value="{{ old('industry', $employer ? $employer->industry : '') }}" class="form-input" placeholder="e.g. Software, Telecommunications">
                    @error('industry')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.25rem;">
                {{-- Phone --}}
                <div>
                    <label class="form-label">Contact Telephone</label>
                    <input type="text" name="phone" value="{{ old('phone', $employer ? $employer->phone : Auth::user()->phone) }}" class="form-input" placeholder="e.g. +254700000000">
                    @error('phone')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                {{-- Email --}}
                <div>
                    <label class="form-label">Company Email</label>
                    <input type="email" name="email" value="{{ old('email', $employer ? $employer->email : Auth::user()->email) }}" class="form-input" placeholder="e.g. contact@company.com">
                    @error('email')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- Website --}}
            <div style="margin-bottom:1.25rem;">
                <label class="form-label">Company Website URL</label>
                <input type="url" name="website" value="{{ old('website', $employer ? $employer->website : '') }}" class="form-input" placeholder="https://www.company.com">
                @error('website')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Address --}}
            <div style="margin-bottom:1.25rem;">
                <label class="form-label">Physical Address</label>
                <textarea name="address" rows="2" class="form-input" placeholder="e.g. Safaricom House, Waiyaki Way, Nairobi">{{ old('address', $employer ? $employer->address : '') }}</textarea>
                @error('address')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            {{-- Description --}}
            <div style="margin-bottom:2rem;">
                <label class="form-label">Company Description</label>
                <textarea name="description" rows="4" class="form-input" placeholder="Briefly describe what your business does and your mission...">{{ old('description', $employer ? $employer->description : '') }}</textarea>
                @error('description')<div class="form-error">{{ $message }}</div>@enderror
            </div>

            <div style="display:flex;gap:1rem;">
                <button type="submit" class="btn btn-primary">Save Profile</button>
                <a href="{{ route('employer.dashboard') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('logoPreview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
@endsection
