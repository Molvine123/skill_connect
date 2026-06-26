@extends('layouts.app')
@section('title', 'Edit Program')
@section('page-title', 'Edit Program')

@section('content')
<div class="animate-fade-up" style="max-width:1100px;">

{{-- Back --}}
<a href="{{ route('organization.programs.show', $program->id) }}" style="display:inline-flex;align-items:center;gap:0.5rem;color:#6b7280;text-decoration:none;font-size:0.875rem;margin-bottom:1.5rem;transition:color .2s;" onmouseover="this.style.color='#f1f5f9'" onmouseout="this.style.color='#6b7280'">
    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    Back to Program
</a>

<form method="POST" action="{{ route('organization.programs.update', $program->id) }}">
@csrf
@method('PUT')
<div style="display:grid;grid-template-columns:2fr 1fr;gap:1.5rem;">

    {{-- Main Form --}}
    <div style="display:grid;gap:1.25rem;">

        {{-- Basic Info --}}
        <div class="card">
            <div class="card-header"><span class="card-title">📋 Program Information</span></div>
            <div class="card-body" style="display:grid;gap:1rem;">
                <div>
                    <label style="font-size:0.8125rem;color:#94a3b8;font-weight:500;margin-bottom:0.375rem;display:block;">Program Name <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $program->name) }}" required style="background:rgba(255,255,255,0.04);border:1px solid rgba(42,42,74,0.6);border-radius:10px;padding:0.75rem 1rem;color:#f1f5f9;width:100%;font-size:0.9rem;">
                    @error('name')<p style="color:#ef4444;font-size:0.75rem;margin-top:0.25rem;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label style="font-size:0.8125rem;color:#94a3b8;font-weight:500;margin-bottom:0.375rem;display:block;">Category <span style="color:#ef4444;">*</span></label>
                    <select name="category_id" required style="background:rgba(255,255,255,0.04);border:1px solid rgba(42,42,74,0.6);border-radius:10px;padding:0.75rem 1rem;color:#f1f5f9;width:100%;font-size:0.9rem;">
                        <option value="">Select a category...</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $program->category_id)==$cat->id?'selected':'' }}>
                            {{ $cat->icon ?? '📚' }} {{ $cat->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('category_id')<p style="color:#ef4444;font-size:0.75rem;margin-top:0.25rem;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label style="font-size:0.8125rem;color:#94a3b8;font-weight:500;margin-bottom:0.375rem;display:block;">Description <span style="color:#ef4444;">*</span></label>
                    <textarea name="description" rows="5" required style="background:rgba(255,255,255,0.04);border:1px solid rgba(42,42,74,0.6);border-radius:10px;padding:0.75rem 1rem;color:#f1f5f9;width:100%;font-size:0.9rem;resize:vertical;">{{ old('description', $program->description) }}</textarea>
                    @error('description')<p style="color:#ef4444;font-size:0.75rem;margin-top:0.25rem;">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Logistics --}}
        <div class="card">
            <div class="card-header"><span class="card-title">⚙️ Program Logistics</span></div>
            <div class="card-body" style="display:grid;gap:1rem;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div>
                        <label style="font-size:0.8125rem;color:#94a3b8;font-weight:500;margin-bottom:0.375rem;display:block;">Duration <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="duration" value="{{ old('duration', $program->duration) }}" required style="background:rgba(255,255,255,0.04);border:1px solid rgba(42,42,74,0.6);border-radius:10px;padding:0.75rem 1rem;color:#f1f5f9;width:100%;font-size:0.9rem;">
                        @error('duration')<p style="color:#ef4444;font-size:0.75rem;margin-top:0.25rem;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label style="font-size:0.8125rem;color:#94a3b8;font-weight:500;margin-bottom:0.375rem;display:block;">Program Fee (KES) <span style="color:#ef4444;">*</span></label>
                        <input type="number" name="cost" value="{{ old('cost', $program->cost) }}" min="0" step="0.01" required style="background:rgba(255,255,255,0.04);border:1px solid rgba(42,42,74,0.6);border-radius:10px;padding:0.75rem 1rem;color:#f1f5f9;width:100%;font-size:0.9rem;">
                        @error('cost')<p style="color:#ef4444;font-size:0.75rem;margin-top:0.25rem;">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div>
                        <label style="font-size:0.8125rem;color:#94a3b8;font-weight:500;margin-bottom:0.375rem;display:block;">Delivery Mode <span style="color:#ef4444;">*</span></label>
                        <select name="mode" id="modeSelect" required style="background:rgba(255,255,255,0.04);border:1px solid rgba(42,42,74,0.6);border-radius:10px;padding:0.75rem 1rem;color:#f1f5f9;width:100%;font-size:0.9rem;" onchange="updateVenuePlaceholder(this.value)">
                            <option value="in_person" {{ old('mode', $program->mode)=='in_person'?'selected':'' }}>🏢 In-Person</option>
                            <option value="online" {{ old('mode', $program->mode)=='online'?'selected':'' }}>🌐 Online</option>
                            <option value="hybrid" {{ old('mode', $program->mode)=='hybrid'?'selected':'' }}>🔀 Hybrid</option>
                        </select>
                        @error('mode')<p style="color:#ef4444;font-size:0.75rem;margin-top:0.25rem;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label style="font-size:0.8125rem;color:#94a3b8;font-weight:500;margin-bottom:0.375rem;display:block;">Capacity (students) <span style="color:#ef4444;">*</span></label>
                        <input type="number" name="capacity" value="{{ old('capacity', $program->capacity) }}" min="1" required style="background:rgba(255,255,255,0.04);border:1px solid rgba(42,42,74,0.6);border-radius:10px;padding:0.75rem 1rem;color:#f1f5f9;width:100%;font-size:0.9rem;">
                        @error('capacity')<p style="color:#ef4444;font-size:0.75rem;margin-top:0.25rem;">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <label style="font-size:0.8125rem;color:#94a3b8;font-weight:500;margin-bottom:0.375rem;display:block;">Venue / Meeting Link</label>
                    <input type="text" name="venue" id="venueInput" value="{{ old('venue', $program->venue) }}" style="background:rgba(255,255,255,0.04);border:1px solid rgba(42,42,74,0.6);border-radius:10px;padding:0.75rem 1rem;color:#f1f5f9;width:100%;font-size:0.9rem;">
                    @error('venue')<p style="color:#ef4444;font-size:0.75rem;margin-top:0.25rem;">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Details --}}
        <div class="card">
            <div class="card-header"><span class="card-title">📝 Additional Details</span></div>
            <div class="card-body" style="display:grid;gap:1rem;">
                <div>
                    <label style="font-size:0.8125rem;color:#94a3b8;font-weight:500;margin-bottom:0.375rem;display:block;">Requirements / Prerequisites</label>
                    <textarea name="requirements" rows="3" style="background:rgba(255,255,255,0.04);border:1px solid rgba(42,42,74,0.6);border-radius:10px;padding:0.75rem 1rem;color:#f1f5f9;width:100%;font-size:0.9rem;resize:vertical;">{{ old('requirements', $program->requirements) }}</textarea>
                </div>
                <div>
                    <label style="font-size:0.8125rem;color:#94a3b8;font-weight:500;margin-bottom:0.375rem;display:block;">Learning Outcomes</label>
                    <textarea name="learning_outcomes" rows="3" style="background:rgba(255,255,255,0.04);border:1px solid rgba(42,42,74,0.6);border-radius:10px;padding:0.75rem 1rem;color:#f1f5f9;width:100%;font-size:0.9rem;resize:vertical;">{{ old('learning_outcomes', $program->learning_outcomes) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Status --}}
        <div class="card">
            <div class="card-header"><span class="card-title">🚀 Publication Status</span></div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:0.75rem;">
                    @foreach(['draft'=>['📝','Draft','rgba(245,158,11,0.15)','#fbbf24'],'published'=>['🟢','Published','rgba(16,185,129,0.15)','#34d399'],'closed'=>['🔒','Closed','rgba(107,114,128,0.15)','#6b7280']] as $val=>[$ico,$lbl,$bg,$color])
                    <label style="cursor:pointer;">
                        <input type="radio" name="status" value="{{ $val }}" {{ old('status', $program->status)==$val?'checked':'' }} style="display:none;" onchange="updateStatusCards()">
                        <div class="status-card-{{ $val }}" style="background:{{ old('status', $program->status)==$val ? $bg : 'rgba(255,255,255,0.03)' }};border:2px solid {{ old('status', $program->status)==$val ? $color : 'rgba(42,42,74,0.5)' }};border-radius:12px;padding:1rem;transition:all .2s;text-align:center;">
                            <div style="font-size:1.25rem;margin-bottom:0.375rem;">{{ $ico }}</div>
                            <div style="font-weight:700;color:{{ old('status', $program->status)==$val ? $color : '#f1f5f9' }};font-size:0.9rem;">{{ $lbl }}</div>
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('status')<p style="color:#ef4444;font-size:0.75rem;margin-top:0.5rem;">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Buttons --}}
        <div style="display:flex;gap:0.75rem;">
            <button type="submit" class="btn btn-primary" style="background:linear-gradient(135deg,#6366f1,#4f46e5);box-shadow:0 4px 15px rgba(99,102,241,0.3);">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Save Changes
            </button>
            <a href="{{ route('organization.programs.show', $program->id) }}" class="btn btn-outline">Cancel</a>
        </div>
    </div>
</form>

    {{-- Danger Zone --}}
    <div style="display:grid;gap:1.25rem;align-content:start;">
        <div class="card" style="border-color:rgba(239,68,68,0.3);">
            <div class="card-header" style="border-bottom-color:rgba(239,68,68,0.3);"><span class="card-title" style="color:#ef4444;">⚠️ Danger Zone</span></div>
            <div class="card-body" style="display:grid;gap:1rem;">
                <p style="font-size:0.875rem;color:#94a3b8;">Deleting this program will permanently remove it along with all sessions, enrollments, and associated records. This action cannot be undone.</p>
                <form action="{{ route('organization.programs.destroy', $program->id) }}" method="POST" onsubmit="return confirm('Are you absolutely sure you want to delete this program?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-full" style="background:rgba(239,68,68,0.1);color:#ef4444;border:1px solid rgba(239,68,68,0.3);">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-right:0.375rem;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Delete Program
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

</div>

@push('scripts')
<script>
function updateVenuePlaceholder(mode) {
    const input = document.getElementById('venueInput');
    const map = {online:'Meeting link (e.g. Zoom, Google Meet)', in_person:'Physical address / venue name', hybrid:'Address & meeting link'};
    input.placeholder = map[mode] || 'Venue / link';
}
function updateStatusCards() {
    const statuses = ['draft', 'published', 'closed'];
    const configs = {
        'draft': {bg: 'rgba(245,158,11,0.15)', color: '#fbbf24'},
        'published': {bg: 'rgba(16,185,129,0.15)', color: '#34d399'},
        'closed': {bg: 'rgba(107,114,128,0.15)', color: '#6b7280'}
    };
    const selected = document.querySelector('input[name="status"]:checked').value;
    
    statuses.forEach(status => {
        const card = document.querySelector(`.status-card-${status}`);
        const title = card.querySelector('div:nth-child(2)');
        if(status === selected) {
            card.style.background = configs[status].bg;
            card.style.borderColor = configs[status].color;
            title.style.color = configs[status].color;
        } else {
            card.style.background = 'rgba(255,255,255,0.03)';
            card.style.borderColor = 'rgba(42,42,74,0.5)';
            title.style.color = '#f1f5f9';
        }
    });
}
// Init on load
updateVenuePlaceholder(document.getElementById('modeSelect').value);
</script>
@endpush
@endsection
