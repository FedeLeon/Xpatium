<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\StudentProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MeetingController extends Controller
{
    public function index(Request $request): View
    {
        $teacher = $request->user();

        $students = StudentProfile::with('student')
            ->where('teacher_id', $teacher->id)
            ->where('status', 'activo')
            ->latest()
            ->get();

        $meetings = Meeting::with('attendees')
            ->where('teacher_id', $teacher->id)
            ->orderBy('scheduled_for')
            ->get();

        return view('teacher.meetings.index', compact('students', 'meetings'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'meeting_type' => ['required', 'in:individual,group'],
            'scheduled_date' => ['required', 'date'],
            'scheduled_time' => ['required', 'date_format:H:i'],
            'duration_minutes' => ['required', 'integer', 'min:15', 'max:480'],
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['integer'],
        ]);

        $teacher = $request->user();
        $studentIds = array_map('intval', $data['student_ids']);

        if ($data['meeting_type'] === Meeting::TYPE_INDIVIDUAL && count($studentIds) > 1) {
            return back()->withErrors(['student_ids' => 'La reunion individual solo puede tener un alumno.'])->withInput();
        }

        $allowedStudentIds = StudentProfile::where('teacher_id', $teacher->id)
            ->whereIn('student_id', $studentIds)
            ->pluck('student_id')
            ->all();

        if (count($allowedStudentIds) !== count($studentIds)) {
            return back()->withErrors(['student_ids' => 'Uno o mas alumnos no son validos.'])->withInput();
        }

        $scheduledFor = Carbon::createFromFormat(
            'Y-m-d H:i',
            $data['scheduled_date'].' '.$data['scheduled_time'],
            config('app.timezone')
        );

        $teacherSlug = Str::slug(trim($teacher->name.' '.$teacher->last_name));
        if ($teacherSlug === '') {
            $teacherSlug = 'profesor-'.$teacher->id;
        }
        $roomCode = Str::lower(Str::random(8));
        $roomName = 'xpatium-'.$teacherSlug.'-'.$roomCode;
        $jitsiDomain = config('services.jitsi.domain', 'meet.jit.si');
        $jitsiDomain = preg_replace('#^https?://#', '', rtrim($jitsiDomain, '/'));
        $roomUrl = 'https://'.$jitsiDomain.'/'.$roomName;

        $meeting = Meeting::create([
            'teacher_id' => $teacher->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'meeting_type' => $data['meeting_type'],
            'scheduled_for' => $scheduledFor,
            'duration_minutes' => $data['duration_minutes'],
            'jitsi_room_name' => $roomName,
            'jitsi_room_url' => $roomUrl,
        ]);

        $meeting->attendees()->sync($allowedStudentIds);

        return redirect()->route('teacher.meetings.index');
    }

    public function destroy(Request $request, Meeting $meeting): RedirectResponse
    {
        if ($meeting->teacher_id !== $request->user()->id) {
            abort(404);
        }

        $meeting->delete();

        return redirect()->route('teacher.meetings.index');
    }
}
