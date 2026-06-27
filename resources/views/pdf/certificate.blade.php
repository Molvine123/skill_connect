<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificate of Completion – {{ $enrollment->student->user->name }}</title>
    <style>
        @page {
            margin: 0px;
            size: A4 landscape;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            width: 297mm;
            height: 210mm;
            font-family: 'Georgia', 'Times New Roman', serif;
            background: #fdfcf7;
            color: #1a1a2e;
            position: relative;
            overflow: hidden;
        }

        /* ── Parchment Background ───────────────────── */
        .canvas {
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse at 20% 20%, rgba(212,175,55,0.07) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 80%, rgba(212,175,55,0.06) 0%, transparent 55%),
                radial-gradient(ellipse at 50% 50%, rgba(245,240,228,0.95) 0%, #fdfcf7 100%);
        }

        /* ── Outer Border Frame ──────────────────────── */
        .border-outer {
            position: absolute;
            inset: 14px;
            border: 3px solid #1a1a4e;
        }
        .border-inner {
            position: absolute;
            inset: 20px;
            border: 1px solid rgba(212,175,55,0.65);
        }
        .border-accent {
            position: absolute;
            inset: 23px;
            border: 1px solid rgba(212,175,55,0.25);
        }

        /* ── Corner Ornament Blocks ──────────────────── */
        .corner {
            position: absolute;
            width: 42px;
            height: 42px;
        }
        .corner-tl { top: 10px; left: 10px; }
        .corner-tr { top: 10px; right: 10px; transform: scaleX(-1); }
        .corner-bl { bottom: 10px; left: 10px; transform: scaleY(-1); }
        .corner-br { bottom: 10px; right: 10px; transform: scale(-1); }

        /* ── Main Layout ─────────────────────────────── */
        .content-wrap {
            position: absolute;
            inset: 30px 38px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
        }

        /* ── Top Row (Logo + Date) ───────────────────── */
        .top-row {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 6px 0 0 0;
        }
        .org-logo-box {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .org-logo-seal {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1a1a4e 0%, #2d2d80 100%);
            border: 2px solid rgba(212,175,55,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #d4af37;
        }
        .org-name-top {
            font-family: 'Georgia', serif;
            font-size: 11px;
            font-weight: bold;
            color: #1a1a4e;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            line-height: 1.4;
        }
        .org-type-top {
            font-size: 9px;
            color: #6b6b8a;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 2px;
        }
        .issue-date-box {
            text-align: right;
        }
        .issue-date-label {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #9a8a70;
        }
        .issue-date-val {
            font-size: 12px;
            color: #1a1a4e;
            font-weight: bold;
            margin-top: 2px;
        }

        /* ── Certificate Title Block ─────────────────── */
        .title-block {
            text-align: center;
            padding: 4px 0;
        }
        .cert-type-label {
            font-size: 9px;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: #d4af37;
            margin-bottom: 5px;
            font-family: 'Georgia', serif;
        }
        .cert-title {
            font-family: 'Georgia', 'Times New Roman', serif;
            font-size: 30px;
            font-weight: bold;
            color: #1a1a4e;
            text-transform: uppercase;
            letter-spacing: 7px;
            line-height: 1.1;
        }
        .title-rule {
            width: 220px;
            height: 1px;
            background: linear-gradient(to right, transparent, #d4af37, transparent);
            margin: 8px auto 4px;
        }
        .cert-subtitle {
            font-size: 11px;
            color: #9a8a70;
            font-style: italic;
            letter-spacing: 2px;
        }

        /* ── Recipient Block ─────────────────────────── */
        .recipient-block {
            text-align: center;
            padding: 2px 0;
        }
        .presented-to {
            font-size: 11px;
            color: #6b6b8a;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 6px;
            font-family: 'Georgia', serif;
        }
        .recipient-name {
            font-family: 'Georgia', 'Times New Roman', serif;
            font-size: 40px;
            font-weight: bold;
            color: #0d0d1a;
            letter-spacing: 2px;
            line-height: 1.1;
        }
        .recipient-underline {
            width: 380px;
            height: 1.5px;
            background: linear-gradient(to right, transparent, #1a1a4e 20%, #1a1a4e 80%, transparent);
            margin: 5px auto 0;
        }

        /* ── Action Clause ───────────────────────────── */
        .action-block {
            text-align: center;
        }
        .action-text {
            font-size: 12px;
            color: #4b4b6a;
            line-height: 1.8;
            letter-spacing: 0.3px;
            font-family: 'Georgia', serif;
        }
        .program-name {
            font-size: 15px;
            font-weight: bold;
            color: #0d5c3a;
            letter-spacing: 0.5px;
        }
        .field-label {
            font-size: 10px;
            color: #9a8a70;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-top: 3px;
        }

        /* ── Bottom Third (Validation Anchors) ───────── */
        .bottom-row {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding-bottom: 6px;
        }

        /* Left: Cert No + URL */
        .cert-meta {
            text-align: left;
            min-width: 160px;
        }
        .cert-no-badge {
            display: inline-block;
            font-family: 'Courier New', monospace;
            font-size: 9px;
            font-weight: bold;
            color: #b8860b;
            background: rgba(212,175,55,0.12);
            border: 1px solid rgba(212,175,55,0.4);
            padding: 2px 7px;
            border-radius: 4px;
            letter-spacing: 0.5px;
        }
        .cert-verify-url {
            font-size: 7.5px;
            color: #9a8a70;
            margin-top: 4px;
            word-break: break-all;
            max-width: 160px;
        }

        /* Center: Signature */
        .sig-block {
            text-align: center;
            flex: 1;
            padding: 0 20px;
        }
        .sig-line {
            width: 200px;
            height: 1px;
            background: #1a1a4e;
            margin: 0 auto 6px;
        }
        .sig-name {
            font-size: 11px;
            font-weight: bold;
            color: #1a1a4e;
            letter-spacing: 1px;
        }
        .sig-title {
            font-size: 9px;
            color: #6b6b8a;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-top: 2px;
        }

        /* Right: QR Code */
        .qr-block {
            text-align: center;
            min-width: 110px;
        }
        .qr-wrapper {
            display: inline-block;
            background: #ffffff;
            border: 2px solid rgba(212,175,55,0.6);
            border-radius: 6px;
            padding: 5px;
            margin: 0 auto 5px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.15);
        }
        .qr-block img, .qr-block svg {
            width: 90px;
            height: 90px;
            display: block;
        }
        .qr-label {
            font-size: 7.5px;
            color: #9a8a70;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        .qr-url {
            font-size: 6px;
            color: #b8a890;
            margin-top: 2px;
            word-break: break-all;
            max-width: 110px;
        }

        /* ── Gold Divider Lines ──────────────────────── */
        .gold-rule {
            width: 60%;
            height: 1px;
            background: linear-gradient(to right, transparent, rgba(212,175,55,0.5), transparent);
            margin: 0 auto;
        }
    </style>
</head>
<body>

    <!-- Parchment Canvas -->
    <div class="canvas"></div>

    <!-- Decorative SVG Border Frame -->
    <div class="border-outer"></div>
    <div class="border-inner"></div>
    <div class="border-accent"></div>

    <!-- Corner Ornaments (SVG inline) -->
    <svg class="corner corner-tl" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M2 40 L2 2 L40 2" stroke="#1a1a4e" stroke-width="2.5" fill="none"/>
        <path d="M8 36 L8 8 L36 8" stroke="#d4af37" stroke-width="1" fill="none"/>
        <circle cx="8" cy="8" r="3" fill="#d4af37" opacity="0.7"/>
        <circle cx="2" cy="2" r="2" fill="#1a1a4e"/>
    </svg>
    <svg class="corner corner-tr" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M2 40 L2 2 L40 2" stroke="#1a1a4e" stroke-width="2.5" fill="none"/>
        <path d="M8 36 L8 8 L36 8" stroke="#d4af37" stroke-width="1" fill="none"/>
        <circle cx="8" cy="8" r="3" fill="#d4af37" opacity="0.7"/>
        <circle cx="2" cy="2" r="2" fill="#1a1a4e"/>
    </svg>
    <svg class="corner corner-bl" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M2 40 L2 2 L40 2" stroke="#1a1a4e" stroke-width="2.5" fill="none"/>
        <path d="M8 36 L8 8 L36 8" stroke="#d4af37" stroke-width="1" fill="none"/>
        <circle cx="8" cy="8" r="3" fill="#d4af37" opacity="0.7"/>
        <circle cx="2" cy="2" r="2" fill="#1a1a4e"/>
    </svg>
    <svg class="corner corner-br" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M2 40 L2 2 L40 2" stroke="#1a1a4e" stroke-width="2.5" fill="none"/>
        <path d="M8 36 L8 8 L36 8" stroke="#d4af37" stroke-width="1" fill="none"/>
        <circle cx="8" cy="8" r="3" fill="#d4af37" opacity="0.7"/>
        <circle cx="2" cy="2" r="2" fill="#1a1a4e"/>
    </svg>

    <!-- Main Content -->
    <div class="content-wrap">

        <!-- Top Row: Org Identity + Issue Date -->
        <div class="top-row">
            <div class="org-logo-box">
                <div class="org-logo-seal">🏢</div>
                <div>
                    <div class="org-name-top">{{ $program->organization->name }}</div>
                    <div class="org-type-top">Issuing Authority</div>
                </div>
            </div>
            <div class="issue-date-box">
                <div class="issue-date-label">Date of Issue</div>
                <div class="issue-date-val">{{ \Carbon\Carbon::parse($certificate->issue_date)->format('F d, Y') }}</div>
            </div>
        </div>

        <!-- Certificate Title -->
        <div class="title-block">
            <div class="cert-type-label">✦ Official Credential ✦</div>
            <div class="cert-title">Certificate of Completion</div>
            <div class="title-rule"></div>
            <div class="cert-subtitle">SkillConnect Excellence Award</div>
        </div>

        <!-- Recipient -->
        <div class="recipient-block">
            <div class="presented-to">— Presented to —</div>
            <div class="recipient-name">{{ strtoupper($enrollment->student->user->name) }}</div>
            <div class="recipient-underline"></div>
        </div>

        <!-- Gold Divider -->
        <div class="gold-rule"></div>

        <!-- Action Clause -->
        <div class="action-block">
            <div class="action-text">
                for successfully completing the training program
            </div>
            <div class="program-name">{{ $program->name }}</div>
            <div class="action-text" style="margin-top:4px;">
                demonstrating outstanding dedication and skill mastery in the field of
                <strong style="color:#0d5c3a;">{{ $program->category->name ?? 'Professional Development' }}</strong>
            </div>
        </div>

        <!-- Gold Divider -->
        <div class="gold-rule"></div>

        <!-- Bottom Third: Validation Anchors -->
        <div class="bottom-row">

            <!-- Left: Certificate Number + Verify URL -->
            <div class="cert-meta">
                <div class="cert-no-badge">
                    NO. {{ $certificate->verification_code }}
                </div>
                <div class="cert-verify-url">
                    Verify at: {{ url('/verify/certificate/'.$certificate->verification_code) }}
                </div>
            </div>

            <!-- Center: Signature -->
            <div class="sig-block">
                <div class="sig-line"></div>
                <div class="sig-name">{{ $program->organization->name }}</div>
                <div class="sig-title">Authorized Signatory · Director of Programs</div>
            </div>

            <!-- Right: QR Code -->
            <div class="qr-block">
                <div class="qr-wrapper">
                    {!! $qrCode !!}
                </div>
                <div class="qr-label">Scan to Verify</div>
                <div class="qr-url">{{ url('/verify/certificate/'.$certificate->verification_code) }}</div>
            </div>

        </div>

    </div><!-- end content-wrap -->

</body>
</html>
