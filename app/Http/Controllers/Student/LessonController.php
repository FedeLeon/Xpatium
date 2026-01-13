<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\View\View;

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

        $contents = $lesson->contents()->with('asset')->get()->groupBy('type');
        $comments = $lesson->comments()
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->get();

        return view('student.lessons.show', compact('lesson', 'course', 'contents', 'comments'));
    }
}
