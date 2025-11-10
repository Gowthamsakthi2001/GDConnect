<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\B2B\Http\Controllers\Api\V1\B2BAgent\B2BAgentController;

class SendVehicleAllNotificationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $rider, $user, $requestId, $vehicle_no, $rider_name, $rider_id, $vehicle_id;

    public function __construct($rider, $user, $requestId, $vehicle_no, $rider_name, $rider_id, $vehicle_id)
    {
        $this->rider = $rider;
        $this->user = $user;
        $this->requestId = $requestId;
        $this->vehicle_no = $vehicle_no;
        $this->rider_name = $rider_name;
        $this->rider_id = $rider_id;
        $this->vehicle_id = $vehicle_id;
    }

    public function handle()
    {
        $controller = app(B2BAgentController::class);

        Log::info('🚀 [SendVehicleAllNotificationsJob] Started', [
            'request_id' => $this->requestId,
            'rider_id' => $this->rider_id,
            'vehicle_no' => $this->vehicle_no
        ]);

        try {
            // 1️⃣ Rider Push Notification
            Log::info('📩 Sending Rider Push Notification...', ['request_id' => $this->requestId]);
            $controller->pushRiderVehicleStatusNotification(
                $this->rider,
                $this->requestId,
                $this->vehicle_no,
                'rider_vehicle_assign_notify'
            );
            Log::info('✅ Rider Push Notification Sent');

            // 2️⃣ Agent Push Notification
            Log::info('📩 Sending Agent Push Notification...', ['request_id' => $this->requestId]);
            $controller->pushAgentVehicleStatusNotification(
                $this->user,
                $this->requestId,
                $this->vehicle_no,
                'agent_vehicle_assign_notify',
                $this->rider_name
            );
            Log::info('✅ Agent Push Notification Sent');

            // 3️⃣ Email Notification
            Log::info('📧 Sending Email Notification...', ['request_id' => $this->requestId]);
            $controller->AutoSendAssignVehicleEmail(
                $this->user,
                $this->requestId,
                $this->rider_id,
                $this->vehicle_id,
                'agent_vehicle_assign_email_notify'
            );
            Log::info('✅ Email Notification Sent');

            // 4️⃣ WhatsApp Notification
            Log::info('💬 Sending WhatsApp Notification...', ['request_id' => $this->requestId]);
            $controller->AutoSendAssignVehicleWhatsApp(
                $this->user,
                $this->requestId,
                $this->rider_id,
                $this->vehicle_id,
                'agent_vehicle_assign_notify'
            );
            Log::info('✅ WhatsApp Notification Sent');

            Log::info('🎯 [SendVehicleAllNotificationsJob] Completed Successfully', [
                'request_id' => $this->requestId
            ]);

        } catch (\Throwable $e) {
            Log::error('❌ [SendVehicleAllNotificationsJob] Failed', [
                'request_id' => $this->requestId,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
