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
        Schema::table('racing_sessions', function (Blueprint $table) {
            $table->json('setup_json')->nullable()->after('car_id');
            $table->boolean('is_active')->default(false)->after('weather_conditions');
            $table->string('weather')->nullable()->after('weather_conditions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('racing_sessions', function (Blueprint $table) {
            $table->dropColumn(['setup_json', 'is_active', 'weather']);
        });
    }
};
