<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use Gemini\Data\Content;
use Gemini\Enums\Role;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    /**
     * The system prompt that gives Gemini context about SkillConnect.
     */
    private const SYSTEM_INSTRUCTION = <<<PROMPT
You are SkillConnect AI Assistant — a knowledgeable, friendly, and professional assistant for the SkillConnect platform, Kenya's national skills training and employment ecosystem.

You help:
- Students: find programs, understand enrollments, navigate their learning journey, and prepare for employment
- Employers: understand how to post jobs, search candidates, and manage applications
- Institutions & Organizations: how to publish training programs and manage students
- Admins: platform management questions

Important guidelines:
- Always be concise, helpful, and warm
- Format responses using clear markdown (bullet points, **bold**, code blocks where needed)
- If a question is unrelated to skills training or employment, politely redirect the user
- Do not discuss sensitive personal data of other users
- You are embedded inside the SkillConnect web platform
PROMPT;

    /**
     * Show the chat UI page.
     */
    public function index(Request $request)
    {
        $sessionId = $request->query('session', session()->get('chat_session_id'));

        // Generate and persist a session ID if this is a fresh chat
        if (! $sessionId) {
            $sessionId = (string) Str::uuid();
            session(['chat_session_id' => $sessionId]);
        }

        // Load existing history for display
        $history = ChatMessage::where('session_id', $sessionId)
            ->orderBy('created_at')
            ->get();

        return view('chat.index', compact('history', 'sessionId'));
    }

    /**
     * Handle an incoming chat message.
     * Rate-limited to 30 requests/minute via route middleware.
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'message'    => ['required', 'string', 'max:2000'],
            'session_id' => ['required', 'string', 'max:36'],
        ]);

        $userMessage = trim($request->input('message'));
        $sessionId   = $request->input('session_id');
        $userId      = Auth::id();

        // ── 1. Persist the user's message ────────────────────────────────────
        ChatMessage::create([
            'user_id'    => $userId,
            'session_id' => $sessionId,
            'role'       => 'user',
            'content'    => $userMessage,
        ]);

        // ── 2. Build the context history for this session ────────────────────
        $pastMessages = ChatMessage::where('session_id', $sessionId)
            ->orderBy('created_at')
            ->get();

        // Format into Gemini Content objects (all but the last user message)
        $history = [];
        $messages = $pastMessages->slice(0, -1); // exclude the message we just inserted
        foreach ($messages as $msg) {
            $role = $msg->role === 'model' ? Role::MODEL : Role::USER;
            $history[] = Content::parse(part: $msg->content, role: $role);
        }

        // ── 3. Call the Gemini API ────────────────────────────────────────────
        try {
            $chat = Gemini::generativeModel(model: 'models/gemini-flash-latest')
                ->withSystemInstruction(Content::parse(part: self::SYSTEM_INSTRUCTION))
                ->startChat(history: $history);

            $response   = $chat->sendMessage($userMessage);
            $modelReply = $response->text();

        } catch (\Throwable $e) {
            // If Gemini call fails, remove the user message and return error
            ChatMessage::where('session_id', $sessionId)
                ->where('role', 'user')
                ->latest()
                ->first()?->delete();

            return response()->json([
                'success' => false,
                'error'   => 'The AI assistant is temporarily unavailable. Please try again shortly.',
            ], 503);
        }

        // ── 4. Persist the model's reply ──────────────────────────────────────
        ChatMessage::create([
            'user_id'    => $userId,
            'session_id' => $sessionId,
            'role'       => 'model',
            'content'    => $modelReply,
        ]);

        return response()->json([
            'success' => true,
            'message' => $modelReply,
        ]);
    }

    /**
     * Clear a chat session's history.
     */
    public function clearSession(Request $request): JsonResponse
    {
        $sessionId = $request->input('session_id');

        if ($sessionId) {
            // Only allow users to delete their own session messages
            ChatMessage::where('session_id', $sessionId)
                ->where(function ($q) {
                    $q->where('user_id', Auth::id())
                        ->orWhereNull('user_id');
                })
                ->delete();
        }

        $newSessionId = (string) Str::uuid();
        session(['chat_session_id' => $newSessionId]);

        return response()->json([
            'success'    => true,
            'session_id' => $newSessionId,
        ]);
    }
}
