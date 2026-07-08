<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AgentAuthController;
use App\Http\Controllers\Api\SessionController;
use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\FaceVerificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Desktop Agent API — Sanctum token-authenticated endpoints for the
| Python EXE client that runs on employee machines.
|
*/

// Public: Agent authentication (get Sanctum token)
Route::post('/agent/login', [AgentAuthController::class, 'login']);

// Protected: Requires valid Sanctum token
Route::middleware('auth:sanctum')->group(function () {

    // Current user info
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Agent logout
    Route::post('/agent/logout', [AgentAuthController::class, 'logout']);

    // Agent routes (desktop client)
    Route::prefix('agent')->group(function () {
        // Location & Face verification
        Route::post('/verify-location', [LocationController::class, 'verify_location']);
        Route::post('/verify-face', [FaceVerificationController::class, 'verify_face']);

        // Work sessions
        Route::get('/working-hours', [SessionController::class, 'getWorkingHours']);
        Route::post('/session/start', [SessionController::class, 'startSession']);
        Route::post('/session/end', [SessionController::class, 'endSession']);
        Route::post('/session/heartbeat', [SessionController::class, 'heartbeat']);

        // Activity uploads
        Route::post('/screenshots', [ActivityController::class, 'uploadScreenshot']);
        Route::post('/webcam-capture', [ActivityController::class, 'uploadWebcamCapture']);
        Route::post('/activity-summary', [ActivityController::class, 'submitActivitySummary']);
    });

    // Admin routes (web dashboard API calls)
    Route::prefix('admin')->group(function () {
        Route::get('/employee-activity', [ActivityController::class, 'getEmployeeActivity']);
    });
});
