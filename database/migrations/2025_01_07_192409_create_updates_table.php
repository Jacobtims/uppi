<?php

use App\Enums\StatusPage\UpdateStatus;
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
            $table->foreignUlid('user_id')->constrained('users')->nullable()->cascadeOnDelete();
            $table->foreignUlid('anomaly_id')->nullable()->constrained('anomalies')->nullOnDelete();
            $table->string('title');
            $table->text('content');
            $table->string('image')->nullable();
            $table->string('slug')->unique()->index();
            $table->boolean('is_published')->default(false);
            $table->dateTime('from')->nullable();
            $table->dateTime('to')->nullable();
            $table->string('type');
            $table->boolean('is_featured')->default(false);
            $table->string('status')->default(UpdateStatus::NEW);
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
