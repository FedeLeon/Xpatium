<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('meetings')) {
            return;
        }

        Schema::table('meetings', function (Blueprint $table) {
            if (! Schema::hasColumn('meetings', 'jitsi_room_name')) {
                $table->string('jitsi_room_name')->nullable()->unique();
            }
            if (! Schema::hasColumn('meetings', 'jitsi_room_url')) {
                $table->string('jitsi_room_url')->nullable();
            }
        });

        if (Schema::hasColumn('meetings', 'daily_room_name') && Schema::hasColumn('meetings', 'daily_room_url')) {
            DB::table('meetings')
                ->whereNotNull('daily_room_name')
                ->whereNull('jitsi_room_name')
                ->update([
                    'jitsi_room_name' => DB::raw('daily_room_name'),
                    'jitsi_room_url' => DB::raw('daily_room_url'),
                ]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('meetings')) {
            return;
        }

        Schema::table('meetings', function (Blueprint $table) {
            if (Schema::hasColumn('meetings', 'jitsi_room_name')) {
                $table->dropColumn('jitsi_room_name');
            }
            if (Schema::hasColumn('meetings', 'jitsi_room_url')) {
                $table->dropColumn('jitsi_room_url');
            }
        });
    }
};
