<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LiraSubmitted extends Mailable
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
