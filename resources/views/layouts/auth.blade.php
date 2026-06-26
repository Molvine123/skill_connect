<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SkillConnect — Kenya's centralized skills training platform connecting students, institutions, and organizations.">
    <title>@yield('title', 'SkillConnect') — Skills Training Platform</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

<div class="auth-page">
    {{-- ── Left Panel ───────────────────────────────────────── --}}
    <div class="auth-left">
        {{-- Decorative orbs --}}
        <div class="orb" style="width:400px;height:400px;background:radial-gradient(circle,rgba(99,102,241,0.2),transparent 70%);top:-100px;left:-100px;"></div>
        <div class="orb" style="width:300px;height:300px;background:radial-gradient(circle,rgba(139,92,246,0.15),transparent 70%);bottom:-50px;right:-50px;"></div>
        <div class="orb" style="width:200px;height:200px;background:radial-gradient(circle,rgba(6,182,212,0.12),transparent 70%);bottom:200px;left:50px;"></div>

        <div style="position:relative;z-index:1;max-width:480px;">
            {{-- Logo --}}
            <div class="auth-logo" style="margin-bottom:3rem;">
                <div class="auth-logo-icon">SC</div>
                <span class="auth-logo-text">SkillConnect</span>
            </div>

            {{-- Headline --}}
            <h1 style="font-size:2.75rem;font-weight:800;line-height:1.15;color:#f1f5f9;margin-bottom:1.25rem;">
                Kenya's National<br>
                <span style="background:linear-gradient(135deg,#6366f1,#8b5cf6,#06b6d4);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Skills Ecosystem</span>
            </h1>

            <p style="font-size:1.0625rem;color:#94a3b8;line-height:1.7;margin-bottom:2.5rem;">
                Connecting students, institutions, and organizations through structured training programs, attendance tracking, and digital certification.
            </p>

            {{-- Feature pills --}}
            <div style="display:flex;flex-wrap:wrap;gap:0.75rem;margin-bottom:3rem;">
                <div class="feature-pill">
                    <div class="feature-pill-dot" style="background:#6366f1;"></div>
                    Student Enrollment
                </div>
                <div class="feature-pill">
                    <div class="feature-pill-dot" style="background:#8b5cf6;"></div>
                    Skills Programs
                </div>
                <div class="feature-pill">
                    <div class="feature-pill-dot" style="background:#06b6d4;"></div>
                    Digital Certificates
                </div>
                <div class="feature-pill">
                    <div class="feature-pill-dot" style="background:#10b981;"></div>
                    Attendance Tracking
                </div>
                <div class="feature-pill">
                    <div class="feature-pill-dot" style="background:#f59e0b;"></div>
                    M-Pesa Payments
                </div>
            </div>

            {{-- Stats row --}}
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1.5rem;">
                <div style="text-align:center;padding:1.25rem;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:14px;">
                    <div style="font-size:1.875rem;font-weight:800;color:#818cf8;">50+</div>
                    <div style="font-size:0.8125rem;color:#6b7280;margin-top:0.25rem;">Institutions</div>
                </div>
                <div style="text-align:center;padding:1.25rem;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:14px;">
                    <div style="font-size:1.875rem;font-weight:800;color:#a78bfa;">200+</div>
                    <div style="font-size:0.8125rem;color:#6b7280;margin-top:0.25rem;">Skill Programs</div>
                </div>
                <div style="text-align:center;padding:1.25rem;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);border-radius:14px;">
                    <div style="font-size:1.875rem;font-weight:800;color:#22d3ee;">5K+</div>
                    <div style="font-size:0.8125rem;color:#6b7280;margin-top:0.25rem;">Students</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Right Panel (Form) ───────────────────────────────── --}}
    <div class="auth-right">
        @yield('content')
    </div>
</div>

<script>
// Password toggle utility
function togglePassword(id, btn) {
    const input = document.getElementById(id);
    const icon  = btn.querySelector('svg');
    if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';
    } else {
        input.type = 'password';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
    }
}
</script>
</body>
</html>
