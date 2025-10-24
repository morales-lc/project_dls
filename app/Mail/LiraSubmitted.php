<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class LiraSubmitted extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $lira;

    public function __construct($lira)
    {
        $this->lira = $lira;
    }

    public function build()
    {
        return $this->subject('New LiRA request submitted')->view('emails.lira_submitted');
    }
}
