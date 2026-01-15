<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MeetingRoomController extends Controller
{
    public function show(Request $request, Meeting $meeting): View
    {
        $user = $request->user();
        $isTeacher = $user->role === User::ROLE_TEACHER && $meeting->teacher_id === $user->id;
        $isStudent = $user->role === User::ROLE_STUDENT
            && $meeting->attendees()->where('users.id', $user->id)->exists();

        if (! $isTeacher && ! $isStudent) {
            abort(404);
        }

        $canJoin = true;
        $joinAvailableAt = null;
        if ($isStudent) {
            $joinAvailableAt = $meeting->scheduled_for->copy()->subMinutes(15);
            $canJoin = now()->greaterThanOrEqualTo($joinAvailableAt);
        }

        return view('meetings.show', compact('meeting', 'canJoin', 'joinAvailableAt'));
    }
}
