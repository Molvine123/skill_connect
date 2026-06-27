@extends('layouts.app')
@section('title', 'Payment History')
@section('page-title', 'Payment History')

@section('content')
<style>
    .pay-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 1rem; margin-bottom: 1.75rem; }
    .stat-tile { padding: 1.25rem; border-radius: 16px; border: 1px solid; }
    .stat-tile .stat-label { font-size: 0.78rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 0.35rem; opacity: 0.7; }
    .stat-tile .stat-val   { font-size: 1.7rem; font-weight: 900; line-height: 1; margin-bottom: 0.25rem; }
    .stat-tile .stat-sub   { font-size: 0.75rem; opacity: 0.6; }

    .tile-green { background: linear-gradient(135deg,rgba(16,185,129,0.1),rgba(6,182,212,0.05)); border-color: rgba(16,185,129,0.25); color: #34d399; }
    .tile-amber { background: linear-gradient(135deg,rgba(245,158,11,0.1),rgba(239,68,68,0.05)); border-color: rgba(245,158,11,0.25); color: #fbbf24; }
    .tile-slate { background: rgba(255,255,255,0.02); border-color: rgba(42,42,74,0.5); color: #f1f5f9; }
    .tile-slate .stat-label { color: #94a3b8; }
    .tile-slate .stat-sub   { color: #94a3b8; }

    .badge-paid    { background:rgba(16,185,129,0.15); color:#10b981; border:1px solid rgba(16,185,129,0.3); padding:0.25rem 0.7rem; border-radius:99px; font-size:0.75rem; font-weight:700; }
    .badge-pending { background:rgba(245,158,11,0.15);  color:#fbbf24; border:1px solid rgba(245,158,11,0.3);  padding:0.25rem 0.7rem; border-radius:99px; font-size:0.75rem; font-weight:700; }
    .badge-failed  { background:rgba(239,68,68,0.15);   color:#f87171; border:1px solid rgba(239,68,68,0.3);   padding:0.25rem 0.7rem; border-radius:99px; font-size:0.75rem; font-weight:700; }

    .pay-btn { display:inline-flex; align-items:center; gap:0.35rem; padding:0.35rem 0.875rem; background:linear-gradient(135deg,#10b981,#059669); color:#fff; border-radius:8px; font-size:0.78rem; font-weight:700; text-decoration:none; transition:all 0.2s; white-space:nowrap; }
    .pay-btn:hover { transform:translateY(-1px); box-shadow:0 6px 16px rgba(16,185,129,0.3); color:#fff; }

    .receipt-code { font-family: monospace; font-size: 0.82rem; color: #34d399; letter-spacing: 0.04em; }
    .ref-code     { font-family: monospace; font-size: 0.8rem; color: #a78bfa; }
</style>

{{-- Header --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.75rem;" class="animate-fade-up">
    <div>
        <h1 style="font-size:1.5rem;font-weight:800;color:#f1f5f9;">💳 Payment History</h1>
        <p style="color:#6b7280;font-size:0.875rem;margin-top:0.2rem;">All M-Pesa transactions and payment records for your enrolled programs.</p>
    </div>
</div>

{{-- Stats --}}
@php
    $totalPaid    = $payments->where('status','paid')->sum('amount');
    $totalPending = $payments->where('status','pending')->sum('amount');
@endphp
<div class="pay-grid animate-fade-up">
    <div class="stat-tile tile-green">
        <div class="stat-label">Total Paid</div>
        <div class="stat-val">KES {{ number_format($totalPaid, 0) }}</div>
        <div class="stat-sub">{{ $payments->where('status','paid')->count() }} transactions</div>
    </div>
    <div class="stat-tile tile-amber">
        <div class="stat-label">Outstanding</div>
        <div class="stat-val">KES {{ number_format($totalPending, 0) }}</div>
        <div class="stat-sub">{{ $payments->where('status','pending')->count() }} pending</div>
    </div>
    <div class="stat-tile tile-slate">
        <div class="stat-label">Total Records</div>
        <div class="stat-val">{{ $payments->count() }}</div>
        <div class="stat-sub">Across all programs</div>
    </div>
</div>

{{-- Table --}}
<div class="card animate-fade-up-delay">
    <div class="card-header">
        <span class="card-title">All Transactions</span>
    </div>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Program</th>
                    <th>Method</th>
                    <th>Phone</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Receipt / Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $pay)
                <tr>
                    <td>
                        <div style="font-size:0.875rem;font-weight:600;color:#f1f5f9;">
                            {{ $pay->enrollment?->program?->name ?? '—' }}
                        </div>
                        <div style="font-size:0.75rem;color:#6b7280;">
                            {{ $pay->enrollment?->program?->organization?->name }}
                        </div>
                    </td>
                    <td style="font-size:0.8125rem;color:#94a3b8;">
                        {{ $pay->payment_method ?? 'M-Pesa' }}
                    </td>
                    <td style="font-size:0.8125rem;color:#94a3b8;">
                        {{ $pay->phone_number ?? '—' }}
                    </td>
                    <td>
                        <span style="font-weight:700;font-size:0.9375rem;color:#f1f5f9;">
                            KES {{ number_format($pay->amount, 0) }}
                        </span>
                    </td>
                    <td style="color:#9ca3af;font-size:0.8125rem;">
                        {{ $pay->created_at->format('d M Y') }}
                    </td>
                    <td>
                        @if($pay->status === 'paid')
                            <span class="badge-paid">✓ Paid</span>
                        @elseif($pay->status === 'pending')
                            <span class="badge-pending">⏳ Pending</span>
                        @elseif($pay->status === 'failed')
                            <span class="badge-failed">✗ Failed</span>
                        @else
                            <span class="badge-pending">{{ ucfirst($pay->status) }}</span>
                        @endif
                    </td>
                    <td>
                        @if($pay->mpesa_receipt_number)
                            <span class="receipt-code">{{ $pay->mpesa_receipt_number }}</span>
                        @elseif($pay->status === 'pending' && $pay->enrollment)
                            <a href="{{ route('student.payment.checkout', $pay->enrollment_id) }}" class="pay-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                                Pay Now
                            </a>
                        @elseif($pay->transaction_reference)
                            <span class="ref-code">{{ $pay->transaction_reference }}</span>
                        @else
                            <span style="color:#4b5563;font-size:0.8rem;">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:3rem 1rem;color:#6b7280;">
                        <div style="font-size:2.5rem;margin-bottom:0.75rem;">💳</div>
                        <div style="font-weight:700;color:#e2e8f0;font-size:1rem;">No Payment Records</div>
                        <div style="font-size:0.8125rem;margin-top:0.25rem;">Payments will appear here once you enroll in a paid program.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
