<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RiderRegisterationMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $riderdata;

    /**
     * Create a new message instance.
     */
    public function __construct($riderdata)
    {
        $this->riderdata = $riderdata;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject(__('Registration successful'))
                    ->view('email-templates.rider_registeration_email')
                    ->with([
                        'riderdata' => $this->riderdata,
                    ]);
    }

}
