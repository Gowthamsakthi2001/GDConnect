<?php

namespace App\Jobs;

use App\Helpers\CustomHandler;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;


class SendWhatsappMessageJobOld implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public $phone;
    public $message;

    public function __construct($phone, $message)
    {
        $this->phone = $phone;
        $this->message = $message;
    }

    public function handle()
    {
        Log::info(" Job Class initial called.");
        try{
            CustomHandler::send_single_whatsapp_message($this->phone, $this->message);
            Log::info(" Job Class WhatsApp messages for sending success.");
        }
        catch(\Exception $e){
            
            Log::info(" Job Class WhatsApp messages for sending Failed." .$e->getMessage());
        }
        
        
    }
}

