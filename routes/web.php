<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DashboardController;

// ========================================
// RUTAS PÚBLICAS (SIN AUTENTICACIÓN)
// ========================================
Route::get('/', [PublicController::class, 'index'])->name('public.index');
Route::get('/doctor/{doctor}', [PublicController::class, 'showDoctor'])->name('public.doctors.show');
Route::get('/appointments/new', [PublicController::class, 'newAppointment'])->name('public.appointments.new');
Route::post('/appointments', [AppointmentController::class, 'store'])->name('public.appointments.store');

// ========================================
// RUTAS PROTEGIDAS (CON AUTENTICACIÓN)
// ========================================
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Calendario
    Route::get('/calendar', [DashboardController::class, 'calendar'])->name('calendar');
    
    // Listado de citas (para "Todas las Citas")
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    
    // Ver detalle de una cita
    Route::get('/appointments/{appointment}', [AppointmentController::class, 'show'])->name('appointments.show');
    
    // Acciones sobre citas
    Route::post('/appointments/{appointment}/accept', [AppointmentController::class, 'accept'])->name('appointments.accept');
    Route::post('/appointments/{appointment}/reject', [AppointmentController::class, 'reject'])->name('appointments.reject');
    Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');
    
    // CRUD de Médicos
    Route::resource('doctors', DoctorController::class);
});

// Fallback para /dashboard sin prefijo (redirect a /admin/dashboard)
Route::get('/dashboard', function () {
    return redirect('/admin/dashboard');
})->middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->name('dashboard');

// Fallback para /home (Jetstream)
Route::get('/home', function () {
    return redirect('/admin/dashboard');
})->middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified']);