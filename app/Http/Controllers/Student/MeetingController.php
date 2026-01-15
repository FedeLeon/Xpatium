<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class MeetingController extends Controller
{
    public function index(Request $request): View
    {
        $meetings = $request->user()
            ->meetings()
            ->with('teacher')
            ->orderBy('scheduled_for')
            ->get();

        $now = Carbon::now();
        $meetings->each(function ($meeting) use ($now) {
            $start = $meeting->scheduled_for;
            $joinAt = $start->copy()->subMinutes(15);
            $endAt = $start->copy()->addMinutes($meeting->duration_minutes);
            $meeting->join_available = $now->between($joinAt, $endAt);
            $meeting->join_available_at = $joinAt;
            $meeting->meeting_end_at = $endAt;
        });

        return view('student.meetings.index', compact('meetings'));
    }
}
