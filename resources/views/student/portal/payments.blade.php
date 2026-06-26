@extends('layouts.app')
@section('title', 'Payments')
@section('page-title', 'Payment History')

@section('content')

{{-- Header --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;" class="animate-fade-up">
    <div>
        <h1 style="font-size:1.5rem;font-weight:800;color:#f1f5f9;">💳 Payment History</h1>
        <p style="color:#6b7280;font-size:0.9rem;margin-top:0.25rem;">Review all payments and outstanding fees for your enrolled programs.</p>
    </div>
</div>

{{-- Summary Cards --}}
@php
    $totalPaid     = $payments->where('status', 'paid')->sum('amount');
    $totalPending  = $payments->where('status', 'pending')->sum('amount');
@endphp
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.75rem;" class="animate-fade-up">
    <div style="padding:1.25rem;background:linear-gradient(135deg,rgba(16,185,129,0.1),rgba(6,182,212,0.05));border:1px solid rgba(16,185,129,0.2);border-radius:14px;">
        <div style="font-size:0.8rem;color:#6b7280;margin-bottom:0.25rem;">Total Paid</div>
        <div style="font-size:1.75rem;font-weight:900;color:#34d399;">KES {{ number_format($totalPaid, 0) }}</div>
        <div style="font-size:0.75rem;color:#059669;margin-top:0.25rem;">{{ $payments->where('status','paid')->count() }} transactions</div>
    </div>
    <div style="padding:1.25rem;background:linear-gradient(135deg,rgba(245,158,11,0.1),rgba(239,68,68,0.05));border:1px solid rgba(245,158,11,0.2);border-radius:14px;">
        <div style="font-size:0.8rem;color:#6b7280;margin-bottom:0.25rem;">Outstanding</div>
        <div style="font-size:1.75rem;font-weight:900;color:#fbbf24;">KES {{ number_format($totalPending, 0) }}</div>
        <div style="font-size:0.75rem;color:#d97706;margin-top:0.25rem;">{{ $payments->where('status','pending')->count() }} pending</div>
    </div>
    <div style="padding:1.25rem;background:rgba(255,255,255,0.02);border:1px solid var(--sc-dark-border);border-radius:14px;">
        <div style="font-size:0.8rem;color:#6b7280;margin-bottom:0.25rem;">Total Transactions</div>
        <div style="font-size:1.75rem;font-weight:900;color:#f1f5f9;">{{ $payments->count() }}</div>
        <div style="font-size:0.75rem;color:#6b7280;margin-top:0.25rem;">Across all programs</div>
    </div>
</div>

{{-- Payments Table --}}
<div class="card animate-fade-up-delay">
    <div class="card-header">
        <span class="card-title">All Transactions</span>
    </div>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Program</th>
                    <th>Reference</th>
                    <th>Method</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Receipt</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $pay)
                @php
                    $sc = [
                        'paid'    => ['badge-active',  '✅ Paid'],
                        'pending' => ['badge-pending', '⏳ Pending'],
                        'failed'  => ['badge-deact',   '✗ Failed'],
                        'refunded'=> ['badge-student', '↩ Refunded'],
                    ];
                    [$bc, $sl] = $sc[$pay->status] ?? ['badge-pending', ucfirst($pay->status)];
                @endphp
                <tr>
                    <td>
                        <div style="font-size:0.875rem;font-weight:600;color:#f1f5f9;">{{ $pay->enrollment?->program?->name }}</div>
                        <div style="font-size:0.75rem;color:#6b7280;">{{ $pay->enrollment?->program?->organization?->name }}</div>
                    </td>
                    <td>
                        <span style="font-family:monospace;font-size:0.8125rem;color:#a78bfa;">{{ $pay->reference ?? '—' }}</span>
                    </td>
                    <td>
                        <span style="text-transform:capitalize;font-size:0.8125rem;color:#9ca3af;">{{ str_replace('_',' ', $pay->payment_method ?? 'N/A') }}</span>
                    </td>
                    <td>
                        <span style="font-weight:700;font-size:0.9375rem;color:#f1f5f9;">KES {{ number_format($pay->amount, 0) }}</span>
                    </td>
                    <td style="color:#9ca3af;font-size:0.8125rem;">{{ $pay->paid_at?->format('d M Y') ?? $pay->created_at->format('d M Y') }}</td>
                    <td><span class="badge {{ $bc }}">{{ $sl }}</span></td>
                    <td>
                        @if($pay->receipt_path)
                        <a href="{{ Storage::url($pay->receipt_path) }}" target="_blank" class="btn btn-outline btn-sm" style="font-size:0.75rem;padding:0.25rem 0.625rem;">📄 View</a>
                        @else
                        <span style="color:#4b5563;font-size:0.8rem;">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:3rem;color:#6b7280;">
                        <div style="font-size:2rem;margin-bottom:0.75rem;">💳</div>
                        <div style="font-weight:600;color:#e2e8f0;">No Payments Found</div>
                        <div style="font-size:0.8125rem;margin-top:0.25rem;">You have no payment records yet.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
