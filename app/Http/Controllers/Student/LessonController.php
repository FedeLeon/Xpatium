<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Carbon;

class LessonController extends Controller
{
    public function show(Request $request, Lesson $lesson): View
    {
        $course = $lesson->module->course;
        $profile = StudentProfile::where('student_id', $request->user()->id)->first();

        if (! $profile || $course->user_id !== $profile->teacher_id) {
            abort(404);
        }

        $hasAccess = $request->user()->enrolledCourses()->where('courses.id', $course->id)->exists();
        if (! $hasAccess) {
            abort(403);
        }

        $lesson->load('module.lessons', 'quiz.questions.options');
        $contents = $lesson->contents()->with('asset')->get()->groupBy('type');
        $comments = $lesson->comments()
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->get();
        $module = $lesson->module;
        $moduleLessons = $module
            ? $module->lessons()->orderBy('id')->get()
            : collect();
        $completedLessonIdList = $module
            ? $request->user()->completedLessons()
                ->where('module_id', $module->id)
                ->pluck('lessons.id')
                ->all()
            : [];
        $moduleTotal = $moduleLessons->count();
        $moduleCompleted = $module ? count($completedLessonIdList) : 0;
        $moduleProgress = $moduleTotal > 0 ? (int) round(($moduleCompleted / $moduleTotal) * 100) : 0;
        $currentIndex = $moduleLessons->search(fn ($item) => $item->id === $lesson->id);
        $previousLesson = $currentIndex !== false ? $moduleLessons->get($currentIndex - 1) : null;
        $nextLesson = $currentIndex !== false ? $moduleLessons->get($currentIndex + 1) : null;

        $isCompleted = $request->user()
            ->completedLessons()
            ->where('lessons.id', $lesson->id)
            ->exists();

        $quiz = $lesson->quiz;
        $latestAttempt = $quiz
            ? $quiz->attempts()->where('user_id', $request->user()->id)->latest('completed_at')->first()
            : null;

        return view(
            'student.lessons.show',
            compact(
                'lesson',
                'course',
                'contents',
                'comments',
                'isCompleted',
                'module',
                'moduleLessons',
                'moduleCompleted',
                'moduleTotal',
                'moduleProgress',
                'previousLesson',
                'nextLesson',
                'quiz',
                'latestAttempt'
            )
        );
    }

    public function toggleCompletion(Request $request, Lesson $lesson): RedirectResponse|JsonResponse
    {
        $course = $lesson->module->course;
        $profile = StudentProfile::where('student_id', $request->user()->id)->first();

        if (! $profile || $course->user_id !== $profile->teacher_id) {
            abort(404);
        }

        $hasAccess = $request->user()->enrolledCourses()->where('courses.id', $course->id)->exists();
        if (! $hasAccess) {
            abort(403);
        }

        $isCompleted = $request->user()
            ->completedLessons()
            ->where('lessons.id', $lesson->id)
            ->exists();

        if ($isCompleted) {
            $request->user()->completedLessons()->detach($lesson->id);
        } else {
            $request->user()->completedLessons()->attach($lesson->id, [
                'completed_at' => Carbon::now(),
            ]);
        }

        if ($request->expectsJson()) {
            $module = $lesson->module;
            $moduleLessons = $module
                ? $module->lessons()->orderBy('id')->get()
                : collect();
            $completedLessonIdList = $module
                ? $request->user()->completedLessons()
                    ->where('module_id', $module->id)
                    ->pluck('lessons.id')
                    ->all()
                : [];
            $moduleTotal = $moduleLessons->count();
            $moduleCompleted = $module ? count($completedLessonIdList) : 0;
            $moduleProgress = $moduleTotal > 0 ? (int) round(($moduleCompleted / $moduleTotal) * 100) : 0;

            return response()->json([
                'is_completed' => ! $isCompleted,
                'module_progress' => $moduleProgress,
                'module_completed' => $moduleCompleted,
                'module_total' => $moduleTotal,
            ]);
        }

        return back();
    }
}
