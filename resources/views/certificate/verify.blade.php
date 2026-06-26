@extends('layouts.app')
@section('title', 'Verify Certificate')
@section('page-title', 'Certificate Verification')

@section('content')

<div class="max-w-2xl mx-auto" style="padding-top: 4rem; padding-bottom: 4rem;">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">SkillConnect Verification System</h1>
        <p class="text-gray-400">Verify the authenticity of a SkillConnect certificate.</p>
    </div>

    @if($valid)
        <div class="card p-8 text-center animate-fade-up border border-emerald-500/30" style="background: rgba(16, 185, 129, 0.05);">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-emerald-500/20 text-emerald-400 mb-6" style="font-size: 2.5rem;">
                ✅
            </div>
            
            <h2 class="text-2xl font-bold text-emerald-400 mb-2">Verified & Authentic</h2>
            <p class="text-gray-300 mb-6">This certificate is valid and was issued by SkillConnect.</p>
            
            <div class="bg-slate-800/50 rounded-xl p-6 text-left border border-slate-700">
                <div class="mb-4">
                    <span class="text-sm text-gray-400 block mb-1">Student Name</span>
                    <span class="text-lg text-white font-semibold">{{ $certificate->enrollment->student->user->name }}</span>
                </div>
                
                <div class="mb-4">
                    <span class="text-sm text-gray-400 block mb-1">Program Completed</span>
                    <span class="text-lg text-white font-semibold">{{ $certificate->enrollment->program->name }}</span>
                </div>
                
                <div class="mb-4">
                    <span class="text-sm text-gray-400 block mb-1">Issuing Organization</span>
                    <span class="text-lg text-white font-semibold">{{ $certificate->enrollment->program->organization->name }}</span>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mt-6 pt-6 border-t border-slate-700">
                    <div>
                        <span class="text-xs text-gray-500 block mb-1">Certificate Number</span>
                        <span class="text-sm text-gray-300 font-mono">{{ $certificate->verification_code }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 block mb-1">Issue Date</span>
                        <span class="text-sm text-gray-300">{{ \Carbon\Carbon::parse($certificate->issue_date)->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="card p-8 text-center animate-fade-up border border-red-500/30" style="background: rgba(239, 68, 68, 0.05);">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-red-500/20 text-red-400 mb-6" style="font-size: 2.5rem;">
                ❌
            </div>
            
            <h2 class="text-2xl font-bold text-red-400 mb-2">Verification Failed</h2>
            <p class="text-gray-300 mb-6">We could not find a valid certificate matching the code <strong>{{ $code }}</strong>.</p>
            
            <div class="bg-slate-800/50 rounded-xl p-6 text-left border border-slate-700">
                <p class="text-sm text-gray-400">
                    This might happen if:
                </p>
                <ul class="list-disc list-inside text-sm text-gray-400 mt-2 space-y-1">
                    <li>The certificate number is typed incorrectly.</li>
                    <li>The certificate has been revoked.</li>
                    <li>The certificate is forged.</li>
                </ul>
            </div>
            
            <div class="mt-8">
                <a href="{{ route('home') }}" class="btn btn-outline">Return Home</a>
            </div>
        </div>
    @endif
</div>

@endsection
