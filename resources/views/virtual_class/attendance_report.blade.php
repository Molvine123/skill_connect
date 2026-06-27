
@extends('layouts.app')
@section('title', 'Attendance Report')
@section('page-title', 'Attendance Report')

{{-- Gradient banner with virtual class info --}}
<div class="bg-gradient-to-r from-[#0a0f1e] to-[#0d2b22] text-[#f1f5f9] p-8 rounded-2xl mb-8 border border-[rgba(42,42,74,0.5)]">
    <h1 class="text-3xl font-bold mb-2">Attendance Report</h1>
    <p class="text-sm">Virtual Class ID: <strong>{{ $virtualClass->id }}</strong> | Session: <strong>{{ $virtualClass->session->title ?? 'N/A' }}</strong></p>
    <p class="text-sm mt-1">Started at: {{ $virtualClass->start_time->format('Y-m-d H:i') }} | Ended at: {{ $virtualClass->end_time->format('Y-m-d H:i') }}</p>
</div>

<div class="card bg-[#0a0f1e] border border-[rgba(42,42,74,0.5)]">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-[#0d2b22] text-[#94a3b8]">
                <tr>
                    <th class="px-4 py-2">#</th>
                    <th class="px-4 py-2">Student</th>
                    <th class="px-4 py-2">Join Time</th>
                    <th class="px-4 py-2">Leave Time</th>
                    <th class="px-4 py-2">Duration (min)</th>
                    <th class="px-4 py-2">Status</th>
                </tr>
            </thead>
            <tbody class="text-[#f1f5f9]">
                @forelse($attendance as $index => $record)
                    <tr class="border-t border-[rgba(42,42,74,0.3)] hover:bg-[#1a1f2a] transition-colors duration-200">
                        <td class="px-4 py-2">{{ $index + 1 }}</td>
                        <td class="px-4 py-2">{{ $record->student->user->name ?? 'Unknown' }}</td>
                        <td class="px-4 py-2">{{ $record->join_time->format('H:i') }}</td>
                        <td class="px-4 py-2">
                            {{ $record->leave_time ? $record->leave_time->format('H:i') : '—' }}
                        </td>
                        <td class="px-4 py-2 text-center">{{ $record->duration }}</td>
                        <td class="px-4 py-2">
                            @if($record->status === 'present')
                                <span class="px-2 py-1 bg-[#10b981] rounded-full text-xs font-medium">Present</span>
                            @else
                                <span class="px-2 py-1 bg-[#f59e0b] rounded-full text-xs font-medium">Absent</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-[#94a3b8]">No attendance records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6 flex justify-end">
    <a href="{{ route('organization.dashboard') }}" class="px-4 py-2 bg-[#06b6d4] text-[#0a0f1e] rounded-lg hover:bg-[#05a0b5] transition-colors duration-200">← Back to Dashboard</a>
</div>

