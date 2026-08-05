<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\PermanentAssignmentController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\EventController;

// ── Dashboard ────────────────────────────────────────────────────
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// ── Inventory ────────────────────────────────────────────────────
Route::resource('devices', DeviceController::class)->only(['index', 'store', 'update']);

// ── Catalogs ─────────────────────────────────────────────────────
// Import routes BEFORE resource so they don't conflict with {staff} param
Route::post('staff/import/preview',  [StaffController::class, 'importPreview'])->name('staff.import.preview');
Route::post('staff/import/confirm',  [StaffController::class, 'importConfirm'])->name('staff.import.confirm');
Route::resource('staff',     StaffController::class)->only(['index', 'store', 'update', 'destroy']);
Route::resource('locations', LocationController::class)->only(['index', 'store', 'update', 'destroy']);
Route::resource('events',    EventController::class)->only(['index', 'store', 'update', 'destroy']);

// ── Exacer Assignments (Vales) ────────────────────────────────────
Route::resource('assignments', AssignmentController::class)->only(['index', 'create', 'store', 'show']);

// Liberation (palomita)
Route::post('assignments/items/{item}/toggle-liberation', [AssignmentController::class, 'toggleLiberation'])
     ->name('assignments.items.toggle-liberation');

// PDF vale de resguardo (Exacer)
Route::get('assignments/{assignment}/pdf', [AssignmentController::class, 'downloadPdf'])
     ->name('assignments.pdf');

// Agregar más dispositivos a un vale existente
Route::post('assignments/{assignment}/add-devices', [AssignmentController::class, 'addDevices'])
     ->name('assignments.add-devices');

// ── Permanent Assignments (Jefes) ─────────────────────────────────
Route::resource('permanent', PermanentAssignmentController::class)
     ->only(['index', 'create', 'store', 'show']);

// Liberar dispositivo de asignación permanente
Route::post('permanent/{permanent}/release', [PermanentAssignmentController::class, 'release'])
     ->name('permanent.release');

// PDF de asignación permanente
Route::get('permanent/{permanent}/pdf', [PermanentAssignmentController::class, 'pdf'])
     ->name('permanent.pdf');