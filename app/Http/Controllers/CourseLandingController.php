<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\View\View;

class CourseLandingController extends Controller
{
    public function show(Course $course): View
    {
        $course->load(['teacher', 'modules.lessons']);

        return view('courses.landing', compact('course'));
    }
}
