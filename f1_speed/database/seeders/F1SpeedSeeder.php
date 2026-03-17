<?php

namespace Database\Seeders;

use App\Models\Racing_session;
use App\Models\Social_post;
use App\Models\Telemetry_log;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\RacingSession;
use App\Models\Lap;
use App\Models\TelemetryLog;
use App\Models\SocialPost;
use Illuminate\Support\Facades\Hash;

class F1SpeedSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear Usuario
        $user = User::updateOrCreate([
            'name' => 'Adrian Piloto',
            'email' => 'test@f1speed.com',
            'password' => Hash::make('password'),
        ]);

        // 2. Crear una Sesión de Carrera
        $session = Racing_session::updateOrCreate([
            'user_id' => $user->id,
            'sim_key' => 'ACC-SPA-2026',
            'track_id' => 'Spa-Francorchamps',
            'car_id' => 'Ferrari 296 GT3',
            'weather_conditions' => 'Dry',
        ]);

        // 3. Crear una Vuelta (Lap)
        $lap = Lap::updateOrCreate([
            'racing_sessions_id' => $session->id,
            'lap_time' => 138.450, // 2:18.450
            'sector_1' => 42.100,
            'sector_2' => 54.250,
            'sector_3' => 42.100,
        ]);

        // 4. Crear Telemetría para esa vuelta
        Telemetry_log::create([
            'lap_id' => $lap->id,
            'telemetry_json' => [
                'speed' => [200, 210, 225, 180, 150],
                'throttle' => [100, 100, 100, 20, 0],
                'brake' => [0, 0, 0, 80, 100],
                'rpm' => [7500, 7600, 7800, 6000, 5000]
            ]
        ]);

        // 5. Crear un Post Social
        $post = Social_post::create([
            'user_id' => $user->id,
            'title' => '¡Mi mejor vuelta en Spa!',
            'content' => 'Acabo de bajar a 2:18 con el Ferrari. ¡Brutal!',
            'media_path' => 'vueltas/spa_ferrari.jpg'
        ]);

        // 6. Añadir un Comentario polimórfico al Post
        $post->comments()->updateOrCreate([
            'user_id' => $user->id,
            'body' => '¡Vaya tiempazo, Adrian!'
        ]);
    }
}