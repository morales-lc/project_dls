<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LiraResponse extends Mailable
{
    use Queueable, SerializesModels;

    public $lira;
    public $subjectLine;
    public $body; // response body

    public function __construct($lira, string $subjectLine, string $body)
    {
        $this->lira = $lira;
        $this->subjectLine = $subjectLine;
        $this->body = $body;
    }

    public function build()
    {
        return $this->subject($this->subjectLine)
            ->view('emails.lira_response');
    }
}
