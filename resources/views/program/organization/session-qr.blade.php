@extends('layouts.app')
@section('title', 'Session Attendance QR')
@section('page-title', 'Session Attendance QR')

@section('content')

<div class="mb-4">
    <a href="{{ route('organization.programs.attendance', [$program->id, $session->id]) }}" class="text-indigo-400 hover:text-indigo-300">
        &larr; Back to Attendance
    </a>
</div>

<div class="card max-w-3xl mx-auto text-center" style="padding: 4rem 2rem;">
    <h2 class="text-3xl font-bold text-white mb-2">{{ $program->name }}</h2>
    <h3 class="text-xl text-gray-300 mb-6">{{ $session->title }} &mdash; {{ \Carbon\Carbon::parse($session->start_date)->format('M d, Y h:i A') }}</h3>
    
    <div class="bg-white p-8 rounded-2xl inline-block shadow-2xl mb-8">
        <div style="width: 350px; height: 350px;">
            {!! $qrCode !!}
        </div>
    </div>
    
    <p class="text-gray-400 text-lg mb-4">
        Ask students to scan this QR code with their mobile devices to mark their attendance.
    </p>

    <div class="mt-8">
        <a href="{{ route('student.sessions.attend', $session->id) }}" target="_blank" class="text-sm text-indigo-400 hover:underline break-all">
            Direct Link: {{ route('student.sessions.attend', $session->id) }}
        </a>
    </div>
</div>

@endsection
