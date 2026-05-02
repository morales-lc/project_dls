<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class LiraDecision extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $lira;
    public $decision; // accepted, rejected, or canceled
    public $reason; // optional reason on rejection/cancellation

    public function __construct($lira, $decision, $reason = null)
    {
        $this->lira = $lira;
        $this->decision = $decision;
        $this->reason = $reason;
    }

    public function build()
    {
        $subject = match ($this->decision) {
            'accepted' => 'Your LiRA request was accepted',
            'canceled' => 'Your LiRA request was canceled',
            default => 'Your LiRA request was rejected',
        };

        return $this->subject($subject)->view('emails.lira_decision');
    }
}
