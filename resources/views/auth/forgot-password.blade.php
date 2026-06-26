@extends('layouts.auth')
@section('title', 'Forgot Password')

@section('content')
<div class="animate-fade-up">
    <div class="auth-logo" style="margin-bottom:2rem;">
        <div class="auth-logo-icon">SC</div>
        <span class="auth-logo-text">SkillConnect</span>
    </div>

    {{-- Icon --}}
    <div style="width:64px;height:64px;background:linear-gradient(135deg,rgba(99,102,241,0.2),rgba(139,92,246,0.2));border:1px solid rgba(99,102,241,0.3);border-radius:16px;display:flex;align-items:center;justify-content:center;margin-bottom:1.5rem;">
        <svg width="30" height="30" fill="none" stroke="#818cf8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
        </svg>
    </div>

    <h2 style="font-size:1.75rem;font-weight:700;color:#f1f5f9;margin-bottom:0.375rem;">Reset your password</h2>
    <p style="color:#6b7280;font-size:0.9375rem;margin-bottom:2rem;line-height:1.6;">Enter your email address and we'll send you a link to reset your password.</p>

    @if(session('success'))
    <div class="alert alert-success">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div>{{ session('success') }}</div>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-error">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div>{{ $errors->first() }}</div>
    </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" id="forgotForm">
        @csrf

        <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <div class="input-icon-wrap">
                <svg class="input-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                    placeholder="you@example.com"
                    autocomplete="email"
                    required
                >
            </div>
            @error('email') <div class="field-error">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-primary btn-full" id="sendBtn">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            Send Reset Link
        </button>
    </form>

    <p style="text-align:center;margin-top:1.75rem;font-size:0.9rem;color:#6b7280;">
        Remember your password?
        <a href="{{ route('login') }}" style="color:var(--sc-primary);font-weight:600;text-decoration:none;margin-left:0.25rem;">Back to login</a>
    </p>

    {{-- Info note --}}
    <div style="margin-top:2rem;padding:1rem;background:rgba(6,182,212,0.06);border:1px solid rgba(6,182,212,0.2);border-radius:12px;display:flex;gap:0.75rem;align-items:flex-start;">
        <svg width="18" height="18" fill="none" stroke="#22d3ee" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p style="font-size:0.8125rem;color:#67e8f9;line-height:1.6;margin:0;">
            The reset link will be sent to your registered email address. Check your spam folder if you don't receive it within a few minutes.
        </p>
    </div>
</div>

<script>
document.getElementById('forgotForm').addEventListener('submit', function() {
    const btn = document.getElementById('sendBtn');
    btn.innerHTML = '<svg class="animate-spin" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Sending...';
    btn.disabled = true;
});
const style = document.createElement('style');
style.textContent = '@keyframes spin { to { transform: rotate(360deg); } } .animate-spin { animation: spin 1s linear infinite; }';
document.head.appendChild(style);
</script>
@endsection
