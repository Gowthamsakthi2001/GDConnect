<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\BusinessSetting;

class SendWhatsappMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $number;
    public string $message;

    // Queue behavior
    public $tries   = 5;                 // total attempts
    public $backoff = [2, 5, 10, 30, 60]; // seconds between retries

    public function __construct(string $number, string $message)
    {
        $this->onQueue('whatsapp');
        $this->number  = $this->normalizeNumber($number);
        $this->message = $message;
    }

    public function handle(): void
    {
        sleep(2);
        
        
        // Load creds each run (or cache them if you prefer)
        $apiKey = (string) BusinessSetting::where('key_name', 'whatshub_api_key')->value('value');
        $apiUrl = (string) BusinessSetting::where('key_name', 'whatshub_api_url')->value('value');

        $payload = [
            "contact" => [[
                "number"  => $this->number,
                "message" => $this->message,
            ]],
        ];

        // Using Laravel HTTP client instead of raw cURL
        $response = Http::timeout(30)
            ->retry(3, 1500) // inside-this-run retries
            ->withHeaders([
                'Api-key'      => $apiKey,
                'Content-Type' => 'application/json',
                'User-Agent'   => 'WhatsappSender/1.0',
            ])
            ->post($apiUrl, $payload);

        if ($response->failed()) {
            Log::warning('WhatsApp send failed', [
                'to'     => $this->number,
                'status' => $response->status(),
                'body'   => $this->safeJson($response->body()),
            ]);

            // Let the queue re-try based on $tries/$backoff
            $this->release($this->backoff[0] ?? 10);
            return;
        }

        // Log::info('WhatsApp message sent', [
        //     'to'   => $this->number,
        //     'resp' => $this->safeJson($response->body()),
        // ]);
    }

    public function failed(\Throwable $e): void
    {
        Log::error('WhatsApp job permanently failed', [
            'to'    => $this->number,
            'error' => $e->getMessage(),
        ]);
    }
    

    private function normalizeNumber(string $n): string
    {
        // remove +, spaces and non-digits
        return preg_replace('/\D+/', '', $n ?? '');
    }

    private function safeJson(?string $raw)
    {
        $decoded = json_decode($raw ?? '', true);
        return $decoded ?? $raw;
    }
}
