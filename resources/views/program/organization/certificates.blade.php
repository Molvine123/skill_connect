@extends('layouts.app')
@section('title', 'Certificates')
@section('page-title', 'Certificate Management')

@section('content')
<div class="animate-fade-up">

{{-- Breadcrumb --}}
<div style="margin-bottom:1.5rem;font-size:0.875rem;color:#94a3b8;display:flex;align-items:center;gap:0.5rem;justify-content:space-between;">
    <div style="display:flex;align-items:center;gap:0.5rem;">
        <a href="{{ route('organization.programs.index') }}" style="color:#6b7280;text-decoration:none;">My Programs</a>
        <span style="color:#475569;">/</span>
        <a href="{{ route('organization.programs.show', $program->id) }}" style="color:#6b7280;text-decoration:none;">{{ Str::limit($program->name, 30) }}</a>
        <span style="color:#475569;">/</span>
        <span style="color:#f1f5f9;font-weight:500;">Certificates</span>
    </div>
    
    @php
        $completedEnrollments = $program->enrollments->where('status', 'completed');
        $unissuedCount = $completedEnrollments->where('certificate', null)->count();
    @endphp
    
    @if($unissuedCount > 0)
    <form method="POST" action="{{ route('organization.programs.certificates.issue-all', $program->id) }}" onsubmit="return confirm('Issue certificates for all {{ $unissuedCount }} eligible students?');">
        @csrf
        <button type="submit" class="btn btn-primary" style="background:linear-gradient(135deg,#a78bfa,#8b5cf6);box-shadow:0 4px 15px rgba(139,92,246,0.35);">
            Issue {{ $unissuedCount }} Remaining Certificates
        </button>
    </form>
    @endif
</div>

{{-- Info Banner --}}
<div style="background:rgba(59,130,246,0.1);border:1px solid rgba(59,130,246,0.2);border-radius:12px;padding:1rem 1.25rem;margin-bottom:1.5rem;display:flex;align-items:center;gap:0.75rem;">
    <div style="font-size:1.25rem;">ℹ️</div>
    <div style="font-size:0.875rem;color:#bfdbfe;">Certificates can only be issued to students whose enrollment status has been marked as <strong>Completed</strong>.</div>
</div>

<div class="card">
    <div class="card-header"><span class="card-title">Eligible Students ({{ $completedEnrollments->count() }})</span></div>
    
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;min-width:700px;">
            <thead>
                <tr style="border-bottom:1px solid rgba(42,42,74,0.5);background:rgba(255,255,255,0.02);">
                    <th style="padding:1rem 1.25rem;text-align:left;font-size:0.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;">Student</th>
                    <th style="padding:1rem 1.25rem;text-align:left;font-size:0.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;">Completed On</th>
                    <th style="padding:1rem 1.25rem;text-align:left;font-size:0.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;">Certificate Details</th>
                    <th style="padding:1rem 1.25rem;text-align:right;font-size:0.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($completedEnrollments->sortByDesc('updated_at') as $enrollment)
                <tr style="border-bottom:1px solid rgba(42,42,74,0.4);transition:background .2s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                    <td style="padding:1rem 1.25rem;">
                        <div style="display:flex;align-items:center;gap:0.75rem;">
                            <img src="{{ $enrollment->student->user->getAvatarUrl() }}" style="width:36px;height:36px;border-radius:8px;">
                            <div>
                                <div style="font-size:0.875rem;font-weight:600;color:#f1f5f9;">{{ $enrollment->student->user->name }}</div>
                                <div style="font-size:0.75rem;color:#6b7280;">{{ $enrollment->student->registration_number ?? $enrollment->student->user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:1rem 1.25rem;font-size:0.875rem;color:#e2e8f0;">
                        <div>{{ $enrollment->updated_at->format('M d, Y') }}</div>
                    </td>
                    <td style="padding:1rem 1.25rem;">
                        @if($enrollment->certificate)
                            <div style="font-size:0.875rem;font-family:monospace;color:#a78bfa;background:rgba(167,139,250,0.1);padding:0.25rem 0.5rem;border-radius:4px;display:inline-block;border:1px solid rgba(167,139,250,0.2);">
                                {{ $enrollment->certificate->verification_code }}
                            </div>
                            <div style="font-size:0.7rem;color:#6b7280;margin-top:0.25rem;">Issued: {{ $enrollment->certificate->issue_date->format('M d, Y') }}</div>
                        @else
                            <span style="font-size:0.75rem;color:#94a3b8;font-style:italic;">Not issued yet</span>
                        @endif
                    </td>
                    <td style="padding:1rem 1.25rem;text-align:right;">
                        @if($enrollment->certificate)
                            <span style="font-size:0.8rem;color:#10b981;font-weight:600;">✓ Issued</span>
                        @else
                            <form method="POST" action="{{ route('organization.programs.certificates.issue', [$program->id, $enrollment->id]) }}" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-outline" style="padding:0.35rem 0.75rem;font-size:0.75rem;border-color:rgba(167,139,250,0.3);color:#a78bfa;">Issue Certificate</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="padding:3rem;text-align:center;">
                        <div style="font-size:3rem;margin-bottom:1rem;">🏆</div>
                        <div style="font-size:1.1rem;font-weight:600;color:#f1f5f9;">No completed students yet</div>
                        <div style="font-size:0.85rem;color:#6b7280;margin-top:0.5rem;">Mark students as 'Completed' in the Enrollments tab to issue certificates.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</div>
@endsection
