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
        Schema::create('laps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('racing_sessions_id')->constrained()->cascadeOnDelete();
            $table->decimal('lap_time', 8,3);
            $table->decimal('sector_1', 6,3);
            $table->decimal('sector_2', 6,3);
            $table->decimal('sector_3', 6,3);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laps');
    }
};
