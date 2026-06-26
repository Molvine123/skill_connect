@extends('layouts.app')
@section('title', 'My Certificates')
@section('page-title', 'My Certificates')

@section('content')

{{-- Header --}}
<div style="background:linear-gradient(135deg,#0f0f1a 0%,#1a1a35 60%,#16213e 100%);border:1px solid rgba(99,102,241,0.25);border-radius:20px;padding:2rem;margin-bottom:2rem;position:relative;overflow:hidden;" class="animate-fade-up">
    <div style="position:absolute;top:-40px;right:-40px;width:220px;height:220px;background:radial-gradient(circle,rgba(245,158,11,0.2),transparent 70%);pointer-events:none;"></div>
    <div style="position:relative;">
        <h1 style="font-size:1.75rem;font-weight:800;color:#f1f5f9;margin-bottom:0.375rem;">🏆 My Certificates</h1>
        <p style="color:#94a3b8;font-size:0.9375rem;">Official proof of your completed training programs on SkillConnect.</p>
    </div>
</div>

{{-- Certificates Grid --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:1.5rem;" class="animate-fade-up">
    @forelse($certificates as $cert)
    <div style="background:linear-gradient(135deg,#1a1a35 0%,#16213e 100%);border:1px solid rgba(245,158,11,0.3);border-radius:18px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.3);position:relative;transition:transform 0.25s,box-shadow 0.25s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 18px 40px rgba(0,0,0,0.45)'" onmouseout="this.style.transform='';this.style.boxShadow='0 4px 24px rgba(0,0,0,0.3)'">
        
        {{-- Gold band top --}}
        <div style="height:6px;background:linear-gradient(90deg,#f59e0b,#fcd34d,#d97706);"></div>

        {{-- Decorative seal --}}
        <div style="position:absolute;top:16px;right:16px;width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,#f59e0b,#d97706);opacity:0.15;"></div>
        <div style="position:absolute;top:20px;right:20px;width:48px;height:48px;border-radius:50%;border:2px solid rgba(245,158,11,0.4);display:flex;align-items:center;justify-content:center;font-size:1.4rem;">🏅</div>

        <div style="padding:1.5rem;">
            {{-- Program Name --}}
            <div style="font-size:0.75rem;color:#f59e0b;text-transform:uppercase;letter-spacing:0.06em;font-weight:700;margin-bottom:0.5rem;">Certificate of Completion</div>
            <h3 style="font-size:1.05rem;font-weight:700;color:#f1f5f9;margin-bottom:0.25rem;line-height:1.4;">{{ $cert->enrollment?->program?->name }}</h3>
            <p style="font-size:0.8rem;color:#6b7280;margin-bottom:1.25rem;">by {{ $cert->enrollment?->program?->organization?->name }}</p>

            {{-- Cert Number --}}
            <div style="padding:0.875rem;background:rgba(245,158,11,0.06);border:1px solid rgba(245,158,11,0.18);border-radius:10px;margin-bottom:1.25rem;">
                <div style="font-size:0.7rem;color:#9ca3af;margin-bottom:0.25rem;">Certificate Number</div>
                <div style="font-family:monospace;font-size:1rem;font-weight:700;color:#fbbf24;letter-spacing:0.08em;">{{ $cert->certificate_number }}</div>
            </div>

            {{-- Dates --}}
            <div style="display:flex;gap:1rem;justify-content:space-between;font-size:0.8rem;color:#9ca3af;margin-bottom:1.25rem;">
                <div>
                    <div style="color:#6b7280;font-size:0.72rem;margin-bottom:0.15rem;">Issued On</div>
                    <div style="color:#e2e8f0;font-weight:600;">{{ $cert->issued_at?->format('d M Y') ?? '—' }}</div>
                </div>
                @if($cert->expires_at)
                <div style="text-align:right;">
                    <div style="color:#6b7280;font-size:0.72rem;margin-bottom:0.15rem;">Expires</div>
                    <div style="color:{{ $cert->expires_at->isPast() ? '#f87171' : '#e2e8f0' }};font-weight:600;">
                        {{ $cert->expires_at->format('d M Y') }}
                        @if($cert->expires_at->isPast()) <span style="color:#f87171;">(Expired)</span> @endif
                    </div>
                </div>
                @else
                <div style="text-align:right;">
                    <div style="color:#6b7280;font-size:0.72rem;margin-bottom:0.15rem;">Validity</div>
                    <div style="color:#34d399;font-weight:600;">No Expiry</div>
                </div>
                @endif
            </div>

            {{-- Actions --}}
            <div style="display:flex;gap:0.625rem;">
                @if($cert->pdf_path)
                <a href="{{ route('student.certificates.download', $cert->id) }}" class="btn btn-primary" style="flex:1;background:linear-gradient(135deg,#f59e0b,#d97706);border:none;font-size:0.8125rem;text-align:center;color:#1f2937;font-weight:700;box-shadow:0 4px 12px rgba(245,158,11,0.4);">
                    ⬇ Download PDF
                </a>
                @endif
                <a href="{{ route('student.certificates.verify', $cert->certificate_number) }}" target="_blank" class="btn btn-outline" style="flex:1;font-size:0.8125rem;text-align:center;border-color:rgba(245,158,11,0.35);color:#f59e0b;">
                    🔍 Verify
                </a>
            </div>
        </div>
    </div>
    @empty
    <div style="grid-column:1/-1;text-align:center;padding:4rem 2rem;" class="coming-soon-card">
        <span class="coming-soon-icon">🏆</span>
        <h3 style="font-size:1.125rem;font-weight:700;color:#e2e8f0;margin-bottom:0.5rem;">No Certificates Yet</h3>
        <p style="color:#6b7280;font-size:0.875rem;max-width:350px;margin:0 auto 1.25rem;">Complete a training program to earn your first official certificate.</p>
        <a href="{{ route('student.programs.index') }}" class="btn btn-primary btn-sm">Browse Programs</a>
    </div>
    @endforelse
</div>

@endsection
