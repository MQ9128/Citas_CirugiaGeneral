<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class DoctorController extends Controller
{
    public function index()
    {
        $doctors = Doctor::with('schedules')->paginate(10);

        return Inertia::render('Admin/Doctors/Index', [
            'doctors' => $doctors
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Doctors/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'specialty' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'email' => 'required|email|unique:doctors,email',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'schedules' => 'required|array|min:1',
            'schedules.*.day_of_week' => 'required|integer|between:0,6',
            'schedules.*.start_time' => 'required|date_format:H:i',
            'schedules.*.end_time' => 'required|date_format:H:i|after:schedules.*.start_time',
        ]);

        $doctor = Doctor::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'specialty' => $validated['specialty'],
            'bio' => $validated['bio'] ?? null,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Crear horarios
        foreach ($validated['schedules'] as $schedule) {
            DoctorSchedule::create([
                'doctor_id' => $doctor->id,
                'day_of_week' => $schedule['day_of_week'],
                'start_time' => $schedule['start_time'],
                'end_time' => $schedule['end_time'],
            ]);
        }

        return redirect()->route('doctors.index')
            ->with('success', 'Médico creado exitosamente.');
    }

    public function show(Doctor $doctor)
    {
        $doctor->load('schedules', 'appointments');

        return Inertia::render('Admin/Doctors/Show', [
            'doctor' => $doctor
        ]);
    }

    public function edit(Doctor $doctor)
    {
        $doctor->load('schedules');

        return Inertia::render('Admin/Doctors/Edit', [
            'doctor' => $doctor
        ]);
    }

    public function update(Request $request, Doctor $doctor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'specialty' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'email' => 'required|email|unique:doctors,email,' . $doctor->id,
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'schedules' => 'required|array|min:1',
            'schedules.*.day_of_week' => 'required|integer|between:0,6',
            'schedules.*.start_time' => 'required|date_format:H:i',
            'schedules.*.end_time' => 'required|date_format:H:i|after:schedules.*.start_time',
        ]);

        $doctor->update([
            'name' => $validated['name'],
            'specialty' => $validated['specialty'],
            'bio' => $validated['bio'] ?? null,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Actualizar horarios (eliminar y recrear)
        $doctor->schedules()->delete();
        foreach ($validated['schedules'] as $schedule) {
            DoctorSchedule::create([
                'doctor_id' => $doctor->id,
                'day_of_week' => $schedule['day_of_week'],
                'start_time' => $schedule['start_time'],
                'end_time' => $schedule['end_time'],
            ]);
        }

        return redirect()->route('doctors.index')
            ->with('success', 'Médico actualizado exitosamente.');
    }

    public function destroy(Doctor $doctor)
    {
        $doctor->delete();

        return redirect()->route('doctors.index')
            ->with('success', 'Médico eliminado exitosamente.');
    }
}