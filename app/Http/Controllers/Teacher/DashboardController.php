<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $courseCount = Course::where('user_id', $user->id)->count();
        $studentCount = StudentProfile::where('teacher_id', $user->id)->count();
        $lessonCount = Lesson::whereHas('module.course', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();
        $announcementCount = Announcement::where('teacher_id', $user->id)->count();

        $announcements = Announcement::where('teacher_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboards.teacher', compact(
            'courseCount',
            'studentCount',
            'lessonCount',
            'announcementCount',
            'announcements'
        ));
    }
}
