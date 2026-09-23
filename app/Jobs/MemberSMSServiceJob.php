<?php

namespace App\Jobs;

use App\Classes\BulkSMS;
use App\MRegister;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class MemberSMSServiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private array $data;

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
        if($this->data['type'] == 'member_select_message' || $this->data['type'] == 'member_all_message')
        {
            $type = 'member';
            $this->url = route('vote.member.register');
        }else{
            $type = 'member_announce';
        }
        
        MRegister::whereIn('id', $this->data['check_val'])->get()->each(function ($member) use ($type) {                        
            $phones = explode(',', $member->phone_number);

            if($type == 'member')
            {
                $result = BulkSMS::sendSMS($phones, $member,$type, $this->url);
            }else{
                $result = BulkSMS::sendSMS($phones, $member,$type, ' ');
            }
        });
    }
}
