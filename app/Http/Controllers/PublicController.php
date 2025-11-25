<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class PublicController extends Controller
{
    public function index()
    {
        $doctors = Doctor::where('is_active', true)
            ->with('schedules')
            ->get();

        return Inertia::render('Public/Index', [
            'doctors' => $doctors
        ]);
    }

    public function showDoctor(Doctor $doctor)
    {
        $doctor->load('schedules');

        // Obtener espacios disponibles de la próxima semana
        $availableSlots = $this->getAvailableSlots($doctor);

        return Inertia::render('Public/DoctorProfile', [
            'doctor' => $doctor,
            'availableSlots' => $availableSlots
        ]);
    }

    public function newAppointment(Request $request)
    {
        $doctor = Doctor::where('slug', $request->doctor)->firstOrFail();
        $startDateTime = $request->start;

        return Inertia::render('Public/NewAppointment', [
            'doctor' => $doctor,
            'appointmentDate' => $startDateTime
        ]);
    }

    private function getAvailableSlots(Doctor $doctor, $days = 7)
    {
        $slots = [];
        $now = Carbon::now();
        $duration = (int) config('app.appointment_duration', 20);

        for ($i = 0; $i < $days; $i++) {
            $date = $now->copy()->addDays($i);
            $dayOfWeek = $date->dayOfWeek;

            $schedules = $doctor->schedules()
                ->where('day_of_week', $dayOfWeek)
                ->get();

            foreach ($schedules as $schedule) {
                $startTime = Carbon::createFromFormat('Y-m-d H:i:s', $date->format('Y-m-d') . ' ' . $schedule->start_time);
                $endTime = Carbon::createFromFormat('Y-m-d H:i:s', $date->format('Y-m-d') . ' ' . $schedule->end_time);

                $currentSlot = $startTime->copy();

                while ($currentSlot->lessThan($endTime)) {
                    if ($currentSlot->greaterThan($now)) {
                        $hasAppointment = Appointment::where('doctor_id', $doctor->id)
                            ->whereBetween('appointment_date', [
                                $currentSlot->copy()->subMinute(),
                                $currentSlot->copy()->addMinute()
                            ])
                            ->whereIn('status', ['pendiente', 'confirmada'])
                            ->exists();

                        if (!$hasAppointment) {
                            $slots[] = [
                                // Enviar en formato ISO para que JavaScript lo pueda parsear
                                'datetime' => $currentSlot->toIso8601String(),
                                'formatted' => $currentSlot->locale('es')->isoFormat('ddd DD/MM/YYYY - hh:mm A')
                            ];
                        }
                    }
                    $currentSlot->addMinutes($duration);
                }
            }
        }

        return $slots;
    }
}