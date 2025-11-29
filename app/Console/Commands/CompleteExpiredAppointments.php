<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CompleteExpiredAppointments extends Command
{
    protected $signature = 'appointments:complete-expired';
    protected $description = 'Marca como completadas las citas confirmadas que ya pasaron';

    public function handle()
    {
        $now = Carbon::now();

        $completed = Appointment::where('status', 'confirmada')
            ->where('appointment_date', '<', $now)
            ->update(['status' => 'completada']);

        $this->info("Se marcaron {$completed} citas como completadas.");

        return Command::SUCCESS;
    }
}