<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var array|string */
    public $to;
    /** @var array|string|null */
    public $cc;
    /** @var array|string|null */
    public $bcc;

    public string $subject;
    public string $htmlBody;

    // queue behavior
    public $tries   = 5;                   // total attempts
    public $backoff = [2, 5, 10, 30, 60];  // seconds between retries

    /**
     * @param array|string      $to
     * @param string            $subject
     * @param string            $htmlBody
     * @param array|string|null $cc
     * @param array|string|null $bcc
     */
    public function __construct($to, string $subject, string $htmlBody, $cc = null, $bcc = null)
    {
        // $this->onQueue('mail');
        $this->to       = $to;
        $this->subject  = $subject;
        $this->htmlBody = $htmlBody;
        $this->cc       = $cc;
        $this->bcc      = $bcc;
    }

    public function handle(): void
    {
        try {
             sleep(2);
             
            // Simple HTML email (no Mailable required)
            Mail::html($this->htmlBody, function ($message) {
                $message->to((array) $this->to)
                        ->subject($this->subject);

                if (!empty($this->cc))  { $message->cc((array) $this->cc); }
                if (!empty($this->bcc)) { $message->bcc((array) $this->bcc); }
            });

            Log::info('Mail sent (queued job)', [
                'to'   => (array) $this->to,
                'cc'   => (array) $this->cc,
                'bcc'  => (array) $this->bcc,
                'time' => now()->toDateTimeString(),
            ]);

        } catch (\Throwable $e) {
            // Let the queue retry based on $tries/$backoff.
            Log::warning('Mail send failed (job run)', [
                'error' => $e->getMessage(),
                'to'    => (array) $this->to,
            ]);
            $this->release($this->backoff[0] ?? 10);
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error('SendEmailJob permanently failed', [
            'error' => $e->getMessage(),
            'to'    => (array) $this->to,
            'cc'    => (array) $this->cc,
            'bcc'   => (array) $this->bcc,
        ]);
    }
}
