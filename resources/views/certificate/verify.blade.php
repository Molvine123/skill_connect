@extends('layouts.app')
@section('title', $valid ? 'Certificate Verified – ' . $certificate->enrollment->student->user->name : 'Certificate Not Found')
@section('page-title', 'Certificate Verification')

@section('content')
<div class="animate-fade-up" style="max-width:780px;margin:0 auto;padding:2rem 1rem 4rem;">

    {{-- ── Page Header ──────────────────────────────────────────── --}}
    <div style="text-align:center;margin-bottom:2.5rem;">
        <div style="display:inline-flex;align-items:center;gap:0.5rem;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:50px;padding:0.4rem 1.1rem;margin-bottom:1.25rem;">
            <span style="font-size:0.72rem;color:#94a3b8;letter-spacing:0.12em;text-transform:uppercase;font-weight:600;">SkillConnect · Credential Registry</span>
        </div>
        <h1 style="font-size:1.75rem;font-weight:800;color:#f1f5f9;margin:0 0 0.5rem;">Certificate Verification</h1>
        <p style="font-size:0.9rem;color:#64748b;margin:0;">Instant cryptographic authenticity check for SkillConnect-issued credentials</p>
    </div>

    @if($valid)
    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- VALID CERTIFICATE                                          --}}
    {{-- ══════════════════════════════════════════════════════════ --}}

    {{-- Status Banner --}}
    <div style="display:flex;align-items:center;gap:1rem;background:linear-gradient(135deg,rgba(16,185,129,0.12),rgba(5,150,105,0.06));border:1px solid rgba(16,185,129,0.3);border-radius:16px;padding:1.1rem 1.5rem;margin-bottom:1.75rem;">
        <div style="width:46px;height:46px;background:rgba(16,185,129,0.15);border:2px solid rgba(16,185,129,0.4);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.35rem;flex-shrink:0;">✅</div>
        <div>
            <div style="font-size:1.05rem;font-weight:800;color:#34d399;letter-spacing:0.02em;">Verified &amp; Authentic</div>
            <div style="font-size:0.8rem;color:#6ee7b7;margin-top:0.15rem;">This certificate is valid and was officially issued by SkillConnect.</div>
        </div>
        <div style="margin-left:auto;text-align:right;flex-shrink:0;">
            <div style="font-size:0.68rem;color:#34d399;text-transform:uppercase;letter-spacing:0.1em;font-weight:700;">Registry Status</div>
            <div style="font-size:0.78rem;color:#6ee7b7;margin-top:0.1rem;">✓ On-Chain Record Found</div>
        </div>
    </div>

    {{-- ── Certificate Showcase Card ────────────────────────────── --}}
    <div style="background:linear-gradient(160deg,#0f172a 0%,#1e1b4b 50%,#0f172a 100%);border:1px solid rgba(167,139,250,0.2);border-radius:20px;overflow:hidden;margin-bottom:1.5rem;position:relative;">

        {{-- Decorative radial glow --}}
        <div style="position:absolute;top:-80px;right:-60px;width:320px;height:320px;background:radial-gradient(circle,rgba(167,139,250,0.15),transparent 70%);pointer-events:none;"></div>
        <div style="position:absolute;bottom:-60px;left:-40px;width:260px;height:260px;background:radial-gradient(circle,rgba(16,185,129,0.08),transparent 70%);pointer-events:none;"></div>

        {{-- Certificate Header --}}
        <div style="position:relative;z-index:1;padding:2rem 2rem 1.5rem;border-bottom:1px solid rgba(255,255,255,0.05);">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
                <div>
                    <div style="font-size:0.65rem;letter-spacing:0.2em;text-transform:uppercase;color:#a78bfa;font-weight:700;margin-bottom:0.4rem;">✦ Official Credential ✦</div>
                    <div style="font-size:1.4rem;font-weight:900;color:#f1f5f9;letter-spacing:0.05em;text-transform:uppercase;">Certificate of Completion</div>
                    <div style="font-size:0.78rem;color:#64748b;margin-top:0.2rem;font-style:italic;">SkillConnect Excellence Award</div>
                </div>
                <div style="text-align:right;">
                    <div style="font-size:0.65rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.1em;">Certificate No.</div>
                    <div style="font-family:'Courier New',monospace;font-size:0.8rem;font-weight:700;color:#f59e0b;background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.3);padding:0.3rem 0.75rem;border-radius:8px;margin-top:0.3rem;letter-spacing:0.08em;">
                        {{ $certificate->verification_code }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Recipient Name (largest element) --}}
        <div style="position:relative;z-index:1;padding:2rem;text-align:center;border-bottom:1px solid rgba(255,255,255,0.04);">
            <div style="font-size:0.7rem;letter-spacing:0.25em;text-transform:uppercase;color:#94a3b8;margin-bottom:0.75rem;">— Presented to —</div>
            <div style="font-size:2.4rem;font-weight:900;color:#ffffff;letter-spacing:0.03em;line-height:1.1;text-shadow:0 0 40px rgba(167,139,250,0.3);">
                {{ strtoupper($certificate->enrollment->student->user->name) }}
            </div>
            <div style="width:200px;height:2px;background:linear-gradient(to right,transparent,rgba(167,139,250,0.6),transparent);margin:1rem auto 0;"></div>
            <div style="font-size:0.8rem;color:#64748b;margin-top:0.6rem;">
                for successfully completing
            </div>
        </div>

        {{-- Program + Org Info --}}
        <div style="position:relative;z-index:1;padding:1.5rem 2rem;border-bottom:1px solid rgba(255,255,255,0.04);">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
                <div>
                    <div style="font-size:0.65rem;color:#6b7280;text-transform:uppercase;letter-spacing:0.12em;margin-bottom:0.4rem;">Program Completed</div>
                    @php $prog = $certificate->enrollment->program; @endphp
                    <div style="font-size:0.7rem;color:#a78bfa;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:0.2rem;">
                        {{ $prog->category?->name ?? 'Training Program' }}
                    </div>
                    <div style="font-size:1.05rem;font-weight:700;color:#e2e8f0;line-height:1.3;">{{ $prog->name }}</div>
                </div>
                <div>
                    <div style="font-size:0.65rem;color:#6b7280;text-transform:uppercase;letter-spacing:0.12em;margin-bottom:0.4rem;">Issuing Organization</div>
                    <div style="display:flex;align-items:center;gap:0.6rem;margin-top:0.2rem;">
                        <div style="width:34px;height:34px;border-radius:8px;background:rgba(167,139,250,0.12);border:1px solid rgba(167,139,250,0.25);display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;">🏢</div>
                        <div>
                            <div style="font-size:0.95rem;font-weight:700;color:#e2e8f0;">{{ $prog->organization->name }}</div>
                            <div style="font-size:0.72rem;color:#64748b;">Authorized Training Provider</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bottom Meta: Date + QR --}}
        <div style="position:relative;z-index:1;padding:1.25rem 2rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
            <div style="display:flex;gap:2rem;flex-wrap:wrap;">
                <div>
                    <div style="font-size:0.65rem;color:#6b7280;text-transform:uppercase;letter-spacing:0.12em;margin-bottom:0.3rem;">📅 Issue Date</div>
                    <div style="font-size:0.95rem;font-weight:700;color:#e2e8f0;">{{ \Carbon\Carbon::parse($certificate->issue_date)->format('F d, Y') }}</div>
                    <div style="font-size:0.72rem;color:#64748b;">{{ \Carbon\Carbon::parse($certificate->issue_date)->diffForHumans() }}</div>
                </div>
                <div>
                    <div style="font-size:0.65rem;color:#6b7280;text-transform:uppercase;letter-spacing:0.12em;margin-bottom:0.3rem;">🔐 Verification Code</div>
                    <div style="font-family:'Courier New',monospace;font-size:0.78rem;font-weight:700;color:#f59e0b;">{{ $certificate->verification_code }}</div>
                    <div style="font-size:0.72rem;color:#64748b;margin-top:0.1rem;">Cryptographically signed</div>
                </div>
            </div>
            {{-- Real QR Code --}}
            <div style="text-align:center;flex-shrink:0;">
                <div class="qr-svg-container" style="width:100px;height:100px;background:#ffffff;border-radius:10px;padding:6px;display:inline-flex;align-items:center;justify-content:center;box-shadow:0 0 0 1px rgba(167,139,250,0.3),0 4px 16px rgba(0,0,0,0.4);">
                    {!! $qrCode !!}
                </div>
                <style>
                    .qr-svg-container svg {
                        width: 88px;
                        height: 88px;
                        display: block;
                    }
                </style>
                <div style="font-size:0.65rem;color:#64748b;text-transform:uppercase;letter-spacing:0.1em;margin-top:6px;">Scan to Verify</div>
            </div>
        </div>

    </div>{{-- end showcase card --}}

    {{-- ── Actions Row ──────────────────────────────────────────── --}}
    @php
        $studentName  = $certificate->enrollment->student->user->name;
        $programName  = $certificate->enrollment->program->name;
        $orgName      = $certificate->enrollment->program->organization->name;
        $verifyUrl    = url('/verify/certificate/' . $certificate->verification_code);
        $linkedInText = urlencode("🏆 I just earned a certificate in \"{$programName}\" from {$orgName} via SkillConnect! Verify it here: {$verifyUrl}");
        $twitterText  = urlencode("🎓 Just completed \"{$programName}\" and earned a verified certificate from {$orgName} on @SkillConnect! ✅ {$verifyUrl}");
        $linkedInUrl  = "https://www.linkedin.com/sharing/share-offsite/?url=" . urlencode($verifyUrl);
        $twitterUrl   = "https://twitter.com/intent/tweet?text={$twitterText}";
    @endphp

    <div style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.06);border-radius:16px;padding:1.5rem;margin-bottom:1.5rem;">
        <div style="font-size:0.75rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.12em;margin-bottom:1rem;">📣 Share This Achievement</div>
        <div style="display:flex;flex-wrap:wrap;gap:0.75rem;align-items:center;">

            {{-- LinkedIn --}}
            <a href="{{ $linkedInUrl }}" target="_blank" rel="noopener"
               id="share-linkedin"
               style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.6rem 1.2rem;border-radius:10px;font-size:0.83rem;font-weight:700;text-decoration:none;background:rgba(10,102,194,0.15);border:1px solid rgba(10,102,194,0.35);color:#60a5fa;transition:all 0.15s;"
               onmouseover="this.style.background='rgba(10,102,194,0.28)';this.style.borderColor='rgba(10,102,194,0.6)'"
               onmouseout="this.style.background='rgba(10,102,194,0.15)';this.style.borderColor='rgba(10,102,194,0.35)'">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                Share on LinkedIn
            </a>

            {{-- Twitter / X --}}
            <a href="{{ $twitterUrl }}" target="_blank" rel="noopener"
               id="share-twitter"
               style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.6rem 1.2rem;border-radius:10px;font-size:0.83rem;font-weight:700;text-decoration:none;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.12);color:#e2e8f0;transition:all 0.15s;"
               onmouseover="this.style.background='rgba(255,255,255,0.1)';this.style.borderColor='rgba(255,255,255,0.25)'"
               onmouseout="this.style.background='rgba(255,255,255,0.05)';this.style.borderColor='rgba(255,255,255,0.12)'">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.748l7.73-8.835L1.254 2.25H8.08l4.261 5.632 5.903-5.632zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                Post on X
            </a>

            {{-- Copy Link --}}
            <button id="copy-link-btn"
               onclick="copyVerifyLink()"
               style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.6rem 1.2rem;border-radius:10px;font-size:0.83rem;font-weight:700;cursor:pointer;background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.28);color:#34d399;transition:all 0.15s;"
               onmouseover="this.style.background='rgba(16,185,129,0.2)';this.style.borderColor='rgba(16,185,129,0.5)'"
               onmouseout="this.style.background='rgba(16,185,129,0.1)';this.style.borderColor='rgba(16,185,129,0.28)'">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                <span id="copy-btn-text">Copy Verify Link</span>
            </button>

            {{-- Download PDF (if auth and student/org owns it) --}}
            <a href="{{ url('/verify/certificate/'.$certificate->verification_code) }}"
               style="margin-left:auto;display:inline-flex;align-items:center;gap:0.5rem;padding:0.6rem 1.2rem;border-radius:10px;font-size:0.83rem;font-weight:700;text-decoration:none;background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.28);color:#f59e0b;transition:all 0.15s;"
               onmouseover="this.style.background='rgba(245,158,11,0.2)';this.style.borderColor='rgba(245,158,11,0.5)'"
               onmouseout="this.style.background='rgba(245,158,11,0.1)';this.style.borderColor='rgba(245,158,11,0.28)'">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                This Page
            </a>
        </div>
    </div>

    {{-- ── Trust Indicators ─────────────────────────────────────── --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;">
        @foreach([
            ['🔐', 'Tamper-Proof', 'Secured with a unique cryptographic verification code'],
            ['📅', 'Timestamped', 'Issue date permanently recorded in our registry'],
            ['🌐', 'Publicly Verifiable', 'Anyone can confirm authenticity via this URL'],
        ] as [$icon, $title, $desc])
        <div style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.05);border-radius:12px;padding:1rem;text-align:center;">
            <div style="font-size:1.5rem;margin-bottom:0.5rem;">{{ $icon }}</div>
            <div style="font-size:0.8rem;font-weight:700;color:#e2e8f0;margin-bottom:0.25rem;">{{ $title }}</div>
            <div style="font-size:0.72rem;color:#64748b;line-height:1.5;">{{ $desc }}</div>
        </div>
        @endforeach
    </div>

    @else
    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- INVALID CERTIFICATE                                        --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div style="background:linear-gradient(135deg,rgba(127,29,29,0.2),rgba(153,27,27,0.08));border:1px solid rgba(239,68,68,0.25);border-radius:20px;padding:2.5rem;text-align:center;margin-bottom:1.5rem;">
        <div style="width:72px;height:72px;background:rgba(239,68,68,0.12);border:2px solid rgba(239,68,68,0.3);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:2rem;margin:0 auto 1.25rem;">❌</div>
        <h2 style="font-size:1.5rem;font-weight:800;color:#f87171;margin:0 0 0.5rem;">Verification Failed</h2>
        <p style="font-size:0.9rem;color:#94a3b8;margin:0 0 1.5rem;">
            No valid certificate was found for code <code style="font-family:'Courier New',monospace;color:#f59e0b;background:rgba(245,158,11,0.1);padding:0.15rem 0.5rem;border-radius:5px;">{{ $code }}</code>
        </p>
        <div style="background:rgba(0,0,0,0.2);border:1px solid rgba(239,68,68,0.15);border-radius:12px;padding:1.25rem;text-align:left;max-width:420px;margin:0 auto 2rem;">
            <div style="font-size:0.75rem;font-weight:700;color:#f87171;margin-bottom:0.75rem;text-transform:uppercase;letter-spacing:0.1em;">Possible Reasons</div>
            @foreach([
                'The certificate code was typed incorrectly.',
                'The certificate may have been revoked or invalidated.',
                'This certificate was not issued through SkillConnect.',
                'The document may be a forgery or altered copy.',
            ] as $reason)
            <div style="display:flex;align-items:flex-start;gap:0.6rem;padding:0.4rem 0;border-bottom:1px solid rgba(239,68,68,0.08);">
                <span style="color:#f87171;flex-shrink:0;font-size:0.8rem;">⚠</span>
                <span style="font-size:0.82rem;color:#94a3b8;">{{ $reason }}</span>
            </div>
            @endforeach
        </div>
        <a href="{{ route('home') }}"
           style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.7rem 1.5rem;border-radius:10px;font-size:0.85rem;font-weight:700;text-decoration:none;background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.12);color:#e2e8f0;">
            ← Return Home
        </a>
    </div>
    @endif

</div>

<script>
function copyVerifyLink() {
    const url = '{{ url('/verify/certificate/' . ($valid ? $certificate->verification_code : '')) }}';
    navigator.clipboard.writeText(url).then(() => {
        const btn  = document.getElementById('copy-link-btn');
        const text = document.getElementById('copy-btn-text');
        const orig = text.textContent;
        text.textContent = '✓ Copied!';
        btn.style.background = 'rgba(16,185,129,0.25)';
        btn.style.borderColor = 'rgba(16,185,129,0.6)';
        setTimeout(() => {
            text.textContent = orig;
            btn.style.background = 'rgba(16,185,129,0.1)';
            btn.style.borderColor = 'rgba(16,185,129,0.28)';
        }, 2500);
    });
}
</script>
@endsection
