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
        Schema::table('status_page_items', function (Blueprint $table) {
            $table->dropForeign(['status_page_id']);
            $table->foreign('status_page_id')->references('id')->on('status_pages')->onDelete('cascade');

            $table->dropForeign(['monitor_id']);
            $table->foreign('monitor_id')->references('id')->on('monitors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('status_page_items', function (Blueprint $table) {
            $table->dropForeign(['status_page_id']);
            $table->foreign('status_page_id')->references('id')->on('status_pages');

            $table->dropForeign(['monitor_id']);
            $table->foreign('monitor_id')->references('id')->on('monitors');
        });
    }
};
