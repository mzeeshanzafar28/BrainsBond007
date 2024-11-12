<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PropController;

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
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::post('/add-employee', [EmployeeController::class, 'add_employee']);
    Route::post('/update-employee', [EmployeeController::class, 'update_employee']);
    Route::post('/delete-employee', [EmployeeController::class, 'delete_employee']);
    Route::post('/get-employees', [EmployeeController::class, 'get_employees']);

    Route::post('/add-prop', [PropController::class, 'add_prop']);
    Route::post('/update-prop', [PropController::class, 'update_prop']);
    Route::post('/delete-prop', [PropController::class, 'delete_prop']);
    Route::post('/get-props', [PropController::class, 'get_props']);

    Route::post('/generate-exe', [PropController::class, 'generate_exe']);



});
