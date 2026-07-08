<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PropController;
use App\Http\Controllers\WebSocketController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\FaceVerificationController;


Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Employees
    Route::get('/employees', function () {
        return Inertia::render('Employees/Index');
    })->name('employees.index');

    Route::get('/employees/create', function () {
        return Inertia::render('Employees/Create');
    })->name('employees.create');

    Route::get('/employees/{id}', function ($id) {
        return Inertia::render('Employees/Show', ['employeeId' => $id]);
    })->name('employees.show');

    // Monitoring
    Route::get('/monitoring', function () {
        return Inertia::render('Monitoring/Live');
    })->name('monitoring.live');

    // Attendance
    Route::get('/attendance', function () {
        return Inertia::render('Attendance/Index');
    })->name('attendance.index');

    // Locations
    Route::get('/locations', function () {
        return Inertia::render('Locations/Index');
    })->name('locations.index');

    // Settings
    Route::get('/settings', function () {
        return Inertia::render('Settings/Index');
    })->name('settings.index');

    // Employee API (used by admin dashboard via Inertia)
    Route::post('/add-employee', [EmployeeController::class, 'add_employee']);
    Route::post('/update-employee', [EmployeeController::class, 'update_employee']);
    Route::post('/delete-employee', [EmployeeController::class, 'delete_employee']);
    Route::post('/get-employees', [EmployeeController::class, 'get_employees']);

    // Props API
    Route::post('/add-prop', [PropController::class, 'add_prop']);
    Route::post('/update-prop', [PropController::class, 'update_prop']);
    Route::post('/delete-prop', [PropController::class, 'delete_prop']);
    Route::post('/get-props', [PropController::class, 'get_props']);
    Route::post('/generate-exe', [PropController::class, 'generate_exe']);

    // WebSocket controls
    Route::post('/start-screencast', [WebSocketController::class, 'start_screencast']);
    Route::post('/seize-system', [WebSocketController::class, 'seize_system']);
    Route::post('/get-screenshots', [WebSocketController::class, 'get_screenshots']);
});

// Desktop agent verification (also available via API routes)
Route::post('/verify-location', [LocationController::class, 'verify_location']);
Route::post('/verify-face', [FaceVerificationController::class, 'verify_face']);