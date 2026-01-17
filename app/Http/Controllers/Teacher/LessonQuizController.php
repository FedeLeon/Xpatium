<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\LessonQuiz;
use App\Models\LessonQuizQuestion;
use App\Models\LessonQuizOption;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LessonQuizController extends Controller
{
    public function store(Request $request, Lesson $lesson): RedirectResponse
    {
        $course = $lesson->module->course;
        if ($course->user_id !== $request->user()->id) {
            abort(404);
        }

        if ($lesson->quiz) {
            return back()->withErrors(['quiz' => 'La leccion ya tiene un cuestionario.']);
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'prompt' => ['required', 'string', 'max:1000'],
            'option_1' => ['required', 'string', 'max:255'],
            'option_2' => ['required', 'string', 'max:255'],
            'option_3' => ['nullable', 'string', 'max:255'],
            'option_4' => ['nullable', 'string', 'max:255'],
            'correct_option' => ['required', 'integer', 'min:1', 'max:4'],
        ]);

        $options = [
            1 => $data['option_1'],
            2 => $data['option_2'],
            3 => $data['option_3'] ?? null,
            4 => $data['option_4'] ?? null,
        ];

        if (empty($options[$data['correct_option']])) {
            return back()->withErrors(['correct_option' => 'La opcion correcta no puede estar vacia.']);
        }

        DB::transaction(function () use ($lesson, $data, $options) {
            $quiz = LessonQuiz::create([
                'lesson_id' => $lesson->id,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
            ]);

            $question = LessonQuizQuestion::create([
                'quiz_id' => $quiz->id,
                'prompt' => $data['prompt'],
                'position' => 1,
            ]);

            foreach ($options as $index => $text) {
                if (! $text) {
                    continue;
                }
                LessonQuizOption::create([
                    'question_id' => $question->id,
                    'option_text' => $text,
                    'is_correct' => $index === (int) $data['correct_option'],
                    'position' => $index,
                ]);
            }
        });

        return redirect()->route('teacher.lessons.show', $lesson);
    }

    public function storeQuestion(Request $request, Lesson $lesson): RedirectResponse
    {
        $course = $lesson->module->course;
        if ($course->user_id !== $request->user()->id) {
            abort(404);
        }

        $quiz = $lesson->quiz;
        if (! $quiz) {
            return back()->withErrors(['quiz' => 'Crea el cuestionario antes de agregar preguntas.']);
        }

        $data = $request->validate([
            'prompt' => ['required', 'string', 'max:1000'],
            'option_1' => ['required', 'string', 'max:255'],
            'option_2' => ['required', 'string', 'max:255'],
            'option_3' => ['nullable', 'string', 'max:255'],
            'option_4' => ['nullable', 'string', 'max:255'],
            'correct_option' => ['required', 'integer', 'min:1', 'max:4'],
        ]);

        $options = [
            1 => $data['option_1'],
            2 => $data['option_2'],
            3 => $data['option_3'] ?? null,
            4 => $data['option_4'] ?? null,
        ];

        if (empty($options[$data['correct_option']])) {
            return back()->withErrors(['correct_option' => 'La opcion correcta no puede estar vacia.']);
        }

        $position = (int) $quiz->questions()->max('position');
        $position = $position > 0 ? $position + 1 : 1;

        $question = LessonQuizQuestion::create([
            'quiz_id' => $quiz->id,
            'prompt' => $data['prompt'],
            'position' => $position,
        ]);

        foreach ($options as $index => $text) {
            if (! $text) {
                continue;
            }
            LessonQuizOption::create([
                'question_id' => $question->id,
                'option_text' => $text,
                'is_correct' => $index === (int) $data['correct_option'],
                'position' => $index,
            ]);
        }

        return redirect()->route('teacher.lessons.show', $lesson);
    }

    public function update(Request $request, Lesson $lesson): RedirectResponse
    {
        $course = $lesson->module->course;
        if ($course->user_id !== $request->user()->id) {
            abort(404);
        }

        $quiz = $lesson->quiz;
        if (! $quiz) {
            return back()->withErrors(['quiz' => 'El cuestionario no existe.']);
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $questionData = $request->validate([
            'prompt' => ['nullable', 'string', 'max:1000'],
            'option_1' => ['nullable', 'string', 'max:255'],
            'option_2' => ['nullable', 'string', 'max:255'],
            'option_3' => ['nullable', 'string', 'max:255'],
            'option_4' => ['nullable', 'string', 'max:255'],
            'correct_option' => ['nullable', 'integer', 'min:1', 'max:4'],
        ]);

        $hasQuestionInput = false;
        foreach (['prompt', 'option_1', 'option_2', 'option_3', 'option_4', 'correct_option'] as $field) {
            if (! empty($questionData[$field])) {
                $hasQuestionInput = true;
                break;
            }
        }

        $options = null;
        if ($hasQuestionInput) {
            $errors = [];
            if (empty($questionData['prompt'])) {
                $errors['prompt'] = 'La pregunta es obligatoria.';
            }
            if (empty($questionData['option_1'])) {
                $errors['option_1'] = 'La opcion 1 es obligatoria.';
            }
            if (empty($questionData['option_2'])) {
                $errors['option_2'] = 'La opcion 2 es obligatoria.';
            }
            if (empty($questionData['correct_option'])) {
                $errors['correct_option'] = 'Selecciona la opcion correcta.';
            }

            if (! empty($errors)) {
                return back()->withErrors($errors)->withInput();
            }

            $options = [
                1 => $questionData['option_1'],
                2 => $questionData['option_2'],
                3 => $questionData['option_3'] ?? null,
                4 => $questionData['option_4'] ?? null,
            ];

            if (empty($options[(int) $questionData['correct_option']])) {
                return back()->withErrors(['correct_option' => 'La opcion correcta no puede estar vacia.'])->withInput();
            }
        }

        DB::transaction(function () use ($quiz, $data, $questionData, $hasQuestionInput, $options) {
            $quiz->update([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
            ]);

            if (! $hasQuestionInput) {
                return;
            }

            $position = (int) $quiz->questions()->max('position');
            $position = $position > 0 ? $position + 1 : 1;

            $question = LessonQuizQuestion::create([
                'quiz_id' => $quiz->id,
                'prompt' => $questionData['prompt'],
                'position' => $position,
            ]);

            foreach ($options as $index => $text) {
                if (! $text) {
                    continue;
                }
                LessonQuizOption::create([
                    'question_id' => $question->id,
                    'option_text' => $text,
                    'is_correct' => $index === (int) $questionData['correct_option'],
                    'position' => $index,
                ]);
            }
        });

        return redirect()->route('teacher.lessons.show', $lesson);
    }

    public function updateQuestion(Request $request, LessonQuizQuestion $question): RedirectResponse
    {
        $course = $question->quiz->lesson->module->course;
        if ($course->user_id !== $request->user()->id) {
            abort(404);
        }

        $data = $request->validate([
            'prompt' => ['required', 'string', 'max:1000'],
            'option_1' => ['required', 'string', 'max:255'],
            'option_2' => ['required', 'string', 'max:255'],
            'option_3' => ['nullable', 'string', 'max:255'],
            'option_4' => ['nullable', 'string', 'max:255'],
            'correct_option' => ['required', 'integer', 'min:1', 'max:4'],
        ]);

        $options = [
            1 => $data['option_1'],
            2 => $data['option_2'],
            3 => $data['option_3'] ?? null,
            4 => $data['option_4'] ?? null,
        ];

        if (empty($options[$data['correct_option']])) {
            return back()->withErrors(['correct_option' => 'La opcion correcta no puede estar vacia.']);
        }

        DB::transaction(function () use ($question, $data, $options) {
            $question->update([
                'prompt' => $data['prompt'],
            ]);

            $existing = $question->options()->get()->keyBy('position');
            foreach ($options as $position => $text) {
                $isCorrect = $position === (int) $data['correct_option'];
                if ($text) {
                    $option = $existing->get($position);
                    if ($option) {
                        $option->update([
                            'option_text' => $text,
                            'is_correct' => $isCorrect,
                        ]);
                    } else {
                        $question->options()->create([
                            'option_text' => $text,
                            'is_correct' => $isCorrect,
                            'position' => $position,
                        ]);
                    }
                } else {
                    $option = $existing->get($position);
                    if ($option) {
                        $option->delete();
                    }
                }
            }
        });

        return redirect()->route('teacher.lessons.show', $question->quiz->lesson);
    }

    public function destroy(Request $request, Lesson $lesson): RedirectResponse
    {
        $course = $lesson->module->course;
        if ($course->user_id !== $request->user()->id) {
            abort(404);
        }

        $quiz = $lesson->quiz;
        if ($quiz) {
            $quiz->delete();
        }

        return redirect()->route('teacher.lessons.show', $lesson);
    }

    public function destroyQuestion(Request $request, LessonQuizQuestion $question): RedirectResponse
    {
        $course = $question->quiz->lesson->module->course;
        if ($course->user_id !== $request->user()->id) {
            abort(404);
        }

        $lesson = $question->quiz->lesson;
        $question->delete();

        return redirect()->route('teacher.lessons.show', $lesson);
    }
}
