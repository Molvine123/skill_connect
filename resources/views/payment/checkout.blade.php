@extends('layouts.app')

@section('title', 'M-Pesa Checkout — ' . $enrollment->program->name)
@section('page-title', 'Complete Payment')

@section('content')
<style>
    .checkout-wrapper {
        max-width: 560px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }

    /* Program Summary Card */
    .program-card {
        background: linear-gradient(135deg, #0a1f1a 0%, #0d2b22 60%, #0a1f1a 100%);
        border: 1px solid rgba(16, 185, 129, 0.2);
        border-radius: 20px;
        padding: 1.75rem;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }

    .program-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(16, 185, 129, 0.08) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .program-card .org-label {
        font-size: 0.75rem;
        color: #10b981;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 0.4rem;
    }

    .program-card .program-name {
        font-size: 1.35rem;
        font-weight: 700;
        color: #f1f5f9;
        margin: 0 0 1rem 0;
        line-height: 1.3;
    }

    .amount-display {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .amount-display .currency {
        font-size: 0.9rem;
        color: #10b981;
        font-weight: 700;
    }

    .amount-display .value {
        font-size: 2rem;
        font-weight: 800;
        color: #f1f5f9;
        letter-spacing: -0.03em;
    }

    /* Checkout Form Card */
    .checkout-card {
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(42, 42, 74, 0.6);
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 1.5rem;
    }

    .checkout-card h2 {
        font-size: 1.1rem;
        font-weight: 700;
        color: #f1f5f9;
        margin: 0 0 0.35rem 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .checkout-card .subtitle {
        font-size: 0.85rem;
        color: #64748b;
        margin: 0 0 1.5rem 0;
    }

    .mpesa-logo {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .mpesa-logo .logo-badge {
        background: #4caf1a;
        color: #fff;
        font-weight: 900;
        font-size: 0.85rem;
        padding: 0.3rem 0.7rem;
        border-radius: 6px;
        letter-spacing: 0.04em;
    }

    .mpesa-logo .logo-text {
        font-size: 0.8rem;
        color: #94a3b8;
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-group label {
        display: block;
        font-size: 0.8125rem;
        color: #94a3b8;
        font-weight: 600;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .phone-input-wrapper {
        display: flex;
        gap: 0;
        border: 1px solid rgba(42, 42, 74, 0.8);
        border-radius: 12px;
        overflow: hidden;
        background: rgba(255,255,255,0.03);
        transition: border-color 0.2s;
    }

    .phone-input-wrapper:focus-within {
        border-color: rgba(16, 185, 129, 0.5);
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.08);
    }

    .phone-prefix {
        display: flex;
        align-items: center;
        padding: 0 0.875rem;
        background: rgba(16, 185, 129, 0.08);
        border-right: 1px solid rgba(42, 42, 74, 0.8);
        color: #10b981;
        font-weight: 700;
        font-size: 0.9rem;
        white-space: nowrap;
    }

    .phone-prefix svg {
        width: 16px;
        height: 16px;
        margin-right: 0.4rem;
    }

    .phone-input-wrapper input {
        flex: 1;
        background: transparent;
        border: none;
        outline: none;
        padding: 0.85rem 1rem;
        color: #f1f5f9;
        font-size: 1rem;
        letter-spacing: 0.04em;
    }

    .phone-input-wrapper input::placeholder {
        color: #475569;
    }

    .form-hint {
        font-size: 0.75rem;
        color: #475569;
        margin-top: 0.4rem;
    }

    .submit-btn {
        width: 100%;
        padding: 1rem;
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.25s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 1.5rem;
        letter-spacing: 0.02em;
    }

    .submit-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.35);
        background: linear-gradient(135deg, #0ea574, #047857);
    }

    .submit-btn:active {
        transform: translateY(0);
    }

    .submit-btn.loading {
        opacity: 0.8;
        cursor: not-allowed;
        transform: none;
    }

    .submit-btn svg.spinner {
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .security-note {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.75rem;
        color: #475569;
        justify-content: center;
        margin-top: 1rem;
    }

    .security-note svg {
        width: 14px;
        height: 14px;
        color: #10b981;
    }

    /* How it works steps */
    .steps-card {
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(42, 42, 74, 0.4);
        border-radius: 16px;
        padding: 1.5rem;
    }

    .steps-card h3 {
        font-size: 0.85rem;
        color: #94a3b8;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin: 0 0 1rem 0;
    }

    .step-item {
        display: flex;
        align-items: flex-start;
        gap: 0.875rem;
        margin-bottom: 0.875rem;
    }

    .step-item:last-child {
        margin-bottom: 0;
    }

    .step-num {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: rgba(16, 185, 129, 0.15);
        border: 1px solid rgba(16, 185, 129, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        font-weight: 800;
        color: #10b981;
        flex-shrink: 0;
    }

    .step-text {
        font-size: 0.82rem;
        color: #94a3b8;
        line-height: 1.5;
        padding-top: 0.2rem;
    }

    .back-link {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        color: #64748b;
        font-size: 0.85rem;
        text-decoration: none;
        margin-bottom: 1.5rem;
        transition: color 0.2s;
    }

    .back-link:hover {
        color: #94a3b8;
    }

    .error-msg {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.3);
        border-radius: 10px;
        padding: 0.75rem 1rem;
        color: #f87171;
        font-size: 0.85rem;
        margin-bottom: 1rem;
    }

    .info-msg {
        background: rgba(99, 102, 241, 0.1);
        border: 1px solid rgba(99, 102, 241, 0.3);
        border-radius: 10px;
        padding: 0.75rem 1rem;
        color: #a5b4fc;
        font-size: 0.85rem;
        margin-bottom: 1rem;
    }
</style>

<div class="checkout-wrapper">
    {{-- Back link --}}
    <a href="{{ route('student.enrollments.index') }}" class="back-link">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
        Back to Enrollments
    </a>

    {{-- Alerts --}}
    @if (session('error'))
        <div class="error-msg">⚠ {{ session('error') }}</div>
    @endif
    @if (session('info'))
        <div class="info-msg">ℹ {{ session('info') }}</div>
    @endif
    @if ($errors->any())
        <div class="error-msg">{{ $errors->first() }}</div>
    @endif

    {{-- Program Summary --}}
    <div class="program-card">
        <div class="org-label">{{ $enrollment->program->organization->name ?? 'SkillConnect' }}</div>
        <h1 class="program-name">{{ $enrollment->program->name }}</h1>
        <div class="amount-display">
            <span class="currency">KES</span>
            <span class="value">{{ number_format($payment->amount, 2) }}</span>
        </div>
    </div>

    {{-- Checkout Form --}}
    <div class="checkout-card">
        <div class="mpesa-logo">
            <span class="logo-badge">M-PESA</span>
            <span class="logo-text">Lipa Na M-Pesa — STK Push</span>
        </div>

        <h2>
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.22 3.41 2 2 0 0 1 3.2 1.2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.09 8.19a16 16 0 0 0 5.06 5.06l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            Enter Your M-Pesa Number
        </h2>
        <p class="subtitle">You will receive a payment prompt on your phone. Enter your PIN to confirm.</p>

        <form id="paymentForm" action="{{ route('student.payment.initiate', $enrollment->id) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="phone">Safaricom Phone Number</label>
                <div class="phone-input-wrapper">
                    <div class="phone-prefix">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                        🇰🇪
                    </div>
                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        placeholder="0712 345 678"
                        value="{{ old('phone') }}"
                        maxlength="13"
                        required
                        autocomplete="tel"
                    >
                </div>
                <div class="form-hint">Enter a Safaricom number: 07XX XXX XXX or 01XX XXX XXX</div>
            </div>

            <button type="submit" class="submit-btn" id="submitBtn">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                Pay KES {{ number_format($payment->amount, 0) }} via M-Pesa
            </button>
        </form>

        <div class="security-note">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            Secured by Safaricom M-Pesa. We never store your PIN.
        </div>
    </div>

    {{-- How It Works --}}
    <div class="steps-card">
        <h3>How It Works</h3>
        <div class="step-item">
            <div class="step-num">1</div>
            <div class="step-text">Enter your Safaricom phone number above and click <strong style="color:#f1f5f9;">Pay</strong>.</div>
        </div>
        <div class="step-item">
            <div class="step-num">2</div>
            <div class="step-text">You will receive an <strong style="color:#f1f5f9;">M-Pesa prompt</strong> on your phone within seconds.</div>
        </div>
        <div class="step-item">
            <div class="step-num">3</div>
            <div class="step-text">Enter your <strong style="color:#f1f5f9;">M-Pesa PIN</strong> to confirm the payment.</div>
        </div>
        <div class="step-item">
            <div class="step-num">4</div>
            <div class="step-text">Your enrollment will be <strong style="color:#10b981;">automatically approved</strong> after payment confirmation.</div>
        </div>
    </div>
</div>

<script>
    document.getElementById('paymentForm').addEventListener('submit', function () {
        const btn = document.getElementById('submitBtn');
        btn.classList.add('loading');
        btn.innerHTML = `
            <svg class="spinner" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
            Sending STK Push...
        `;
        btn.disabled = true;
    });
</script>
@endsection
