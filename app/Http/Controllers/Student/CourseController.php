<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(Request $request): View
    {
        $profile = StudentProfile::where('student_id', $request->user()->id)->first();
        $courses = collect();

        if ($profile) {
            $courses = $request->user()
                ->enrolledCourses()
                ->where('courses.user_id', $profile->teacher_id)
                ->latest('courses.created_at')
                ->get();
        }

        $enrolledIds = $courses->pluck('id')->all();

        return view('student.courses.index', compact('courses', 'enrolledIds'));
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

        return view('student.courses.show', compact('course'));
    }
}
