<?php

use App\Http\Controllers\DashboardController;
use App\Models\Lap;
use Illuminate\Support\Facades\Route;
use Laravel\Jetstream\InertiaManager;
use Inertia\Inertia;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    /* Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard'); */

    Route::get('dashboard-f1', [DashboardController::class, 'index'])->name('dashboard');
    Route::delete('telemetry/lap/{lap}', [DashboardController::class, 'destroy'])->name('lap.destroy');
});



Route::get('/test-react', function () {
    return view('test-react');
});
