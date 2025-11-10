<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class B2BTermsAndCondition extends Mailable
{
    use Queueable, SerializesModels;

    public $rider;
    public $user;
    public $mail_type;

    public function __construct($rider, $user, $mail_type = 'user')
    {
        $this->rider = $rider;
        $this->user = $user;
        $this->mail_type = $mail_type;
    }

    public function build()
    {
        return $this->subject('Terms & Conditions Accepted')
                    ->view('email-templates.terms_condition_email')
                    ->with([
                        'rider' => $this->rider,
                        'user' => $this->user,
                        'mail_type' => $this->mail_type,
                    ]);
    }
}
