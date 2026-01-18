<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('subtitle')->nullable();
            $table->text('description');
            $table->decimal('price_ars', 10, 2)->default(0);
            $table->decimal('price_usd', 10, 2)->default(0);
            $table->date('start_date')->nullable();
            $table->string('image_path')->nullable();
            $table->string('payment_link')->nullable();
            $table->text('landing_custom_text')->nullable();
            $table->string('landing_custom_image_path')->nullable();
            $table->string('landing_video_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
