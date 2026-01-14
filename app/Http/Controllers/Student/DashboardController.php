<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentProfile;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $profile = StudentProfile::where('student_id', $request->user()->id)->first();
        $courses = collect();

        if ($profile) {
            $courses = $request->user()
                ->enrolledCourses()
                ->where('courses.user_id', $profile->teacher_id)
                ->latest('courses.created_at')
                ->get();
        }

        return view('dashboards.student', compact('courses'));
    }
}
