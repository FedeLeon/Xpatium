<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function pending(Request $request)
    {
        $student = $request->user();
        $commissionIds = $student->commissions()->pluck('commissions.id')->all();

        $tasks = Task::with(['commission', 'submissions' => function ($query) use ($student) {
                $query->where('student_id', $student->id);
            }])
            ->whereHas('items')
            ->where(function ($query) use ($student, $commissionIds) {
                $query->whereHas('students', function ($subquery) use ($student) {
                    $subquery->where('student_id', $student->id);
                });

                if (!empty($commissionIds)) {
                    $query->orWhereIn('commission_id', $commissionIds);
                }
            })
            ->whereDoesntHave('submissions', function ($query) use ($student) {
                $query->where('student_id', $student->id)
                    ->whereNotNull('submitted_at');
            })
            ->latest()
            ->get();

        return view('student.tasks.pending', compact('tasks'));
    }

    public function index(Request $request)
    {
        $student = $request->user();

        $tasks = Task::with(['commission', 'submissions' => function ($query) use ($student) {
                $query->where('student_id', $student->id);
            }])
            ->whereHas('submissions', function ($query) use ($student) {
                $query->where('student_id', $student->id)
                    ->whereNotNull('submitted_at');
            })
            ->latest()
            ->get();

        return view('student.tasks.index', compact('tasks'));
    }
}
