@extends('layouts.app')
@section('title', 'Virtual Classroom — ' . $session->title)
@section('page-title', 'Virtual Classroom')

@section('content')

@php
    $isOrg     = auth()->user()->isOrganization();
    $isStudent = auth()->user()->isStudent();
    $joinUrl   = route('virtual-class.attend.join', $virtualClass->id);
    $leaveUrl  = route('virtual-class.attend.leave', $virtualClass->id);
    $chatGetUrl  = route('virtual-class.chat.get', $virtualClass->id);
    $chatSendUrl = route('virtual-class.chat.send', $virtualClass->id);
    $csrfToken = csrf_token();
@endphp

{{-- Session header --}}
<div style="background:linear-gradient(135deg,#0d1f0e,#0a2a1e);border:1px solid rgba(16,185,129,0.25);border-radius:16px;padding:1.25rem 1.75rem;margin-bottom:1.5rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
    <div>
        <div style="font-size:0.8rem;color:#6ee7b7;font-weight:500;margin-bottom:0.25rem;">📡 Live Virtual Classroom</div>
        <h1 style="font-size:1.25rem;font-weight:800;color:#f1f5f9;margin:0;">{{ $session->title }}</h1>
        <div style="font-size:0.825rem;color:#94a3b8;margin-top:0.2rem;">
            {{ $session->program?->name }} &middot;
            {{ $session->start_date?->format('D, M j Y · H:i') }} &mdash; {{ $session->end_date?->format('H:i') }}
        </div>
    </div>
    <div style="display:flex;gap:0.75rem;align-items:center;">
        @if($isOrg)
            @if($virtualClass->status === 'pending')
            <form method="POST" action="{{ route('virtual-class.open', $virtualClass->id) }}">
                @csrf
                <button type="submit" class="btn btn-primary" style="background:#10b981;">▶ Start Session</button>
            </form>
            @elseif($virtualClass->status === 'active')
            <form method="POST" action="{{ route('virtual-class.close', $virtualClass->id) }}" onsubmit="return confirm('End this session? Attendance will be finalized.')">
                @csrf
                <button type="submit" class="btn btn-outline" style="border-color:rgba(239,68,68,0.4);color:#ef4444;">⏹ End Session</button>
            </form>
            @else
            <span style="padding:0.35rem 0.875rem;background:rgba(100,116,139,0.15);border:1px solid rgba(100,116,139,0.3);border-radius:8px;font-size:0.825rem;color:#94a3b8;">Session Closed</span>
            @endif
        @endif
        <span class="badge {{ $virtualClass->status === 'active' ? 'badge-active' : ($virtualClass->status === 'closed' ? 'badge-deact' : 'badge-pending') }}" style="font-size:0.8rem;">
            {{ ucfirst($virtualClass->status) }}
        </span>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success" style="margin-bottom:1rem;">{{ session('success') }}</div>
@endif
@if($virtualClass->status === 'pending' && $isStudent)
<div class="alert alert-warning" style="margin-bottom:1rem;">⏳ The host has not started this session yet. Please wait.</div>
@endif

{{-- Main room layout: Video | Sidebar --}}
<div style="display:grid;grid-template-columns:1fr 360px;gap:1.5rem;align-items:start;">

    {{-- ── LEFT: Jitsi Video Frame ── --}}
    <div style="display:flex;flex-direction:column;gap:1rem;">
        <div id="jitsi-container" style="width:100%;border-radius:16px;overflow:hidden;background:#000;border:1px solid rgba(16,185,129,0.2);min-height:520px;display:flex;align-items:center;justify-content:center;">
            @if($virtualClass->status === 'active')
                <div id="jitsi-meet" style="width:100%;height:540px;"></div>
            @else
                <div style="text-align:center;padding:3rem 2rem;">
                    <div style="font-size:3.5rem;margin-bottom:1rem;">📹</div>
                    <div style="color:#94a3b8;font-size:1rem;">
                        {{ $virtualClass->status === 'pending' ? 'Session has not started yet.' : 'This session has ended.' }}
                    </div>
                    @if($virtualClass->status === 'closed')
                    <div style="font-size:0.8rem;color:#6b7280;margin-top:0.5rem;">Ended at {{ $virtualClass->end_time?->format('H:i') }}</div>
                    @endif
                </div>
            @endif
        </div>

        {{-- Materials Upload (Org Only) --}}
        @if($isOrg)
        <div class="card">
            <div class="card-header"><span class="card-title">📎 Upload Session Material</span></div>
            <div class="card-body">
                <form method="POST" action="{{ route('virtual-class.materials.upload', $virtualClass->id) }}" enctype="multipart/form-data"
                      style="display:flex;gap:0.75rem;flex-wrap:wrap;align-items:flex-end;">
                    @csrf
                    <div style="flex:1;min-width:180px;">
                        <label style="font-size:0.8rem;color:#94a3b8;display:block;margin-bottom:0.3rem;">Title</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Week 1 Slides" required style="height:38px;">
                    </div>
                    <div style="flex:1;min-width:180px;">
                        <label style="font-size:0.8rem;color:#94a3b8;display:block;margin-bottom:0.3rem;">File (PDF, PPT, DOC, Video)</label>
                        <input type="file" name="file" class="form-control" required style="height:38px;font-size:0.8rem;padding:0.35rem;">
                    </div>
                    <button type="submit" class="btn btn-primary" style="height:38px;padding:0 1.25rem;font-size:0.875rem;white-space:nowrap;">Upload</button>
                </form>
            </div>
        </div>
        @endif

        {{-- Materials List --}}
        @if($virtualClass->materials->count())
        <div class="card">
            <div class="card-header"><span class="card-title">📚 Session Materials</span></div>
            <div class="card-body" style="display:grid;gap:0.625rem;">
                @foreach($virtualClass->materials as $mat)
                <a href="{{ asset('storage/' . $mat->file_path) }}" target="_blank"
                   style="display:flex;align-items:center;gap:0.875rem;padding:0.75rem 1rem;background:rgba(255,255,255,0.02);border:1px solid var(--sc-dark-border);border-radius:10px;text-decoration:none;transition:background 0.15s;"
                   onmouseover="this.style.background='rgba(16,185,129,0.06)'" onmouseout="this.style.background='rgba(255,255,255,0.02)'">
                    <span style="font-size:1.5rem;">{{ $mat->icon }}</span>
                    <div style="flex:1;">
                        <div style="font-size:0.875rem;font-weight:600;color:#e2e8f0;">{{ $mat->title }}</div>
                        <div style="font-size:0.75rem;color:#6b7280;text-transform:uppercase;">{{ $mat->file_type }}</div>
                    </div>
                    <svg width="16" height="16" fill="none" stroke="#4b5563" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- ── RIGHT: Sidebar (Chat + Attendance) ── --}}
    <div style="display:flex;flex-direction:column;gap:1rem;position:sticky;top:1.5rem;">

        {{-- Chat Panel --}}
        <div class="card" style="height:480px;display:flex;flex-direction:column;">
            <div class="card-header" style="flex-shrink:0;"><span class="card-title">💬 Live Chat</span></div>

            {{-- Messages --}}
            <div id="chat-messages" style="flex:1;overflow-y:auto;padding:1rem;display:flex;flex-direction:column;gap:0.625rem;">
                @foreach($virtualClass->messages as $msg)
                @php $itsMe = $msg->user_id === auth()->id(); @endphp
                <div style="display:flex;flex-direction:column;align-items:{{ $itsMe ? 'flex-end' : 'flex-start' }};">
                    <div style="font-size:0.7rem;color:#6b7280;margin-bottom:0.15rem;">{{ $itsMe ? 'You' : $msg->user->name }} · {{ $msg->created_at->format('H:i') }}</div>
                    <div style="max-width:85%;padding:0.5rem 0.875rem;border-radius:12px;font-size:0.85rem;line-height:1.5;
                        background:{{ $itsMe ? 'rgba(16,185,129,0.2)' : 'rgba(255,255,255,0.05)' }};
                        border:1px solid {{ $itsMe ? 'rgba(16,185,129,0.3)' : 'var(--sc-dark-border)' }};
                        color:{{ $itsMe ? '#6ee7b7' : '#e2e8f0' }};">
                        {{ $msg->message }}
                    </div>
                </div>
                @endforeach
                <div id="chat-bottom"></div>
            </div>

            {{-- Input --}}
            <div style="padding:0.75rem 1rem;border-top:1px solid var(--sc-dark-border);flex-shrink:0;">
                <form id="chat-form" style="display:flex;gap:0.5rem;">
                    @csrf
                    <input id="chat-input" type="text" placeholder="Type a message…" autocomplete="off"
                           style="flex:1;background:rgba(255,255,255,0.04);border:1px solid var(--sc-dark-border);border-radius:8px;padding:0.5rem 0.875rem;color:#f1f5f9;font-size:0.875rem;outline:none;">
                    <button type="submit" style="background:#10b981;border:none;border-radius:8px;padding:0.5rem 0.875rem;cursor:pointer;color:#fff;font-size:0.875rem;">Send</button>
                </form>
            </div>
        </div>

        {{-- Attendance Status Card (student only) --}}
        @if($isStudent)
        @php
            $myAtt = $virtualClass->attendances->where('student_id', auth()->user()->student?->id ?? 0)->first();
        @endphp
        <div class="card">
            <div class="card-header"><span class="card-title">📋 Your Attendance</span></div>
            <div class="card-body" style="display:grid;gap:0.5rem;">
                <div style="display:flex;justify-content:space-between;font-size:0.875rem;">
                    <span style="color:#94a3b8;">Status</span>
                    <span id="att-status" style="font-weight:600;color:{{ $myAtt?->status === 'present' ? '#34d399' : '#f59e0b' }};">
                        {{ $myAtt ? ucfirst($myAtt->status) : '—' }}
                    </span>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:0.875rem;">
                    <span style="color:#94a3b8;">Duration</span>
                    <span id="att-duration" style="color:#e2e8f0;">{{ $myAtt ? $myAtt->duration . ' min' : '—' }}</span>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
const CSRF      = '{{ $csrfToken }}';
const IS_ACTIVE = {{ $virtualClass->status === 'active' ? 'true' : 'false' }};
const IS_STUDENT= {{ $isStudent ? 'true' : 'false' }};
const JOIN_URL  = '{{ $joinUrl }}';
const LEAVE_URL = '{{ $leaveUrl }}';
const CHAT_GET  = '{{ $chatGetUrl }}';
const CHAT_SEND = '{{ $chatSendUrl }}';
const ROOM_NAME = '{{ $virtualClass->room_name }}';
const USER_NAME = '{{ addslashes(auth()->user()->name) }}';

// ── Jitsi Integration ──────────────────────────────────────────────────────
function loadJitsi() {
    if (!IS_ACTIVE) return;

    const script = document.createElement('script');
    script.src = 'https://meet.jit.si/external_api.js';
    script.onload = function () {
        const api = new JitsiMeetExternalAPI('meet.jit.si', {
            roomName: ROOM_NAME,
            width: '100%',
            height: 540,
            parentNode: document.getElementById('jitsi-meet'),
            configOverwrite: {
                startWithAudioMuted: false,
                disableDeepLinking: true,
            },
            interfaceConfigOverwrite: {
                TOOLBAR_BUTTONS: [
                    'microphone','camera','closedcaptions','desktop',
                    'fullscreen','fodeviceselection','hangup','chat',
                    'raisehand','videoquality','tileview','shortcuts',
                    'mute-everyone','security'
                ],
                SHOW_JITSI_WATERMARK: false,
                SHOW_WATERMARK_FOR_GUESTS: false,
            },
            userInfo: { displayName: USER_NAME },
        });

        // Auto-track attendance for students
        if (IS_STUDENT) {
            api.addEventListener('videoConferenceJoined', () => {
                fetch(JOIN_URL, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json' },
                    body: JSON.stringify({})
                });
            });

            api.addEventListener('videoConferenceLeft', () => {
                fetch(LEAVE_URL, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json' },
                    body: JSON.stringify({})
                }).then(r => r.json()).then(d => {
                    const dur = document.getElementById('att-duration');
                    if (dur) dur.textContent = (d.duration || 0) + ' min';
                });
            });
        }
    };
    document.head.appendChild(script);
}

loadJitsi();

// ── Chat Polling ───────────────────────────────────────────────────────────
const chatMessages = document.getElementById('chat-messages');
const chatInput    = document.getElementById('chat-input');
const chatBottom   = document.getElementById('chat-bottom');
let lastId = 0;

function scrollDown() {
    if (chatBottom) chatBottom.scrollIntoView({ behavior: 'smooth' });
}

function renderMessage(m) {
    const wrap = document.createElement('div');
    wrap.style.cssText = 'display:flex;flex-direction:column;align-items:' + (m.is_me ? 'flex-end' : 'flex-start') + ';';
    wrap.innerHTML = `
        <div style="font-size:0.7rem;color:#6b7280;margin-bottom:0.15rem;">${m.is_me ? 'You' : m.user} · ${m.time}</div>
        <div style="max-width:85%;padding:0.5rem 0.875rem;border-radius:12px;font-size:0.85rem;line-height:1.5;
            background:${m.is_me ? 'rgba(16,185,129,0.2)' : 'rgba(255,255,255,0.05)'};
            border:1px solid ${m.is_me ? 'rgba(16,185,129,0.3)' : 'rgba(42,42,74,0.5)'};
            color:${m.is_me ? '#6ee7b7' : '#e2e8f0'};">
            ${m.message}
        </div>`;
    chatMessages.insertBefore(wrap, document.getElementById('chat-bottom'));
}

function pollChat() {
    fetch(CHAT_GET + '?after=' + lastId)
        .then(r => r.json())
        .then(msgs => {
            msgs.forEach(m => {
                if (m.id > lastId) {
                    lastId = m.id;
                    renderMessage(m);
                }
            });
            scrollDown();
        });
}

// Initialize last id from server-rendered messages
document.querySelectorAll('#chat-messages > div[data-id]').forEach(el => {
    lastId = Math.max(lastId, parseInt(el.dataset.id || 0));
});

scrollDown();
setInterval(pollChat, 3000);

// ── Send message ───────────────────────────────────────────────────────────
document.getElementById('chat-form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const msg = chatInput.value.trim();
    if (!msg) return;
    chatInput.value = '';

    fetch(CHAT_SEND, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json' },
        body: JSON.stringify({ message: msg })
    })
    .then(r => r.json())
    .then(m => {
        lastId = Math.max(lastId, m.id);
        renderMessage(m);
        scrollDown();
    });
});
</script>
@endpush
