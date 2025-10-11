<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SampleMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        return $this->subject($this->data['subject'])
                    ->view('email-templates.suspension') // Blade template
                    ->with([
                        'employee' => $this->data['employee'],
                        'userType' => $this->data['userType'],
                    ]);
    }
}
