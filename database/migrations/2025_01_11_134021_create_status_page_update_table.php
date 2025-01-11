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
        Schema::create('status_page_update', function (Blueprint $table) {
            $table->foreignUlid('status_page_id')->constrained('status_pages')->cascadeOnDelete();
            $table->foreignUlid('update_id')->constrained('updates')->cascadeOnDelete();

            $table->primary(['status_page_id', 'update_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_page_update');
    }
};
