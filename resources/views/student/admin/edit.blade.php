@extends('layouts.app')
@section('title', 'Edit Student — ' . $student->user?->name)
@section('page-title', 'Edit Student Profile')

@section('content')

{{-- Breadcrumbs / Header --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;" class="animate-fade-up">
    <div style="display:flex;align-items:center;gap:0.75rem;">
        <a href="{{ route('admin.students.index') }}" style="color:#6b7280;text-decoration:none;font-size:0.875rem;">Students</a>
        <span style="color:#4b5563;">/</span>
        <a href="{{ route('admin.students.show', $student->id) }}" style="color:#6b7280;text-decoration:none;font-size:0.875rem;">{{ $student->user?->name }}</a>
        <span style="color:#4b5563;">/</span>
        <span style="color:#e2e8f0;font-size:0.875rem;">Edit</span>
    </div>
</div>

<div class="card animate-fade-up" style="max-width:640px;margin:0 auto;">
    <div class="card-header">
        <span class="card-title">✏️ Modify Student Profile</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.students.update', $student->id) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $student->user?->name) }}" required>
                @error('name')
                <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $student->user?->email) }}" required>
                @error('email')
                <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $student->phone ?? $student->user?->phone) }}" placeholder="+254700000000">
                @error('phone')
                <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Registration / Admission Number</label>
                <input type="text" name="registration_number" class="form-control @error('registration_number') is-invalid @enderror" value="{{ old('registration_number', $student->registration_number) }}" placeholder="e.g. SCH/2026/01">
                @error('registration_number')
                <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div style="display:flex;justify-content:flex-end;gap:0.75rem;margin-top:2rem;">
                <a href="{{ route('admin.students.show', $student->id) }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

@endsection
