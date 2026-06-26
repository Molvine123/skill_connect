@extends('layouts.app')
@section('title', 'Create Program')
@section('page-title', 'Create Program')

@section('content')
<div class="animate-fade-up" style="max-width:1100px;">

{{-- Back --}}
<a href="{{ route('organization.programs.index') }}" style="display:inline-flex;align-items:center;gap:0.5rem;color:#6b7280;text-decoration:none;font-size:0.875rem;margin-bottom:1.5rem;transition:color .2s;" onmouseover="this.style.color='#f1f5f9'" onmouseout="this.style.color='#6b7280'">
    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    Back to My Programs
</a>

<form method="POST" action="{{ route('organization.programs.store') }}">
@csrf
<div style="display:grid;grid-template-columns:2fr 1fr;gap:1.5rem;">

    {{-- Main Form --}}
    <div style="display:grid;gap:1.25rem;">

        {{-- Basic Info --}}
        <div class="card">
            <div class="card-header"><span class="card-title">📋 Program Information</span></div>
            <div class="card-body" style="display:grid;gap:1rem;">
                <div>
                    <label style="font-size:0.8125rem;color:#94a3b8;font-weight:500;margin-bottom:0.375rem;display:block;">Program Name <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Web Development Bootcamp" style="background:rgba(255,255,255,0.04);border:1px solid rgba(42,42,74,0.6);border-radius:10px;padding:0.75rem 1rem;color:#f1f5f9;width:100%;font-size:0.9rem;">
                    @error('name')<p style="color:#ef4444;font-size:0.75rem;margin-top:0.25rem;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label style="font-size:0.8125rem;color:#94a3b8;font-weight:500;margin-bottom:0.375rem;display:block;">Category <span style="color:#ef4444;">*</span></label>
                    <select name="category_id" required style="background:rgba(255,255,255,0.04);border:1px solid rgba(42,42,74,0.6);border-radius:10px;padding:0.75rem 1rem;color:#f1f5f9;width:100%;font-size:0.9rem;">
                        <option value="">Select a category...</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id')==$cat->id?'selected':'' }}>
                            {{ $cat->icon ?? '📚' }} {{ $cat->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('category_id')<p style="color:#ef4444;font-size:0.75rem;margin-top:0.25rem;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label style="font-size:0.8125rem;color:#94a3b8;font-weight:500;margin-bottom:0.375rem;display:block;">Description <span style="color:#ef4444;">*</span></label>
                    <textarea name="description" rows="5" required placeholder="Describe what this program covers, who it's for, and what students will gain..." style="background:rgba(255,255,255,0.04);border:1px solid rgba(42,42,74,0.6);border-radius:10px;padding:0.75rem 1rem;color:#f1f5f9;width:100%;font-size:0.9rem;resize:vertical;">{{ old('description') }}</textarea>
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
                        <input type="text" name="duration" value="{{ old('duration') }}" required placeholder="e.g. 6 Weeks, 3 Months" style="background:rgba(255,255,255,0.04);border:1px solid rgba(42,42,74,0.6);border-radius:10px;padding:0.75rem 1rem;color:#f1f5f9;width:100%;font-size:0.9rem;">
                        @error('duration')<p style="color:#ef4444;font-size:0.75rem;margin-top:0.25rem;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label style="font-size:0.8125rem;color:#94a3b8;font-weight:500;margin-bottom:0.375rem;display:block;">Program Fee (KES) <span style="color:#ef4444;">*</span></label>
                        <input type="number" name="cost" value="{{ old('cost', 0) }}" min="0" step="0.01" required style="background:rgba(255,255,255,0.04);border:1px solid rgba(42,42,74,0.6);border-radius:10px;padding:0.75rem 1rem;color:#f1f5f9;width:100%;font-size:0.9rem;">
                        @error('cost')<p style="color:#ef4444;font-size:0.75rem;margin-top:0.25rem;">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div>
                        <label style="font-size:0.8125rem;color:#94a3b8;font-weight:500;margin-bottom:0.375rem;display:block;">Delivery Mode <span style="color:#ef4444;">*</span></label>
                        <select name="mode" id="modeSelect" required style="background:rgba(255,255,255,0.04);border:1px solid rgba(42,42,74,0.6);border-radius:10px;padding:0.75rem 1rem;color:#f1f5f9;width:100%;font-size:0.9rem;" onchange="updateVenuePlaceholder(this.value)">
                            <option value="in_person" {{ old('mode')=='in_person'?'selected':'' }}>🏢 In-Person</option>
                            <option value="online" {{ old('mode')=='online'?'selected':'' }}>🌐 Online</option>
                            <option value="hybrid" {{ old('mode')=='hybrid'?'selected':'' }}>🔀 Hybrid</option>
                        </select>
                        @error('mode')<p style="color:#ef4444;font-size:0.75rem;margin-top:0.25rem;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label style="font-size:0.8125rem;color:#94a3b8;font-weight:500;margin-bottom:0.375rem;display:block;">Capacity (students) <span style="color:#ef4444;">*</span></label>
                        <input type="number" name="capacity" value="{{ old('capacity', 50) }}" min="1" required style="background:rgba(255,255,255,0.04);border:1px solid rgba(42,42,74,0.6);border-radius:10px;padding:0.75rem 1rem;color:#f1f5f9;width:100%;font-size:0.9rem;">
                        @error('capacity')<p style="color:#ef4444;font-size:0.75rem;margin-top:0.25rem;">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <label style="font-size:0.8125rem;color:#94a3b8;font-weight:500;margin-bottom:0.375rem;display:block;">Venue / Meeting Link</label>
                    <input type="text" name="venue" id="venueInput" value="{{ old('venue') }}" placeholder="Physical address or meeting link" style="background:rgba(255,255,255,0.04);border:1px solid rgba(42,42,74,0.6);border-radius:10px;padding:0.75rem 1rem;color:#f1f5f9;width:100%;font-size:0.9rem;">
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
                    <textarea name="requirements" rows="3" placeholder="Any prior knowledge or qualifications needed..." style="background:rgba(255,255,255,0.04);border:1px solid rgba(42,42,74,0.6);border-radius:10px;padding:0.75rem 1rem;color:#f1f5f9;width:100%;font-size:0.9rem;resize:vertical;">{{ old('requirements') }}</textarea>
                </div>
                <div>
                    <label style="font-size:0.8125rem;color:#94a3b8;font-weight:500;margin-bottom:0.375rem;display:block;">Learning Outcomes</label>
                    <textarea name="learning_outcomes" rows="3" placeholder="What skills and knowledge will students gain..." style="background:rgba(255,255,255,0.04);border:1px solid rgba(42,42,74,0.6);border-radius:10px;padding:0.75rem 1rem;color:#f1f5f9;width:100%;font-size:0.9rem;resize:vertical;">{{ old('learning_outcomes') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Status --}}
        <div class="card">
            <div class="card-header"><span class="card-title">🚀 Publication Status</span></div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
                    @foreach(['draft'=>['📝','Draft','Save as draft. Students cannot see or enroll.','rgba(245,158,11,0.15)','#fbbf24'],'published'=>['🟢','Published','Visible to students. Enrollment is open.','rgba(16,185,129,0.15)','#34d399']] as $val=>[$ico,$lbl,$desc,$bg,$color])
                    <label style="cursor:pointer;">
                        <input type="radio" name="status" value="{{ $val }}" {{ old('status','published')==$val?'checked':'' }} style="display:none;" onchange="updateStatusCards()">
                        <div class="status-card-{{ $val }}" style="background:{{ old('status','published')==$val ? $bg : 'rgba(255,255,255,0.03)' }};border:2px solid {{ old('status','published')==$val ? $color : 'rgba(42,42,74,0.5)' }};border-radius:12px;padding:1rem;transition:all .2s;">
                            <div style="font-size:1.25rem;margin-bottom:0.375rem;">{{ $ico }}</div>
                            <div style="font-weight:700;color:{{ old('status','published')==$val ? $color : '#f1f5f9' }};font-size:0.9rem;">{{ $lbl }}</div>
                            <div style="font-size:0.75rem;color:#6b7280;margin-top:0.25rem;">{{ $desc }}</div>
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
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Create Program
            </button>
            <a href="{{ route('organization.programs.index') }}" class="btn btn-outline">Cancel</a>
        </div>
    </div>

    {{-- Right Panel --}}
    <div style="display:grid;gap:1.25rem;align-content:start;">
        <div class="card">
            <div class="card-header"><span class="card-title">💡 Tips</span></div>
            <div class="card-body" style="display:grid;gap:0.75rem;">
                @foreach(['Write a clear, detailed description to attract more students','Set realistic capacity limits to manage quality training','Use learning outcomes to set clear expectations','Start as Draft to preview before publishing'] as $tip)
                <div style="display:flex;gap:0.625rem;align-items:flex-start;">
                    <span style="color:#34d399;flex-shrink:0;margin-top:1px;">✓</span>
                    <span style="font-size:0.8rem;color:#94a3b8;line-height:1.5;">{{ $tip }}</span>
                </div>
                @endforeach
            </div>
        </div>
        <div class="card">
            <div class="card-header"><span class="card-title">🗺️ Delivery Modes</span></div>
            <div class="card-body" style="display:grid;gap:0.625rem;">
                @foreach([['🏢','In-Person','Physical classroom. Enter a location address.'],['🌐','Online','Virtual sessions. Enter a meeting link.'],['🔀','Hybrid','Mix of both. Enter both address & link.']] as [$ico,$m,$d])
                <div style="padding:0.625rem;background:rgba(255,255,255,0.03);border-radius:8px;">
                    <div style="font-size:0.8rem;font-weight:600;color:#f1f5f9;">{{ $ico }} {{ $m }}</div>
                    <div style="font-size:0.75rem;color:#6b7280;margin-top:0.125rem;">{{ $d }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
</form>
</div>

@push('scripts')
<script>
function updateVenuePlaceholder(mode) {
    const input = document.getElementById('venueInput');
    const map = {online:'Meeting link (e.g. Zoom, Google Meet)', in_person:'Physical address / venue name', hybrid:'Address & meeting link'};
    input.placeholder = map[mode] || 'Venue / link';
}
</script>
@endpush
@endsection
