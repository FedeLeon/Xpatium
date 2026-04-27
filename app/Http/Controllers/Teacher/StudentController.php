<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\StudentProfile;
use App\Models\User;
use App\Notifications\CourseAccessNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $students = StudentProfile::with('student')
            ->where('teacher_id', $request->user()->id)
            ->latest()
            ->get();

        $courses = $request->user()->courses()->get();

        $studentIds = $students->pluck('student_id')->all();
        $courseIds = $courses->pluck('id')->all();

        $enrolled = collect();
        if ($studentIds && $courseIds) {
            $enrolled = DB::table('course_user')
                ->whereIn('user_id', $studentIds)
                ->whereIn('course_id', $courseIds)
                ->get();
        }

        $enrolledMap = [];
        foreach ($enrolled as $row) {
            $enrolledMap[$row->user_id][$row->course_id] = true;
        }

        return view('teacher.students.index', compact('students', 'courses', 'enrolledMap'));
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

    public function updateCourseAccess(Request $request, StudentProfile $studentProfile): RedirectResponse
    {
        if ($studentProfile->teacher_id !== $request->user()->id) {
            abort(404);
        }

        $data = $request->validate([
            'course_id' => ['required', 'integer'],
            'action' => ['required', 'in:grant,revoke'],
        ]);

        $course = $request->user()->courses()->where('id', $data['course_id'])->first();
        if (! $course) {
            return back()->withErrors(['course_id' => 'Contenido de ayuda invalido.']);
        }

        if ($data['action'] === 'grant') {
            $alreadyEnrolled = $course->students()
                ->where('users.id', $studentProfile->student_id)
                ->exists();
            $course->students()->syncWithoutDetaching([$studentProfile->student_id]);
            if (! $alreadyEnrolled) {
                $studentProfile->student->notify(new CourseAccessNotification($request->user(), $course));
            }
        } else {
            $course->students()->detach($studentProfile->student_id);
        }

        return redirect()->route('teacher.students.index');
    }

    public function destroy(Request $request, StudentProfile $studentProfile): RedirectResponse
    {
        if ($studentProfile->teacher_id !== $request->user()->id) {
            abort(404);
        }

        $student = $studentProfile->student;

        $studentProfile->delete();
        $student->delete();

        return redirect()->route('teacher.students.index');
    }

    public function updatePastoralProfile(Request $request, StudentProfile $studentProfile): RedirectResponse
    {
        if ($studentProfile->teacher_id !== $request->user()->id) {
            abort(404);
        }

        $data = $request->validate([
            'pastoral_profile_id' => ['required', 'integer'],
            'has_children' => ['required', 'boolean'],
            'children_count' => ['nullable', 'integer', 'min:0', 'max:20'],
            'sentimental_status' => ['nullable', 'string', 'max:120'],
            'family_situation' => ['nullable', 'string', 'max:2000'],
            'current_challenges' => ['nullable', 'string', 'max:2000'],
            'leader_notes' => ['nullable', 'string', 'max:4000'],
        ]);

        if ((int) $data['pastoral_profile_id'] !== (int) $studentProfile->id) {
            return redirect()
                ->route('teacher.students.index')
                ->with('error', 'La ficha seleccionada no es valida.');
        }

        $hasChildren = (bool) $data['has_children'];
        $childrenCount = $hasChildren ? (int) ($data['children_count'] ?? 0) : null;
        if ($hasChildren && $childrenCount < 1) {
            return redirect()
                ->route('teacher.students.index')
                ->withErrors(['children_count' => 'Si tiene hijos, indica una cantidad mayor a 0.'])
                ->withInput();
        }

        $studentProfile->update([
            'has_children' => $hasChildren,
            'children_count' => $childrenCount,
            'sentimental_status' => $data['sentimental_status'] ?: null,
            'family_situation' => $data['family_situation'] ?: null,
            'current_challenges' => $data['current_challenges'] ?: null,
            'leader_notes' => $data['leader_notes'] ?: null,
            'pastoral_updated_at' => now(),
        ]);

        return redirect()
            ->route('teacher.students.index')
            ->with('success', 'Ficha de acompanamiento guardada.');
    }

}
