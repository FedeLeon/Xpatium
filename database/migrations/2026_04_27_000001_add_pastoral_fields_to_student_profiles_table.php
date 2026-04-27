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
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->boolean('has_children')->default(false)->after('status');
            $table->unsignedTinyInteger('children_count')->nullable()->after('has_children');
            $table->string('sentimental_status', 120)->nullable()->after('children_count');
            $table->text('family_situation')->nullable()->after('sentimental_status');
            $table->text('current_challenges')->nullable()->after('family_situation');
            $table->text('leader_notes')->nullable()->after('current_challenges');
            $table->timestamp('pastoral_updated_at')->nullable()->after('leader_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'has_children',
                'children_count',
                'sentimental_status',
                'family_situation',
                'current_challenges',
                'leader_notes',
                'pastoral_updated_at',
            ]);
        });
    }
};

