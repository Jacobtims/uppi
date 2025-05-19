<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monitors', function (Blueprint $table) {
            $table->timestamp('next_check_at')->nullable()->after('last_checked_at');
        });

        // Set initial next_check_at for existing monitors
        $monitors = DB::table('monitors')->whereNull('next_check_at')->get();
        foreach ($monitors as $monitor) {
            DB::table('monitors')
                ->where('id', $monitor->id)
                ->update([
                    'next_check_at' => $monitor->last_checked_at
                        ? date('Y-m-d H:i:s', strtotime($monitor->last_checked_at) + ($monitor->interval * 60))
                        : now(),
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('monitors', function (Blueprint $table) {
            $table->dropColumn('next_check_at');
        });
    }
};
