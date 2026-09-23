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
            $phones = explode(',', $voter->phone_no);
            $res = BulkSMS::sendSMS($phones, $voter, $type);
            $response = $res->getData()->info;
            Log::debug("responses", [
                $res,
                $response
            ]);
            error_log(print_r($response, true));
            if ($response->status == true || $response->status == 200 || $response->status == 'success') {
                $this->create($voter->id, $response->data->messages, $type);
            } else {
                Log::channel('single')->info(print_r($response, true));
                $messages = [];
                foreach($phones as $phone) {
                    $data = new \stdClass();
                    $data->id = null;
                    $data->message_to = $phone;    
                    $data->value = 'UNDELIVERED'; // Ensure 'UNDELIVERED' is defined or replace with a constant
                    array_push($messages, $data);
                }

                $this->create($voter->id, $messages, $type);
            }        
        });
    }

    /**
     * Create logs for SMS sent.
     *
     * @param int $voter_id
     * @param array $response
     * @param string|null $type
     * @return void
     */
    public function create(int $voter_id, array $response, ?string $type)
    {
        foreach ($response as $data) {
            $operator = $data->operator;
            $status = 'DELIVERED';
            if (isset($data->value)) {
                $status = $data->value;
            } else if(in_array(strtolower($data->operator), ['telenor', 'atom', 'mpt'])) {
                $status = 'DELIVERED';
            }
            AppLog::create([
                'voter_id' => $voter_id,
                'type' => $type != null ? 'sms_reminder' : 'sms',
                'key' => $data->id,
                'value' => $status, // Ensure 'PENDING' is defined or replace with a constant
                'message_to' => $data->message_to,
                'operator' => $operator
            ]);
        }
    }
}
