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
            return back()->withErrors(['course_id' => 'Curso invalido.']);
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

}
