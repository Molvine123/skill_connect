@extends('layouts.app')

@section('title', 'AI Assistant')
@section('page-title', 'SkillConnect AI Assistant')

@push('styles')
<style>
/* ── Premium Font Pairing ── */
#chat-container {
    font-family: 'Plus Jakarta Sans', 'Outfit', sans-serif;
}

/* ══════════════════════════════════════════════════════════════
   §2.2  MAIN LAYOUT CONTAINER — Viewport-locked flex column
   Isolates header / feed / suggestions / input into rigid zones.
   ══════════════════════════════════════════════════════════════ */
#chat-container {
    display: flex;
    flex-direction: column;
    height: calc(100vh - 170px);
    min-height: 520px;
    max-height: calc(100vh - 100px);
    background: rgba(15, 23, 42, 0.45);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 20px;
    overflow: hidden; /* Nothing escapes the container frame */
    box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
    transition: all 0.3s ease;
}

/* ── Header (flex-shrink: 0 — never collapses) ── */
#chat-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.1rem 1.5rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(12px);
    z-index: 10;
    flex-shrink: 0;
}
.chat-header-info { display: flex; align-items: center; gap: 0.85rem; }
.chat-logo {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    background: linear-gradient(135deg, #0d9488, #7c3aed);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    box-shadow: 0 4px 12px rgba(13, 148, 136, 0.2);
}
.chat-status-dot {
    width: 8px;
    height: 8px;
    background: #10b981;
    border-radius: 50%;
    display: inline-block;
    margin-right: 5px;
    box-shadow: 0 0 8px #10b981;
}

/* ══════════════════════════════════════════════════════════════
   §2.2  CHAT FEED VIEWPORT — Autonomous scrolling zone
   flex:1 consumes ALL remaining vertical space between header
   and the bottom dock. overflow-y:auto isolates its scrollbar.
   ══════════════════════════════════════════════════════════════ */
#chat-messages {
    flex: 1;
    min-height: 0;  /* Critical: allows flex child to shrink below content height */
    overflow-y: auto;
    padding: 1.75rem 1.75rem 1rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    scroll-behavior: smooth;
    background: radial-gradient(circle at top right, rgba(13, 148, 136, 0.03), transparent 45%),
                radial-gradient(circle at bottom left, rgba(124, 58, 237, 0.03), transparent 45%);
}

#chat-messages::-webkit-scrollbar { width: 6px; }
#chat-messages::-webkit-scrollbar-track { background: transparent; }
#chat-messages::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.08); border-radius: 10px; }
#chat-messages::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.15); }

/* ══════════════════════════════════════════════════════════════
   §3.1  MESSAGE ROW — Flex-row pattern for every chat event
   Avatar + bubble sit side-by-side in a strict horizontal row.
   ══════════════════════════════════════════════════════════════ */
.msg-wrapper {
    display: flex;
    flex-direction: row;
    gap: 0.85rem;
    align-items: flex-start;
    animation: msgFadeUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    max-width: 85%;
}
.msg-wrapper.user {
    flex-direction: row-reverse;
    align-self: flex-end;
}
.msg-wrapper.ai {
    align-self: flex-start;
}

@keyframes msgFadeUp {
    from { opacity: 0; transform: translateY(12px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ══════════════════════════════════════════════════════════════
   §2.1  NUCLEAR AVATAR CONSTRAINTS
   Every dimension axis is locked with !important.
   The avatar can NEVER grow, shrink, or flex beyond 40×40px.
   ══════════════════════════════════════════════════════════════ */
.msg-avatar {
    width: 40px !important;
    height: 40px !important;
    min-width: 40px !important;
    min-height: 40px !important;
    max-width: 40px !important;
    max-height: 40px !important;
    border-radius: 50%;
    flex-shrink: 0;
    flex-grow: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    object-fit: contain;
}
.msg-avatar.ai  { background: linear-gradient(135deg, #0d9488, #0ea5e9); color: #fff; }
.msg-avatar.usr { background: linear-gradient(135deg, #7c3aed, #4f46e5); color: #fff; }

/* SVG/IMG inside avatar — also locked to pixel grid */
.msg-avatar svg,
.msg-avatar img {
    width: 20px !important;
    height: 20px !important;
    max-width: 20px !important;
    max-height: 20px !important;
    stroke-width: 2.2;
    flex-shrink: 0;
    flex-grow: 0;
    display: inline-block;
}

/* ── Message Bubbles ── */
.msg-bubble {
    padding: 0.85rem 1.25rem;
    border-radius: 20px;
    font-size: 0.95rem;
    line-height: 1.6;
    word-break: break-word;
    min-width: 0; /* Prevents flex child from overflowing */
}
.msg-bubble.ai {
    background: rgba(30, 41, 59, 0.85);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-top-left-radius: 4px;
    color: #f8fafc;
}
.msg-bubble.user {
    background: linear-gradient(135deg, #7c3aed, #4f46e5);
    border-top-right-radius: 4px;
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(124, 58, 237, 0.15);
}

/* ── Typography Scannability ── */
.msg-bubble.ai p {
    margin: 0.75rem 0;
}
.msg-bubble.ai p:first-child { margin-top: 0; }
.msg-bubble.ai p:last-child { margin-bottom: 0; }

.msg-bubble.ai h1, .msg-bubble.ai h2, .msg-bubble.ai h3 {
    font-weight: 700;
    margin: 1.2rem 0 0.5rem;
    color: #ffffff;
}
.msg-bubble.ai h1 { font-size: 1.15rem; }
.msg-bubble.ai h2 { font-size: 1.05rem; }
.msg-bubble.ai h3 { font-size: 0.98rem; }

.msg-bubble.ai ul, .msg-bubble.ai ol {
    padding-left: 1.5rem;
    margin: 0.75rem 0;
}
.msg-bubble.ai li {
    margin-bottom: 0.5rem;
}
.msg-bubble.ai li:last-child { margin-bottom: 0; }

/* Custom coloring for category headers */
.msg-bubble.ai strong {
    color: #2dd4bf;
    font-weight: 700;
}
.msg-bubble.ai em { color: #c084fc; }

.msg-bubble.ai code {
    background: rgba(0,0,0,0.4);
    padding: 0.15rem 0.4rem;
    border-radius: 6px;
    font-family: monospace;
    font-size: 0.85rem;
    color: #38bdf8;
}
.msg-bubble.ai pre {
    background: rgba(0,0,0,0.4);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 10px;
    padding: 0.9rem;
    overflow-x: auto;
    margin: 0.8rem 0;
}
.msg-bubble.ai pre code {
    background: transparent;
    padding: 0;
    color: #a5f3fc;
}
.msg-bubble.ai a { color: #0ea5e9; text-decoration: underline; font-weight: 600; }

/* ══════════════════════════════════════════════════════════════
   §3.2  ERROR STATE QUARANTINE
   System errors render inside an isolated crimson alert block
   instead of dropping raw text into the feed.
   ══════════════════════════════════════════════════════════════ */
.msg-wrapper.bot-error-state .msg-bubble.ai {
    background: rgba(239, 68, 68, 0.08);
    border: 1px solid rgba(239, 68, 68, 0.3);
    color: #fca5a5;
}
.msg-wrapper.bot-error-state .msg-bubble.ai strong {
    color: #f87171;
}

/* ── Custom Typing Indicator (Pulsing Wave) ── */
.typing-indicator {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 0.6rem 0.8rem;
}
.typing-dot {
    width: 8px;
    height: 8px;
    background: #0d9488;
    border-radius: 50%;
    animation: wavePulse 1.4s infinite ease-in-out;
}
.typing-dot:nth-child(2) { animation-delay: 0.2s; }
.typing-dot:nth-child(3) { animation-delay: 0.4s; }
@keyframes wavePulse {
    0%, 100% { transform: scale(0.8); opacity: 0.4; background: #0d9488; }
    50%       { transform: scale(1.2); opacity: 1; background: #2dd4bf; }
}

/* ══════════════════════════════════════════════════════════════
   §4.1  SUGGESTION GRID — Touch-responsive chip cards
   Replaces inline emoji chains with structured card layout.
   ══════════════════════════════════════════════════════════════ */
#quick-replies-bar {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    padding: 0.65rem 1.5rem;
    background: rgba(15, 23, 42, 0.4);
    border-top: 1px solid rgba(255, 255, 255, 0.05);
    flex-shrink: 0;
    scrollbar-width: none;
}
#quick-replies-bar::-webkit-scrollbar { display: none; }

.suggestion-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    justify-content: center;
}

.chip {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: #2dd4bf;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    font-family: inherit;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    margin: 0;
    white-space: nowrap;
}
.chip:hover {
    background: rgba(255, 255, 255, 0.15);
    border-color: rgba(255, 255, 255, 0.3);
    transform: translateY(-1px) scale(1.02);
    box-shadow: 0 4px 8px rgba(13, 148, 136, 0.15);
}

/* ══════════════════════════════════════════════════════════════
   §4.2  FOOTER DOCK — Fixed bottom input area
   Encased in its own flex-shrink:0 wrapper so it never collapses.
   ══════════════════════════════════════════════════════════════ */
#chat-input-area {
    padding: 1rem 1.5rem;
    border-top: 1px solid rgba(255, 255, 255, 0.06);
    background: rgba(15, 23, 42, 0.85);
    display: flex;
    gap: 0.85rem;
    align-items: flex-end;
    flex-shrink: 0;
}
#chat-input {
    flex: 1;
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 14px;
    color: #ffffff;
    padding: 0.85rem 1.15rem;
    font-size: 1.02rem;
    font-family: inherit;
    resize: none;
    min-height: 48px;
    max-height: 150px;
    outline: none;
    transition: border-color 0.25s ease, background-color 0.25s ease, box-shadow 0.25s ease;
    overflow-y: auto;
    line-height: 1.5;
}
#chat-input:focus {
    border-color: #0d9488;
    background: rgba(255, 255, 255, 0.06);
    box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.25);
}
#chat-input::placeholder { color: #64748b; }

/* ── Button Spring Micro-interactions ── */
#send-btn {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    background: linear-gradient(135deg, #0d9488, #0ea5e9);
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.2s ease;
    flex-shrink: 0;
    box-shadow: 0 4px 10px rgba(13, 148, 136, 0.2);
}
#send-btn svg {
    width: 20px;
    height: 20px;
    stroke: #ffffff;
    stroke-width: 2.2;
    transition: transform 0.2s ease;
}
#send-btn:hover:not(:disabled) {
    transform: translateY(-2px) scale(1.08);
    box-shadow: 0 6px 14px rgba(13, 148, 136, 0.3);
}
#send-btn:hover:not(:disabled) svg {
    transform: rotate(-10deg) translate(1px, -1px);
}
#send-btn:active:not(:disabled) {
    transform: scale(0.95);
}
#send-btn:disabled { opacity: 0.35; cursor: not-allowed; }

/* ── Welcome Screen ── */
#welcome-screen {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 1.5rem;
    text-align: center;
    padding: 3rem 1.5rem;
    flex: 1;
}
.welcome-logo-icon {
    width: 72px;
    height: 72px;
    border-radius: 20px;
    background: linear-gradient(135deg, #0d9488, #7c3aed);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.2rem;
    box-shadow: 0 10px 20px rgba(13, 148, 136, 0.15);
}

/* ══════════════════════════════════════════════════════════════
   MOBILE RESPONSIVE — Avatar & layout fixes for narrow screens
   ══════════════════════════════════════════════════════════════ */
@media (max-width: 600px) {
    #welcome-screen {
        padding: 1.5rem 1rem;
        gap: 0.75rem;
    }
    .welcome-logo-icon {
        width: 52px;
        height: 52px;
        font-size: 1.6rem;
        box-shadow: 0 6px 12px rgba(13, 148, 136, 0.1);
    }
    #welcome-screen h2 {
        font-size: 1.15rem !important;
        margin-bottom: 0.2rem !important;
    }
    #welcome-screen p {
        font-size: 0.85rem !important;
    }

    .msg-bubble {
        font-size: 0.875rem;
        line-height: 1.5;
        padding: 0.7rem 1rem;
    }

    /* Avatars stay locked on mobile too */
    .msg-avatar {
        width: 32px !important;
        height: 32px !important;
        min-width: 32px !important;
        min-height: 32px !important;
        max-width: 32px !important;
        max-height: 32px !important;
    }
    .msg-avatar svg,
    .msg-avatar img {
        width: 16px !important;
        height: 16px !important;
        max-width: 16px !important;
        max-height: 16px !important;
    }

    .msg-wrapper {
        max-width: 92%;
        gap: 0.6rem;
    }

    #chat-container {
        height: calc(100vh - 120px);
        border-radius: 12px;
    }

    #chat-messages {
        padding: 1rem;
        gap: 1.1rem;
    }

    .suggestion-chips, #quick-replies-bar {
        gap: 0.4rem;
        padding: 0.5rem 1rem;
    }
    .chip {
        font-size: 0.78rem;
        padding: 0.35rem 0.75rem;
    }

    #chat-input-area {
        padding: 0.75rem 1rem;
        gap: 0.6rem;
    }
    #chat-input {
        font-size: 16px; /* Prevents iOS Safari auto-zoom */
        padding: 0.7rem 0.9rem;
        min-height: 40px;
    }
    #send-btn {
        width: 40px;
        height: 40px;
    }
}

/* ── Dynamic Light Theme Toggle Styling ── */
#chat-container.light-theme {
    background: rgba(255, 255, 255, 0.45);
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
}

#chat-container.light-theme #chat-header {
    background: rgba(255, 255, 255, 0.65);
    border-bottom: 1px solid rgba(0, 0, 0, 0.08);
}

#chat-container.light-theme #chat-header .chat-header-info div:first-child {
    color: #0f172a !important;
}

#chat-container.light-theme #chat-header .chat-header-info div:last-child {
    color: #475569 !important;
}

#chat-container.light-theme #chat-messages {
    background: radial-gradient(circle at top right, rgba(13, 148, 136, 0.05), transparent 45%),
                radial-gradient(circle at bottom left, rgba(124, 58, 237, 0.05), transparent 45%);
}

#chat-container.light-theme .msg-bubble.ai {
    background: rgba(241, 245, 249, 0.95);
    border: 1px solid rgba(0, 0, 0, 0.06);
    color: #0f172a;
}

#chat-container.light-theme .msg-bubble.ai h1,
#chat-container.light-theme .msg-bubble.ai h2,
#chat-container.light-theme .msg-bubble.ai h3,
#chat-container.light-theme .msg-bubble.ai strong {
    color: #0f172a;
}

#chat-container.light-theme .msg-bubble.ai code {
    background: rgba(0, 0, 0, 0.05);
    color: #0d9488;
}

#chat-container.light-theme .msg-bubble.ai pre {
    background: rgba(0, 0, 0, 0.04);
    border: 1px solid rgba(0, 0, 0, 0.06);
}

#chat-container.light-theme .msg-bubble.ai pre code {
    color: #4f46e5;
}

#chat-container.light-theme #welcome-screen h2 {
    color: #0f172a !important;
}

#chat-container.light-theme #welcome-screen p {
    color: #475569 !important;
}

#chat-container.light-theme #chat-input-area {
    background: rgba(255, 255, 255, 0.7);
    border-top: 1px solid rgba(0, 0, 0, 0.06);
}

#chat-container.light-theme #chat-input {
    background: rgba(0, 0, 0, 0.03);
    border: 1px solid rgba(0, 0, 0, 0.08);
    color: #0f172a;
}

#chat-container.light-theme #chat-input:focus {
    border-color: #0d9488;
    background: rgba(0, 0, 0, 0.01);
    box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.2);
}

#chat-container.light-theme #chat-input::placeholder {
    color: #94a3b8;
}

#chat-container.light-theme #quick-replies-bar {
    background: rgba(255, 255, 255, 0.5);
    border-top: 1px solid rgba(0, 0, 0, 0.05);
}

#chat-container.light-theme .chip {
    background: rgba(0, 0, 0, 0.04);
    border-color: rgba(0, 0, 0, 0.1);
    color: #0d9488;
}
#chat-container.light-theme .chip:hover {
    background: rgba(0, 0, 0, 0.08);
    border-color: rgba(0, 0, 0, 0.2);
}

/* Light-theme error quarantine */
#chat-container.light-theme .msg-wrapper.bot-error-state .msg-bubble.ai {
    background: rgba(239, 68, 68, 0.06);
    border: 1px solid rgba(239, 68, 68, 0.2);
    color: #dc2626;
}
#chat-container.light-theme .msg-wrapper.bot-error-state .msg-bubble.ai strong {
    color: #ef4444;
}

#chat-container.light-theme #clear-btn {
    background: rgba(239, 68, 68, 0.06);
    border-color: rgba(239, 68, 68, 0.2);
    color: #ef4444;
}

#chat-container.light-theme #clear-btn:hover {
    background: rgba(239, 68, 68, 0.12) !important;
}

/* Theme toggle button styling with morphing animation */
.theme-toggle-btn {
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: #cbd5e1;
    width: 36px;
    height: 36px;
    border-radius: 10px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

#chat-container.light-theme .theme-toggle-btn {
    background: rgba(0, 0, 0, 0.05);
    border-color: rgba(0, 0, 0, 0.08);
    color: #475569;
}

.theme-toggle-btn:hover {
    transform: scale(1.05);
    color: #fbbf24;
}

.theme-toggle-btn svg {
    width: 18px;
    height: 18px;
    transition: transform 0.5s ease;
}
</style>
@endpush

@section('content')
<div class="animate-fade-up" style="max-width:900px;margin:0 auto;">

    <div id="chat-container">

        {{-- Chat Header --}}
        <div id="chat-header">
            <div class="chat-header-info">
                <div class="chat-logo">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:22px; height:22px; stroke-width:2.2;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 2v2m-6.5 0h13M6 7h12a2 2 0 012 2v10a2 2 0 01-2 2H6a2 2 0 01-2-2V9a2 2 0 012-2zm3 4h.01M15 11h.01M9 16h6" />
                    </svg>
                </div>
                <div>
                    <div style="font-weight:800;font-size:1rem;color:#ffffff;letter-spacing:0.02em;">SkillConnect AI</div>
                    <div style="font-size:.78rem;color:#94a3b8;display:flex;align-items:center;">
                        <span class="chat-status-dot"></span>
                        Active · Gemini 1.5 Flash
                    </div>
                </div>
            </div>
            <div style="display: flex; gap: 0.5rem; align-items: center;">
                <button class="theme-toggle-btn" onclick="toggleTheme()" title="Toggle Light/Dark Theme" id="theme-toggle">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" id="theme-toggle-icon">
                        <!-- Sun Icon -->
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707m12.728 6.364A9 9 0 115.636 5.636 9.75 9.75 0 0012 3v9z" />
                    </svg>
                </button>
                <button id="clear-btn" title="Start new conversation" onclick="clearSession()"
                    style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.25);color:#f87171;padding:.45rem .85rem;border-radius:10px;cursor:pointer;font-size:.78rem;font-weight:600;font-family:inherit;display:flex;align-items:center;gap:.4rem;transition:all .2s;"
                    onmouseover="this.style.background='rgba(239,68,68,0.2)'" onmouseout="this.style.background='rgba(239,68,68,0.1)'">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    New Chat
                </button>
            </div>
        </div>

        {{-- Messages Area --}}
        <div id="chat-messages">

            @if($history->isEmpty())
                {{-- Welcome screen for fresh sessions --}}
                <div id="welcome-screen">
                    <div class="welcome-logo-icon">🤖</div>
                    <div>
                        <h2 style="font-size:1.4rem;font-weight:800;color:#ffffff;margin-bottom:.5rem;letter-spacing:0.01em;">How can I help you today?</h2>
                        <p style="color:#94a3b8;font-size:.95rem;max-width:480px;line-height:1.5;">Ask me anything about SkillConnect — programs, jobs, internships, enrollments, and more.</p>
                    </div>
                    <div class="suggestion-chips" style="margin-top:0.75rem;">
                        <button class="chip" onclick="sendSuggestion(this)">🔍 Find training</button>
                        <button class="chip" onclick="sendSuggestion(this)">💼 Post a job</button>
                        <button class="chip" onclick="sendSuggestion(this)">🚀 Get career tips</button>
                    </div>
                </div>
            @else
                {{-- Load existing history --}}
                @foreach($history as $msg)
                    @if($msg->role === 'user')
                        <div class="msg-wrapper user">
                            <div class="msg-avatar usr">
                                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div class="msg-bubble user">{{ $msg->content }}</div>
                        </div>
                    @else
                        <div class="msg-wrapper ai">
                            <div class="msg-avatar ai">
                                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 2v2m-6.5 0h13M6 7h12a2 2 0 012 2v10a2 2 0 01-2 2H6a2 2 0 01-2-2V9a2 2 0 012-2zm3 4h.01M15 11h.01M9 16h6" />
                                </svg>
                            </div>
                            <div class="msg-bubble ai" data-raw="{{ htmlspecialchars($msg->content) }}"></div>
                        </div>
                    @endif
                @endforeach
            @endif

        </div>

        {{-- Sticky Quick Replies Bar (always visible above input) --}}
        <div id="quick-replies-bar">
            <button class="chip" onclick="sendSuggestion(this)">🔍 Find training</button>
            <button class="chip" onclick="sendSuggestion(this)">💼 Post a job</button>
            <button class="chip" onclick="sendSuggestion(this)">🚀 Get career tips</button>
            <button class="chip" onclick="sendSuggestion(this)">📖 How to enroll?</button>
            <button class="chip" onclick="sendSuggestion(this)">🎓 View certificates</button>
        </div>

        {{-- Input Area --}}
        <div id="chat-input-area">
            <textarea
                id="chat-input"
                placeholder="Ask me anything about SkillConnect…"
                rows="1"
                maxlength="2000"
                onkeydown="handleKeydown(event)"
                oninput="autoResize(this)"
            ></textarea>
            <button id="send-btn" onclick="sendMessage()" title="Send message">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
            </button>
        </div>

    </div>

</div>
@endsection

@push('scripts')
{{-- Marked.js for Markdown rendering --}}
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
const SESSION_ID  = @json($sessionId);
const SEND_URL    = @json(route('chat.send'));
const CLEAR_URL   = @json(route('chat.clear'));
const CSRF_TOKEN  = document.querySelector('meta[name="csrf-token"]')?.content;

// Configure marked.js
marked.setOptions({
    gfm: true,
    breaks: true,
    headerIds: false,
    mangle: false,
});

const robotAvatarSVG = `
    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 2v2m-6.5 0h13M6 7h12a2 2 0 012 2v10a2 2 0 01-2 2H6a2 2 0 01-2-2V9a2 2 0 012-2zm3 4h.01M15 11h.01M9 16h6" />
    </svg>
`;

const userAvatarSVG = `
    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
    </svg>
`;

// Render any pre-loaded AI bubbles from history
document.querySelectorAll('.msg-bubble.ai[data-raw]').forEach(el => {
    el.innerHTML = marked.parse(el.dataset.raw);
    el.removeAttribute('data-raw');
});

// Scroll to bottom on load
scrollToBottom(false);

// ── Message Sending ───────────────────────────────────────────────────────────
async function sendMessage() {
    const input = document.getElementById('chat-input');
    const text  = input.value.trim();
    if (!text) return;

    // Hide welcome screen if shown
    const welcome = document.getElementById('welcome-screen');
    if (welcome) welcome.remove();

    // Append the user bubble
    appendBubble('user', text);
    input.value = '';
    autoResize(input);

    // Show typing indicator
    const typingId = appendTyping();

    // Disable send button
    setSending(true);

    try {
        const res = await fetch(SEND_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ message: text, session_id: SESSION_ID }),
        });

        removeTyping(typingId);

        if (res.status === 429) {
            appendBubble('ai', '⚠️ **Rate limit reached.** You can send up to 30 messages per minute. Please wait a moment and try again.', true, true);
            return;
        }

        const data = await res.json();

        if (data.success) {
            appendBubble('ai', data.message, true);
        } else {
            appendBubble('ai', `⚠️ **Error:** ${data.error || 'Something went wrong. Please try again.'}`, true, true);
        }
    } catch (err) {
        removeTyping(typingId);
        appendBubble('ai', '⚠️ **Network error.** Please check your connection and try again.', true, true);
    } finally {
        setSending(false);
        document.getElementById('chat-input').focus();
    }
}

// ── UI Helpers ────────────────────────────────────────────────────────────────
function appendBubble(role, content, isMarkdown = false, isError = false) {
    const msgs   = document.getElementById('chat-messages');
    const wrapper = document.createElement('div');
    wrapper.className = `msg-wrapper ${role === 'user' ? 'user' : 'ai'}`;

    // §3.2: Quarantine error responses in an isolated alert block
    if (isError && role !== 'user') {
        wrapper.classList.add('bot-error-state');
    }

    const avatar = document.createElement('div');
    avatar.className = `msg-avatar ${role === 'user' ? 'usr' : 'ai'}`;
    avatar.innerHTML = role === 'user' ? userAvatarSVG : robotAvatarSVG;

    const bubble = document.createElement('div');
    bubble.className = `msg-bubble ${role === 'user' ? 'user' : 'ai'}`;

    if (isMarkdown && role === 'ai') {
        bubble.innerHTML = marked.parse(content);
    } else {
        bubble.textContent = content;
    }

    if (role === 'user') {
        wrapper.appendChild(bubble);
        wrapper.appendChild(avatar);
    } else {
        wrapper.appendChild(avatar);
        wrapper.appendChild(bubble);
    }

    msgs.appendChild(wrapper);
    scrollToBottom();
    return wrapper;
}

function appendTyping() {
    const msgs = document.getElementById('chat-messages');
    const id = 'typing-' + Date.now();
    const wrapper = document.createElement('div');
    wrapper.className = 'msg-wrapper ai';
    wrapper.id = id;
    wrapper.innerHTML = `
        <div class="msg-avatar ai">
            ${robotAvatarSVG}
        </div>
        <div class="msg-bubble ai" style="padding:.55rem .85rem;">
            <div class="typing-indicator">
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
            </div>
        </div>`;
    msgs.appendChild(wrapper);
    scrollToBottom();
    return id;
}

function removeTyping(id) {
    document.getElementById(id)?.remove();
}

function setSending(isSending) {
    const btn = document.getElementById('send-btn');
    const inp = document.getElementById('chat-input');
    btn.disabled = isSending;
    inp.disabled = isSending;
}

function scrollToBottom(smooth = true) {
    const el = document.getElementById('chat-messages');
    el.scrollTo({ top: el.scrollHeight, behavior: smooth ? 'smooth' : 'instant' });
}

function autoResize(el) {
    el.style.height = 'auto';
    el.style.height = Math.min(el.scrollHeight, 150) + 'px';
}

function handleKeydown(event) {
    if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault();
        sendMessage();
    }
}

function sendSuggestion(btn) {
    document.getElementById('chat-input').value = btn.textContent.trim();
    sendMessage();
}

// ── Clear Session ─────────────────────────────────────────────────────────────
async function clearSession() {
    if (!confirm('Start a new conversation? Current chat history will be cleared.')) return;

    try {
        const res = await fetch(CLEAR_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
            },
            body: JSON.stringify({ session_id: SESSION_ID }),
        });
        const data = await res.json();
        if (data.success) {
            window.location.href = `/chat?session=${data.session_id}`;
        }
    } catch (e) {
        alert('Could not reset the session. Please try again.');
    }
}

// Theme Toggle Logic
function toggleTheme() {
    const container = document.getElementById('chat-container');
    const isLight = container.classList.toggle('light-theme');
    
    localStorage.setItem('chat_theme', isLight ? 'light' : 'dark');
    updateThemeIcon(isLight);
    
    // Rotate the toggle icon dynamically
    const toggleBtn = document.getElementById('theme-toggle');
    toggleBtn.style.transform = 'scale(1.05) rotate(360deg)';
    setTimeout(() => {
        toggleBtn.style.transform = '';
    }, 300);
}

function updateThemeIcon(isLight) {
    const icon = document.getElementById('theme-toggle-icon');
    if (isLight) {
        // Moon Icon SVG
        icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />`;
    } else {
        // Sun/Moon Blend Icon SVG
        icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707m12.728 6.364A9 9 0 115.636 5.636 9.75 9.75 0 0012 3v9z" />`;
    }
}

// Load persisted theme on page load
window.addEventListener('DOMContentLoaded', () => {
    const savedTheme = localStorage.getItem('chat_theme');
    if (savedTheme === 'light') {
        const container = document.getElementById('chat-container');
        if (container) {
            container.classList.add('light-theme');
            updateThemeIcon(true);
        }
    }
});
</script>
@endpush
