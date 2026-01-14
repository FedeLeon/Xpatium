<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AnnouncementRead;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $profile = StudentProfile::where('student_id', $request->user()->id)->first();
        $courses = collect();
        $announcements = collect();
        $readAnnouncementIds = [];
        $unreadAnnouncementsCount = 0;

        if ($profile) {
            $courses = $request->user()
                ->enrolledCourses()
                ->where('courses.user_id', $profile->teacher_id)
                ->latest('courses.created_at')
                ->get();

            $announcements = Announcement::with('teacher')
                ->where('teacher_id', $profile->teacher_id)
                ->where('status', Announcement::STATUS_ACTIVE)
                ->where(function ($query) {
                    $query
                        ->whereNull('starts_at')
                        ->orWhere('starts_at', '<=', now());
                })
                ->where(function ($query) {
                    $query
                        ->whereNull('ends_at')
                        ->orWhere('ends_at', '>=', now());
                })
                ->latest()
                ->get();

            if ($announcements->isNotEmpty()) {
                $readAnnouncementIds = AnnouncementRead::where('student_id', $request->user()->id)
                    ->whereIn('announcement_id', $announcements->pluck('id'))
                    ->pluck('announcement_id')
                    ->all();

                $unreadAnnouncementsCount = $announcements
                    ->whereNotIn('id', $readAnnouncementIds)
                    ->count();
            }
        }

        return view('dashboards.student', compact(
            'courses',
            'announcements',
            'readAnnouncementIds',
            'unreadAnnouncementsCount'
        ));
    }
}
