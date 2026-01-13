<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\LessonComment;
use App\Models\StudentProfile;
use App\Models\User;
use App\Notifications\LessonCommentNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LessonCommentController extends Controller
{
    public function store(Request $request, Lesson $lesson): RedirectResponse
    {
        $user = $request->user();
        $course = $lesson->module->course;

        if ($user->role === User::ROLE_TEACHER) {
            if ($course->user_id !== $user->id) {
                abort(404);
            }
        } elseif ($user->role === User::ROLE_STUDENT) {
            $profile = StudentProfile::where('student_id', $user->id)->first();
            if (! $profile || $course->user_id !== $profile->teacher_id) {
                abort(404);
            }

            $hasAccess = $user->enrolledCourses()->where('courses.id', $course->id)->exists();
            if (! $hasAccess) {
                abort(403);
            }
        } else {
            abort(403);
        }

        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
            'parent_id' => ['nullable', 'integer', 'exists:lesson_comments,id'],
        ]);

        if (! empty($data['parent_id'])) {
            $parent = LessonComment::find($data['parent_id']);
            if (! $parent || $parent->lesson_id !== $lesson->id) {
                return back()->withErrors(['parent_id' => 'Comentario invalido.']);
            }
        }

        $comment = LessonComment::create([
            'lesson_id' => $lesson->id,
            'user_id' => $user->id,
            'parent_id' => $data['parent_id'] ?? null,
            'body' => $data['body'],
        ]);

        if ($user->role === User::ROLE_STUDENT) {
            $teacher = $course->teacher;
            if ($teacher && $teacher->id !== $user->id) {
                $teacher->notify(new LessonCommentNotification($comment));
            }
        }

        if (! empty($comment->parent_id)) {
            $parent = LessonComment::find($comment->parent_id);
            if ($parent && $parent->user_id !== $user->id) {
                $parent->user->notify(new LessonCommentNotification($comment));
            }
        }

        return back();
    }

    public function destroy(Request $request, LessonComment $comment): RedirectResponse
    {
        $user = $request->user();
        $lesson = $comment->lesson;
        $course = $lesson->module->course;

        if ($user->role === User::ROLE_TEACHER) {
            if ($course->user_id !== $user->id) {
                abort(404);
            }
        } elseif ($user->role === User::ROLE_STUDENT) {
            if ($comment->user_id !== $user->id) {
                abort(403);
            }
        } else {
            abort(403);
        }

        $comment->delete();

        return back();
    }
}
