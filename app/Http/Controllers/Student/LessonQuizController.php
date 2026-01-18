<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\LessonQuizAttempt;
use App\Models\LessonQuizAnswer;
use App\Models\StudentProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LessonQuizController extends Controller
{
    public function store(Request $request, Lesson $lesson): RedirectResponse|JsonResponse
    {
        $course = $lesson->module->course;
        $profile = StudentProfile::where('student_id', $request->user()->id)->first();

        if (! $profile || $course->user_id !== $profile->teacher_id) {
            abort(404);
        }

        $hasAccess = $request->user()->enrolledCourses()->where('courses.id', $course->id)->exists();
        if (! $hasAccess) {
            abort(403);
        }

        $quiz = $lesson->quiz()->with('questions.options')->first();
        if (! $quiz || $quiz->questions->isEmpty()) {
            return back()->withErrors(['quiz' => 'Esta leccion no tiene un cuestionario disponible.']);
        }

        $data = $request->validate([
            'answers' => ['required', 'array'],
        ]);

        $answers = $data['answers'];
        $missing = $quiz->questions->filter(fn ($question) => ! array_key_exists($question->id, $answers));
        if ($missing->isNotEmpty()) {
            return back()->withErrors(['answers' => 'Responde todas las preguntas antes de enviar.']);
        }

        $selectedOptions = [];
        foreach ($quiz->questions as $question) {
            $optionId = (int) ($answers[$question->id] ?? 0);
            $option = $question->options->firstWhere('id', $optionId);
            if (! $option) {
                return back()->withErrors(['answers' => 'Selecciona una opcion valida para cada pregunta.']);
            }
            $selectedOptions[$question->id] = $option;
        }

        $totalQuestions = $quiz->questions->count();
        $score = 0;
        $feedback = [];
        foreach ($quiz->questions as $question) {
            $selected = $selectedOptions[$question->id];
            $correctOption = $question->options->firstWhere('is_correct', true);
            $feedback[$question->id] = [
                'selected' => $selected->id,
                'correct' => $correctOption?->id,
                'is_correct' => $selected->is_correct,
            ];

            if ($selected->is_correct) {
                $score++;
            }
        }

        DB::transaction(function () use ($quiz, $request, $score, $totalQuestions, $selectedOptions) {
            $attempt = LessonQuizAttempt::create([
                'quiz_id' => $quiz->id,
                'user_id' => $request->user()->id,
                'score' => $score,
                'total_questions' => $totalQuestions,
                'completed_at' => Carbon::now(),
            ]);

            foreach ($selectedOptions as $questionId => $option) {
                LessonQuizAnswer::create([
                    'attempt_id' => $attempt->id,
                    'question_id' => $questionId,
                    'option_id' => $option->id,
                    'is_correct' => $option->is_correct,
                ]);
            }
        });

        if ($request->expectsJson()) {
            return response()->json([
                'score' => $score,
                'total' => $totalQuestions,
                'feedback' => $feedback,
            ]);
        }

        return redirect()
            ->route('student.lessons.show', $lesson)
            ->with('quiz_result', [
                'score' => $score,
                'total' => $totalQuestions,
                'feedback' => $feedback,
            ]);
    }
}
