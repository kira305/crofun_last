<?php

namespace App\Jobs;

use App\Service\MailService;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SaveUserTeamsId implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public    $timeout = 60;
    protected $user;
    protected $action;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, $graph, $action)
    {
        $this->graph = $graph;
        $this->user = $user;
        $this->action = $action;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        echo 'Start set user teams  id';
        $mailService = new MailService();
        $mailService->saveUserTeamsId($this->user, $this->graph, $this->action);
        echo 'End set user teams  id';
    }
}
