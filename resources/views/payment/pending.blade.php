@extends('layouts.app')

@section('title', 'Payment Pending — ' . $enrollment->program->name)
@section('page-title', 'Waiting for Payment')

@section('content')
<style>
    .pending-wrapper {
        max-width: 520px;
        margin: 3rem auto;
        text-align: center;
        padding: 1rem;
    }

    .pulse-ring {
        position: relative;
        width: 100px;
        height: 100px;
        margin: 0 auto 2rem auto;
    }

    .pulse-ring .ring {
        position: absolute;
        inset: 0;
        border-radius: 50%;
        border: 3px solid rgba(16, 185, 129, 0.4);
        animation: pulse-out 2s ease-out infinite;
    }

    .pulse-ring .ring:nth-child(2) { animation-delay: 0.5s; }
    .pulse-ring .ring:nth-child(3) { animation-delay: 1s; }

    .pulse-ring .icon {
        position: absolute;
        inset: 10px;
        border-radius: 50%;
        background: linear-gradient(135deg, #10b981, #059669);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 24px rgba(16, 185, 129, 0.4);
    }

    .pulse-ring .icon svg {
        width: 36px;
        height: 36px;
        color: #fff;
    }

    @keyframes pulse-out {
        0%   { transform: scale(1); opacity: 0.6; }
        100% { transform: scale(1.8); opacity: 0; }
    }

    .pending-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: #f1f5f9;
        margin-bottom: 0.5rem;
    }

    .pending-subtitle {
        font-size: 0.9rem;
        color: #64748b;
        margin-bottom: 2rem;
        line-height: 1.6;
    }

    .pending-card {
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(42, 42, 74, 0.6);
        border-radius: 20px;
        padding: 1.75rem;
        margin-bottom: 1.5rem;
        text-align: left;
    }

    .pending-card .row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px solid rgba(42, 42, 74, 0.4);
        font-size: 0.875rem;
    }

    .pending-card .row:last-child { border-bottom: none; }

    .pending-card .row .key { color: #64748b; }
    .pending-card .row .val { color: #f1f5f9; font-weight: 600; }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.3rem 0.75rem;
        border-radius: 99px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .status-badge.pending {
        background: rgba(245, 158, 11, 0.15);
        color: #fbbf24;
        border: 1px solid rgba(245, 158, 11, 0.3);
    }

    .status-badge.paid {
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .status-badge.failed {
        background: rgba(239, 68, 68, 0.15);
        color: #f87171;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    .dot { width: 7px; height: 7px; border-radius: 50%; background: currentColor; }
    .dot.blink { animation: blink 1s infinite; }
    @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0.2; } }

    .action-btns {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .btn-primary {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        padding: 0.875rem;
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.9rem;
        text-decoration: none;
        transition: all 0.25s;
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 20px rgba(16,185,129,0.3);
        color: white;
    }

    .btn-outline {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        padding: 0.875rem;
        background: transparent;
        color: #94a3b8;
        border: 1px solid rgba(42, 42, 74, 0.8);
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        transition: all 0.2s;
    }

    .btn-outline:hover {
        border-color: rgba(100, 116, 139, 0.6);
        color: #f1f5f9;
    }

    .success-state { display: none; }
    .failed-state { display: none; }
</style>

<div class="pending-wrapper">
    {{-- Animated icon --}}
    <div class="pulse-ring">
        <div class="ring"></div>
        <div class="ring"></div>
        <div class="ring"></div>
        <div class="icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.22 3.41 2 2 0 0 1 3.2 1.2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.09 8.19a16 16 0 0 0 5.06 5.06l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
        </div>
    </div>

    <h1 class="pending-title">Check Your Phone</h1>
    <p class="pending-subtitle">
        An M-Pesa STK Push has been sent to your phone.<br>
        Enter your <strong style="color:#f1f5f9;">M-Pesa PIN</strong> to complete the payment.
    </p>

    {{-- Payment Summary --}}
    <div class="pending-card">
        <div class="row">
            <span class="key">Program</span>
            <span class="val">{{ Str::limit($enrollment->program->name, 35) }}</span>
        </div>
        <div class="row">
            <span class="key">Amount</span>
            <span class="val">KES {{ number_format($enrollment->payment->amount ?? 0, 2) }}</span>
        </div>
        <div class="row">
            <span class="key">Phone</span>
            <span class="val">{{ $enrollment->payment->phone_number ?? '—' }}</span>
        </div>
        <div class="row">
            <span class="key">Status</span>
            <span id="statusBadge" class="status-badge pending">
                <span class="dot blink"></span>
                Awaiting Payment
            </span>
        </div>
        <div class="row" id="receiptRow" style="display:none;">
            <span class="key">Receipt</span>
            <span class="val" id="receiptNo">—</span>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="action-btns" id="waitingActions">
        <a href="{{ route('student.enrollments.index') }}" class="btn-outline">
            ← Go to My Enrollments
        </a>
        <a href="{{ route('student.payment.checkout', $enrollment->id) }}" class="btn-outline">
            ↺ Try a Different Number
        </a>
    </div>

    {{-- Success CTA (shown after payment confirmed) --}}
    <div class="action-btns success-state" id="successActions">
        <a href="{{ route('student.enrollments.index') }}" class="btn-primary">
            ✓ View My Enrollments
        </a>
    </div>
</div>

<script>
    const enrollmentId = {{ $enrollment->id }};
    const statusUrl = "{{ route('student.payment.status', $enrollment->id) }}";
    let pollInterval;

    function updateStatus(data) {
        const badge = document.getElementById('statusBadge');
        const receiptRow = document.getElementById('receiptRow');
        const receiptNo = document.getElementById('receiptNo');

        if (data.status === 'paid') {
            badge.className = 'status-badge paid';
            badge.innerHTML = '<span class="dot"></span> Paid ✓';
            clearInterval(pollInterval);

            if (data.receipt) {
                receiptRow.style.display = 'flex';
                receiptNo.textContent = data.receipt;
            }

            // Switch CTAs
            document.getElementById('waitingActions').style.display = 'none';
            document.getElementById('successActions').style.display = 'flex';

            // Redirect after 3s
            setTimeout(() => {
                window.location.href = "{{ route('student.enrollments.index') }}";
            }, 3500);
        } else if (data.status === 'failed') {
            badge.className = 'status-badge failed';
            badge.innerHTML = '<span class="dot"></span> Payment Failed';
            clearInterval(pollInterval);
        }
    }

    // Poll every 4 seconds
    pollInterval = setInterval(async () => {
        try {
            const res = await fetch(statusUrl, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            updateStatus(data);
        } catch (e) {
            // Network error — keep polling
        }
    }, 4000);

    // Stop polling after 5 minutes
    setTimeout(() => clearInterval(pollInterval), 300000);
</script>
@endsection
