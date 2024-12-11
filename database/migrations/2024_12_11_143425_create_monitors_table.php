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
            $table->id();
            $table->string('type');
            $table->string('address')->index();
            $table->string('name');
            $table->text('body');
            $table->string('expects');
            $table->boolean('is_enabled')->default(true);
            $table->integer('interval')->default(1);
            $table->string('status')->default(Status::UNKNOWN);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
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
