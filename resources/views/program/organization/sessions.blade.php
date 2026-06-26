@extends('layouts.app')
@section('title', 'Manage Sessions')
@section('page-title', 'Training Sessions')

@section('content')
<div class="animate-fade-up">

{{-- Breadcrumb --}}
<div style="margin-bottom:1.5rem;font-size:0.875rem;color:#94a3b8;display:flex;align-items:center;gap:0.5rem;">
    <a href="{{ route('organization.programs.index') }}" style="color:#6b7280;text-decoration:none;">My Programs</a>
    <span style="color:#475569;">/</span>
    <a href="{{ route('organization.programs.show', $program->id) }}" style="color:#6b7280;text-decoration:none;">{{ Str::limit($program->name, 30) }}</a>
    <span style="color:#475569;">/</span>
    <span style="color:#f1f5f9;font-weight:500;">Sessions</span>
</div>

<div style="display:grid;grid-template-columns:3fr 2fr;gap:1.5rem;">

    {{-- Left: Sessions List --}}
    <div style="display:grid;gap:1rem;align-content:start;">
        <div class="card">
            <div class="card-header"><span class="card-title">📅 All Sessions ({{ $program->sessions->count() }})</span></div>
            
            @forelse($program->sessions->sortByDesc('start_date') as $session)
            @php 
                $isPast = $session->end_date < now(); 
                $isOngoing = $session->start_date <= now() && $session->end_date >= now();
                $statusColor = $isOngoing ? '#34d399' : ($isPast ? '#6b7280' : '#818cf8');
                $statusText = $isOngoing ? 'Ongoing' : ($isPast ? 'Completed' : 'Upcoming');
                $isOnlineMode = in_array($program->mode, ['online', 'hybrid']);
                $virtualClass = $session->virtualClass;
            @endphp
            <div style="padding:1.25rem;border-bottom:1px solid rgba(42,42,74,0.5);display:flex;gap:1.25rem;align-items:flex-start;">
                <div style="width:64px;height:64px;border-radius:12px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);display:flex;flex-direction:column;align-items:center;justify-content:center;flex-shrink:0;">
                    <div style="font-size:0.75rem;color:{{ $statusColor }};text-transform:uppercase;font-weight:600;">{{ $session->start_date->format('M') }}</div>
                    <div style="font-size:1.5rem;font-weight:800;color:#f1f5f9;line-height:1.1;">{{ $session->start_date->format('d') }}</div>
                </div>
                
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:0.25rem;">
                        <div style="font-size:1.05rem;font-weight:700;color:#f1f5f9;">{{ $session->title }}</div>
                        <div style="display:flex;gap:0.5rem;align-items:center;">
                            @if($isOnlineMode)
                            <span style="font-size:0.7rem;color:#06b6d4;border:1px solid rgba(6,182,212,0.35);padding:0.15rem 0.5rem;border-radius:12px;">🌐 Online</span>
                            @endif
                            <span style="font-size:0.7rem;color:{{ $statusColor }};border:1px solid {{ $statusColor }}40;padding:0.15rem 0.5rem;border-radius:12px;">{{ $statusText }}</span>
                        </div>
                    </div>
                    
                    <div style="font-size:0.8rem;color:#94a3b8;margin-bottom:0.5rem;">{{ $session->description }}</div>
                    
                    <div style="display:flex;gap:1rem;flex-wrap:wrap;font-size:0.75rem;color:#6b7280;">
                        <span title="Time">🕒 {{ $session->start_date->format('H:i') }} - {{ $session->end_date->format('H:i') }}</span>
                        <span title="Venue/Link">📍 {{ $session->venue ?? 'Online' }}</span>
                        @if($session->trainer_information)<span title="Trainer">👨‍🏫 {{ Str::limit($session->trainer_information, 20) }}</span>@endif
                    </div>
                    
                    <div style="margin-top:0.75rem;display:flex;gap:0.5rem;flex-wrap:wrap;">
                        {{-- Virtual Class button (only for online/hybrid programs) --}}
                        @if($isOnlineMode)
                            @if($virtualClass)
                                <a href="{{ route('virtual-class.room', $virtualClass->id) }}"
                                   class="btn btn-primary"
                                   style="padding:0.35rem 0.85rem;font-size:0.75rem;background:linear-gradient(135deg,#0891b2,#06b6d4);">
                                    📹 {{ $virtualClass->status === 'active' ? 'Enter Live Room' : 'Open Classroom' }}
                                </a>
                            @else
                                <form method="POST" action="{{ route('virtual-class.create', $session->id) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-primary"
                                            style="padding:0.35rem 0.85rem;font-size:0.75rem;background:linear-gradient(135deg,#0891b2,#06b6d4);">
                                        📹 Create Virtual Room
                                    </button>
                                </form>
                            @endif
                        @endif
                        <a href="{{ route('organization.programs.attendance', [$program->id, $session->id]) }}" class="btn btn-outline" style="padding:0.35rem 0.75rem;font-size:0.75rem;border-color:rgba(16,185,129,0.3);color:#34d399;">
                            ✓ Take Attendance
                        </a>
                        <form method="POST" action="{{ route('organization.programs.sessions.destroy', [$program->id, $session->id]) }}" onsubmit="return confirm('Delete this session?');" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline" style="padding:0.35rem 0.75rem;font-size:0.75rem;border-color:rgba(239,68,68,0.3);color:#ef4444;">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div style="padding:3rem;text-align:center;">
                <div style="font-size:3rem;margin-bottom:1rem;">📅</div>
                <div style="font-size:1.1rem;font-weight:600;color:#f1f5f9;">No sessions scheduled</div>
                <div style="font-size:0.85rem;color:#6b7280;margin-top:0.5rem;">Add your first training session using the form.</div>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Right: Add Session Form --}}
    <div style="display:grid;align-content:start;">
        <div class="card" style="border-top:3px solid #6366f1;">
            <div class="card-header"><span class="card-title">➕ Add New Session</span></div>
            <div class="card-body">
                <form method="POST" action="{{ route('organization.programs.sessions.store', $program->id) }}" style="display:grid;gap:1rem;">
                    @csrf
                    
                    <div>
                        <label style="font-size:0.8125rem;color:#94a3b8;font-weight:500;margin-bottom:0.375rem;display:block;">Session Title <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="title" required value="{{ old('title') }}" placeholder="e.g. Week 1: Introduction" style="background:rgba(255,255,255,0.04);border:1px solid rgba(42,42,74,0.6);border-radius:10px;padding:0.65rem 1rem;color:#f1f5f9;width:100%;font-size:0.875rem;">
                        @error('title')<span style="color:#ef4444;font-size:0.75rem;">{{ $message }}</span>@enderror
                    </div>
                    
                    <div>
                        <label style="font-size:0.8125rem;color:#94a3b8;font-weight:500;margin-bottom:0.375rem;display:block;">Start Date & Time <span style="color:#ef4444;">*</span></label>
                        <input type="datetime-local" name="start_date" required value="{{ old('start_date') }}" style="background:rgba(255,255,255,0.04);border:1px solid rgba(42,42,74,0.6);border-radius:10px;padding:0.65rem 1rem;color:#f1f5f9;width:100%;font-size:0.875rem;">
                        @error('start_date')<span style="color:#ef4444;font-size:0.75rem;">{{ $message }}</span>@enderror
                    </div>
                    
                    <div>
                        <label style="font-size:0.8125rem;color:#94a3b8;font-weight:500;margin-bottom:0.375rem;display:block;">End Date & Time <span style="color:#ef4444;">*</span></label>
                        <input type="datetime-local" name="end_date" required value="{{ old('end_date') }}" style="background:rgba(255,255,255,0.04);border:1px solid rgba(42,42,74,0.6);border-radius:10px;padding:0.65rem 1rem;color:#f1f5f9;width:100%;font-size:0.875rem;">
                        @error('end_date')<span style="color:#ef4444;font-size:0.75rem;">{{ $message }}</span>@enderror
                    </div>
                    
                    <div>
                        <label style="font-size:0.8125rem;color:#94a3b8;font-weight:500;margin-bottom:0.375rem;display:block;">Description</label>
                        <textarea name="description" rows="2" style="background:rgba(255,255,255,0.04);border:1px solid rgba(42,42,74,0.6);border-radius:10px;padding:0.65rem 1rem;color:#f1f5f9;width:100%;font-size:0.875rem;">{{ old('description') }}</textarea>
                    </div>
                    
                    <div>
                        <label style="font-size:0.8125rem;color:#94a3b8;font-weight:500;margin-bottom:0.375rem;display:block;">Venue</label>
                        <input type="text" name="venue" value="{{ old('venue', $program->venue) }}" style="background:rgba(255,255,255,0.04);border:1px solid rgba(42,42,74,0.6);border-radius:10px;padding:0.65rem 1rem;color:#f1f5f9;width:100%;font-size:0.875rem;">
                    </div>
                    
                    <div>
                        <label style="font-size:0.8125rem;color:#94a3b8;font-weight:500;margin-bottom:0.375rem;display:block;">Meeting Link (Online)</label>
                        <input type="url" name="meeting_link" value="{{ old('meeting_link') }}" placeholder="https://..." style="background:rgba(255,255,255,0.04);border:1px solid rgba(42,42,74,0.6);border-radius:10px;padding:0.65rem 1rem;color:#f1f5f9;width:100%;font-size:0.875rem;">
                    </div>
                    
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
                        <div>
                            <label style="font-size:0.8125rem;color:#94a3b8;font-weight:500;margin-bottom:0.375rem;display:block;">Max Participants</label>
                            <input type="number" name="max_participants" value="{{ old('max_participants', $program->capacity) }}" style="background:rgba(255,255,255,0.04);border:1px solid rgba(42,42,74,0.6);border-radius:10px;padding:0.65rem 1rem;color:#f1f5f9;width:100%;font-size:0.875rem;">
                        </div>
                        <div>
                            <label style="font-size:0.8125rem;color:#94a3b8;font-weight:500;margin-bottom:0.375rem;display:block;">Trainer</label>
                            <input type="text" name="trainer_information" value="{{ old('trainer_information') }}" style="background:rgba(255,255,255,0.04);border:1px solid rgba(42,42,74,0.6);border-radius:10px;padding:0.65rem 1rem;color:#f1f5f9;width:100%;font-size:0.875rem;">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="margin-top:0.5rem;justify-content:center;">Add Session</button>
                </form>
            </div>
        </div>
    </div>
</div>

</div>
@endsection
