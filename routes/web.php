<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DashboardController; // Import the controller

// Home Route
Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// Dashboard Route
Route::get('dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Include other route files
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
