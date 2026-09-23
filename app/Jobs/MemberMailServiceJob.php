<?php

namespace App\Jobs;

use App\MRegister;
use App\Notifications\SendEmail;
use App\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Lang;

class MemberMailServiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $setting;
    private array $data;
    private $url;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Setting $setting, $data = [], $url = null)
    {
        $this->setting = $setting;
        $this->data = $data;
        $this->url = $url;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $setting = $this->setting;

        if($this->data['type'] == 'member_select_message' || $this->data['type'] == 'member_all_message')
        {
            $type = 'member';
            $this->url = route('vote.member.register');
        }else{
            $type = 'member_announce';
        }         

        MRegister::whereIn('id', $this->data['check_val'])->get()->each(function ($member) use ($type, $setting) {
            if ($type == 'member_announce') {
                $content = ($setting->member_annouce == null) ? Lang::get('message.annouce') :
                    str_replace('[:MemberName]', $member->name, $setting->member_annouce);
            }else{
                $content = ($setting->member_sms_text == null) ? Lang::get('message.member') .$this->url. Lang::get('message.contact'):
                str_replace('[:MemberName]', $member->name, $setting->member_sms_text);
            }
            
            $member->notify(new SendEmail($content, $this->url));
        });
    }
}
