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
        Schema::create('alert_triggers', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('anomaly_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('alert_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('monitor_id')->constrained()->cascadeOnDelete();
            $table->string('type')->index(); // 'down' or 'recovery'
            $table->json('channels_notified'); // List of notification channels that were notified
            $table->json('metadata')->nullable(); // Additional data about the trigger
            $table->timestamp('triggered_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alert_triggers');
    }
};
