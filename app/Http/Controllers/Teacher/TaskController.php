<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\StudentProfile;
use App\Models\Task;
use App\Models\TaskItem;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        return view('teacher.tasks.index');
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
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'task_type' => ['required', 'in:open_question,multiple_choice,file_delivery,mixed,review'],
            'description' => ['nullable', 'string', 'max:5000'],
            'due_date' => ['nullable', 'date'],
        ]);

        $task = Task::create([
            'title' => $data['title'],
            'task_type' => $data['task_type'],
        ]);

        return redirect()->route('teacher.tasks.builder', $task);
    }

    public function builder(Task $task)
    {
        $typeLabels = [
            'open_question' => 'Pregunta abierta',
            'multiple_choice' => 'Multiple choice',
            'file_delivery' => 'Entrega de archivo',
            'mixed' => 'Mixta',
            'review' => 'Revision',
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

        if ($taskType === 'open_question') {
            $data = $request->validate([
                'question' => ['required', 'string', 'max:5000'],
            ]);
            $content = [
                'question' => $data['question'],
            ];
        } elseif ($taskType === 'multiple_choice') {
            $data = $request->validate([
                'questions' => ['required', 'array', 'min:1'],
                'questions.*.question' => ['required', 'string', 'max:5000'],
                'questions.*.options' => ['required', 'array', 'min:2'],
                'questions.*.options.*' => ['required', 'string', 'max:5000'],
                'questions.*.correct_option' => ['required', 'integer', 'min:0'],
            ]);

            $items = [];

            foreach ($data['questions'] as $questionData) {
                $options = array_values(array_filter($questionData['options'], fn ($option) => trim($option) !== ''));

                if (count($options) < 2) {
                    return back()->withErrors(['questions' => 'Cada pregunta debe tener al menos 2 opciones.']);
                }

                if (!array_key_exists($questionData['correct_option'], $options)) {
                    return back()->withErrors(['questions' => 'Selecciona una opcion correcta valida en cada pregunta.']);
                }

                $items[] = [
                    'question' => $questionData['question'],
                    'options' => $options,
                    'correct_option' => $questionData['correct_option'],
                ];
            }

            $content = [
                'items' => $items,
            ];
        } elseif ($taskType === 'file_delivery') {
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
        } elseif ($taskType === 'review') {
            $data = $request->validate([
                'requires_completion' => ['nullable', 'boolean'],
            ]);

            $content = [
                'requires_completion' => (bool) ($data['requires_completion'] ?? false),
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
            ->route('teacher.tasks.builder', $task)
            ->with('success', 'Contenido guardado.');
    }
}
