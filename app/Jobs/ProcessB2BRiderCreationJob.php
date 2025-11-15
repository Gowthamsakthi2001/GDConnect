<?php

namespace App\Jobs;

use Modules\B2B\Entities\B2BRider;//updated by Mugesh.B
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\B2B\Http\Controllers\B2BVehicleController; 

class ProcessB2BRiderCreationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $riderId;
    protected $submissionType;
    

    /**
     * Create a new job instance.
     */
    public function __construct($riderId, $submissionType)
    {
        $this->riderId = $riderId;
        $this->submissionType = $submissionType;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $riderData = B2BRider::with(['city', 'zone', 'customerLogin.customer_relation'])
            ->find($this->riderId);

        if (!$riderData) {
            \Log::error('ProcessRiderCreationJob: Rider not found', ['riderId' => $this->riderId]);
            return;
        }

        $controller = new B2BVehicleController(); 

        try {
            $controller->RiderCredencials_SentWhatsAppMessage($riderData, 'b2b_rider_account_created');
            $controller->riderWelcomeNotification($riderData);
            $controller->RiderCredencials_SentEmailNotify($riderData, 'b2b_rider_ac_emailNotify');

            if ($this->submissionType === 'terms') {
                $controller->RiderTermsAndCondition_SentEmailNotify($riderData, 'b2b_rider_terms_emailNotify');
            }
            


            \Log::info('ProcessRiderCreationJob completed successfully', ['riderId' => $this->riderId]);
        } catch (\Throwable $e) {
            \Log::error('ProcessRiderCreationJob failed', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);
        }
    }
}
