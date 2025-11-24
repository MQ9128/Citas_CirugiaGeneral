<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = Appointment::with('doctor')
            ->where('appointment_date', '>=', now());

        // Filtrar por médico si se especifica
        if ($request->has('doctor_id') && $request->doctor_id) {
            $query->where('doctor_id', $request->doctor_id);
        }

        $pendingAppointments = (clone $query)
            ->where('status', 'pendiente')
            ->orderBy('appointment_date', 'asc')
            ->limit(10)
            ->get();

        $confirmedAppointments = (clone $query)
            ->where('status', 'confirmada')
            ->orderBy('appointment_date', 'asc')
            ->limit(10)
            ->get();

        $doctors = Doctor::where('is_active', true)->get();

        // Estadísticas
        $stats = [
            'total_pending' => Appointment::where('status', 'pendiente')->count(),
            'total_confirmed' => Appointment::where('status', 'confirmada')->count(),
            'total_rejected' => Appointment::where('status', 'rechazada')->count(),
            'today_appointments' => Appointment::whereDate('appointment_date', today())
                ->whereIn('status', ['pendiente', 'confirmada'])
                ->count(),
        ];

        return Inertia::render('Dashboard', [
            'pendingAppointments' => $pendingAppointments,
            'confirmedAppointments' => $confirmedAppointments,
            'doctors' => $doctors,
            'stats' => $stats,
            'filters' => $request->only(['doctor_id'])
        ]);
    }

    // Calendario semanal
    public function calendar(Request $request)
    {
        $doctorSlug = $request->get('doctor');
        $weekStart = $request->get('week_start', now()->startOfWeek()->toDateString());

        $startDate = Carbon::parse($weekStart)->startOfWeek();
        $endDate = $startDate->copy()->endOfWeek();

        $query = Appointment::with('doctor')
            ->whereBetween('appointment_date', [$startDate, $endDate])
            ->whereIn('status', ['pendiente', 'confirmada']);

        if ($doctorSlug) {
            $doctor = Doctor::where('slug', $doctorSlug)->firstOrFail();
            $query->where('doctor_id', $doctor->id);
        }

        $appointments = $query->orderBy('appointment_date', 'asc')->get();

        $doctors = Doctor::where('is_active', true)->get();

        return Inertia::render('Admin/Calendar', [
            'appointments' => $appointments,
            'doctors' => $doctors,
            'weekStart' => $startDate->toDateString(),
            'weekEnd' => $endDate->toDateString(),
            'selectedDoctor' => $doctorSlug ?? null
        ]);
    }
}