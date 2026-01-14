<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AnnouncementRead;
use App\Models\StudentProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function markRead(Request $request, Announcement $announcement): RedirectResponse
    {
        $profile = StudentProfile::where('student_id', $request->user()->id)->first();

        if (! $profile || $announcement->teacher_id !== $profile->teacher_id) {
            abort(403);
        }

        AnnouncementRead::updateOrCreate(
            [
                'announcement_id' => $announcement->id,
                'student_id' => $request->user()->id,
            ],
            [
                'read_at' => now(),
            ]
        );

        return back();
    }
}
