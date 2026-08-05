<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PresenceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API routes for presence functionality
Route::prefix('presence')->group(function () {
    // Get attendance data for real-time updates
    Route::get('/{slug}/attendance-data', [PresenceController::class, 'getAttendanceData'])->name('api.presence.attendance-data');
    
    // Process QR scan
    Route::post('/{slug}/process-scan', [PresenceController::class, 'processScan'])->name('api.presence.process-scan');
    
    // Process manual input
    Route::post('/{slug}/process-manual', [PresenceController::class, 'processManual'])->name('api.presence.process-manual');
});