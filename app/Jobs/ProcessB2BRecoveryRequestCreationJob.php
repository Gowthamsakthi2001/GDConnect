<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Helpers\RecoveryNotifyHandler;

class ProcessB2BRecoveryRequestCreationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $requestId;
    protected $riderId;
    protected $vehicleId;
    protected $recoveryInfo;
    protected $tcCreateType;

    /**
     * Create a new job instance.
     */
    public function __construct($requestId, $riderId, $vehicleId, $recoveryInfo, $tcCreateType)
    {
        $this->requestId      = $requestId;
        $this->riderId        = $riderId;
        $this->vehicleId      = $vehicleId;
        $this->recoveryInfo   = $recoveryInfo;
        $this->tcCreateType   = $tcCreateType;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            \Log::info('🚀 ProcessB2BRecoveryRequestCreationJob started', [
                'request_id' => $this->requestId,
                'rider_id'   => $this->riderId,
                'vehicle_id' => $this->vehicleId,
                'info'       => $this->recoveryInfo,
            ]);

            // Call email and WhatsApp handlers
            RecoveryNotifyHandler::AutoSendRecoveryRequestEmail(
                $this->requestId,
                $this->riderId,
                $this->vehicleId,
                $this->recoveryInfo,
                $this->tcCreateType
            );
            

            RecoveryNotifyHandler::AutoSendRecoveryRequestWhatsApp(
                $this->requestId,
                $this->riderId,
                $this->vehicleId,
                $this->recoveryInfo,
                $this->tcCreateType
            );
            

            \Log::info('ProcessB2BRecoveryRequestCreationJob completed successfully', [
                'request_id' => $this->requestId,
                'rider_id'   => $this->riderId,
            ]);

        } catch (\Throwable $e) {
            \Log::error('❌ ProcessB2BRecoveryRequestCreationJob failed', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'request_id' => $this->requestId,
                'rider_id'   => $this->riderId,
            ]);
            throw $e; // ensures retry/backoff works
        }
    }
}

