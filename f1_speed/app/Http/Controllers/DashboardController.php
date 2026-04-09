<?php

namespace App\Http\Controllers;

use App\Models\Lap;
use App\Models\Racing_session;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $activeSession = Racing_session::where('user_id', auth()->id())
            ->where('is_active', true)
            ->first();

        $laps = Lap::whereHas('session', function($query){
            $query->where('user_id', auth()->id());
        })
        ->with('telemetryLogs')
        ->latest()
        ->take(15)
        ->get();

        return Inertia::render('Dashboard', [
            'laps' => $laps,
            'activeSession' => $activeSession
        ]);
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
        //
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
    
    public function destroy(Lap $lap){
        if($lap->session->user_id !== auth()->id()){
            abort(403, 'Acceso Denegado: No tienes permisos para esa acción.');
        };

        $lap->delete();
        return redirect()->back();
    }
}
