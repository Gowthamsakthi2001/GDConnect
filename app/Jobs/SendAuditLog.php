<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\AuditHeader; // <-- your token generator

class SendAuditLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var array<string,mixed> */
    public array $data;

    // queue behavior
    public $tries = 5;                       // total attempts by queue worker
    public $backoff = [2, 5, 10, 30, 60];    // seconds between retries

    /**
     * @param array<string,mixed> $data  // the exact params you want to log
     */
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->onQueue('audit'); // optional dedicated queue
    }

    public function handle(): void
    {
        if (!config('app.log_mode')) {
            logger()->info('Audit log skipped because LOG_MODE=0.');
            return;
        }
        $tokenHeader = config('services.audit.header', 'X-Audit-Token');
        $base        = rtrim(config('services.audit.base_url'), '/');

        // ✅ generate token inside the Job
        $token = AuditHeader::make();

        $response = Http::retry(3, 1500) // 3 attempts, 1.5s delay inside this run
            ->withHeaders([
                $tokenHeader => $token,
                'User-Agent' => 'ProducerApp/1.0',
            ])
            ->asJson()
            ->post($base.'/api/logs', $this->data);

        // Let queue retry if remote fails (do not throw unless you want it to fail immediately)
        if ($response->failed()) {
            // optional: add context to your logs
            logger()->warning('Audit API failed', [
                'status' => $response->status(),
                'body'   => $response->json(),
            ]);
            // requeue per $backoff sequence
            $this->release($this->backoff[0] ?? 10);
        }
    }
}
