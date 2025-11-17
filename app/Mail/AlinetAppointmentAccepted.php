<?php

namespace App\Mail;

use App\Models\AlinetAppointment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;

class AlinetAppointmentAccepted extends Mailable
{
    use Queueable, SerializesModels;

    public AlinetAppointment $appointment;
    public ?\App\Models\User $guestUser;

    public function __construct(AlinetAppointment $appointment, ?\App\Models\User $guestUser = null)
    {
        $this->appointment = $appointment;
        $this->guestUser = $guestUser;
    }

    public function build()
    {
        // Use the newly created guest user if provided, otherwise use existing fallback logic
        $guestUser = $this->guestUser ?? User::query()
            ->where('role', 'guest')
            ->orderBy('id')
            ->first();

        $guestEmail = $guestUser?->email ?? config('services.alinet.guest_email', 'guest@example.com');
        // Prefer encrypted plaintext from DB; fallback to config if not set
        $guestPassword = null;
        if ($guestUser && !empty($guestUser->guest_plain_password)) {
            try {
                $guestPassword = Crypt::decryptString($guestUser->guest_plain_password);
            } catch (\Throwable $e) {
                // ignore and fallback
            }
        }
        if (!$guestPassword) {
            $guestPassword = config('services.alinet.guest_password', 'guest12345');
        }

        // Get expiration date if guest user exists
        $expiresAt = $guestUser?->guest_expires_at;

        return $this
            ->subject('ALINET Appointment Accepted')
            ->view('mail.alinet.accepted')
            ->with([
                'appointment' => $this->appointment,
                'guestEmail' => $guestEmail,
                'guestPassword' => $guestPassword,
                'expiresAt' => $expiresAt,
            ]);
    }
}


