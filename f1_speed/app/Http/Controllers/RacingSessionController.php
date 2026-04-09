<?php

namespace App\Http\Controllers;

use App\Models\Racing_session;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RacingSessionController extends Controller
{
    public function create()
    {
        return Inertia::render('SessionSetup');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'track_id' => 'required|string',
            'car_id' => 'required|string',
            'weather' => 'required|string',
            'setup_json' => 'required|array',
        ]);

        
        Racing_session::where('user_id', auth()->id())->update(['is_active' => false]);

        $session = Racing_session::create([
            'user_id' => auth()->id(),
            'sim_key' => bin2hex(random_bytes(8)),
            'track_id' => $validated['track_id'],
            'car_id' => $validated['car_id'],
            'weather' => $validated['weather'],
            'weather_conditions' => $validated['weather'], 
            'setup_json' => $validated['setup_json'],
            'is_active' => true,
        ]);

        return redirect()->route('dashboard');
    }
}
