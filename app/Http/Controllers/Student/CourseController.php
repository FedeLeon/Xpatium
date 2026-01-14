<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(Request $request): View
    {
        $profile = StudentProfile::where('student_id', $request->user()->id)->first();
        $courses = collect();
        $lessonTotals = collect();
        $completedTotals = collect();

        if ($profile) {
            $courses = $request->user()
                ->enrolledCourses()
                ->where('courses.user_id', $profile->teacher_id)
                ->latest('courses.created_at')
                ->get();
        }

        $enrolledIds = $courses->pluck('id')->all();
        $courseIds = $courses->pluck('id')->all();

        if (! empty($courseIds)) {
            $lessonTotals = Lesson::query()
                ->selectRaw('course_modules.course_id, COUNT(lessons.id) as total')
                ->join('course_modules', 'lessons.module_id', '=', 'course_modules.id')
                ->whereIn('course_modules.course_id', $courseIds)
                ->groupBy('course_modules.course_id')
                ->pluck('total', 'course_modules.course_id');

            $completedTotals = Lesson::query()
                ->selectRaw('course_modules.course_id, COUNT(lessons.id) as completed')
                ->join('course_modules', 'lessons.module_id', '=', 'course_modules.id')
                ->join('lesson_user', 'lesson_user.lesson_id', '=', 'lessons.id')
                ->where('lesson_user.user_id', $request->user()->id)
                ->whereIn('course_modules.course_id', $courseIds)
                ->groupBy('course_modules.course_id')
                ->pluck('completed', 'course_modules.course_id');
        }

        return view('student.courses.index', compact('courses', 'enrolledIds', 'lessonTotals', 'completedTotals'));
    }

    public function show(Request $request, Course $course): View
    {
        $profile = StudentProfile::where('student_id', $request->user()->id)->first();
        if (! $profile || $course->user_id !== $profile->teacher_id) {
            abort(404);
        }

        $hasAccess = $request->user()
            ->enrolledCourses()
            ->where('courses.id', $course->id)
            ->exists();
        if (! $hasAccess) {
            abort(403);
        }

        $course->load(['modules.lessons']);
        $lessonIds = $course->modules->flatMap->lessons->pluck('id');
        $completedLessonIds = $lessonIds->isEmpty()
            ? collect()
            : $request->user()
                ->completedLessons()
                ->whereIn('lessons.id', $lessonIds)
                ->pluck('lessons.id');
        $completedLessonIdList = $completedLessonIds->all();
        $moduleCompletionIds = $course->modules
            ->filter(function ($module) use ($completedLessonIdList) {
                $lessonIds = $module->lessons->pluck('id');
                if ($lessonIds->isEmpty()) {
                    return false;
                }

                foreach ($lessonIds as $lessonId) {
                    if (! in_array($lessonId, $completedLessonIdList, true)) {
                        return false;
                    }
                }

                return true;
            })
            ->pluck('id')
            ->all();
        $totalLessons = $lessonIds->count();
        $completedLessons = $completedLessonIds->count();
        $courseProgress = $totalLessons > 0
            ? (int) round(($completedLessons / $totalLessons) * 100)
            : 0;

        return view(
            'student.courses.show',
            compact('course', 'completedLessonIdList', 'moduleCompletionIds', 'totalLessons', 'completedLessons', 'courseProgress')
        );
    }
}
