<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DashboardController;

// Configurar redirección después del login
Route::get('/home', function () {
    return redirect('/admin/dashboard');
});

// ========================================
// RUTAS PÚBLICAS (SIN AUTENTICACIÓN)
// ========================================
Route::get('/', [PublicController::class, 'index'])->name('public.index');

Route::get('/doctor/{doctor}', [PublicController::class, 'showDoctor'])->name('public.doctors.show');

Route::get('/appointments/new', [PublicController::class, 'newAppointment'])->name('public.appointments.new');

// IMPORTANTE: Esta ruta debe estar FUERA del grupo admin
Route::post('/appointments', [AppointmentController::class, 'store'])->name('public.appointments.store');
// ========================================
// RUTAS PROTEGIDAS (CON AUTENTICACIÓN)
// Panel administrativo con prefijo /admin
// ========================================
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->prefix('admin')->group(function () {
    
    // Dashboard principal
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Calendario semanal
    Route::get('/calendar', [DashboardController::class, 'calendar'])->name('calendar');
    
    // CRUD de Médicos (ahora en /admin/doctors)
    Route::resource('doctors', DoctorController::class);
    
    // CRUD de Citas (ahora en /admin/appointments)
    Route::resource('appointments', AppointmentController::class);
    
    // Acciones especiales de citas
    Route::post('/appointments/{appointment}/accept', [AppointmentController::class, 'accept'])
        ->name('appointments.accept');
    
    Route::post('/appointments/{appointment}/reject', [AppointmentController::class, 'reject'])
        ->name('appointments.reject');
});