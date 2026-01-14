<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Lesson;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseModuleController extends Controller
{
    public function index(Request $request, Course $course): View
    {
        if ($course->user_id !== $request->user()->id) {
            abort(404);
        }

        $course->load(['modules.lessons']);

        return view('teacher.courses.modules.index', compact('course'));
    }

    public function storeModule(Request $request, Course $course): RedirectResponse
    {
        if ($course->user_id !== $request->user()->id) {
            abort(404);
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        CourseModule::create([
            'course_id' => $course->id,
            'title' => $data['title'],
            'subtitle' => $data['subtitle'],
            'description' => $data['description'],
        ]);

        return redirect()->route('teacher.courses.modules.index', $course);
    }

    public function storeLesson(Request $request, CourseModule $module): RedirectResponse
    {
        $course = $module->course;
        if ($course->user_id !== $request->user()->id) {
            abort(404);
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        Lesson::create([
            'module_id' => $module->id,
            'title' => $data['title'],
            'description' => $data['description'],
        ]);

        return redirect()->route('teacher.courses.modules.index', $course);
    }

    public function updateModule(Request $request, CourseModule $module): RedirectResponse
    {
        $course = $module->course;
        if ($course->user_id !== $request->user()->id) {
            abort(404);
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $module->update($data);

        return redirect()->route('teacher.courses.modules.index', $course);
    }

    public function destroyModule(Request $request, CourseModule $module): RedirectResponse
    {
        $course = $module->course;
        if ($course->user_id !== $request->user()->id) {
            abort(404);
        }

        $module->delete();

        return redirect()->route('teacher.courses.modules.index', $course);
    }

    public function destroyLesson(Request $request, Lesson $lesson): RedirectResponse
    {
        $course = $lesson->module->course;
        if ($course->user_id !== $request->user()->id) {
            abort(404);
        }

        $lesson->delete();

        return redirect()->route('teacher.courses.modules.index', $course);
    }
}
