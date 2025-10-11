<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class QualityCheckMail extends Mailable
{
    use Queueable, SerializesModels;

    public $qcData;

    public function __construct($qcData)
    {
        $this->qcData = $qcData;
    }

    public function build()
    {
        return $this->subject('Quality Check Created Successfully')
                    ->view('email-templates.quality_check_email')
                    ->with(['qcData' => $this->qcData]);
    }
}
