<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\StudentProfile;
use App\Models\Task;
use App\Models\TaskItem;
use App\Models\TaskSubmission;
use App\Notifications\TaskAssignedNotification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with(['commission', 'students'])
            ->withCount('students')
            ->whereHas('items')
            ->latest()
            ->get();

        return view('teacher.tasks.index', compact('tasks'));
    }

    public function create(Request $request)
    {
        $teacher = $request->user();

        $students = StudentProfile::with('student')
            ->where('teacher_id', $teacher->id)
            ->latest()
            ->get();

        $commissions = Commission::withCount('students')
            ->where('teacher_id', $teacher->id)
            ->latest()
            ->get();

        return view('teacher.tasks.create', compact('students', 'commissions'));
    }

    public function store(Request $request)
    {
        $teacher = $request->user();
        $allowedStudentIds = StudentProfile::where('teacher_id', $teacher->id)
            ->pluck('student_id')
            ->all();
        $allowedCommissionIds = Commission::where('teacher_id', $teacher->id)
            ->pluck('id')
            ->all();

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'task_type' => ['required', 'in:file_delivery,mixed'],
            'grading_mode' => ['required', 'in:feedback,score,both'],
            'description' => ['nullable', 'string', 'max:5000'],
            'due_date' => ['nullable', 'date'],
            'commission_id' => ['nullable', 'integer', Rule::in($allowedCommissionIds)],
            'student_ids' => ['nullable', 'array'],
            'student_ids.*' => ['integer', Rule::in($allowedStudentIds)],
        ]);

        $task = Task::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'task_type' => $data['task_type'],
            'due_date' => $data['due_date'] ?? null,
            'commission_id' => $data['commission_id'] ?? null,
            'grading_mode' => $data['grading_mode'],
        ]);

        if (!empty($data['student_ids'])) {
            $task->students()->sync($data['student_ids']);
        }

        $task->load('students', 'commission.students');
        $commissionStudents = $task->commission?->students ?? collect();
        $recipients = $task->students->merge($commissionStudents)->unique('id');
        foreach ($recipients as $student) {
            $student->notify(new TaskAssignedNotification($task));
        }

        return redirect()->route('teacher.tasks.builder', $task);
    }

    public function edit(Request $request, Task $task)
    {
        $teacher = $request->user();

        $students = StudentProfile::with('student')
            ->where('teacher_id', $teacher->id)
            ->latest()
            ->get();

        $commissions = Commission::withCount('students')
            ->where('teacher_id', $teacher->id)
            ->latest()
            ->get();

        $task->load('students');

        return view('teacher.tasks.create', compact('students', 'commissions', 'task'));
    }

    public function update(Request $request, Task $task)
    {
        $teacher = $request->user();
        $allowedStudentIds = StudentProfile::where('teacher_id', $teacher->id)
            ->pluck('student_id')
            ->all();
        $allowedCommissionIds = Commission::where('teacher_id', $teacher->id)
            ->pluck('id')
            ->all();

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'task_type' => ['required', 'in:file_delivery,mixed'],
            'grading_mode' => ['required', 'in:feedback,score,both'],
            'description' => ['nullable', 'string', 'max:5000'],
            'due_date' => ['nullable', 'date'],
            'commission_id' => ['nullable', 'integer', Rule::in($allowedCommissionIds)],
            'student_ids' => ['nullable', 'array'],
            'student_ids.*' => ['integer', Rule::in($allowedStudentIds)],
        ]);

        $task->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'task_type' => $data['task_type'],
            'due_date' => $data['due_date'] ?? null,
            'commission_id' => $data['commission_id'] ?? null,
            'grading_mode' => $data['grading_mode'],
        ]);

        $task->students()->sync($data['student_ids'] ?? []);

        return redirect()->route('teacher.tasks.builder', $task);
    }

    public function builder(Task $task)
    {
        $typeLabels = [
            'file_delivery' => 'Entrega de archivo',
            'mixed' => 'Mixta',
        ];

        return view('teacher.tasks.builder', [
            'task' => $task,
            'typeLabel' => $typeLabels[$task->task_type] ?? $task->task_type,
        ]);
    }

    public function storeContent(Request $request, Task $task)
    {
        $taskType = $task->task_type;
        $content = [];
        $itemType = $taskType;

        if ($taskType === 'file_delivery') {
            $data = $request->validate([
                'instructions' => ['required', 'string', 'max:5000'],
            ]);
            $content = [
                'instructions' => $data['instructions'],
            ];
        } elseif ($taskType === 'mixed') {
            $data = $request->validate([
                'items' => ['required', 'array', 'min:1'],
                'items.*.type' => ['required', 'in:open_question,multiple_choice'],
                'items.*.question' => ['required', 'string', 'max:5000'],
                'items.*.options' => ['nullable', 'array'],
                'items.*.options.*' => ['nullable', 'string', 'max:5000'],
                'items.*.correct_option' => ['nullable', 'integer', 'min:0'],
            ]);

            $items = [];

            foreach ($data['items'] as $item) {
                $type = $item['type'];
                $question = $item['question'];

                if ($type === 'multiple_choice') {
                    $options = array_values(array_filter($item['options'] ?? [], fn ($option) => trim($option) !== ''));
                    if (count($options) < 2) {
                        return back()->withErrors(['items' => 'Cada multiple choice debe tener al menos 2 opciones.']);
                    }
                    $correct = $item['correct_option'] ?? null;
                    if ($correct === null || !array_key_exists($correct, $options)) {
                        return back()->withErrors(['items' => 'Selecciona una opcion correcta valida en cada multiple choice.']);
                    }
                    $items[] = [
                        'type' => $type,
                        'question' => $question,
                        'options' => $options,
                        'correct_option' => $correct,
                    ];
                } else {
                    $items[] = [
                        'type' => $type,
                        'question' => $question,
                    ];
                }
            }

            $content = [
                'items' => $items,
            ];
        } else {
            abort(404);
        }

        TaskItem::create([
            'task_id' => $task->id,
            'type' => $itemType,
            'content' => $content,
        ]);

        return redirect()
            ->route('teacher.tasks.index')
            ->with('success', 'Contenido guardado.');
    }

    public function show(Task $task)
    {
        $task->load('items');

        return view('teacher.tasks.show', compact('task'));
    }

    public function correct(Task $task)
    {
        $task->load(['items', 'commission.students', 'students']);

        $students = $task->students;
        if ($students->isEmpty() && $task->commission) {
            $students = $task->commission->students;
        }

        $submissions = TaskSubmission::where('task_id', $task->id)
            ->get()
            ->keyBy('student_id');

        if ($task->task_type === 'mixed') {
            $taskContentItem = $task->items->firstWhere('type', 'mixed');
            $taskItems = $taskContentItem?->content['items'] ?? [];

            $submissions->each(function (TaskSubmission $submission) use ($taskItems) {
                $submittedItems = $submission->content['items'] ?? [];
                $normalized = [];

                foreach ($taskItems as $index => $taskItem) {
                    $type = $taskItem['type'] ?? 'open_question';
                    $submitted = $submittedItems[$index] ?? [];
                    if ($type === 'multiple_choice') {
                        $normalized[] = [
                            'type' => $type,
                            'selected_option' => $submitted['selected_option'] ?? null,
                        ];
                    } else {
                        $normalized[] = [
                            'type' => $type,
                            'answer' => $submitted['answer'] ?? null,
                        ];
                    }
                }

                $submission->content = ['items' => $normalized];
            });
        }

        return view('teacher.tasks.correct', compact('task', 'students', 'submissions'));
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()
            ->route('teacher.tasks.index')
            ->with('success', 'Tarea eliminada.');
    }

    public function storeCorrection(Request $request, Task $task, TaskSubmission $submission)
    {
        if ($submission->task_id !== $task->id) {
            abort(404);
        }

        $data = $request->validate([
            'feedback' => ['nullable', 'string', 'max:5000'],
            'score' => ['nullable', 'numeric'],
        ]);

        $gradingMode = $task->grading_mode;
        $feedback = null;
        $score = null;

        if ($gradingMode === 'feedback' || $gradingMode === 'both') {
            $feedback = $data['feedback'] ?? null;
        }

        if ($gradingMode === 'score' || $gradingMode === 'both') {
            $score = $data['score'] ?? null;
        }

        $submission->update([
            'feedback' => $feedback,
            'score' => $score,
            'corrected_at' => now(),
        ]);

        return redirect()
            ->route('teacher.tasks.correct', $task)
            ->with('success', 'Correccion guardada.');
    }
}
