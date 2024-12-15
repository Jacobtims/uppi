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
        Schema::table('anomalies', function (Blueprint $table) {
            $table->dropForeign(['alert_id']);
            $table->dropColumn('alert_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anomalies', function (Blueprint $table) {
            $table->foreignUlid('alert_id')->nullable()->constrained('alerts')->nullOnDelete();
        });
    }
};
