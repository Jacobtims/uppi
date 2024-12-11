<?php

use App\Enums\Checks\Status;
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
        Schema::create('monitors', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('address')->index();
            $table->integer('port')->nullable();
            $table->string('name');
            $table->text('body')->nullable();
            $table->string('expects')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->integer('interval')->default(1);
            $table->string('status')->default(Status::UNKNOWN);
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitors');
    }
};
