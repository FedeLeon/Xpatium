<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(Request $request): View
    {
        $courses = Course::where('user_id', $request->user()->id)
            ->withCount(['modules', 'lessons'])
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
            'subtitle' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price_ars' => ['required', 'numeric', 'min:0'],
            'price_usd' => ['nullable', 'numeric', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'image' => ['nullable', 'image', 'max:2048'],
            'duration' => ['nullable', 'string', 'max:255'],
            'level' => ['nullable', 'in:principiante,intermedio,avanzado'],
            'payment_link' => ['nullable', 'url', 'max:2048'],
            'landing_custom_text' => ['nullable', 'string', 'max:2000'],
            'landing_custom_image' => ['nullable', 'image', 'max:2048'],
            'landing_video_url' => ['nullable', 'url', 'max:2048'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('courses', 'public');
            $converted = ImageService::convertToWebp('public', $imagePath, 'courses');
            $imagePath = $converted['path'];
        }

        $landingCustomImagePath = null;
        if ($request->hasFile('landing_custom_image')) {
            $landingCustomImagePath = $request->file('landing_custom_image')->store('courses/landing', 'public');
            $converted = ImageService::convertToWebp('public', $landingCustomImagePath, 'courses/landing');
            $landingCustomImagePath = $converted['path'];
        }

        $course = Course::create([
            'user_id' => $request->user()->id,
            'name' => $data['name'],
            'subtitle' => $data['subtitle'],
            'description' => $data['description'],
            'price_ars' => $data['price_ars'],
            'price_usd' => $data['price_usd'] ?? 0,
            'start_date' => $data['start_date'],
            'image_path' => $imagePath,
            'duration' => $data['duration'],
            'level' => $data['level'],
            'payment_link' => $data['payment_link'] ?? null,
            'landing_custom_text' => $data['landing_custom_text'] ?? null,
            'landing_custom_image_path' => $landingCustomImagePath,
            'landing_video_url' => $data['landing_video_url'] ?? null,
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

    public function edit(Request $request, Course $course): View
    {
        if ($course->user_id !== $request->user()->id) {
            abort(404);
        }

        return view('teacher.courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course): RedirectResponse
    {
        if ($course->user_id !== $request->user()->id) {
            abort(404);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'subtitle' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price_ars' => ['required', 'numeric', 'min:0'],
            'price_usd' => ['nullable', 'numeric', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'image' => ['nullable', 'image', 'max:2048'],
            'duration' => ['nullable', 'string', 'max:255'],
            'level' => ['nullable', 'in:principiante,intermedio,avanzado'],
            'payment_link' => ['nullable', 'url', 'max:2048'],
            'landing_custom_text' => ['nullable', 'string', 'max:2000'],
            'landing_custom_image' => ['nullable', 'image', 'max:2048'],
            'landing_video_url' => ['nullable', 'url', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            if ($course->image_path) {
                Storage::disk('public')->delete($course->image_path);
            }
            $imagePath = $request->file('image')->store('courses', 'public');
            $converted = ImageService::convertToWebp('public', $imagePath, 'courses');
            $course->image_path = $converted['path'];
        }

        if ($request->hasFile('landing_custom_image')) {
            if ($course->landing_custom_image_path) {
                Storage::disk('public')->delete($course->landing_custom_image_path);
            }
            $landingImagePath = $request->file('landing_custom_image')->store('courses/landing', 'public');
            $converted = ImageService::convertToWebp('public', $landingImagePath, 'courses/landing');
            $course->landing_custom_image_path = $converted['path'];
        }

        $course->fill([
            'name' => $data['name'],
            'subtitle' => $data['subtitle'],
            'description' => $data['description'],
            'price_ars' => $data['price_ars'],
            'price_usd' => $data['price_usd'] ?? 0,
            'start_date' => $data['start_date'],
            'duration' => $data['duration'],
            'level' => $data['level'],
            'payment_link' => $data['payment_link'] ?? null,
            'landing_custom_text' => $data['landing_custom_text'] ?? null,
            'landing_video_url' => $data['landing_video_url'] ?? null,
        ]);
        $course->save();

        return redirect()->route('teacher.courses.show', $course);
    }

    public function destroy(Request $request, Course $course): RedirectResponse
    {
        if ($course->user_id !== $request->user()->id) {
            abort(404);
        }

        if ($course->image_path) {
            Storage::disk('public')->delete($course->image_path);
        }
        if ($course->landing_custom_image_path) {
            Storage::disk('public')->delete($course->landing_custom_image_path);
        }

        $course->delete();

        return redirect()->route('teacher.courses.index');
    }

}
