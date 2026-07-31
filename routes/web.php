<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\EventController;

// Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Inventory Routes
Route::resource('devices', DeviceController::class)->only(['index', 'store', 'update']);

// Staff / Personal
Route::resource('staff', StaffController::class)->only(['index', 'store', 'update', 'destroy']);

// Locations / Sedes
Route::resource('locations', LocationController::class)->only(['index', 'store', 'update', 'destroy']);

// Events / Exacers
Route::resource('events', EventController::class)->only(['index', 'store', 'update', 'destroy']);

// Assignments and Resguardo Routes
Route::resource('assignments', AssignmentController::class)->only(['index', 'create', 'store', 'show']);

// API: Palomita Liberation Checklist
Route::post('assignments/items/{item}/toggle-liberation', [AssignmentController::class, 'toggleLiberation'])
    ->name('assignments.items.toggle-liberation');

// Printable PDF Route
Route::get('assignments/{assignment}/pdf', [AssignmentController::class, 'downloadPdf'])
    ->name('assignments.pdf');