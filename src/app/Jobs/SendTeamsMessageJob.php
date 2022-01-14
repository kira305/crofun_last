<?php

namespace App\Jobs;

use App\Service\MailService;
use App\User;
use App\User_MST;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Microsoft\Graph\Graph;

class SendTeamsMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public    $timeout = 60;
    protected $user;
    protected $graph;
    protected $mailObj;
    protected $content;
    /**php artisan queue:work
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, Graph $graph, $mailObj, $content)
    {
        $this->user = $user;
        $this->graph = $graph;
        $this->mailObj = $mailObj;
        $this->content = $content;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $mail_service = new MailService();
        echo 'Start send teams message';
        $mail_service->sendChatMessage($this->user, $this->graph, $this->mailObj, $this->content);
        echo 'End send teams message';
    }
}
