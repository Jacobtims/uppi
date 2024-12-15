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
        Schema::create('status_page_items', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('status_page_id')->constrained();
            $table->foreignUlid('monitor_id')->constrained();
            $table->string('name');
            $table->integer('order')->default(0);
            $table->boolean('is_enabled')->default(true);
            $table->boolean('is_showing_favicon')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_page_items');
    }
};
