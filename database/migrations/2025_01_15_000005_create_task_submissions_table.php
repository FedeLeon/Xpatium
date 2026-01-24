<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('task_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->json('content')->nullable();
            $table->string('file_path')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('corrected_at')->nullable();
            $table->text('feedback')->nullable();
            $table->decimal('score', 6, 2)->nullable();
            $table->timestamps();
            $table->unique(['task_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_submissions');
    }
};
