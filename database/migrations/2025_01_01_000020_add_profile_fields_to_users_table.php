<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar_path')->nullable()->after('password');
            $table->date('birth_date')->nullable()->after('last_name');
            $table->string('phone', 50)->nullable()->after('birth_date');
            $table->string('address')->nullable()->after('phone');
            $table->string('country', 120)->nullable()->after('address');
            $table->string('state', 120)->nullable()->after('country');
            $table->string('city', 120)->nullable()->after('state');
            $table->string('postal_code', 30)->nullable()->after('city');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'avatar_path',
                'birth_date',
                'phone',
                'address',
                'country',
                'state',
                'city',
                'postal_code',
            ]);
        });
    }
};
