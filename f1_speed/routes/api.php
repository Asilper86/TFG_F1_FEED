<?php

use App\Http\Controllers\Api\TelemetryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Models\Lap;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('telemetry/lap', [TelemetryController::class, 'store'])->middleware('auth:sanctum');
Route::get('active-session', [TelemetryController::class, 'activeSession'])->middleware('auth:sanctum');
Route::get('telemetry/test', function(){
    return Lap::with('telemetryLogs')->latest()->get();
});

Route::get('/test-db', function () {
    return Lap::with(['session', 'telemetryLogs', 'comments', 'likes'])->get();
});

Route::post('telemetry/status', [TelemetryController::class, 'updateStatus'])->middleware('auth:sanctum');