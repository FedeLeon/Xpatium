<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Lesson;
use App\Models\LessonComment;
use App\Models\LessonContent;
use App\Models\LessonQuiz;
use App\Models\LessonQuizQuestion;
use App\Models\LessonQuizOption;
use App\Models\Message;
use App\Models\Conversation;
use App\Models\Meeting;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $avatarPaths = $this->seedImages([
            'landing/assets/img/team/dev1.jpg',
            'landing/assets/img/team/dev2.jpg',
            'landing/assets/img/team/dev3.jpg',
            'landing/assets/img/team/dev4.jpg',
            'landing/assets/img/team/dev1-fun.jpg',
            'landing/assets/img/team/dev2-fun.jpg',
            'landing/assets/img/team/dev3-fun.jpg',
            'landing/assets/img/team/dev4-fun.jpg',
            'landing/assets/img/team/dev1-fun2.jpg',
            'landing/assets/img/team/dev2-fun2.jpg',
            'landing/assets/img/team/dev3-fun2.jpg',
        ], 'seed/avatars', 'avatar');

        $courseImagePaths = $this->seedImages([
            'seed/course-examples/web-development.jpg',
            'seed/course-examples/digital-marketing.jpg',
            'seed/course-examples/databases.jpg',
        ], 'seed/courses', 'course');

        $nextAvatar = $this->cyclePicker($avatarPaths);
        $nextCourseImage = $this->cyclePicker($courseImagePaths);

        User::factory()->create([
            'name' => 'Admin Demo',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'password' => Hash::make('admin1234'),
            'status' => 'activo',
            'avatar_path' => $nextAvatar(),
        ]);

        $teacher = User::factory()->create([
            'name' => 'Profesor Demo',
            'email' => 'profesor@example.com',
            'role' => 'teacher',
            'password' => Hash::make('profesor1234'),
            'status' => 'activo',
            'avatar_path' => $nextAvatar(),
        ]);

        $demoStudent = User::factory()->create([
            'name' => 'Alumno Demo',
            'email' => 'alumno@example.com',
            'role' => 'student',
            'password' => Hash::make('alumno1234'),
            'status' => 'activo',
            'avatar_path' => $nextAvatar(),
        ]);

        $lessonAssets = $this->seedLessonAssets($teacher);

        StudentProfile::create([
            'teacher_id' => $teacher->id,
            'student_id' => $demoStudent->id,
            'status' => 'activo',
        ]);

        $students = User::factory(3)->create([
            'role' => 'student',
            'status' => 'activo',
        ]);

        foreach ($students as $student) {
            $student->update([
                'avatar_path' => $nextAvatar(),
            ]);
            StudentProfile::create([
                'teacher_id' => $teacher->id,
                'student_id' => $student->id,
                'status' => 'activo',
            ]);
        }

        $courseNames = [
            'Introduccion al desarrollo web',
            'Marketing digital para principiantes',
            'Bases de datos desde cero',
        ];
        $courseDurations = [
            '8 semanas',
            '6 semanas',
            '10 semanas',
        ];
        $courseLevels = [
            'principiante',
            'intermedio',
            'avanzado',
        ];

        $createdCourses = collect();

        foreach ($courseNames as $index => $name) {
            $course = Course::create([
                'user_id' => $teacher->id,
                'name' => $name,
                'subtitle' => 'Curso base de ejemplo',
                'description' => 'Contenido inicial para pruebas.',
                'price_ars' => 12000,
                'price_usd' => 39,
                'start_date' => now()->addDays(rand(7, 30)),
                'image_path' => $nextCourseImage(),
                'duration' => $courseDurations[$index] ?? null,
                'level' => $courseLevels[$index] ?? null,
            ]);
            $createdCourses->push($course);

            $modules = [
                [
                    'title' => 'Modulo 1',
                    'subtitle' => 'Introduccion',
                    'description' => 'Conceptos base del curso.',
                ],
                [
                    'title' => 'Modulo 2',
                    'subtitle' => 'Practica',
                    'description' => 'Ejercicios y casos reales.',
                ],
            ];

            foreach ($modules as $moduleData) {
                $module = CourseModule::create([
                    'course_id' => $course->id,
                    'title' => $moduleData['title'],
                    'subtitle' => $moduleData['subtitle'],
                    'description' => $moduleData['description'],
                ]);

                $lessonOne = Lesson::create([
                    'module_id' => $module->id,
                    'title' => 'Leccion 1',
                    'description' => 'Contenido inicial de la leccion.',
                ]);

                $lessonTwo = Lesson::create([
                    'module_id' => $module->id,
                    'title' => 'Leccion 2',
                    'description' => 'Contenido complementario.',
                ]);

                $this->seedLessonContent($lessonOne, $lessonAssets);
                $this->seedLessonQuiz($lessonOne);
                $this->seedLessonContent($lessonTwo, $lessonAssets);
                $this->seedLessonQuiz($lessonTwo);
            }
        }

        $allStudents = $students->concat([$demoStudent]);
        $courseIds = $createdCourses->pluck('id')->all();
        foreach ($allStudents as $student) {
            $student->enrolledCourses()->sync($courseIds);
        }

        $this->seedLessonComments($teacher, $allStudents);
        $this->seedChatMessages($teacher, $demoStudent, $students);
        $this->seedUpcomingMeetings($teacher, $allStudents);
    }

    private function seedImages(array $sources, string $destinationDirectory, string $prefix): array
    {
        Storage::disk('public')->makeDirectory($destinationDirectory);
        $paths = [];

        foreach ($sources as $index => $source) {
            $extension = pathinfo($source, PATHINFO_EXTENSION);
            $destination = $destinationDirectory.'/'.$prefix.'-'.($index + 1).'.'.$extension;
            $stored = $this->storeSeedImage($source, $destination);
            if ($stored) {
                $paths[] = $stored;
            }
        }

        return $paths;
    }

    private function storeSeedImage(string $source, string $destination): ?string
    {
        $sourcePath = public_path($source);
        if (! File::exists($sourcePath)) {
            return null;
        }

        Storage::disk('public')->put($destination, File::get($sourcePath));

        return $destination;
    }

    private function cyclePicker(array $items): callable
    {
        $items = collect($items)->values();

        return function () use ($items) {
            if ($items->isEmpty()) {
                return null;
            }

            $item = $items->shift();
            $items->push($item);

            return $item;
        };
    }

    private function seedLessonContent(Lesson $lesson, array $lessonAssets): void
    {
        $contentBlocks = [
            'Objetivos de la leccion: entender los conceptos clave y aplicarlos con un caso simple.',
            'Resumen practico con ejemplos reales y una mini actividad para resolver en 10 minutos.',
            'Puntos clave: definiciones basicas, errores frecuentes y recomendaciones del profesor.',
            'Actividad guiada: crea tu primer entregable y revisa una lista de verificacion.',
            'Material extra: enlaces sugeridos y preguntas para debatir en clase.',
        ];

        $selected = collect($contentBlocks)->shuffle()->take(2);

        foreach ($selected as $text) {
            LessonContent::create([
                'lesson_id' => $lesson->id,
                'type' => 'text',
                'text_content' => $text,
            ]);
        }

        $imageAssets = collect($lessonAssets['images'] ?? [])->filter();
        if ($imageAssets->isNotEmpty()) {
            $imageAssets->shuffle()->take(2)->each(function (Asset $asset) use ($lesson) {
                LessonContent::create([
                    'lesson_id' => $lesson->id,
                    'type' => 'image',
                    'asset_id' => $asset->id,
                ]);
            });
        }

        $videoAssets = collect($lessonAssets['videos'] ?? [])->filter();
        if ($videoAssets->isNotEmpty()) {
            $asset = $videoAssets->random();
            LessonContent::create([
                'lesson_id' => $lesson->id,
                'type' => 'video',
                'asset_id' => $asset->id,
            ]);
        }

        $fileAssets = collect($lessonAssets['files'] ?? [])->filter();
        if ($fileAssets->isNotEmpty() && rand(0, 1) === 1) {
            $asset = $fileAssets->random();
            LessonContent::create([
                'lesson_id' => $lesson->id,
                'type' => 'file',
                'asset_id' => $asset->id,
            ]);
        }
    }

    private function seedLessonQuiz(Lesson $lesson): void
    {
        $quiz = LessonQuiz::create([
            'lesson_id' => $lesson->id,
            'title' => 'Chequeo rapido',
            'description' => 'Responde estas preguntas para reforzar lo visto.',
        ]);

        $questionSets = [
            [
                'prompt' => 'Cual es el objetivo principal de esta leccion?',
                'options' => [
                    ['text' => 'Comprender los conceptos clave', 'is_correct' => true],
                    ['text' => 'Memorizar definiciones sin aplicarlas', 'is_correct' => false],
                    ['text' => 'Completar el curso en una clase', 'is_correct' => false],
                    ['text' => 'Ignorar los ejercicios practicos', 'is_correct' => false],
                ],
            ],
            [
                'prompt' => 'Que accion es recomendable al finalizar la practica?',
                'options' => [
                    ['text' => 'Revisar la lista de verificacion', 'is_correct' => true],
                    ['text' => 'Guardar todo sin revisar', 'is_correct' => false],
                    ['text' => 'Omitir el material extra', 'is_correct' => false],
                    ['text' => 'Dejar el ejercicio a la mitad', 'is_correct' => false],
                ],
            ],
            [
                'prompt' => 'Que ayuda mas para entender el contenido?',
                'options' => [
                    ['text' => 'Aplicar un ejemplo real', 'is_correct' => true],
                    ['text' => 'Evitar practicar', 'is_correct' => false],
                    ['text' => 'No hacer preguntas', 'is_correct' => false],
                    ['text' => 'Leer sin anotar', 'is_correct' => false],
                ],
            ],
        ];

        $position = 1;
        foreach ($questionSets as $questionData) {
            $question = LessonQuizQuestion::create([
                'quiz_id' => $quiz->id,
                'prompt' => $questionData['prompt'],
                'position' => $position++,
            ]);

            $optionPosition = 1;
            foreach ($questionData['options'] as $optionData) {
                LessonQuizOption::create([
                    'question_id' => $question->id,
                    'option_text' => $optionData['text'],
                    'is_correct' => $optionData['is_correct'],
                    'position' => $optionPosition++,
                ]);
            }
        }
    }

    private function seedLessonAssets(User $teacher): array
    {
        $imageSources = [
            'seed/lesson-examples/lesson-image-1.jpg',
            'seed/lesson-examples/lesson-image-2.jpg',
            'seed/lesson-examples/lesson-image-3.jpg',
            'seed/lesson-examples/lesson-image-4.jpg',
        ];

        $videoSources = [
            'seed/lesson-examples/lesson-video.mp4',
            'seed/lesson-examples/lesson-video-2.mp4',
            'seed/lesson-examples/lesson-video-3.mp4',
        ];

        $fileSources = [
            'seed/lesson-examples/lesson-handout.pdf',
        ];

        $imageAssets = $this->createAssetsFromSources(
            $teacher,
            $imageSources,
            'image',
            'assets/'.$teacher->id.'/image'
        );

        $videoAssets = $this->createAssetsFromSources(
            $teacher,
            $videoSources,
            'video',
            'assets/'.$teacher->id.'/video'
        );

        $fileAssets = $this->createAssetsFromSources(
            $teacher,
            $fileSources,
            'file',
            'assets/'.$teacher->id.'/file'
        );

        return [
            'images' => $imageAssets,
            'videos' => $videoAssets,
            'files' => $fileAssets,
        ];
    }

    private function createAssetsFromSources(User $user, array $sources, string $type, string $destinationDirectory): array
    {
        Storage::disk('public')->makeDirectory($destinationDirectory);
        $assets = [];

        foreach ($sources as $index => $source) {
            $sourcePath = public_path($source);
            if (! File::exists($sourcePath)) {
                continue;
            }

            $extension = pathinfo($source, PATHINFO_EXTENSION);
            $filename = $type.'-seed-'.($index + 1).'.'.$extension;
            $destination = $destinationDirectory.'/'.$filename;
            Storage::disk('public')->put($destination, File::get($sourcePath));

            $assets[] = Asset::create([
                'user_id' => $user->id,
                'type' => $type,
                'path' => $destination,
                'original_name' => $filename,
                'mime' => File::mimeType($sourcePath) ?: null,
                'size' => File::size($sourcePath) ?: 0,
            ]);
        }

        return $assets;
    }

    private function seedLessonComments(User $teacher, Collection $students): void
    {
        $commentBodies = [
            'Me quedo mas claro con este ejemplo, gracias.',
            'Podrias ampliar la parte del ejercicio 2? Me costo un poco.',
            'Buenisimo, ya lo aplique en mi proyecto.',
            'Tengo una duda sobre el paso 3, lo revisamos en clase?',
            'Excelente resumen, me ayudo a repasar.',
        ];

        Lesson::query()->each(function (Lesson $lesson) use ($teacher, $students, $commentBodies) {
            $student = $students->random();
            $comment = LessonComment::create([
                'lesson_id' => $lesson->id,
                'user_id' => $student->id,
                'body' => $commentBodies[array_rand($commentBodies)],
            ]);

            LessonComment::create([
                'lesson_id' => $lesson->id,
                'user_id' => $teacher->id,
                'parent_id' => $comment->id,
                'body' => 'Gracias por el aporte! En la proxima clase lo revisamos con mas detalle.',
            ]);

            $anotherStudent = $students->random();
            if ($anotherStudent->id !== $student->id) {
                LessonComment::create([
                    'lesson_id' => $lesson->id,
                    'user_id' => $anotherStudent->id,
                    'body' => $commentBodies[array_rand($commentBodies)],
                ]);
            }
        });
    }

    private function seedChatMessages(User $teacher, User $demoStudent, Collection $students): void
    {
        $directConversation = Conversation::create([
            'type' => Conversation::TYPE_DIRECT,
            'title' => null,
            'created_by' => $teacher->id,
        ]);
        $directConversation->participants()->sync([
            $teacher->id => ['last_read_at' => now()],
            $demoStudent->id => ['last_read_at' => now()],
        ]);

        $directMessages = [
            [$teacher, 'Hola! Bienvenido al curso. Cualquier duda avisame.'],
            [$demoStudent, 'Gracias profe! Estoy empezando con la leccion 1.'],
            [$teacher, 'Perfecto. Fijate los objetivos y despues hacemos el ejercicio.'],
            [$demoStudent, 'Dale, te aviso si me trabo con algo.'],
        ];

        $this->createMessages($directConversation, $directMessages);

        $extraStudent = $students->first();
        if ($extraStudent) {
            $studentConversation = Conversation::create([
                'type' => Conversation::TYPE_DIRECT,
                'title' => null,
                'created_by' => $teacher->id,
            ]);
            $studentConversation->participants()->sync([
                $teacher->id => ['last_read_at' => now()],
                $extraStudent->id => ['last_read_at' => now()],
            ]);

            $studentMessages = [
                [$extraStudent, 'Hola profe, ya vi la clase y me encanto.'],
                [$teacher, 'Genial! Practica con el desafio y me contas.'],
                [$extraStudent, 'Si, lo hago hoy a la tarde.'],
            ];

            $this->createMessages($studentConversation, $studentMessages);
        }

        $groupStudents = $students->take(2);
        if ($groupStudents->count() >= 2) {
            $groupConversation = Conversation::create([
                'type' => Conversation::TYPE_GROUP,
                'title' => 'Grupo de estudio demo',
                'created_by' => $teacher->id,
            ]);
            $participantIds = $groupStudents->pluck('id')->push($teacher->id)->unique();
            $groupConversation->participants()->syncWithPivotValues($participantIds->all(), [
                'last_read_at' => now(),
            ]);

            $groupMessages = [
                [$teacher, 'Bienvenidos! Compartan dudas y avances por aca.'],
                [$groupStudents->get(0), 'Gracias! Estoy con el modulo 1.'],
                [$groupStudents->get(1), 'Yo hice la actividad, despues paso mi solucion.'],
                [$teacher, 'Excelente, asi aprendemos en conjunto.'],
            ];

            $this->createMessages($groupConversation, $groupMessages);
        }
    }

    private function seedUpcomingMeetings(User $teacher, Collection $students): void
    {
        foreach ($students as $student) {
            $scheduledFor = now()->addDays(rand(1, 7))->setTime(rand(9, 19), 0);
            $roomName = 'demo-'.$teacher->id.'-'.$student->id.'-'.Str::random(8);

            $meeting = Meeting::create([
                'teacher_id' => $teacher->id,
                'title' => 'Clase individual de seguimiento',
                'description' => 'Sesion demo para resolver dudas y avanzar.',
                'meeting_type' => Meeting::TYPE_INDIVIDUAL,
                'scheduled_for' => $scheduledFor,
                'duration_minutes' => 45,
                'daily_room_name' => $roomName,
                'daily_room_url' => 'https://demo.daily.co/'.$roomName,
            ]);

            $meeting->attendees()->sync([$student->id]);
        }
    }

    private function createMessages(Conversation $conversation, array $messages): void
    {
        $time = now()->subMinutes(count($messages) * 6);

        foreach ($messages as [$user, $body]) {
            $message = new Message([
                'conversation_id' => $conversation->id,
                'user_id' => $user->id,
                'body' => $body,
            ]);
            $message->created_at = $time;
            $message->updated_at = $time;
            $message->save();

            $time = $time->addMinutes(6);
        }
    }
}
