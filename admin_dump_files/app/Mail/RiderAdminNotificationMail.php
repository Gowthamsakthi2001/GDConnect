<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RiderAdminNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $riderdata;

    public function __construct($riderdata)
    {
        $this->riderdata = $riderdata;
    }

    public function build()
    {
        return $this->subject('New Rider Registration')
                    ->view('email-templates.rider_admin_notification')
                    ->with([
                        'riderdata' => $this->riderdata,
                    ]);
    }
}
