<?php

namespace App\Jobs;

use App\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SMSAPICallBackJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data = [])
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $payload = $this->normalizePayload($this->data);
        $messageKey = $payload['messageId'] ?? $payload['id'] ?? null;

        if ($messageKey === null || $messageKey === '') {
            error_log('SMS callback missing messageId');

            return;
        }

        $log = Log::where('key', $messageKey)->first();

        if (! $log && ! empty($payload['to'])) {
            $log = Log::where('message_to', $payload['to'])
                ->whereIn('type', ['sms', 'sms_reminder'])
                ->orderByDesc('id')
                ->first();
        }

        if (! $log) {
            error_log('SMS callback log not found for messageId: ' . $messageKey);

            return;
        }

        $log->update([
            'value' => $payload['status'] ?? $log->value,
        ]);

        error_log('....Completed....');
    }

    /**
     * @param array $data
     * @return array
     */
    private function normalizePayload($data)
    {
        if (! is_array($data)) {
            return [];
        }

        if (isset($data['messageId']) || isset($data['id']) || isset($data['status'])) {
            return $data;
        }

        if (isset($data[0]) && is_array($data[0])) {
            return $data[0];
        }

        return $data;
    }
}
