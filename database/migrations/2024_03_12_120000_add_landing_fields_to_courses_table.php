<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->string('payment_link')->nullable()->after('image_path');
            $table->text('landing_custom_text')->nullable()->after('payment_link');
            $table->string('landing_custom_image_path')->nullable()->after('landing_custom_text');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['payment_link', 'landing_custom_text', 'landing_custom_image_path']);
        });
    }
};
