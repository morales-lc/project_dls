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
    public $decision; // accepted or rejected
    public $reason; // optional reason on rejection

    public function __construct($lira, $decision, $reason = null)
    {
        $this->lira = $lira;
        $this->decision = $decision;
        $this->reason = $reason;
    }

    public function build()
    {
        $subject = $this->decision === 'accepted' ? 'Your LiRA request was accepted' : 'Your LiRA request was rejected';
        return $this->subject($subject)->view('emails.lira_decision');
    }
}
