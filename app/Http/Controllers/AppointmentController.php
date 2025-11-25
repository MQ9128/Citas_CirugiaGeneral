<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Mail\AppointmentCreated;
use App\Mail\AppointmentStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    // Mostrar listado de citas (Panel Admin)
    public function index(Request $request)
    {
        $query = Appointment::with('doctor')
            ->orderBy('appointment_date', 'desc');

        // Filtrar por doctor si se especifica
        if ($request->has('doctor_id') && $request->doctor_id) {
            $query->where('doctor_id', $request->doctor_id);
        }

        // Filtrar por estado
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $appointments = $query->paginate(15);
        $doctors = Doctor::where('is_active', true)->get();

        return Inertia::render('Admin/Appointments/Index', [
            'appointments' => $appointments,
            'doctors' => $doctors,
            'filters' => $request->only(['doctor_id', 'status'])
        ]);
    }

    // Crear cita desde el formulario público
public function store(Request $request)
{
    // Debug: Ver qué datos llegan
    \Log::info('Datos recibidos:', $request->all());

    $validated = $request->validate([
        'doctor_id' => 'required|exists:doctors,id',
        'patient_name' => 'required|string|max:255',
        'patient_email' => 'required|email|max:255',
        'patient_phone' => 'required|string|max:20',
        'reason' => 'nullable|string|max:500',
        'appointment_date' => 'required|date_format:Y-m-d H:i:s', // ← Cambio aquí
    ], [
        'appointment_date.date_format' => 'El formato de la fecha es inválido.',
        'appointment_date.required' => 'La fecha de la cita es obligatoria.',
    ]);

    try {
        $appointmentDate = Carbon::parse($validated['appointment_date']);
        $duration = (int) config('app.appointment_duration', 20);

        // Validar que la fecha sea futura
        if ($appointmentDate->isPast()) {
            return back()->withErrors([
                'appointment_date' => 'No puedes agendar una cita en el pasado.'
            ])->withInput();
        }

        // Resto del código igual...
        $hasConflict = Appointment::where('doctor_id', $validated['doctor_id'])
            ->whereBetween('appointment_date', [
                $appointmentDate->copy()->subMinutes($duration),
                $appointmentDate->copy()->addMinutes($duration)
            ])
            ->whereIn('status', ['pendiente', 'confirmada'])
            ->exists();

        if ($hasConflict) {
            return back()->withErrors([
                'appointment_date' => 'Este horario ya está ocupado. Por favor selecciona otro.'
            ])->withInput();
        }

        $doctor = Doctor::with('schedules')->findOrFail($validated['doctor_id']);
        $dayOfWeek = $appointmentDate->dayOfWeek;
        $timeSlot = $appointmentDate->format('H:i:s');

        $isAvailable = $doctor->schedules()
            ->where('day_of_week', $dayOfWeek)
            ->where('start_time', '<=', $timeSlot)
            ->where('end_time', '>', $timeSlot)
            ->exists();

        if (!$isAvailable) {
            return back()->withErrors([
                'appointment_date' => 'El médico no atiende en este horario.'
            ])->withInput();
        }

        $appointment = Appointment::create([
            'doctor_id' => $validated['doctor_id'],
            'patient_name' => $validated['patient_name'],
            'patient_email' => $validated['patient_email'],
            'patient_phone' => $validated['patient_phone'],
            'reason' => $validated['reason'],
            'appointment_date' => $appointmentDate,
            'duration_minutes' => $duration,
            'status' => 'pendiente',
        ]);

        // Log de éxito
        \Log::info('Cita creada:', ['id' => $appointment->id]);

        try {
            Mail::to($validated['patient_email'])
                ->send(new AppointmentCreated($appointment));
        } catch (\Exception $e) {
            \Log::error('Error enviando correo: ' . $e->getMessage());
        }

        return redirect()->route('public.index')
            ->with('success', '¡Cita agendada exitosamente! Recibirás un correo de confirmación.');

    } catch (\Exception $e) {
        \Log::error('Error al crear cita:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return back()->withErrors([
            'error' => 'Hubo un error al agendar la cita: ' . $e->getMessage()
        ])->withInput();
    }
}

    // Mostrar detalles de una cita
    public function show(Appointment $appointment)
    {
        $appointment->load('doctor');

        return Inertia::render('Admin/Appointments/Show', [
            'appointment' => $appointment
        ]);
    }

    // Aceptar cita
    public function accept(Appointment $appointment)
    {
        if ($appointment->status !== 'pendiente') {
            return back()->withErrors([
                'status' => 'Solo se pueden aceptar citas pendientes.'
            ]);
        }

        $appointment->update(['status' => 'confirmada']);

        // Enviar correo
        try {
            Mail::to($appointment->patient_email)
                ->send(new AppointmentStatusChanged($appointment, 'aceptada'));
        } catch (\Exception $e) {
            \Log::error('Error enviando correo: ' . $e->getMessage());
        }

        return back()->with('success', 'Cita aceptada y correo enviado al paciente.');
    }

    // Rechazar cita
    public function reject(Request $request, Appointment $appointment)
    {
        if ($appointment->status !== 'pendiente') {
            return back()->withErrors([
                'status' => 'Solo se pueden rechazar citas pendientes.'
            ]);
        }

        $appointment->update([
            'status' => 'rechazada',
            'notes' => $request->notes ?? 'Cita rechazada por el administrador.'
        ]);

        // Enviar correo
        try {
            Mail::to($appointment->patient_email)
                ->send(new AppointmentStatusChanged($appointment, 'rechazada'));
        } catch (\Exception $e) {
            \Log::error('Error enviando correo: ' . $e->getMessage());
        }

        return back()->with('success', 'Cita rechazada y correo enviado al paciente.');
    }

    // Cancelar cita
    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return back()->with('success', 'Cita eliminada exitosamente.');
    }
}