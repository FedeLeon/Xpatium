<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(Request $request): View
    {
        $courses = Course::where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return view('teacher.courses.index', compact('courses'));
    }

    public function create(): View
    {
        return view('teacher.courses.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price_ars' => ['required', 'numeric', 'min:0'],
            'price_usd' => ['required', 'numeric', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('courses', 'public');
        }

        $course = Course::create([
            'user_id' => $request->user()->id,
            'name' => $data['name'],
            'subtitle' => $data['subtitle'],
            'description' => $data['description'],
            'price_ars' => $data['price_ars'],
            'price_usd' => $data['price_usd'],
            'start_date' => $data['start_date'],
            'image_path' => $imagePath,
        ]);

        return redirect()->route('teacher.courses.show', $course);
    }

    public function show(Request $request, Course $course): View
    {
        if ($course->user_id !== $request->user()->id) {
            abort(404);
        }

        return view('teacher.courses.show', compact('course'));
    }
}
