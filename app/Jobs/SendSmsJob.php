<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $mobile;
    public $msg;
    public function __construct($mobile, $msg)
    {
        $this->mobile = $mobile;
        $this->msg = $msg;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // if($this->queue == 'smsapi'){
        //     \App\Helpers\SmsHelper::send($this->mobile, $this->msg);
        // }else{
        //     event(new \App\Events\SmsEvent($this->mobile, $this->msg));
        // }

        event(new \App\Events\SmsEvent($this->mobile, $this->msg));
    }
}
