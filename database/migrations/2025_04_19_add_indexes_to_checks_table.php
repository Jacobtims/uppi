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
        Schema::table('checks', function (Blueprint $table) {
            // Add index for soft delete with checked_at
            $table->index(['deleted_at', 'checked_at']);

            // Add composite index for the status page history aggregator query
            $table->index(['monitor_id', 'checked_at', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checks', function (Blueprint $table) {
            $table->dropIndex(['deleted_at', 'checked_at']);
            $table->dropIndex(['monitor_id', 'checked_at', 'deleted_at']);
        });
    }
};
