<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('checks', function (Blueprint $table) {
            $table->index(['monitor_id', 'checked_at', 'response_time'], 'checks_performance_index');
        });

        Schema::table('monitors', function (Blueprint $table) {
            $table->index('is_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('checks', function (Blueprint $table) {
            $table->dropIndex('checks_performance_index');
        });

        Schema::table('monitors', function (Blueprint $table) {
            $table->dropIndex(['is_enabled']);
        });
    }
};
