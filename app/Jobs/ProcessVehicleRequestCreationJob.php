<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;//updated by Mugesh.B
use Modules\B2B\Entities\B2BRider;
use Modules\B2B\Http\Controllers\B2BVehicleController;
use App\Mail\B2BVehicleRequestMail;//updated by Mugesh.B

class ProcessVehicleRequestCreationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $admins;
    protected $agents;
    protected $customerEmail;
    protected $customerRelationEmail;
    protected $vehicleRequest;
    protected $rider;
    protected $user;
    protected $riderId;
    protected $requestId;
    protected $agentArr;
    protected $acTypeName;

    /**
     * Create a new job instance.
     */
    public function __construct(
        $admins,
        $agents,
        $customerEmail,
        $customerRelationEmail,
        $vehicleRequest,
        $rider,
        $user,
        $riderId,
        $requestId,
        $agentArr = [],
        $acTypeName = null
    ) {
        $this->admins = $admins;
        $this->agents = $agents;
        $this->customerEmail = $customerEmail;
        $this->customerRelationEmail = $customerRelationEmail;
        $this->vehicleRequest = $vehicleRequest;
        $this->rider = $rider;
        $this->user = $user;
        $this->riderId = $riderId;
        $this->requestId = $requestId;
        $this->agentArr = $agentArr;
        $this->acTypeName = $acTypeName;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $controller = new B2BVehicleController();

        try {
            // --- Send Admin mails ---
            foreach ($this->admins as $adminEmail) {
                Mail::to($adminEmail)
                    ->send(new B2BVehicleRequestMail(
                        $this->vehicleRequest,
                        $this->rider,
                        $this->user,
                        'admin'
                    ));
            }

            // --- Send Agent mails ---
            foreach ($this->agents as $agentEmail) {
                Mail::to($agentEmail)
                    ->send(new B2BVehicleRequestMail(
                        $this->vehicleRequest,
                        $this->rider,
                        $this->user,
                        'agent'
                    ));
            }

            // --- Send Customer mail ---
            if ($this->customerEmail) {
                Mail::to($this->customerEmail)
                    ->cc($this->customerRelationEmail ?? null)
                    ->send(new B2BVehicleRequestMail(
                        $this->vehicleRequest,
                        $this->rider,
                        $this->user,
                        'user'
                    ));
            }

            // --- WhatsApp + Notification ---
            // $controller->AutoSendQrCodeWhatsApp($this->riderId);
            $controller->pushRiderNotificationSent($this->rider, $this->requestId);
            $controller->AutoAgentSendQrCodeWhatsApp($this->agentArr, $this->riderId);
            $controller->pushAgentNotificationSent($this->agentArr, $this->requestId, $this->acTypeName);
            

            \Log::info('ProcessVehicleRequestCreationJob completed successfully', [
                'riderId' => $this->riderId,
                'requestId' => $this->requestId,
            ]);
        } catch (\Throwable $e) {
            \Log::error('ProcessVehicleRequestCreationJob failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'riderId' => $this->riderId,
                'requestId' => $this->requestId,
            ]);
        }
    }
}
