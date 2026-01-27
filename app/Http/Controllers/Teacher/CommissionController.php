<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\StudentProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CommissionController extends Controller
{
    public function index(Request $request): View
    {
        $teacher = $request->user();

        $students = StudentProfile::with('student')
            ->where('teacher_id', $teacher->id)
            ->latest()
            ->get();

        $commissions = Commission::with('students')
            ->withCount('students')
            ->where('teacher_id', $teacher->id)
            ->latest()
            ->get();

        return view('teacher.students.commission', compact('students', 'commissions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $teacher = $request->user();
        $allowedStudentIds = StudentProfile::where('teacher_id', $teacher->id)
            ->pluck('student_id')
            ->all();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'students' => ['nullable', 'array'],
            'students.*' => ['integer', Rule::in($allowedStudentIds)],
        ]);

        $commission = Commission::create([
            'teacher_id' => $teacher->id,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        $commission->students()->sync($data['students'] ?? []);

        return redirect()->route('teacher.students.commission');
    }

    public function updateStudents(Request $request, Commission $commission): RedirectResponse
    {
        if ($commission->teacher_id !== $request->user()->id) {
            abort(404);
        }

        $allowedStudentIds = StudentProfile::where('teacher_id', $request->user()->id)
            ->pluck('student_id')
            ->all();

        $data = $request->validate([
            'students' => ['nullable', 'array'],
            'students.*' => ['integer', Rule::in($allowedStudentIds)],
        ]);

        $commission->students()->sync($data['students'] ?? []);

        return redirect()->route('teacher.students.commission');
    }

    public function update(Request $request, Commission $commission): RedirectResponse
    {
        if ($commission->teacher_id !== $request->user()->id) {
            abort(404);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $commission->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        return redirect()->route('teacher.students.commission');
    }

    public function destroy(Request $request, Commission $commission): RedirectResponse
    {
        if ($commission->teacher_id !== $request->user()->id) {
            abort(404);
        }

        $commission->delete();

        return redirect()->route('teacher.students.commission');
    }
}
