@extends('layouts.auth')
@section('title', 'Reset Password')

@section('content')
<div class="animate-fade-up">
    <div class="auth-logo" style="margin-bottom:2rem;">
        <div class="auth-logo-icon">SC</div>
        <span class="auth-logo-text">SkillConnect</span>
    </div>

    <div style="width:64px;height:64px;background:linear-gradient(135deg,rgba(16,185,129,0.2),rgba(6,182,212,0.2));border:1px solid rgba(16,185,129,0.3);border-radius:16px;display:flex;align-items:center;justify-content:center;margin-bottom:1.5rem;">
        <svg width="30" height="30" fill="none" stroke="#34d399" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
    </div>

    <h2 style="font-size:1.75rem;font-weight:700;color:#f1f5f9;margin-bottom:0.375rem;">Set new password</h2>
    <p style="color:#6b7280;font-size:0.9375rem;margin-bottom:2rem;">Create a strong password for your account.</p>

    @if($errors->any())
    <div class="alert alert-error">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div>{{ $errors->first() }}</div>
    </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}" id="resetForm">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

        <div class="form-group">
            <label for="email_display" class="form-label">Email Address</label>
            <div class="input-icon-wrap">
                <svg class="input-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <input type="email" id="email_display" class="form-control" value="{{ $email ?? old('email') }}" disabled style="opacity:0.6;cursor:not-allowed;">
            </div>
        </div>

        <div class="form-group">
            <label for="password" class="form-label">New Password</label>
            <div class="input-icon-wrap" style="position:relative;">
                <svg class="input-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <input type="password" id="password" name="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" placeholder="Min. 8 characters" required style="padding-right:3rem;" oninput="checkStrength(this.value)">
                <button type="button" class="pw-toggle" onclick="togglePassword('password', this)">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </button>
            </div>
            {{-- Strength bar --}}
            <div style="margin-top:0.5rem;height:4px;background:#1e1e35;border-radius:4px;overflow:hidden;">
                <div id="strengthBar" style="height:100%;width:0;border-radius:4px;transition:all 0.3s;"></div>
            </div>
            <div id="strengthLabel" style="font-size:0.75rem;margin-top:0.375rem;color:#4b5563;"></div>
            @error('password') <div class="field-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation" class="form-label">Confirm New Password</label>
            <div class="input-icon-wrap" style="position:relative;">
                <svg class="input-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Re-enter new password" required style="padding-right:3rem;">
                <button type="button" class="pw-toggle" onclick="togglePassword('password_confirmation', this)">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </button>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-full" id="resetBtn" style="margin-top:0.5rem;">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Reset Password
        </button>
    </form>

    <p style="text-align:center;margin-top:1.75rem;font-size:0.9rem;color:#6b7280;">
        <a href="{{ route('login') }}" style="color:var(--sc-primary);font-weight:600;text-decoration:none;">← Back to login</a>
    </p>
</div>

<script>
function checkStrength(val) {
    const bar   = document.getElementById('strengthBar');
    const label = document.getElementById('strengthLabel');
    let score = 0;
    if (val.length >= 8)  score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    const levels = [
        {w:'0%',   color:'transparent', text:''},
        {w:'25%',  color:'#ef4444',     text:'Weak'},
        {w:'50%',  color:'#f59e0b',     text:'Fair'},
        {w:'75%',  color:'#3b82f6',     text:'Good'},
        {w:'100%', color:'#10b981',     text:'Strong'},
    ];
    bar.style.width      = levels[score].w;
    bar.style.background = levels[score].color;
    label.textContent    = levels[score].text;
    label.style.color    = levels[score].color;
}

document.getElementById('resetForm').addEventListener('submit', function() {
    const btn = document.getElementById('resetBtn');
    btn.innerHTML = '<svg class="animate-spin" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Resetting...';
    btn.disabled = true;
});
const style = document.createElement('style');
style.textContent = '@keyframes spin { to { transform: rotate(360deg); } } .animate-spin { animation: spin 1s linear infinite; }';
document.head.appendChild(style);
</script>
@endsection
