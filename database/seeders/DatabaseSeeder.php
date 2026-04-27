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

        $courseImagePaths = array_values($this->seedImages([
            'seed/course-examples/web-development.jpg',
            'seed/course-examples/digital-marketing.jpg',
            'seed/course-examples/databases.jpg',
        ], 'seed/courses', 'church-course'));

        $nextAvatar = $this->cyclePicker($avatarPaths);

        User::factory()->create([
            'name' => 'Admin Demo',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'password' => Hash::make('admin1234'),
            'status' => 'activo',
            'avatar_path' => $nextAvatar(),
        ]);

        $teacher = User::factory()->create([
            'name' => 'Lider Demo',
            'email' => 'lider@example.com',
            'role' => 'teacher',
            'password' => Hash::make('lider1234'),
            'status' => 'activo',
            'avatar_path' => $nextAvatar(),
        ]);

        $demoStudent = User::factory()->create([
            'name' => 'Usuario Demo',
            'email' => 'usuario@example.com',
            'role' => 'student',
            'password' => Hash::make('usuario1234'),
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
            'Fortaleciendo la fe en tiempos dificiles',
            'Sanidad interior y acompanamiento espiritual',
            'Vida familiar con proposito cristiano',
        ];
        $courseDurations = [
            '5 semanas',
            '6 semanas',
            '4 semanas',
        ];
        $courseLevels = [
            'inicial',
            'intermedio',
            'inicial',
        ];

        $createdCourses = collect();

        foreach ($courseNames as $index => $name) {
            $course = Course::create([
                'user_id' => $teacher->id,
                'name' => $name,
                'subtitle' => 'Contenido de ayuda para crecimiento espiritual',
                'description' => 'Material de apoyo para acompanar a usuarios en su camino de fe.',
                'price_ars' => 0,
                'price_usd' => 0,
                'start_date' => now()->addDays(rand(7, 30)),
                'image_path' => $courseImagePaths[$index] ?? null,
                'duration' => $courseDurations[$index] ?? null,
                'level' => $courseLevels[$index] ?? null,
            ]);
            $createdCourses->push($course);

            $modules = [
                [
                    'title' => 'Modulo 1',
                    'subtitle' => 'Escucha y contencion',
                    'description' => 'Principios para escuchar con empatia y acompanar con amor.',
                ],
                [
                    'title' => 'Modulo 2',
                    'subtitle' => 'Accion y seguimiento',
                    'description' => 'Pasos practicos para sostener procesos de ayuda en la iglesia.',
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
                    'description' => 'Fundamentos biblicos para el acompanamiento.',
                ]);

                $lessonTwo = Lesson::create([
                    'module_id' => $module->id,
                    'title' => 'Leccion 2',
                    'description' => 'Herramientas practicas para reuniones de ayuda.',
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
            'Objetivo de la leccion: aprender a acompanar a la persona con escucha activa, oracion y seguimiento responsable.',
            'Guia practica: identificar necesidades espirituales, familiares y emocionales sin emitir juicios.',
            'Puntos clave: respeto por la confidencialidad, trato amoroso y derivacion cuando sea necesario.',
            'Actividad sugerida: planificar una reunion corta de acompanamiento y registrar acuerdos claros.',
            'Reflexion final: elegir un salmo de apoyo y compartir una palabra de esperanza para la semana.',
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
            'title' => 'Chequeo de acompanamiento',
            'description' => 'Responde estas preguntas para reforzar la ayuda espiritual.',
        ]);

        $questionSets = [
            [
                'prompt' => 'Cual es el objetivo principal del acompanamiento en la iglesia?',
                'options' => [
                    ['text' => 'Acompanar con amor, escucha y orientacion biblica', 'is_correct' => true],
                    ['text' => 'Apurar procesos sin conocer a la persona', 'is_correct' => false],
                    ['text' => 'Resolver todo en una sola reunion', 'is_correct' => false],
                    ['text' => 'Evitar el seguimiento semanal', 'is_correct' => false],
                ],
            ],
            [
                'prompt' => 'Que accion es recomendable luego de una reunion de ayuda?',
                'options' => [
                    ['text' => 'Registrar acuerdos y definir proximo contacto', 'is_correct' => true],
                    ['text' => 'No tomar nota de la situacion', 'is_correct' => false],
                    ['text' => 'Cortar el vinculo sin seguimiento', 'is_correct' => false],
                    ['text' => 'Delegar sin explicar el contexto', 'is_correct' => false],
                ],
            ],
            [
                'prompt' => 'Que fortalece mejor el proceso de ayuda espiritual?',
                'options' => [
                    ['text' => 'Orar, escuchar y aplicar la Palabra en casos reales', 'is_correct' => true],
                    ['text' => 'Dar consejos generales sin conocer la situacion', 'is_correct' => false],
                    ['text' => 'Ignorar el contexto familiar', 'is_correct' => false],
                    ['text' => 'Esperar cambios sin acompanar', 'is_correct' => false],
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
            $filename = $type.'-church-seed-'.($index + 1).'.'.$extension;
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
            'Este contenido me ayudo a entender como pedir ayuda con confianza.',
            'Podemos profundizar como acompanar a una familia en crisis?',
            'Gracias por la guia, me dio paz para seguir adelante.',
            'Tengo dudas sobre como sostener la rutina de oracion diaria.',
            'Muy claro el enfoque de escucha y contencion.',
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
                'body' => 'Gracias por compartirlo. En el proximo encuentro lo trabajamos juntos.',
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
            [$teacher, 'Hola! Bienvenido. Estoy para acompanarte en este proceso.'],
            [$demoStudent, 'Gracias lider! Quiero comenzar con el primer contenido.'],
            [$teacher, 'Excelente. Empeza por la leccion 1 y luego coordinamos una reunion.'],
            [$demoStudent, 'Perfecto, hoy mismo la leo y te escribo.'],
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
                [$extraStudent, 'Hola lider, termine el modulo y me hizo bien.'],
                [$teacher, 'Que bueno! Anota tus dudas y las vemos en la reunion.'],
                [$extraStudent, 'Dale, gracias por el acompanamiento.'],
            ];

            $this->createMessages($studentConversation, $studentMessages);
        }

        $groupStudents = $students->take(2);
        if ($groupStudents->count() >= 2) {
            $groupConversation = Conversation::create([
                'type' => Conversation::TYPE_GROUP,
                'title' => 'Comision de ayuda demo',
                'created_by' => $teacher->id,
            ]);
            $participantIds = $groupStudents->pluck('id')->push($teacher->id)->unique();
            $groupConversation->participants()->syncWithPivotValues($participantIds->all(), [
                'last_read_at' => now(),
            ]);

            $groupMessages = [
                [$teacher, 'Bienvenidos! Este espacio es para orar y compartir avances de acompanamiento.'],
                [$groupStudents->get(0), 'Gracias! Estoy trabajando la guia de escucha activa.'],
                [$groupStudents->get(1), 'Yo empece el seguimiento de una familia esta semana.'],
                [$teacher, 'Excelente, sigamos en unidad y con respeto por cada caso.'],
            ];

            $this->createMessages($groupConversation, $groupMessages);
        }
    }

    private function seedUpcomingMeetings(User $teacher, Collection $students): void
    {
        $jitsiDomain = config('services.jitsi.domain', 'meet.jit.si');
        $jitsiDomain = preg_replace('#^https?://#', '', rtrim($jitsiDomain, '/'));

        $teacherSlug = Str::slug(trim($teacher->name.' '.$teacher->last_name));
        if ($teacherSlug === '') {
            $teacherSlug = 'lider-'.$teacher->id;
        }

        foreach ($students as $student) {
            $scheduledFor = now()->addDays(rand(1, 7))->setTime(rand(9, 19), 0);
            $roomCode = Str::lower(Str::random(8));
            $roomName = 'xpatium-'.$teacherSlug.'-'.$roomCode;

            $meeting = Meeting::create([
                'teacher_id' => $teacher->id,
                'title' => 'Reunion de acompanamiento espiritual',
                'description' => 'Encuentro de ayuda para orar, escuchar y definir pasos de seguimiento.',
                'meeting_type' => Meeting::TYPE_INDIVIDUAL,
                'scheduled_for' => $scheduledFor,
                'duration_minutes' => 45,
                'jitsi_room_name' => $roomName,
                'jitsi_room_url' => 'https://'.$jitsiDomain.'/'.$roomName,
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
