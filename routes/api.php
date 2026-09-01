<?php

use App\Http\Controllers\PresenceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('presence')->group(function () {
    Route::get('/{slug}/attendance-data', [PresenceController::class, 'getAttendanceData'])->name('api.presence.attendance-data');
    Route::post('/{slug}/process-scan', [PresenceController::class, 'processScan'])->name('api.presence.process-scan');
    Route::post('/{slug}/process-manual', [PresenceController::class, 'processManual'])->name('api.presence.process-manual');
});
