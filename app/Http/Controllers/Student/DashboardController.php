<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $profile = StudentProfile::where('student_id', $request->user()->id)->first();
        $announcements = collect();

        if ($profile) {
            $announcements = Announcement::where('teacher_id', $profile->teacher_id)
                ->where('status', Announcement::STATUS_ACTIVE)
                ->where(function ($query) {
                    $query->whereNull('starts_at')->orWhere('starts_at', '<=', now()->toDateString());
                })
                ->where(function ($query) {
                    $query->whereNull('ends_at')->orWhere('ends_at', '>=', now()->toDateString());
                })
                ->latest()
                ->get();
        }

        return view('dashboards.student', compact('announcements'));
    }
}
