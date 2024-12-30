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
        Schema::create('checks', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('monitor_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default(App\Enums\Checks\Status::UNKNOWN)->index();
            $table->float('response_time')->nullable();
            $table->integer('response_code')->nullable()->index();
            $table->text('output')->nullable();
            $table->timestamp('checked_at')->index();
            $table->timestamps();

            // Add composite indexes for the response time widget query
            $table->index(['monitor_id', 'checked_at', 'response_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checks');
    }
};
