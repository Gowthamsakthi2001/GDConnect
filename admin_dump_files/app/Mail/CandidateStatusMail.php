<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\Deliveryman\Entities\Deliveryman;

class CandidateStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $application;
    public $status;
    public $remarks;
    public $actionBy;

    public function __construct(
        Deliveryman $application,
        string $status,
        ?string $remarks,
        string $actionBy
    ) {
        $this->application = $application;
        $this->status = $status;
        $this->remarks = $remarks;
        $this->actionBy = $actionBy;
    
        
    }

    public function build()
    {
    return $this->subject($this->getSubject())
        ->view('email-templates.candidate_status')
        ->with([
            'application' => $this->application,
            'status' => $this->status,
            'remarks' => $this->remarks,
            'actionBy' => $this->actionBy,
        ]);
    }
    

    protected function getSubject()
    {
        return match($this->status) {
            'approve_sent_to_hr02' => 'Application Approved',
            'sent_to_bgv' => 'Application Sent for Verification',
            'on_hold' => 'Application On Hold',
            'rejected' => 'Application Status Update',
            default => 'Application Status Notification'
        };
    }
}