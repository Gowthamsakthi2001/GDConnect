<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Helpers\ServiceTicketHandler;

class ProcessB2BServiceRequestCreationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $ticketId;
    protected $riderData;
    protected $vehicleId;
    protected $repairInfo;
    protected $tcCreateType;
    protected $customerName;

    /**
     * Create a new job instance.
     */
    public function __construct($ticketId, $riderData, $vehicleId, $repairInfo, $tcCreateType, $customerName)
    {
        $this->ticketId     = $ticketId;
        $this->riderData    = $riderData;
        $this->vehicleId    = $vehicleId;
        $this->repairInfo   = $repairInfo;
        $this->tcCreateType = $tcCreateType;
        $this->customerName = $customerName;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            \Log::info('🚀 ProcessB2BServiceRequestCreationJob started', [
                'ticket_id'   => $this->ticketId,
                'rider_id'    => $this->riderData->id ?? null,
                'vehicle_id'  => $this->vehicleId,
                'info'        => $this->repairInfo,
            ]);

            // Push notification
            ServiceTicketHandler::pushRiderServiceTicketNotification(
                $this->riderData,
                $this->ticketId,
                $this->repairInfo,
                'create_by_customer',
                $this->customerName
            );

            // Send Email
            ServiceTicketHandler::AutoSendServiceRequestEmail(
                $this->ticketId,
                $this->riderData->id,
                $this->vehicleId,
                $this->repairInfo,
                'customer_create_ticket',
                'create_by_customer'
            );
            

            // Send WhatsApp
            ServiceTicketHandler::AutoSendServiceRequestWhatsApp(
                $this->ticketId,
                $this->riderData->id,
                $this->vehicleId,
                $this->repairInfo,
                'customer_create_ticket',
                'create_by_customer'
            );

            \Log::info('ProcessB2BServiceRequestCreationJob completed successfully', [
                'ticket_id' => $this->ticketId,
                'rider_id'  => $this->riderData->id ?? null,
            ]);

        } catch (\Throwable $e) {
            \Log::error('❌ ProcessB2BServiceRequestCreationJob failed', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'ticket_id' => $this->ticketId,
                'rider_id'  => $this->riderData->id ?? null,
            ]);
            throw $e; // Let queue retry it
        }
    }
}
