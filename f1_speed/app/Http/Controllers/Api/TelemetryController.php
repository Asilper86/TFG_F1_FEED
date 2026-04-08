<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lap;
use App\Models\Telemetry_log;
use App\Models\User;
use DB;
use Illuminate\Http\Request;

class TelemetryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user_id = auth()->id();
        $laps = Lap::whereHas('session', function($query) use ($user_id){
            $query->where('user_id', $user_id);
        })
        ->with(['session', 'telemetryLogs'])
        ->latest()
        ->get();

        return response()->json($laps);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'session_id' => 'required',
            'lap_time' => 'required|numeric',
            'sector_1' => 'nullable|numeric',
            'sector_2' => 'nullable|numeric',
            'sector_3' => 'nullable|numeric',
            'telemetry' => 'required|array',
            'lap_number' => 'required|numeric'
        ]);

        return DB::transaction(function () use ($validated) {
            $lap = Lap::create([
                'session_id' => $validated['session_id'],
                'lap_time'   => $validated['lap_time'],
                'lap_number' => $validated['lap_number'],
                'sector_1'   => $validated['sector_1'] ?? 0,
                'sector_2'   => $validated['sector_2'] ?? 0,
                'sector_3'   => $validated['sector_3'] ?? 0,
            ]);

            Telemetry_log::create([
                'lap_id'         => $lap->id,
                'telemetry_json' => $validated['telemetry']
            ]);

            return response()->json(['message' => 'OK', 'id' => $lap->id], 201);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
