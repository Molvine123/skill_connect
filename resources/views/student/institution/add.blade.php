@extends('layouts.app')
@section('title', 'Add Student — ' . $institution->name)
@section('page-title', 'Add Student to Institution')

@section('content')

{{-- Breadcrumbs --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;" class="animate-fade-up">
    <div style="display:flex;align-items:center;gap:0.75rem;">
        <a href="{{ route('institution.students.index') }}" style="color:#6b7280;text-decoration:none;font-size:0.875rem;">My Students</a>
        <span style="color:#4b5563;">/</span>
        <span style="color:#e2e8f0;font-size:0.875rem;">Add Student</span>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;" class="animate-fade-up">

    {{-- Left: Register New Student --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">🆕 Register New Student Account</span>
        </div>
        <div class="card-body">
            <p style="color:#94a3b8;font-size:0.875rem;margin-bottom:1.5rem;line-height:1.5;">Register a fresh student user. This will create a login account and link them directly to {{ $institution->name }}.</p>
            
            <form method="POST" action="{{ route('institution.students.store') }}">
                @csrf
                
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. Mary Atieno" required>
                    @error('name') <div class="field-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="e.g. mary@gmail.com" required>
                    @error('email') <div class="field-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="+254700000000">
                    @error('phone') <div class="field-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Admission / Registration Number</label>
                    <input type="text" name="registration_number" class="form-control @error('registration_number') is-invalid @enderror" value="{{ old('registration_number') }}" placeholder="e.g. NTI/2026/0991">
                    @error('registration_number') <div class="field-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                    @error('password') <div class="field-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary btn-full" style="margin-top:1.5rem;">Create & Link Student</button>
            </form>
        </div>
    </div>

    {{-- Right: Link Existing Unassigned Student --}}
    <div style="display:grid;gap:1.5rem;align-content:start;">
        <div class="card">
            <div class="card-header">
                <span class="card-title">🔗 Claim Unassigned Student</span>
            </div>
            <div class="card-body">
                <p style="color:#94a3b8;font-size:0.875rem;margin-bottom:1.25rem;line-height:1.5;">Search for students who registered independently or have no institution linked, and assign them to your institution.</p>
                
                {{-- Search unassigned form --}}
                <form method="GET" action="{{ route('institution.students.add') }}" style="display:flex;gap:0.5rem;margin-bottom:1.5rem;">
                    <input type="text" name="search_unassigned" class="form-control" placeholder="Search by name or email…" value="{{ request('search_unassigned') }}" style="height:38px;">
                    <button type="submit" class="btn btn-outline btn-sm" style="height:38px;white-space:nowrap;">Search</button>
                    @if(request()->filled('search_unassigned'))
                    <a href="{{ route('institution.students.add') }}" class="btn btn-outline btn-sm" style="height:38px;display:flex;align-items:center;">✕</a>
                    @endif
                </form>

                <div style="overflow-x:auto;">
                    <table class="sc-table" style="font-size:0.85rem;">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($unassignedStudents as $st)
                            <tr>
                                <td>
                                    <div style="display:flex;align-items:center;gap:0.5rem;">
                                        <img src="{{ $st->user?->getAvatarUrl() }}" style="width:30px;height:30px;border-radius:6px;">
                                        <div>
                                            <div style="font-weight:600;color:#e2e8f0;">{{ $st->user?->name }}</div>
                                            <div style="font-size:0.75rem;color:#6b7280;">{{ $st->user?->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('institution.students.assign') }}">
                                        @csrf
                                        <input type="hidden" name="student_id" value="{{ $st->id }}">
                                        <button type="submit" class="btn btn-sm btn-outline" style="font-size:0.7rem;padding:0.35rem 0.65rem;" onmouseover="this.style.borderColor='var(--sc-success)';this.style.color='var(--sc-success)'" onmouseout="this.style.borderColor='var(--sc-dark-border)';this.style.color='#cbd5e1'">
                                            + Link Student
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" style="text-align:center;color:#4b5563;padding:2rem;">No unassigned students found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($unassignedStudents->hasPages())
                <div style="padding:0.75rem 0 0;border-top:1px solid var(--sc-dark-border);margin-top:1rem;">
                    {{ $unassignedStudents->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
