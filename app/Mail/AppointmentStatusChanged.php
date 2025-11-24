<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public $appointment;
    public $action;

    public function __construct(Appointment $appointment, string $action)
    {
        $this->appointment = $appointment;
        $this->action = $action;
    }

    public function envelope()
    {
        $subject = $this->action === 'aceptada' 
            ? 'Cita Confirmada - Cirugía General'
            : 'Cita Rechazada - Cirugía General';

        return new Envelope(
            subject: $subject,
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.appointment-status-changed',
        );
    }
}