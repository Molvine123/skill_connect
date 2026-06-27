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
    @php
        $progName   = $cert->enrollment?->program?->name ?? '';
        $orgName    = $cert->enrollment?->program?->organization?->name ?? '';
        $verifyUrl  = url('/verify/certificate/' . $cert->verification_code);
        $linkedInUrl = "https://www.linkedin.com/sharing/share-offsite/?url=" . urlencode($verifyUrl);
        $twitterText = urlencode("🎓 Just earned a certificate in \"{$progName}\" from {$orgName} via @SkillConnect! ✅ {$verifyUrl}");
        $twitterUrl  = "https://twitter.com/intent/tweet?text={$twitterText}";
    @endphp
    <div style="background:linear-gradient(135deg,#1a1a35 0%,#16213e 100%);border:1px solid rgba(245,158,11,0.3);border-radius:18px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.3);position:relative;transition:transform 0.25s,box-shadow 0.25s;display:flex;flex-direction:column;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 18px 40px rgba(0,0,0,0.45)'" onmouseout="this.style.transform='';this.style.boxShadow='0 4px 24px rgba(0,0,0,0.3)'">

        {{-- Gold band top --}}
        <div style="height:6px;background:linear-gradient(90deg,#f59e0b,#fcd34d,#d97706);flex-shrink:0;"></div>

        {{-- Decorative seal --}}
        <div style="position:absolute;top:16px;right:16px;width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,#f59e0b,#d97706);opacity:0.15;"></div>
        <div style="position:absolute;top:20px;right:20px;width:48px;height:48px;border-radius:50%;border:2px solid rgba(245,158,11,0.4);display:flex;align-items:center;justify-content:center;font-size:1.4rem;">🏅</div>

        <div style="padding:1.5rem;flex:1;display:flex;flex-direction:column;">
            {{-- Program Name --}}
            <div style="font-size:0.75rem;color:#f59e0b;text-transform:uppercase;letter-spacing:0.06em;font-weight:700;margin-bottom:0.5rem;">Certificate of Completion</div>
            <h3 style="font-size:1.05rem;font-weight:700;color:#f1f5f9;margin-bottom:0.25rem;line-height:1.4;">{{ $progName }}</h3>
            <p style="font-size:0.8rem;color:#6b7280;margin-bottom:1.25rem;">by {{ $orgName }}</p>

            {{-- Cert Number --}}
            <div style="padding:0.875rem;background:rgba(245,158,11,0.06);border:1px solid rgba(245,158,11,0.18);border-radius:10px;margin-bottom:1.25rem;">
                <div style="font-size:0.7rem;color:#9ca3af;margin-bottom:0.25rem;">Certificate Number</div>
                <div style="font-family:monospace;font-size:1rem;font-weight:700;color:#fbbf24;letter-spacing:0.08em;">{{ $cert->certificate_number }}</div>
            </div>

            {{-- Dates --}}
            <div style="display:flex;gap:1rem;justify-content:space-between;font-size:0.8rem;margin-bottom:1.25rem;">
                <div>
                    <div style="color:#6b7280;font-size:0.72rem;margin-bottom:0.15rem;">Issued On</div>
                    <div style="color:#e2e8f0;font-weight:600;">{{ $cert->issue_date?->format('d M Y') ?? '—' }}</div>
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

            {{-- Primary Actions --}}
            <div style="display:flex;gap:0.625rem;margin-bottom:0.75rem;">
                <a href="{{ route('student.certificates.download', $cert->id) }}"
                   style="flex:1;background:linear-gradient(135deg,#f59e0b,#d97706);font-size:0.8125rem;text-align:center;color:#1f2937;font-weight:700;box-shadow:0 4px 12px rgba(245,158,11,0.4);display:flex;align-items:center;justify-content:center;gap:0.35rem;text-decoration:none;padding:0.65rem 0.5rem;border-radius:10px;border:none;transition:opacity 0.15s;"
                   onmouseover="this.style.opacity='0.88'" onmouseout="this.style.opacity='1'">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Download PDF
                </a>
                <a href="{{ route('certificates.verify', $cert->verification_code) }}" target="_blank"
                   style="flex:1;font-size:0.8125rem;text-align:center;display:flex;align-items:center;justify-content:center;gap:0.35rem;text-decoration:none;padding:0.65rem 0.5rem;border-radius:10px;background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.3);color:#f59e0b;transition:all 0.15s;"
                   onmouseover="this.style.background='rgba(245,158,11,0.18)'" onmouseout="this.style.background='rgba(245,158,11,0.08)'">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Verify
                </a>
            </div>

            {{-- Share Row --}}
            <div style="border-top:1px solid rgba(255,255,255,0.05);padding-top:0.75rem;">
                <div style="font-size:0.65rem;color:#64748b;text-transform:uppercase;letter-spacing:0.12em;font-weight:600;margin-bottom:0.5rem;">📣 Share Achievement</div>
                <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
                    <a href="{{ $linkedInUrl }}" target="_blank" rel="noopener"
                       style="display:inline-flex;align-items:center;gap:0.3rem;padding:0.35rem 0.7rem;border-radius:8px;font-size:0.72rem;font-weight:600;text-decoration:none;background:rgba(10,102,194,0.12);border:1px solid rgba(10,102,194,0.3);color:#60a5fa;transition:all 0.15s;"
                       onmouseover="this.style.background='rgba(10,102,194,0.25)'" onmouseout="this.style.background='rgba(10,102,194,0.12)'">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                        LinkedIn
                    </a>
                    <a href="{{ $twitterUrl }}" target="_blank" rel="noopener"
                       style="display:inline-flex;align-items:center;gap:0.3rem;padding:0.35rem 0.7rem;border-radius:8px;font-size:0.72rem;font-weight:600;text-decoration:none;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.1);color:#94a3b8;transition:all 0.15s;"
                       onmouseover="this.style.background='rgba(255,255,255,0.1)';this.style.color='#e2e8f0'" onmouseout="this.style.background='rgba(255,255,255,0.04)';this.style.color='#94a3b8'">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.748l7.73-8.835L1.254 2.25H8.08l4.261 5.632 5.903-5.632zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        X / Twitter
                    </a>
                    <button onclick="copyLink_{{ $cert->id }}()"
                            id="copy-btn-{{ $cert->id }}"
                            style="display:inline-flex;align-items:center;gap:0.3rem;padding:0.35rem 0.7rem;border-radius:8px;font-size:0.72rem;font-weight:600;cursor:pointer;background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.25);color:#34d399;transition:all 0.15s;"
                            onmouseover="this.style.background='rgba(16,185,129,0.18)'" onmouseout="this.style.background='rgba(16,185,129,0.08)'">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                        <span id="copy-text-{{ $cert->id }}">Copy Link</span>
                    </button>
                </div>
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

@push('scripts')
<script>
@foreach($certificates as $cert)
function copyLink_{{ $cert->id }}() {
    navigator.clipboard.writeText('{{ url('/verify/certificate/' . $cert->verification_code) }}').then(() => {
        const btn = document.getElementById('copy-btn-{{ $cert->id }}');
        const txt = document.getElementById('copy-text-{{ $cert->id }}');
        txt.textContent = '✓ Copied!';
        btn.style.background = 'rgba(16,185,129,0.25)';
        btn.style.borderColor = 'rgba(16,185,129,0.6)';
        setTimeout(() => {
            txt.textContent = 'Copy Link';
            btn.style.background = 'rgba(16,185,129,0.08)';
            btn.style.borderColor = 'rgba(16,185,129,0.25)';
        }, 2500);
    });
}
@endforeach
</script>
@endpush

@endsection
