<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lap;
use App\Models\Racing_session;
use App\Models\Telemetry_log;
use App\Models\User;
use DB;
use Exception;
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
     * Devuelve la sesión activa para el script de telemetría.
     */
    public function activeSession()
    {
        $session = Racing_session::where('is_active', true)->latest()->first();

        if (!$session) {
            return response()->json(['error' => 'No hay sesión activa'], 404);
        }

        return response()->json($session);
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


    public function updateStatus(Request $request){
        $request->validate([
            'session_id' => 'required|exists:racing_sessions,id',
            'status' => 'required|array'
        ]);

        $session = Racing_session::findOrFail($request->session_id);
        $session->update([
            'last_status_json' => $request->status
        ]);

        return response()->json(['message' => 'Status updated']);
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

    public function updateMetadata(Request $request){
        $request->validate([
            'session_id' => 'required|exists:racing_sessions,id',
            'track_id' => 'required|string',
            'car_id'=>'nullable|string'
        ]);

        $session = Racing_session::findOrFail($request->session_id);
        $session->update([
            'track_id'=> $request->track_id,
            'car_id'=> $request->car_id,
        ]);

        return response()->json(['message'=> 'Metadata updated', 'track' => $request->track_id]); 
    }

    public function stopEngine(){
        shell_exec('taskkill /F /IM python.exe');
        @unlink(base_path('scripts/telemetry.pid'));
        cache()->forget('telemetry_engine_running');
        return response()->json(['message' => 'Engine Stopped']);
    }

    public function startEngine(){
        $scriptPath = base_path('scripts/f1_24_real_telemetry.py');
        $python = 'C:\Users\adria\AppData\Local\Python\bin\python.exe';
        $command = "start /B \"TelemetryEngine\" \"$python\" \"$scriptPath\"";

        try {
            pclose(popen($command, "r"));
            return response()->json(['message' => 'Engine Started!']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function checkEngine(){
        return cache()->remember('telemetry_engine_running', 1, function() {
            $pidFile = base_path('scripts/telemetry.pid');
            if (!file_exists($pidFile)) return ['running' => false];
            
            $pid = trim(file_get_contents($pidFile));
            if (!$pid) return ['running' => false];

            $output = shell_exec("tasklist /FI \"PID eq $pid\" /NH");
            $isRunning = str_contains($output, (string)$pid);

            if (!$isRunning) {
                @unlink($pidFile);
            }

            return ['running' => $isRunning];
        });
    }



    public function cerrarSesion(Request $request){
        $request->validate(['session_id'=> 'required|exists:racing_sessions,id']);

        $session = Racing_session::findOrFail($request->session_id);

        $session->update([
            'is_active' => false
        ]);
        return response()->json(['message' => 'Sesion cerrada correctamente']);
    }
}
