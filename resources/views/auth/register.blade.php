@extends('layouts.auth')
@section('title', 'Create Account')

@section('content')
<div class="animate-fade-up">
    <div class="auth-logo" style="margin-bottom:1.75rem;">
        <div class="auth-logo-icon">SC</div>
        <span class="auth-logo-text">SkillConnect</span>
    </div>

    <h2 style="font-size:1.75rem;font-weight:700;color:#f1f5f9;margin-bottom:0.375rem;">Create your account</h2>
    <p style="color:#6b7280;font-size:0.9375rem;margin-bottom:1.75rem;">Join Kenya's national skills ecosystem</p>

    @if($errors->any())
    <div class="alert alert-error">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div>{{ $errors->first() }}</div>
    </div>
    @endif

    <form method="POST" action="{{ route('register') }}" id="registerForm">
        @csrf

        {{-- Role Selection --}}
        <div class="form-group">
            <label class="form-label">I am registering as</label>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:0.625rem;margin-bottom:0.25rem;" id="roleSelector">
                @foreach($roles as $role)
                @php
                    $icons = [
                        'institution'  => ['emoji'=>'🏫','color'=>'#22d3ee'],
                        'organization' => ['emoji'=>'🏢','color'=>'#a78bfa'],
                        'student'      => ['emoji'=>'🎓','color'=>'#34d399'],
                    ];
                    $ic = $icons[$role->name] ?? ['emoji'=>'👤','color'=>'#818cf8'];
                @endphp
                <label for="role_{{ $role->id }}" style="cursor:pointer;">
                    <input type="radio" id="role_{{ $role->id }}" name="role_id" value="{{ $role->id }}" data-role-name="{{ $role->name }}" {{ old('role_id') == $role->id ? 'checked' : '' }} style="display:none;" class="role-radio">
                    <div class="role-card" data-role="{{ $role->id }}" style="text-align:center;padding:0.875rem 0.5rem;background:#1e1e35;border:2px solid var(--sc-dark-border);border-radius:12px;transition:all 0.2s;">
                        <div style="font-size:1.5rem;margin-bottom:0.375rem;">{{ $ic['emoji'] }}</div>
                        <div style="font-size:0.75rem;font-weight:600;color:#9ca3af;line-height:1.3;">{{ $role->display_name }}</div>
                    </div>
                </label>
                @endforeach
            </div>
            @error('role_id') <div class="field-error">{{ $message }}</div> @enderror
        </div>

        {{-- Dynamic Role Fields --}}
        <div id="role_fields_container" style="margin-bottom: 1.25rem;">
            {{-- Student Fields --}}
            <div id="fields_student" style="display:none;">
                <div class="form-group">
                    <label for="institution_id" class="form-label">Linked Institution / TVET / University</label>
                    <select id="institution_id" name="institution_id" class="form-control">
                        <option value="">None / Independent Student</option>
                        @foreach($institutions as $inst)
                            <option value="{{ $inst->id }}" {{ old('institution_id') == $inst->id ? 'selected' : '' }}>{{ $inst->name }} ({{ $inst->location }})</option>
                        @endforeach
                    </select>
                    @error('institution_id') <div class="field-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label for="registration_number" class="form-label">Admission / Registration Number</label>
                    <input type="text" id="registration_number" name="registration_number" value="{{ old('registration_number') }}" class="form-control" placeholder="e.g. NTI/2026/0912">
                    @error('registration_number') <div class="field-error">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- Institution Fields --}}
            <div id="fields_institution" style="display:none;">
                <div class="form-group">
                    <label for="inst_registration_number" class="form-label">Institution Registration Number (Ministry/TVETA)</label>
                    <input type="text" id="inst_registration_number" name="inst_registration_number" value="{{ old('inst_registration_number') }}" class="form-control" placeholder="e.g. TVETA/PRIVATE/TVC/0024/2021">
                    @error('inst_registration_number') <div class="field-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label for="location" class="form-label">Location / Town / Campus</label>
                    <input type="text" id="location" name="location" value="{{ old('location') }}" class="form-control" placeholder="e.g. Nairobi, Ngong Road">
                    @error('location') <div class="field-error">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- Organization Fields --}}
            <div id="fields_organization" style="display:none;">
                <div class="form-group">
                    <label for="contact_person" class="form-label">Contact Person (Full Name)</label>
                    <input type="text" id="contact_person" name="contact_person" value="{{ old('contact_person') }}" class="form-control" placeholder="e.g. Dennis Mwangi">
                    @error('contact_person') <div class="field-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label for="description" class="form-label">Brief Description of Training Provided</label>
                    <textarea id="description" name="description" class="form-control" rows="3" placeholder="e.g. Offering software engineering bootcamps and digital training courses...">{{ old('description') }}</textarea>
                    @error('description') <div class="field-error">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        {{-- Full Name --}}
        <div class="form-group">
            <label for="name" class="form-label">Full Name / Entity Name</label>
            <div class="input-icon-wrap">
                <svg class="input-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" placeholder="Jane Wambui or NTI" autocomplete="name" required>
            </div>
            @error('name') <div class="field-error">{{ $message }}</div> @enderror
        </div>

        {{-- Email --}}
        <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <div class="input-icon-wrap">
                <svg class="input-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" placeholder="you@example.com" autocomplete="email" required>
            </div>
            @error('email') <div class="field-error">{{ $message }}</div> @enderror
        </div>

        {{-- Phone --}}
        <div class="form-group">
            <label for="phone" class="form-label">Phone Number <span style="color:#4b5563;font-weight:400;">(optional)</span></label>
            <div class="input-icon-wrap">
                <svg class="input-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" class="form-control {{ $errors->has('phone') ? 'is-invalid' : '' }}" placeholder="+254 7XX XXX XXX">
            </div>
            @error('phone') <div class="field-error">{{ $message }}</div> @enderror
        </div>

        {{-- Password --}}
        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <div class="input-icon-wrap" style="position:relative;">
                <svg class="input-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <input type="password" id="password" name="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" placeholder="Min. 8 characters" autocomplete="new-password" required style="padding-right:3rem;">
                <button type="button" class="pw-toggle" onclick="togglePassword('password', this)">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </button>
            </div>
            @error('password') <div class="field-error">{{ $message }}</div> @enderror
        </div>

        {{-- Confirm Password --}}
        <div class="form-group">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <div class="input-icon-wrap" style="position:relative;">
                <svg class="input-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Re-enter password" autocomplete="new-password" required style="padding-right:3rem;">
                <button type="button" class="pw-toggle" onclick="togglePassword('password_confirmation', this)">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </button>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-full" id="registerBtn" style="margin-top:0.5rem;">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            Create Account
        </button>
    </form>

    <p style="text-align:center;margin-top:1.5rem;font-size:0.9rem;color:#6b7280;">
        Already have an account?
        <a href="{{ route('login') }}" style="color:var(--sc-primary);font-weight:600;text-decoration:none;margin-left:0.25rem;">Sign in</a>
    </p>
</div>

<script>
// Role card selection highlight and dynamic field toggling
document.querySelectorAll('.role-radio').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.role-card').forEach(card => {
            card.style.borderColor = 'var(--sc-dark-border)';
            card.style.background  = '#1e1e35';
            card.querySelector('div:last-child').style.color = '#9ca3af';
        });
        const card = document.querySelector(`.role-card[data-role="${this.value}"]`);
        if (card) {
            card.style.borderColor = 'var(--sc-primary)';
            card.style.background  = 'rgba(99,102,241,0.1)';
            card.querySelector('div:last-child').style.color = '#c7d2fe';
        }

        const roleName = this.getAttribute('data-role-name');
        document.getElementById('fields_student').style.display = 'none';
        document.getElementById('fields_institution').style.display = 'none';
        document.getElementById('fields_organization').style.display = 'none';

        if (roleName === 'student') {
            document.getElementById('fields_student').style.display = 'block';
        } else if (roleName === 'institution') {
            document.getElementById('fields_institution').style.display = 'block';
        } else if (roleName === 'organization') {
            document.getElementById('fields_organization').style.display = 'block';
        }
    });
});

// Restore selection on page reload (old input)
const oldRole = "{{ old('role_id') }}";
if (oldRole) {
    const radio = document.querySelector(`input[value="${oldRole}"]`);
    if (radio) radio.dispatchEvent(new Event('change'));
} else {
    // Select first role by default
    const firstRadio = document.querySelector('.role-radio');
    if (firstRadio) {
        firstRadio.checked = true;
        firstRadio.dispatchEvent(new Event('change'));
    }
}

// Loading state
document.getElementById('registerForm').addEventListener('submit', function() {
    const btn = document.getElementById('registerBtn');
    btn.innerHTML = '<svg class="animate-spin" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Creating account...';
    btn.disabled = true;
});

const style = document.createElement('style');
style.textContent = '@keyframes spin { to { transform: rotate(360deg); } } .animate-spin { animation: spin 1s linear infinite; }';
document.head.appendChild(style);
</script>
@endsection
