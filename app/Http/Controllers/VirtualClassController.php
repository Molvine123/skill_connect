<?php

namespace App\Http\Controllers;

use App\Models\ClassAttendance;
use App\Models\ClassMaterial;
use App\Models\ClassMessage;
use App\Models\Student;
use App\Models\TrainingSession;
use App\Models\VirtualClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VirtualClassController extends Controller
{
    // ═══════════════════════════════════════════════════════════════════════
    // ── Organization / Lecturer: Create & Manage
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Create (or retrieve existing) virtual class for a training session.
     */
    public function create(TrainingSession $session)
    {
        // Reuse existing room if already created
        $virtualClass = $session->virtualClass ?? VirtualClass::create([
            'training_session_id' => $session->id,
            'room_name'           => 'skillconnect-' . $session->id . '-' . Str::random(8),
            'start_time'          => $session->start_date,
            'end_time'            => $session->end_date,
            'status'              => 'pending',
        ]);

        return redirect()->route('virtual-class.room', $virtualClass->id)
            ->with('success', 'Virtual classroom is ready. You can start the session.');
    }

    /**
     * Open (activate) a virtual class room.
     */
    public function open(VirtualClass $virtualClass)
    {
        $virtualClass->update(['status' => 'active', 'start_time' => now()]);

        return redirect()->route('virtual-class.room', $virtualClass->id)
            ->with('success', 'Session started.');
    }

    /**
     * Close (end) a virtual class room.
     */
    public function close(VirtualClass $virtualClass)
    {
        $virtualClass->update(['status' => 'closed', 'end_time' => now()]);

        // Mark anyone still in the class as having left
        ClassAttendance::where('virtual_class_id', $virtualClass->id)
            ->whereNull('leave_time')
            ->each(function ($att) use ($virtualClass) {
                $leaveTime = now();
                $duration  = (int) $att->join_time->diffInMinutes($leaveTime);
                $sessionDurationMins = $virtualClass->session->start_date
                    ? (int) $virtualClass->session->start_date->diffInMinutes($virtualClass->session->end_date)
                    : 60;
                $att->update([
                    'leave_time' => $leaveTime,
                    'duration'   => $duration,
                    'status'     => $duration >= ($sessionDurationMins * 0.5) ? 'present' : 'absent',
                ]);
            });

        return back()->with('success', 'Session closed and attendance finalized.');
    }

    // ═══════════════════════════════════════════════════════════════════════
    // ── Shared: Room View
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Render the virtual classroom room (Jitsi embed + chat + materials).
     */
    public function room(VirtualClass $virtualClass)
    {
        $user    = Auth::user();
        $session = $virtualClass->session()->with('program.organization')->first();

        // Access control: must be enrolled student or the owning organization
        if ($user->isStudent()) {
            $student = $user->student;
            $isEnrolled = $student && $session->program->enrollments()
                ->where('student_id', $student->id)
                ->whereIn('status', ['approved', 'completed'])
                ->exists();

            if (!$isEnrolled) {
                abort(403, 'You must be enrolled in this program to join this class.');
            }
        }

        $virtualClass->load(['messages.user', 'materials']);

        return view('virtual_class.room', compact('virtualClass', 'session', 'user'));
    }

    // ═══════════════════════════════════════════════════════════════════════
    // ── AJAX: Attendance Tracking
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Record student joining the virtual class. Called by Jitsi `videoConferenceJoined` event.
     */
    public function recordJoin(Request $request, VirtualClass $virtualClass)
    {
        $student = Auth::user()->student;
        if (!$student) return response()->json(['error' => 'Not a student'], 403);

        // Upsert attendance record
        ClassAttendance::updateOrCreate(
            ['virtual_class_id' => $virtualClass->id, 'student_id' => $student->id],
            ['join_time' => now(), 'leave_time' => null, 'duration' => 0, 'status' => 'absent']
        );

        return response()->json(['status' => 'joined']);
    }

    /**
     * Record student leaving the virtual class. Called by Jitsi `videoConferenceLeft` event.
     */
    public function recordLeave(Request $request, VirtualClass $virtualClass)
    {
        $student = Auth::user()->student;
        if (!$student) return response()->json(['error' => 'Not a student'], 403);

        $att = ClassAttendance::where('virtual_class_id', $virtualClass->id)
            ->where('student_id', $student->id)
            ->first();

        if ($att) {
            $leaveTime   = now();
            $duration    = (int) $att->join_time->diffInMinutes($leaveTime);

            // Get session total duration; fallback to 60 min
            $session = $virtualClass->session;
            $sessionDurationMins = ($session->start_date && $session->end_date)
                ? (int) $session->start_date->diffInMinutes($session->end_date)
                : 60;

            // Present if attended >= 50% of the session
            $status = $duration >= ($sessionDurationMins * 0.5) ? 'present' : 'absent';

            $att->update([
                'leave_time' => $leaveTime,
                'duration'   => $duration,
                'status'     => $status,
            ]);
        }

        return response()->json(['status' => 'left', 'duration' => $att?->duration ?? 0]);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // ── AJAX: Chat
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Return latest messages (polling).
     */
    public function getMessages(VirtualClass $virtualClass)
    {
        $messages = $virtualClass->messages()
            ->with('user:id,name,role_id')
            ->latest()
            ->take(50)
            ->get()
            ->map(fn($m) => [
                'id'         => $m->id,
                'user'       => $m->user->name,
                'message'    => $m->message,
                'time'       => $m->created_at->format('H:i'),
                'is_me'      => $m->user_id === Auth::id(),
            ]);

        return response()->json($messages->reverse()->values());
    }

    /**
     * Post a new chat message.
     */
    public function sendMessage(Request $request, VirtualClass $virtualClass)
    {
        $request->validate(['message' => 'required|string|max:1000']);

        $msg = ClassMessage::create([
            'virtual_class_id' => $virtualClass->id,
            'user_id'          => Auth::id(),
            'message'          => $request->message,
        ]);

        return response()->json([
            'id'      => $msg->id,
            'user'    => Auth::user()->name,
            'message' => $msg->message,
            'time'    => $msg->created_at->format('H:i'),
            'is_me'   => true,
        ]);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // ── Materials Upload
    // ═══════════════════════════════════════════════════════════════════════

    public function uploadMaterial(Request $request, VirtualClass $virtualClass)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file'  => 'required|file|mimes:pdf,ppt,pptx,doc,docx,xls,xlsx,mp4,png,jpg|max:51200',
        ]);

        $ext  = $request->file('file')->getClientOriginalExtension();
        $path = $request->file('file')->store('class_materials/' . $virtualClass->id, 'public');

        ClassMaterial::create([
            'virtual_class_id' => $virtualClass->id,
            'title'            => $request->title,
            'file_path'        => $path,
            'file_type'        => strtolower($ext),
        ]);

        return back()->with('success', 'Material uploaded successfully.');
    }
}
