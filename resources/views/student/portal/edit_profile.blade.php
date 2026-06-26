@extends('layouts.app')
@section('title', 'Edit Profile')
@section('page-title', 'Edit My Profile')

@section('content')

<div class="card animate-fade-up" style="max-width:600px;margin:0 auto;">
    <div class="card-header">
        <span class="card-title">👤 Edit Profile Information</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('student.profile.update') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                @error('name')
                <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" class="form-control" value="{{ $user->email }}" disabled style="opacity:0.6;cursor:not-allowed;" title="Email address cannot be changed.">
                <span style="font-size:0.75rem;color:#6b7280;margin-top:0.25rem;display:block;">Contact system administrator to change your email address.</span>
            </div>

            <div class="form-group">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $student->phone ?? $user->phone) }}" placeholder="+254700000000">
                @error('phone')
                <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Admission / Registration Number</label>
                <input type="text" name="registration_number" class="form-control @error('registration_number') is-invalid @enderror" value="{{ old('registration_number', $student->registration_number) }}" placeholder="e.g. SCH/2026/0991">
                @error('registration_number')
                <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <hr style="border-color:var(--sc-dark-border);margin:2rem 0 1.5rem;">
            <h3 style="font-size:0.95rem;font-weight:700;color:#cbd5e1;margin-bottom:1rem;">🔐 Change Password (Optional)</h3>

            <div class="form-group">
                <label class="form-label">New Password</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Leave blank to keep current password">
                @error('password')
                <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Confirm New Password</label>
                <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm new password">
            </div>

            <div style="display:flex;justify-content:flex-end;gap:0.75rem;margin-top:2rem;">
                <a href="{{ route('student.dashboard') }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Profile</button>
            </div>
        </form>
    </div>
</div>

@endsection
