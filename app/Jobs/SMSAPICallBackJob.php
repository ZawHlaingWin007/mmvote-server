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
        $voter = Log::where('key', $this->data['id'])->first();
        error_log('....Processing....');
        if($voter)
        {
            $voter->update([
                'value' => $this->data['status'],
            ]);
        }
        error_log('....Completed....');
    }
}
