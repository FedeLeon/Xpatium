<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
        ]);

        User::factory()->create([
            'name' => 'Profesor Demo',
            'email' => 'profesor@example.com',
            'role' => 'teacher',
        ]);

        User::factory()->create([
            'name' => 'Alumno Demo',
            'email' => 'alumno@example.com',
            'role' => 'student',
        ]);
    }
}
