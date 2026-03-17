<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Models\Lap;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/test-db', function () {
    return Lap::with(['session', 'telemetryLogs', 'comments', 'likes'])->get();
});