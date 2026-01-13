<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $students = StudentProfile::with('student')
            ->where('teacher_id', $request->user()->id)
            ->latest()
            ->get();

        return view('teacher.students.index', compact('students'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $student = User::create([
            'name' => $data['name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => User::ROLE_STUDENT,
            'status' => 'activo',
        ]);

        StudentProfile::create([
            'teacher_id' => $request->user()->id,
            'student_id' => $student->id,
            'status' => 'activo',
        ]);

        return redirect()->route('teacher.students.index');
    }

    public function updateStatus(Request $request, StudentProfile $studentProfile): RedirectResponse
    {
        if ($studentProfile->teacher_id !== $request->user()->id) {
            abort(404);
        }

        $data = $request->validate([
            'status' => ['required', 'in:activo,bloqueado'],
        ]);

        $studentProfile->status = $data['status'];
        $studentProfile->save();

        $student = $studentProfile->student;
        $student->status = $data['status'];
        $student->save();

        return redirect()->route('teacher.students.index');
    }
}
