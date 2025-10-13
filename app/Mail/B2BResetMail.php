<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class B2BResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $resetLink;
    public $agentName;

  
    public function __construct($resetLink, $agentName = null)
    {
        $this->resetLink = $resetLink;
        $this->agentName = $agentName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Password Reset Request - B2B Customer Account')
                    ->view('email-templates.b2b_login_password_reset')
                     ->with([
                    'resetLink' => $this->resetLink,
                    'agentName' => $this->agentName
                ]);
    }
}