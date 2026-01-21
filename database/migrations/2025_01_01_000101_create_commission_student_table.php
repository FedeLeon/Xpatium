<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commission_student', function (Blueprint $table) {
            $table->foreignId('commission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->primary(['commission_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_student');
    }
};
