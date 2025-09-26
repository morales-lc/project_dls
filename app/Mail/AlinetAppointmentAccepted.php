<?php

namespace App\Mail;

use App\Models\AlinetAppointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AlinetAppointmentAccepted extends Mailable
{
    use Queueable, SerializesModels;

    public AlinetAppointment $appointment;

    public function __construct(AlinetAppointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function build()
    {
        return $this
            ->subject('ALINET Appointment Accepted')
            ->view('mail.alinet.accepted')
            ->with([
                'appointment' => $this->appointment,
            ]);
    }
}


