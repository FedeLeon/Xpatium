<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $conversations = $user->conversations()
            ->with([
                'participants',
                'messages' => function ($query) {
                    $query->latest()->limit(1);
                },
            ])
            ->latest()
            ->get();

        $students = collect();
        if ($user->role === User::ROLE_TEACHER) {
            $students = StudentProfile::with('student')
                ->where('teacher_id', $user->id)
                ->get()
                ->pluck('student');
        }

        $activeConversation = null;

        return view('chat.index', compact('conversations', 'students', 'activeConversation'));
    }

    public function show(Request $request, Conversation $conversation): View
    {
        $user = $request->user();

        $conversation->load([
            'participants',
            'messages' => function ($query) {
                $query->orderBy('created_at');
            },
            'messages.user',
        ]);
        if (! $conversation->participants->contains($user->id)) {
            abort(404);
        }

        $conversation->participants()->updateExistingPivot($user->id, [
            'last_read_at' => now(),
        ]);

        $conversations = $user->conversations()
            ->with([
                'participants',
                'messages' => function ($query) {
                    $query->latest()->limit(1);
                },
            ])
            ->latest()
            ->get();

        $students = collect();
        if ($user->role === User::ROLE_TEACHER) {
            $students = StudentProfile::with('student')
                ->where('teacher_id', $user->id)
                ->get()
                ->pluck('student');
        }

        $activeConversation = $conversation;

        return view('chat.index', compact('conversations', 'students', 'activeConversation'));
    }

    public function storeMessage(Request $request, Conversation $conversation)
    {
        $user = $request->user();
        if (! $conversation->participants->contains($user->id)) {
            abort(404);
        }

        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
            'body' => $data['body'],
        ]);

        broadcast(new MessageSent($message))->toOthers();

        if ($request->expectsJson()) {
            $avatarUrl = $user->avatar_path ? asset('storage/' . $user->avatar_path) : null;
            return response()->json([
                'message' => [
                    'id' => $message->id,
                    'body' => $message->body,
                    'time' => $message->created_at->format('H:i'),
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'last_name' => $user->last_name,
                        'avatar_url' => $avatarUrl,
                        'initial' => strtoupper(substr($user->name ?? 'A', 0, 1)),
                    ],
                ],
            ]);
        }

        return redirect()->route('chat.show', $conversation);
    }

    public function storeDirect(Request $request): RedirectResponse
    {
        $user = $request->user();
        if ($user->role !== User::ROLE_TEACHER) {
            abort(403);
        }

        $data = $request->validate([
            'student_id' => ['required', 'integer'],
        ]);

        $student = StudentProfile::where('teacher_id', $user->id)
            ->where('student_id', $data['student_id'])
            ->first();

        if (! $student) {
            return back()->withErrors(['student_id' => 'Usuario invalido.']);
        }

        $existing = Conversation::where('type', Conversation::TYPE_DIRECT)
            ->whereHas('participants', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })
            ->whereHas('participants', function ($query) use ($data) {
                $query->where('users.id', $data['student_id']);
            })
            ->first();

        if ($existing) {
            return redirect()->route('chat.show', $existing);
        }

        $conversation = Conversation::create([
            'type' => Conversation::TYPE_DIRECT,
            'title' => null,
            'created_by' => $user->id,
        ]);

        $conversation->participants()->sync([$user->id, $data['student_id']]);

        return redirect()->route('chat.show', $conversation);
    }

    public function storeGroup(Request $request): RedirectResponse
    {
        $user = $request->user();
        if ($user->role !== User::ROLE_TEACHER) {
            abort(403);
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'students' => ['required', 'array', 'min:2'],
            'students.*' => ['integer'],
        ]);

        $validStudentIds = StudentProfile::where('teacher_id', $user->id)
            ->whereIn('student_id', $data['students'])
            ->pluck('student_id')
            ->all();

        if (count($validStudentIds) < 2) {
            return back()->withErrors(['students' => 'Selecciona al menos 2 usuarios validos.']);
        }

        $conversation = Conversation::create([
            'type' => Conversation::TYPE_GROUP,
            'title' => $data['title'],
            'created_by' => $user->id,
        ]);

        $participantIds = array_merge([$user->id], $validStudentIds);
        $conversation->participants()->sync($participantIds);

        return redirect()->route('chat.show', $conversation);
    }
}
