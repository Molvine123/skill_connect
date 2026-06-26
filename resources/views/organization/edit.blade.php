@extends('layouts.app')
@section('title', 'Edit Organization Profile')
@section('page-title', 'Edit Organization Profile')

@section('content')

<div style="max-width:860px;margin:0 auto;" class="animate-fade-up">

    {{-- Page Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;">
        <div>
            <h1 style="font-size:1.5rem;font-weight:800;color:#f1f5f9;">⚙️ Organization Profile</h1>
            <p style="color:#6b7280;font-size:0.9rem;margin-top:0.25rem;">Update your organization's details and branding</p>
        </div>
        <a href="{{ route('organization.dashboard') }}" class="btn btn-outline" style="font-size:0.875rem;">← Back to Dashboard</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success" style="margin-bottom:1.5rem;">✅ {{ session('success') }}</div>
    @endif

    @if($errors->any())
    <div class="alert alert-error" style="margin-bottom:1.5rem;">
        <ul style="list-style:none;padding:0;margin:0;">
            @foreach($errors->all() as $error)
            <li>⚠️ {{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('organization.profile.update') }}" enctype="multipart/form-data">
        @csrf

        <div style="display:grid;gap:1.5rem;">

            {{-- Logo Upload --}}
            <div class="card">
                <div class="card-header"><span class="card-title">🖼️ Organization Logo</span></div>
                <div class="card-body">
                    <div style="display:flex;align-items:center;gap:2rem;flex-wrap:wrap;">
                        <div>
                            <div id="logoPreview" style="width:100px;height:100px;border-radius:18px;background:rgba(139,92,246,0.08);border:2px dashed rgba(139,92,246,0.3);display:flex;align-items:center;justify-content:center;overflow:hidden;cursor:pointer;transition:all 0.2s;" onclick="document.getElementById('logoInput').click()">
                                @if($organization && $organization->logo_path)
                                <img id="logoImg" src="{{ asset('storage/'.$organization->logo_path) }}" style="width:100%;height:100%;object-fit:cover;">
                                @else
                                <div style="text-align:center;">
                                    <div style="font-size:2.5rem;">🏢</div>
                                    <div style="font-size:0.65rem;color:#6b7280;margin-top:0.25rem;">Click to upload</div>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div>
                            <input type="file" name="logo" id="logoInput" accept="image/*" style="display:none;" onchange="previewLogo(this)">
                            <button type="button" onclick="document.getElementById('logoInput').click()" class="btn btn-outline" style="font-size:0.875rem;margin-bottom:0.75rem;">📤 Upload Logo</button>
                            <div style="font-size:0.8rem;color:#6b7280;">
                                <div>• JPEG, PNG, GIF accepted</div>
                                <div>• Maximum file size: 2MB</div>
                                <div>• Recommended: 200×200px or larger</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Basic Info --}}
            <div class="card">
                <div class="card-header"><span class="card-title">🏢 Basic Information</span></div>
                <div class="card-body" style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
                    <div class="form-group" style="grid-column:1/-1;">
                        <label class="form-label">Organization Name *</label>
                        <input type="text" name="org_name" class="form-control" value="{{ old('org_name', $organization?->name ?? auth()->user()->name) }}" required placeholder="e.g. TechSkills Kenya Ltd">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Organization Type *</label>
                        <select name="org_type" class="form-control" required>
                            <option value="">— Select Type —</option>
                            @foreach($typeLabels as $value => $label)
                            <option value="{{ $value }}" {{ old('org_type', $organization?->org_type) === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Contact Person</label>
                        <input type="text" name="contact_person" class="form-control" value="{{ old('contact_person', $organization?->contact_person) }}" placeholder="Primary contact name">
                    </div>
                </div>
            </div>

            {{-- Contact & Location --}}
            <div class="card">
                <div class="card-header"><span class="card-title">📍 Contact & Location</span></div>
                <div class="card-body" style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $organization?->phone ?? auth()->user()->phone) }}" placeholder="+254 7XX XXX XXX">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $organization?->email ?? auth()->user()->email) }}" placeholder="info@organization.com">
                    </div>
                    <div class="form-group" style="grid-column:1/-1;">
                        <label class="form-label">Physical Address</label>
                        <input type="text" name="address" class="form-control" value="{{ old('address', $organization?->address) }}" placeholder="Street, building, floor, room">
                    </div>
                    <div class="form-group">
                        <label class="form-label">County</label>
                        <select name="county" class="form-control">
                            <option value="">— Select County —</option>
                            @foreach(['Nairobi','Mombasa','Kisumu','Nakuru','Eldoret','Kiambu','Machakos','Nyeri','Meru','Kakamega','Kisii','Garissa','Turkana','Kilifi','Uasin Gishu','Trans-Nzoia','Kericho','Bomet','Migori','Homa Bay','Siaya','Busia','Bungoma','Vihiga','Nandi','Laikipia','Nyandarua','Murang\'a','Kirinyaga','Embu','Tharaka-Nithi','Isiolo','Marsabit','Mandera','Wajir','Samburu','Baringo','Elgeyo-Marakwet','West Pokot','Lamu','Tana River','Taita-Taveta','Kajiado','Makueni','Kitui','Kwale'] as $county)
                            <option value="{{ $county }}" {{ old('county', $organization?->county) === $county ? 'selected' : '' }}>{{ $county }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Website</label>
                        <input type="url" name="website" class="form-control" value="{{ old('website', $organization?->website) }}" placeholder="https://www.organization.co.ke">
                    </div>
                </div>
            </div>

            {{-- Description --}}
            <div class="card">
                <div class="card-header"><span class="card-title">📝 About the Organization</span></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="6" placeholder="Describe what your organization does, your mission, programs you offer, and what makes your training unique...">{{ old('description', $organization?->description) }}</textarea>
                        <div style="font-size:0.75rem;color:#6b7280;margin-top:0.375rem;">Maximum 1500 characters</div>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div style="display:flex;gap:1rem;justify-content:flex-end;">
                <a href="{{ route('organization.dashboard') }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary" style="min-width:180px;background:linear-gradient(135deg,#8b5cf6,#6366f1);">💾 Save Changes</button>
            </div>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
function previewLogo(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('logoPreview');
            preview.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
