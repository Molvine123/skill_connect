@extends('layouts.auth')
@section('title', 'Login')

@section('content')
<div class="animate-fade-up">
    {{-- Logo (mobile) --}}
    <div class="auth-logo" style="margin-bottom:2rem;">
        <div class="auth-logo-icon">SC</div>
        <span class="auth-logo-text">SkillConnect</span>
    </div>

    <h2 style="font-size:1.75rem;font-weight:700;color:#f1f5f9;margin-bottom:0.375rem;">Welcome back</h2>
    <p style="color:#6b7280;font-size:0.9375rem;margin-bottom:2rem;">Sign in to your account to continue</p>

    {{-- Errors --}}
    @if($errors->any())
    <div class="alert alert-error">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div>{{ $errors->first() }}</div>
    </div>
    @endif

    @if(session('success'))
    <div class="alert alert-success">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div>{{ session('success') }}</div>
    </div>
    @endif

    <form method="POST" action="{{ route('login') }}" id="loginForm">
        @csrf

        {{-- Email --}}
        <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <div class="input-icon-wrap">
                <svg class="input-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
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

        {{-- Password --}}
        <div class="form-group">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.5rem;">
                <label for="password" class="form-label" style="margin-bottom:0;">Password</label>
                <a href="{{ route('password.request') }}" style="font-size:0.8125rem;color:var(--sc-primary);text-decoration:none;font-weight:500;">Forgot password?</a>
            </div>
            <div class="input-icon-wrap" style="position:relative;">
                <svg class="input-icon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                    placeholder="••••••••"
                    autocomplete="current-password"
                    required
                    style="padding-right:3rem;"
                >
                <button type="button" class="pw-toggle" onclick="togglePassword('password', this)" title="Show/hide password">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>
            @error('password') <div class="field-error">{{ $message }}</div> @enderror
        </div>

        {{-- Remember Me --}}
        <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:1.75rem;">
            <input type="checkbox" id="remember" name="remember" style="width:16px;height:16px;accent-color:var(--sc-primary);cursor:pointer;">
            <label for="remember" style="font-size:0.875rem;color:#9ca3af;cursor:pointer;">Remember me for 30 days</label>
        </div>

        <button type="submit" class="btn btn-primary btn-full" id="loginBtn">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
            Sign In
        </button>
    </form>

    <p style="text-align:center;margin-top:1.75rem;font-size:0.9rem;color:#6b7280;">
        Don't have an account?
        <a href="{{ route('register') }}" style="color:var(--sc-primary);font-weight:600;text-decoration:none;margin-left:0.25rem;">Create account</a>
    </p>


</div>

<script>


document.getElementById('loginForm').addEventListener('submit', function() {
    const btn = document.getElementById('loginBtn');
    btn.innerHTML = '<svg class="animate-spin" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Signing in...';
    btn.disabled = true;
});

const style = document.createElement('style');
style.textContent = '@keyframes spin { to { transform: rotate(360deg); } } .animate-spin { animation: spin 1s linear infinite; }';
document.head.appendChild(style);
</script>
@endsection
