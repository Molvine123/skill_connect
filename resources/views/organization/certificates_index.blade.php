@extends('layouts.app')
@section('title', 'Certificates Management')
@section('page-title', 'Certificates Dashboard')

@section('content')
<style>
    .org-qr-box svg {
        width: 60px;
        height: 60px;
        display: block;
    }
</style>
<div class="animate-fade-up">

    {{-- Banner --}}
    <div style="background:linear-gradient(135deg,#1e1b4b 0%,#311042 60%,#1e1b4b 100%);border:1px solid rgba(139,92,246,0.2);border-radius:20px;padding:2rem;margin-bottom:2rem;position:relative;overflow:hidden;">
        <div style="position:absolute;top:-60px;right:-40px;width:280px;height:280px;background:radial-gradient(circle,rgba(167,139,250,0.18),transparent 70%);pointer-events:none;"></div>
        <div style="position:relative;z-index:1;">
            <h1 style="font-size:1.75rem;font-weight:800;color:#f1f5f9;margin-bottom:0.5rem;">🏆 Certificate Management</h1>
            <p style="font-size:0.875rem;color:#94a3b8;margin:0;">
                Issue and track professional certifications for your students. Choose a program below to issue certificates to graduates, or view recently generated credentials.
            </p>
        </div>
    </div>

    {{-- Grid Layout: Programs overview --}}
    <h2 style="font-size:1.25rem;font-weight:700;color:#f1f5f9;margin-bottom:1rem;">Manage by Program</h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(280px, 1fr));gap:1rem;margin-bottom:2rem;">
        @forelse($programs as $program)
            <div class="card" style="display:flex;flex-direction:column;justify-content:space-between;padding:1.25rem;">
                <div>
                    <div style="font-size:0.75rem;color:#a78bfa;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.35rem;">
                        {{ $program->category->name ?? 'Program' }}
                    </div>
                    <h3 style="font-size:1.05rem;font-weight:700;color:#f1f5f9;margin-bottom:0.75rem;line-height:1.4;">
                        {{ $program->name }}
                    </h3>
                </div>
                <div style="border-top:1px solid rgba(255,255,255,0.05);padding-top:0.75rem;margin-top:0.5rem;display:flex;align-items:center;justify-content:space-between;">
                    <div style="font-size:0.8rem;color:#94a3b8;">
                        🎓 <strong style="color:#f1f5f9;">{{ $program->enrollments_count }}</strong> Graduated Students
                    </div>
                    <a href="{{ route('organization.programs.certificates', $program->id) }}" class="btn btn-outline" style="padding:0.35rem 0.75rem;font-size:0.75rem;color:#a78bfa;border-color:rgba(167,139,250,0.3);text-decoration:none;">
                        Manage →
                    </a>
                </div>
            </div>
        @empty
            <div style="grid-column:1/-1;text-align:center;padding:2rem;background:rgba(255,255,255,0.01);border:1px solid rgba(255,255,255,0.03);border-radius:12px;color:#6b7280;">
                No active training programs found.
            </div>
        @endforelse
    </div>

    {{-- Recent Issued Certificates --}}
    <div class="card">
        <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;">
            <span class="card-title">📜 Recently Issued Certificates</span>
            <span style="font-size:0.8rem;color:#6b7280;">{{ $recentCertificates->total() }} total</span>
        </div>

        @if($recentCertificates->count() > 0)
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:0.875rem;min-width:800px;">
                    <thead>
                        <tr style="border-bottom:1px solid rgba(255,255,255,0.05);color:#94a3b8;font-size:0.72rem;text-transform:uppercase;letter-spacing:0.07em;text-align:left;">
                            <th style="padding:0.9rem 1.25rem;">Student</th>
                            <th style="padding:0.9rem 1.25rem;">Program</th>
                            <th style="padding:0.9rem 1.25rem;">Certificate No.</th>
                            <th style="padding:0.9rem 1.25rem;">Issued</th>
                            <th style="padding:0.9rem 1.25rem;text-align:center;">QR Code</th>
                            <th style="padding:0.9rem 1.25rem;text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentCertificates as $cert)
                            @php
                                $student = $cert->student;
                                $user    = $student?->user;
                            @endphp
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.025);transition:background 0.15s;"
                                onmouseover="this.style.background='rgba(255,255,255,0.018)'"
                                onmouseout="this.style.background='transparent'">

                                {{-- Student --}}
                                <td style="padding:0.85rem 1.25rem;">
                                    <div style="display:flex;align-items:center;gap:0.75rem;">
                                        @if($user)
                                            <img src="{{ $user->getAvatarUrl() }}"
                                                 style="width:36px;height:36px;border-radius:9px;object-fit:cover;border:1.5px solid rgba(167,139,250,0.25);flex-shrink:0;">
                                            <div>
                                                <div style="font-weight:600;color:#f1f5f9;line-height:1.3;">{{ $user->name }}</div>
                                                <div style="font-size:0.73rem;color:#6b7280;">{{ $user->email }}</div>
                                            </div>
                                        @else
                                            <div style="width:36px;height:36px;border-radius:9px;background:rgba(255,255,255,0.04);border:1.5px solid rgba(255,255,255,0.08);display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0;">🎓</div>
                                            <div style="font-weight:600;color:#6b7280;">Unknown Student</div>
                                        @endif
                                    </div>
                                </td>

                                {{-- Program --}}
                                <td style="padding:0.85rem 1.25rem;">
                                    @php $prog = $cert->enrollment?->program; @endphp
                                    @if($prog)
                                        <div style="font-size:0.7rem;color:#a78bfa;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.2rem;">
                                            {{ $prog->category?->name ?? 'Program' }}
                                        </div>
                                        <div style="font-weight:600;color:#e2e8f0;max-width:220px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $prog->name }}</div>
                                    @else
                                        <span style="color:#4b5563;">—</span>
                                    @endif
                                </td>

                                {{-- Certificate Number --}}
                                <td style="padding:0.85rem 1.25rem;">
                                    <span style="font-family:'Courier New',monospace;font-weight:700;font-size:0.78rem;color:#f59e0b;background:rgba(245,158,11,0.09);border:1px solid rgba(245,158,11,0.22);padding:0.28rem 0.65rem;border-radius:7px;letter-spacing:0.06em;white-space:nowrap;">
                                        {{ $cert->certificate_number }}
                                    </span>
                                </td>

                                {{-- Issue Date --}}
                                <td style="padding:0.85rem 1.25rem;">
                                    @if($cert->issue_date)
                                        <div style="display:flex;align-items:center;gap:0.4rem;">
                                            <span style="font-size:0.85rem;">📅</span>
                                            <div>
                                                <div style="color:#e2e8f0;font-weight:500;font-size:0.83rem;">{{ $cert->issue_date->format('M d, Y') }}</div>
                                                <div style="color:#6b7280;font-size:0.72rem;">{{ $cert->issue_date->diffForHumans() }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <span style="color:#4b5563;">—</span>
                                    @endif
                                </td>

                                {{-- QR Code --}}
                                <td style="padding:0.85rem 1.25rem;text-align:center;">
                                    @if(isset($qrCodes[$cert->verification_code]))
                                        <div class="org-qr-box" style="display:inline-block;background:#fff;border-radius:8px;padding:5px;box-shadow:0 0 0 1px rgba(167,139,250,0.25),0 2px 10px rgba(0,0,0,0.4);">
                                            {!! $qrCodes[$cert->verification_code] !!}
                                        </div>
                                        <div style="font-size:0.62rem;color:#64748b;margin-top:4px;text-transform:uppercase;letter-spacing:0.08em;">Scan</div>
                                    @else
                                        <span style="color:#4b5563;">—</span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td style="padding:0.85rem 1.25rem;text-align:right;">
                                    <div style="display:inline-flex;gap:0.5rem;align-items:center;justify-content:flex-end;">
                                        <a href="{{ route('organization.programs.certificates.download', ['programId' => $cert->enrollment->program_id, 'enrollmentId' => $cert->enrollment->id]) }}"
                                           style="display:inline-flex;align-items:center;gap:0.3rem;padding:0.35rem 0.75rem;font-size:0.75rem;font-weight:600;color:#f59e0b;background:rgba(245,158,11,0.09);border:1px solid rgba(245,158,11,0.25);border-radius:8px;text-decoration:none;transition:all 0.15s;"
                                           onmouseover="this.style.background='rgba(245,158,11,0.2)';this.style.borderColor='rgba(245,158,11,0.5)'"
                                           onmouseout="this.style.background='rgba(245,158,11,0.09)';this.style.borderColor='rgba(245,158,11,0.25)'">
                                            ⬇ PDF
                                        </a>
                                        <a href="{{ route('certificates.verify', $cert->verification_code) }}" target="_blank"
                                           style="display:inline-flex;align-items:center;gap:0.3rem;padding:0.35rem 0.75rem;font-size:0.75rem;font-weight:600;color:#94a3b8;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.09);border-radius:8px;text-decoration:none;transition:all 0.15s;"
                                           onmouseover="this.style.background='rgba(255,255,255,0.08)';this.style.color='#f1f5f9';this.style.borderColor='rgba(255,255,255,0.2)'"
                                           onmouseout="this.style.background='rgba(255,255,255,0.03)';this.style.color='#94a3b8';this.style.borderColor='rgba(255,255,255,0.09)'">
                                            🔍 Verify
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="padding:1.25rem;border-top:1px solid rgba(42,42,74,0.5);">
                {{ $recentCertificates->links() }}
            </div>
        @else
            <div style="padding:5rem 2rem;text-align:center;">
                <div style="font-size:3.5rem;margin-bottom:1.25rem;">🏆</div>
                <h3 style="font-size:1.1rem;font-weight:700;color:#f1f5f9;margin:0 0 0.5rem 0;">No certificates issued yet</h3>
                <p style="font-size:0.875rem;color:#6b7280;max-width:380px;margin:0 auto;">
                    Navigate to a program card above and click <strong style="color:#a78bfa;">Manage →</strong> to start issuing certificates to your graduates.
                </p>
            </div>
        @endif
    </div>

</div>
@endsection
