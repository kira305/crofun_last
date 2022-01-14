<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Service\MailService;
class SendCreditMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public    $timeout = 60;
    protected $client_id;
    protected $mail_service;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($client_id,MailService $mail_service)
    {
        $this->client_id    = $client_id;
        $this->mail_service = $mail_service;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {   

        echo 'Start send email';
   
        $this->mail_service->sendCreditMail($this->client_id);

        echo 'End send email';
    }
}
