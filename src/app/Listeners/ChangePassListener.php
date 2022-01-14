<?php

namespace App\Listeners;

use App\Events\ChangePassEvent;
use App\Log_MST;
use App\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;


class ChangePassListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  LogEvent  $event
     * @return void
     */
    public function handle(ChangePassEvent $event)
    {
        $user_code = $event->user_code;

        $user = User::where('usr_code',$user_code)->first();
        $log  = new Log_MST();

        if($user){
            
         $log->user_id    = $user->id;
         $log->process    = "PW 変更";
         $log->company_id = $user->company->id;
         $log->table_id   = 5;
         $log->name       = $user->usr_name;
         $log->save();

          
        }

    }
}
