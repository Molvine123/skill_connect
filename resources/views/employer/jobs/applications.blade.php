@extends('layouts.app')

@section('title', 'Job Applications')
@section('page-title', 'Applications: ' . $job->title)

@section('content')
<div class="animate-fade-up">

    <div style="margin-bottom:1.5rem;">
        <a href="{{ route('employer.jobs.index') }}" style="color:#0d9488;text-decoration:none;font-weight:600;font-size:.9rem;">← Back to Job Listings</a>
        <div style="margin-top:.5rem;display:flex;align-items:center;gap:.75rem;">
            <h2 style="font-size:1.25rem;font-weight:700;">{{ $job->title }}</h2>
            <span style="background:{{ $job->type_badge_color }};color:#fff;padding:.15rem .6rem;border-radius:20px;font-size:.7rem;font-weight:600;">{{ $job->type_label }}</span>
        </div>
        <p style="color:#6b7280;font-size:.85rem;margin-top:.25rem;">Total Applications: {{ $job->applications_count }}</p>
    </div>

    @if($job->applications->isEmpty())
    <div class="table-container" style="text-align:center;padding:3rem;">
        <div style="font-size:3rem;margin-bottom:1rem;">👥</div>
        <h3 style="font-size:1.1rem;font-weight:600;color:#6b7280;">No Applications Yet</h3>
        <p style="color:#9ca3af;font-size:.9rem;margin-top:.25rem;">As students apply, they will appear here.</p>
    </div>
    @else
    <div style="display:grid;gap:1rem;">
        @foreach($job->applications as $app)
        <div class="table-container" style="padding:1.5rem;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:1.5rem;flex-wrap:wrap;">
                
                {{-- Student Information --}}
                <div style="display:flex;gap:1.25rem;flex:1;">
                    <img src="{{ $app->student->user->getAvatarUrl() }}" style="width:54px;height:54px;border-radius:50%;object-fit:cover;" alt="">
                    <div>
                        <h3 style="font-size:1rem;font-weight:700;margin-bottom:.25rem;">
                            {{ $app->student->user->name }}
                        </h3>
                        <p style="color:#6b7280;font-size:.85rem;margin-bottom:.5rem;">
                            🎓 {{ $app->student->institution ? $app->student->institution->name : 'Independent Student' }}
                        </p>
                        
                        {{-- Skills --}}
                        <div style="display:flex;flex-wrap:wrap;gap:.4rem;margin-bottom:.75rem;">
                            @php
                                $categories = $app->student->enrollments->map(fn($e) => $e->program->category->name)->unique();
                            @endphp
                            @foreach($categories as $cat)
                                <span style="background:rgba(13,148,136,0.1);color:#0d9488;padding:.15rem .5rem;border-radius:6px;font-size:.7rem;font-weight:600;">{{ $cat }}</span>
                            @endforeach
                            @if($app->student->location)
                                <span style="background:rgba(107,114,128,0.1);color:#4b5563;padding:.15rem .5rem;border-radius:6px;font-size:.7rem;font-weight:600;">📍 {{ $app->student->location }}</span>
                            @endif
                        </div>

                        {{-- Bio/Cover Letter snippet --}}
                        @if($app->cover_letter)
                            <div style="background:rgba(255,255,255,0.02);border:1px solid var(--sc-dark-border);border-radius:8px;padding:.75rem;font-size:.85rem;margin-bottom:.75rem;max-width:600px;">
                                <strong>Cover Letter:</strong><br>
                                <p style="margin-top:.25rem;color:#d1d5db;">{{ $app->cover_letter }}</p>
                            </div>
                        @endif

                        {{-- Resume/CV Link --}}
                        @if($app->cv_file)
                            <a href="{{ asset('storage/' . $app->cv_file) }}" target="_blank" class="btn btn-secondary" style="font-size:.75rem;padding:.4rem .8rem;display:inline-flex;align-items:center;gap:.3rem;">
                                📄 View Resume / CV
                            </a>
                        @endif
                        <a href="{{ route('employer.portfolio', $app->student->id) }}" class="btn btn-secondary" style="font-size:.75rem;padding:.4rem .8rem;display:inline-flex;align-items:center;gap:.3rem;margin-left:.5rem;">
                            💼 View Full Portfolio
                        </a>
                    </div>
                </div>

                {{-- Status & Actions --}}
                <div style="text-align:right;min-width:220px;">
                    <div style="margin-bottom:1rem;">
                        <span style="background:{{ $app->status_color }};color:#fff;padding:.25rem .75rem;border-radius:20px;font-size:.8rem;font-weight:600;">
                            {{ $app->status_label }}
                        </span>
                        <div style="color:#6b7280;font-size:.75rem;margin-top:.25rem;">
                            Applied {{ $app->applied_at ? $app->applied_at->diffForHumans() : $app->created_at->diffForHumans() }}
                        </div>
                    </div>

                    {{-- Schedule Interview Form --}}
                    @if($app->status === 'interview_scheduled' && $app->interview)
                        <div style="text-align:left;background:rgba(124,58,237,0.05);border:1px solid rgba(124,58,237,0.2);border-radius:8px;padding:.75rem;font-size:.8rem;margin-bottom:1rem;color:#c084fc;">
                            <strong>🗓️ Scheduled Interview:</strong><br>
                            Date: {{ $app->interview->interview_date->format('d M Y') }} at {{ $app->interview->interview_time }}<br>
                            @if($app->interview->venue)Venue: {{ $app->interview->venue }}<br>@endif
                            @if($app->interview->meeting_link)<a href="{{ $app->interview->meeting_link }}" target="_blank" style="color:#a78bfa;text-decoration:underline;">Join Meeting</a><br>@endif
                        </div>
                    @endif

                    <form method="POST" action="{{ route('employer.applications.status', $app->id) }}" style="display:flex;gap:.5rem;justify-content:flex-end;margin-bottom:.5rem;">
                        @csrf
                        <select name="status" class="form-input" style="width:auto;font-size:.8rem;padding:.4rem .8rem;" onchange="this.form.submit()">
                            <option value="">Change Status...</option>
                            @foreach(App\Models\JobApplication::STATUSES as $k => $val)
                                <option value="{{ $k }}" {{ $app->status===$k?'selected':'' }}>{{ $val['label'] }}</option>
                            @endforeach
                        </select>
                    </form>

                    <button onclick="toggleInterviewForm({{ $app->id }})" class="btn btn-secondary" style="font-size:.8rem;padding:.4rem .8rem;width:100%;">
                        🗓️ {{ $app->interview ? 'Reschedule Interview' : 'Schedule Interview' }}
                    </button>

                    {{-- Interview Scheduling Form (Hidden by default) --}}
                    <div id="interview-form-{{ $app->id }}" style="display:none;margin-top:1rem;text-align:left;background:rgba(255,255,255,0.02);border:1px solid var(--sc-dark-border);border-radius:8px;padding:1rem;">
                        <h4 style="font-size:.9rem;font-weight:600;margin-bottom:.75rem;color:#fff;">Schedule Interview</h4>
                        <form method="POST" action="{{ route('employer.applications.interview', $app->id) }}">
                            @csrf
                            <div style="margin-bottom:.75rem;">
                                <label class="form-label" style="font-size:.75rem;">Date *</label>
                                <input type="date" name="interview_date" value="{{ $app->interview ? $app->interview->interview_date->format('Y-m-d') : '' }}" class="form-input" style="font-size:.8rem;padding:.4rem;" required>
                            </div>
                            <div style="margin-bottom:.75rem;">
                                <label class="form-label" style="font-size:.75rem;">Time *</label>
                                <input type="time" name="interview_time" value="{{ $app->interview ? $app->interview->interview_time : '' }}" class="form-input" style="font-size:.8rem;padding:.4rem;" required>
                            </div>
                            <div style="margin-bottom:.75rem;">
                                <label class="form-label" style="font-size:.75rem;">Venue (Physical Location)</label>
                                <input type="text" name="venue" value="{{ $app->interview ? $app->interview->venue : '' }}" class="form-input" style="font-size:.8rem;padding:.4rem;" placeholder="e.g. Safaricom HQ, Boardroom 4">
                            </div>
                            <div style="margin-bottom:.75rem;">
                                <label class="form-label" style="font-size:.75rem;">Virtual Meeting Link</label>
                                <input type="url" name="meeting_link" value="{{ $app->interview ? $app->interview->meeting_link : '' }}" class="form-input" style="font-size:.8rem;padding:.4rem;" placeholder="e.g. https://teams.microsoft.com/...">
                            </div>
                            <div style="margin-bottom:1rem;">
                                <label class="form-label" style="font-size:.75rem;">Remarks / Instructions</label>
                                <textarea name="remarks" rows="2" class="form-input" style="font-size:.8rem;padding:.4rem;" placeholder="e.g. Please bring a copy of your portfolio projects...">{{ $app->interview ? $app->interview->remarks : '' }}</textarea>
                            </div>
                            <div style="display:flex;gap:.5rem;">
                                <button type="submit" class="btn btn-primary" style="font-size:.75rem;padding:.4rem .8rem;">Save</button>
                                <button type="button" onclick="toggleInterviewForm({{ $app->id }})" class="btn btn-secondary" style="font-size:.75rem;padding:.4rem .8rem;">Cancel</button>
                            </div>
                        </form>
                    </div>

                </div>

            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>

@push('scripts')
<script>
function toggleInterviewForm(id) {
    const el = document.getElementById('interview-form-' + id);
    if(el.style.display === 'none') {
        el.style.display = 'block';
    } else {
        el.style.display = 'none';
    }
}
</script>
@endpush
@endsection
