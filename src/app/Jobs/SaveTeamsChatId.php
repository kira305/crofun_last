<?php

namespace App\Jobs;

use App\Service\MailService;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Microsoft\Graph\Graph;

class SaveTeamsChatId implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $graph;
    protected $action;
    /**
     * Create a new job instance.
     *　パラーメーター
     * @return void
     */
    public function __construct(User $user, Graph $graph, $action)
    {
        $this->graph = $graph;
        $this->user = $user;
        $this->action = $action;
    }

    /**
     * Execute the job.
     *　実際動くコード
     * @return void
     */
    public function handle()
    {
        echo 'Start set chat teams  id';
        $mailService = new MailService();
        $mailService->saveTeamsChatId($this->user, $this->graph, $this->action);
        echo 'end set chat teams  id';
    }
}
