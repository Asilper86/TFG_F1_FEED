<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('f1_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');     // quien recibe la notif
            $table->foreignId('actor_id')->constrained('users')->onDelete('cascade'); // quien la genera
            $table->string('type');        // 'like', 'follow', 'repost', 'comment'
            $table->nullableMorphs('notifiable'); // post o lap al que hace referencia
            $table->timestamp('read_at')->nullable(); // null = no leída
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('f1_notifications');
    }
};
