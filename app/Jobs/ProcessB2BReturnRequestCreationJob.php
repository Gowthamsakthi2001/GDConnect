<?php

namespace App\Jobs;

use Modules\B2B\Entities\B2BRider;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\B2B\Http\Controllers\B2BVehicleController;

class ProcessB2BReturnRequestCreationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $requestId;
    protected $riderId;
    protected $vehicleId;
    protected $selectReason;
    protected $returnDescription;
    protected $agentArr;

    /**
     * Create a new job instance.
     */
    public function __construct($requestId, $riderId, $vehicleId, $selectReason, $returnDescription, $agentArr = [])
    {
        $this->requestId = $requestId;
        $this->riderId = $riderId;
        $this->vehicleId = $vehicleId;
        $this->selectReason = $selectReason;
        $this->returnDescription = $returnDescription;
        $this->agentArr = $agentArr;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $rider = B2BRider::with(['city', 'zone', 'customerLogin.customer_relation'])
                ->find($this->riderId);

            if (!$rider) {
                \Log::error('ProcessB2BReturnRequestCreationJob: Rider not found', [
                    'riderId' => $this->riderId,
                ]);
                return;
            }

            $controller = new B2BVehicleController();

            // Send emails, WhatsApp & notifications
            $controller->AutoSendReturnRequestEmail(
                $this->requestId,
                $this->riderId,
                $this->vehicleId,
                $this->selectReason,
                $this->returnDescription
            );

            $controller->AutoReturnRequestSendWhatsApp(
                $this->riderId,
                $this->selectReason,
                $this->returnDescription
            );

            $controller->pushRiderReturnRequestNotificationSent(
                $rider,
                $this->requestId,
                $this->selectReason,
                $this->returnDescription
            );

            $controller->pushAgentReturnRequestNotificationSent(
                $this->agentArr,
                $this->requestId,
                $this->selectReason,
                $this->returnDescription
            );

            $controller->AutoAgentReturnRequestSendWhatsApp(
                $this->agentArr,
                $this->riderId,
                $this->selectReason,
                $this->returnDescription
            );
            

            \Log::info('ProcessB2BReturnRequestCreationJob completed successfully', [
                'riderId' => $this->riderId,
                'requestId' => $this->requestId,
            ]);

        } catch (\Throwable $e) {
            \Log::error('ProcessB2BReturnRequestCreationJob failed', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'riderId' => $this->riderId,
                'requestId' => $this->requestId,
            ]);
        }
    }
}
