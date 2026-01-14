<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $courses = $request->user()
            ->enrolledCourses()
            ->latest('courses.created_at')
            ->get();

        return view('dashboards.student', compact('courses'));
    }
}
