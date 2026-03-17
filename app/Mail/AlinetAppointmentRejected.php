<?php

namespace App\Mail;

use App\Models\AlinetAppointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AlinetAppointmentRejected extends Mailable
{
    use Queueable, SerializesModels;

    public AlinetAppointment $appointment;
    public ?string $reason;

    public function __construct(AlinetAppointment $appointment, ?string $reason = null)
    {
        $this->appointment = $appointment;
        $this->reason = $reason;
    }

    public function build()
    {
        return $this
            ->subject('ALINET Appointment Rejected')
            ->view('mail.alinet.rejected')
            ->with([
                'appointment' => $this->appointment,
                'reason' => $this->reason,
            ]);
    }
}


