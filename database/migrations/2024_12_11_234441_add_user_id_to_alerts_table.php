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
        Schema::disableForeignKeyConstraints();
        Schema::table('alerts', function (Blueprint $table) {
            $table->foreignUlid('user_id')->after('id')->constrained()->cascadeOnDelete();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('alerts', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->dropColumn('user_id');
        });
        Schema::enableForeignKeyConstraints();

    }
};
