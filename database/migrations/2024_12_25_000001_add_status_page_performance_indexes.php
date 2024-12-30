<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('status_page_items', function (Blueprint $table) {
            // Index for enabled items ordered by order
            $table->index(['status_page_id', 'is_enabled', 'order'], 'status_page_items_list_index');
        });

        Schema::table('anomalies', function (Blueprint $table) {
            // Index for 30-day status lookups
            $table->index(['monitor_id', 'started_at'], 'anomalies_status_lookup_index');
        });

        Schema::table('checks', function (Blueprint $table) {
            // Index for 30-day status lookups
            $table->index(['monitor_id', 'checked_at', 'status'], 'checks_status_lookup_index');
        });
    }

    public function down(): void
    {
        Schema::table('status_page_items', function (Blueprint $table) {
            $table->dropIndex('status_page_items_list_index');
        });

        Schema::table('anomalies', function (Blueprint $table) {
            $table->dropIndex('anomalies_status_lookup_index');
        });

        Schema::table('checks', function (Blueprint $table) {
            $table->dropIndex('checks_status_lookup_index');
        });
    }
};
