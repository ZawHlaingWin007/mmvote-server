<?php

namespace App\Jobs;

use App\Classes\BulkEmail;
use App\Log as AppLog;
use App\Notifications\SendEmail;
use App\Setting;
use App\Voter;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MailServiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $setting;
    private array $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Setting $setting, $data = [])
    {
        $this->setting = $setting;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $setting = $this->setting;

        if ($this->data['type'] == 'select_reminder' || $this->data['type'] == 'all_reminder') {
            $type = 'reminder';
        } else if ($this->data['type'] == 'select_annouce' || $this->data['type'] == 'all_annouce') {
            $type = 'voter_announce';
        }else {
            $type = null;
        }

        Voter::whereIn('id', $this->data['check_val'])->with('electionVoter')->get()->each(function ($voter) use ($type, $setting) {
            $email = trim((string) $voter->email);
            if ($email === '') {
                Log::info('Skipping mail, voter has no email address', ['voter_id' => $voter->id]);
                return;
            }

            $url = route('vote.link', ['voter_id' => $voter->voter_id]);

            if ($type == 'reminder') {
                $content = ($setting->reminder_text == null) ? Lang::get('message.reminder') :
                    str_replace(['[:VoterName]', '[:ShareCount]'], [$voter->name, $voter->vote_count], $setting->reminder_text);
            } else if ($type == 'voter_announce') {
                $content = ($setting->voter_annouce == null) ? Lang::get('message.annouce') :
                    str_replace(['[:VoterName]', '[:ShareCount]'], [$voter->name, $voter->vote_count], $setting->voter_annouce);
            } else {
                $content = ($setting->sms_text == null) ? Lang::get('message.text') :
                    str_replace(['[:VoterName]', '[:ShareCount]'], [$voter->name, $voter->vote_count], $setting->sms_text);
            }
                        
            try{
                $voter->notify(new SendEmail($content, $url));
                
                if($type == 'reminder'){
                    $this->create($voter, 'DELIVERED', $type);
                }elseif($type == null){
                    $this->create($voter, 'DELIVERED', $type);
                }


                    if (count(Mail::failures()) > 0) {
                        Log::channel('single')->error('Mail failed for: ' . $voter->email, [
                            'failures' => Mail::failures(),
                        ]);
                    }
            }
            catch(\Throwable $e){ // Using a generic exception
                Log::channel('single')->info($e);
                Log::error('Mail send failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                if($type == 'reminder'){
                    $this->create($voter, 'UNDELIVERED', $type);
                }elseif($type == null){
                    $this->create($voter, 'UNDELIVERED', $type);
                }
            }            
        });
    }

    public function create($voter,$response,$type)
    {
        $messageTo = trim((string) $voter->email);
        if ($messageTo === '') {
            Log::info('Skipping mail log, message_to is empty', ['voter_id' => $voter->id]);
            return;
        }

        AppLog::create([
            'voter_id' => $voter->id,
            'type' => $type != null ? 'mail_reminder' : 'mail',
            'key' => 'status',
            'value' => $response ?: 'UNDELIVERED',
            'message_to' => $messageTo,
        ]);
    }
}
