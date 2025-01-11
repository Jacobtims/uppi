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
        Schema::create('monitor_update', function (Blueprint $table) {
            $table->foreignUlid('monitor_id')->constrained('monitors')->cascadeOnDelete();
            $table->foreignUlid('update_id')->constrained('updates')->cascadeOnDelete();

            $table->primary(['monitor_id', 'update_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitor_update');
    }
};
