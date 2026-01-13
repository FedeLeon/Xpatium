<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Lesson;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin Demo',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'password' => Hash::make('admin1234'),
            'status' => 'activo',
        ]);

        $teacher = User::factory()->create([
            'name' => 'Profesor Demo',
            'email' => 'profesor@example.com',
            'role' => 'teacher',
            'password' => Hash::make('profesor1234'),
            'status' => 'activo',
        ]);

        $demoStudent = User::factory()->create([
            'name' => 'Alumno Demo',
            'email' => 'alumno@example.com',
            'role' => 'student',
            'password' => Hash::make('alumno1234'),
            'status' => 'activo',
        ]);

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

        foreach ($courseNames as $name) {
            $course = Course::create([
                'user_id' => $teacher->id,
                'name' => $name,
                'subtitle' => 'Curso base de ejemplo',
                'description' => 'Contenido inicial para pruebas.',
                'price_ars' => 12000,
                'price_usd' => 39,
                'start_date' => now()->addDays(rand(7, 30)),
                'image_path' => null,
            ]);

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

                Lesson::create([
                    'module_id' => $module->id,
                    'title' => 'Leccion 1',
                    'description' => 'Contenido inicial de la leccion.',
                ]);

                Lesson::create([
                    'module_id' => $module->id,
                    'title' => 'Leccion 2',
                    'description' => 'Contenido complementario.',
                ]);
            }
        }
    }
}
