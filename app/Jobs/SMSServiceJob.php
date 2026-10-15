<?php

namespace App\Jobs;

use App\Classes\BulkSMS;
use App\Classes\MPSSBulk;
use App\Log as AppLog;
use App\Setting;
use App\Voter;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;

class SMSServiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $data;

    /**
     * Create a new job instance.
     *
     * @param array $data
     * @return void
     */
    public function __construct(array $data = [])
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
        if ($this->data['type'] == 'select_reminder' || $this->data['type'] == 'all_reminder') {
            $type = 'reminder';
        } else if ($this->data['type'] == 'select_annouce' || $this->data['type'] == 'all_annouce') {
            $type = 'voter_announce';
        } else {
            $type = null;
        }

        // SMS Poh
        Voter::whereIn('id', $this->data['check_val'])->with('electionVoter')->get()->each(function ($voter) use ($type) {
            $phones = $this->phoneNumbers($voter);
            if (count($phones) === 0) {
                Log::info('Skipping SMS, voter has no phone number', ['voter_id' => $voter->id]);
                return;
            }

            $res = BulkSMS::sendSMS($phones, $voter, $type);
            $response = $res->getData()->info;
            Log::debug("responses", [
                $res,
                $response
            ]);
            error_log(print_r($response, true));
            $messages = $this->extractMessages($response);

            if ($this->isSuccessfulResponse($response) && count($messages) > 0) {
                $this->create($voter->id, $messages, $type, $phones);
            } else {
                Log::channel('single')->info(print_r($response, true));
                $fallbackMessages = [];
                foreach ($phones as $phone) {
                    $data = new \stdClass();
                    $data->id = null;
                    $data->message_to = $phone;
                    $data->operator = null;
                    $data->value = 'UNDELIVERED';
                    $fallbackMessages[] = $data;
                }

                $this->create($voter->id, $fallbackMessages, $type, $phones);
            }
        });
    }

    /**
     * @param Voter $voter
     * @return array
     */
    private function phoneNumbers(Voter $voter)
    {
        $phones = array_map(function ($phone) {
            return trim(str_replace(['-', ' '], '', $phone));
        }, explode(',', (string) $voter->phone_no));

        return array_values(array_filter($phones, function ($phone) {
            return $phone !== '';
        }));
    }

    /**
     * @param object|null $response
     * @return array
     */
    private function extractMessages($response)
    {
        if (! is_object($response)) {
            return [];
        }

        if (isset($response->data->messages) && is_array($response->data->messages)) {
            return $response->data->messages;
        }

        if (isset($response->messages) && is_array($response->messages)) {
            return $response->messages;
        }

        return [];
    }

    /**
     * @param object|null $response
     * @return bool
     */
    private function isSuccessfulResponse($response)
    {
        if (! is_object($response)) {
            return false;
        }

        if (isset($response->status)) {
            if ($response->status === true || $response->status === 200 || $response->status === 'success') {
                return true;
            }
        }

        $messages = $this->extractMessages($response);
        if (count($messages) === 0) {
            return false;
        }

        foreach ($messages as $message) {
            if (! is_object($message) || ! isset($message->status)) {
                continue;
            }

            $status = strtolower((string) $message->status);
            if (in_array($status, ['failed', 'rejected', 'undelivered', 'error'], true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param mixed $status
     * @return string
     */
    private function mapApiStatusToLogValue($status)
    {
        $normalized = strtolower((string) $status);

        if (in_array($normalized, ['accepted', 'sent', 'delivered', 'success', 'pending'], true)) {
            return 'DELIVERED';
        }

        if (in_array($normalized, ['undelivered', 'failed', 'rejected', 'error'], true)) {
            return 'UNDELIVERED';
        }

        return strtoupper((string) $status);
    }

    /**
     * Create logs for SMS sent.
     *
     * @param int $voter_id
     * @param array $response
     * @param string|null $type
     * @param array $phones
     * @return void
     */
    public function create(int $voter_id, array $response, ?string $type, array $phones = [])
    {
        foreach ($response as $index => $data) {
            $operator = $data->network ?? $data->operator ?? null;
            $messageId = $data->messageId ?? $data->id ?? null;
            $messageTo = $this->messageRecipient($data, $phones, $index);

            if ($messageTo === null) {
                Log::info('Skipping SMS log, message_to is empty', ['voter_id' => $voter_id]);
                continue;
            }

            if (isset($data->value)) {
                $status = $data->value;
            } elseif (isset($data->status)) {
                $status = $this->mapApiStatusToLogValue($data->status);
            } elseif ($operator !== null && in_array(strtolower((string) $operator), ['telenor', 'atom', 'mpt'], true)) {
                $status = 'DELIVERED';
            } else {
                $status = 'DELIVERED';
            }

            AppLog::create([
                'voter_id' => $voter_id,
                'type' => $type != null ? 'sms_reminder' : 'sms',
                'key' => $messageId,
                'value' => $status ?: 'UNDELIVERED',
                'message_to' => $messageTo,
                'operator' => $operator,
            ]);
        }
    }

    /**
     * @param object $data
     * @param array $phones
     * @param int $index
     * @return string|null
     */
    private function messageRecipient($data, array $phones, $index)
    {
        $raw = $data->to ?? $data->message_to ?? null;
        if (is_array($raw)) {
            $raw = $raw[0] ?? null;
        }

        $messageTo = trim((string) $raw);
        if ($messageTo === '') {
            $messageTo = trim((string) ($phones[$index] ?? ($phones[0] ?? '')));
        }

        return $messageTo === '' ? null : $messageTo;
    }
}
