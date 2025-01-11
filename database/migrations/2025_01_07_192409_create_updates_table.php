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
        Schema::create('updates', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained('users');
            $table->string('title');
            $table->text('content');
            $table->string('image');
            $table->string('slug')->unique();
            $table->boolean('is_published')->default(false);
            $table->dateTime('from')->nullable();
            $table->dateTime('to')->nullable();
            $table->string('type');
            $table->text('body');
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('updates');
    }
};
