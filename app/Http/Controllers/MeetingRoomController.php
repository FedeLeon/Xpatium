<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MeetingRoomController extends Controller
{
    public function show(Request $request, Meeting $meeting): RedirectResponse
    {
        $user = $request->user();
        $isTeacher = $user->role === User::ROLE_TEACHER && $meeting->teacher_id === $user->id;
        $isStudent = $user->role === User::ROLE_STUDENT
            && $meeting->attendees()->where('users.id', $user->id)->exists();

        if (! $isTeacher && ! $isStudent) {
            abort(404);
        }

        if (! $meeting->daily_room_url) {
            abort(404);
        }

        if ($isStudent) {
            $joinAvailableAt = $meeting->scheduled_for->copy()->subMinutes(15);
            if (now()->lessThan($joinAvailableAt)) {
                abort(403, 'La reunion estara disponible desde '.$joinAvailableAt->format('d/m/Y H:i').'.');
            }
        }

        return redirect()->away($meeting->daily_room_url);
    }
}
