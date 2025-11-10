<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class B2BVehicleRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $vehicleRequest;
    public $rider;
    public $user;
    public $mail_type;

    public function __construct($vehicleRequest, $rider, $user, $mail_type = 'user')
    {
        $this->vehicleRequest = $vehicleRequest;
        $this->rider          = $rider;
        $this->user           = $user;
        $this->mail_type      = $mail_type;
    }

    public function build()
    {
        return $this->subject('New Vehicle Request Created')
                    ->view('email-templates.vehicle_request')
                    ->with([
                        'vehicleRequest' => $this->vehicleRequest,
                        'rider'          => $this->rider,
                        'user'           => $this->user,
                        'mail_type'      => $this->mail_type,
                    ]);
    }
}
