<?php

use App\Http\Controllers\DashboardController;
use App\Livewire\SocialFeed;
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
    

    Route::get('dashboard-f1', [DashboardController::class, 'index'])->name('dashboard');
    Route::delete('telemetry/lap/{lap}', [DashboardController::class, 'destroy'])->name('lap.destroy');
    
    
    Route::get('session/setup', [\App\Http\Controllers\RacingSessionController::class, 'create'])->name('session.setup');
    Route::post('session/setup', [\App\Http\Controllers\RacingSessionController::class, 'store'])->name('session.store');
    Route::get('/feed', SocialFeed::class)->name('social.feed');
    Route::get('/profile/{user?}', \App\Livewire\UserProfile::class)->name('social.profile');
    Route::get('/search', \App\Livewire\SearchGlobal::class)->name('social.search');
});



Route::get('/test-react', function () {
    return view('test-react');
});
