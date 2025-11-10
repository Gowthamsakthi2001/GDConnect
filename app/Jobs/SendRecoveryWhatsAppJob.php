<?php

namespace App\Jobs;

use App\Helpers\RecoveryNotifyHandler; 
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendRecoveryWhatsAppJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $requestID;
    protected $rider_id;
    protected $vehicle_id;
    protected $recoveryInfo;
    protected $tc_create_type;

    /**
     * Create a new job instance.
     */
    public function __construct($requestID, $rider_id, $vehicle_id, $recoveryInfo, $tc_create_type)
    {
        $this->requestID = $requestID;
        $this->rider_id = $rider_id;
        $this->vehicle_id = $vehicle_id;
        $this->recoveryInfo = $recoveryInfo;
        $this->tc_create_type = $tc_create_type;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        // Call your existing static function
        RecoveryNotifyHandler::AutoSendRecoveryRequestWhatsApp(
            $this->requestID,
            $this->rider_id,
            $this->vehicle_id,
            $this->recoveryInfo,
            $this->tc_create_type
        );
    }
}
