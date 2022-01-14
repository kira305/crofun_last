<?php

namespace App\Listeners;

use App\Events\LogEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
class LogEventListener
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
    public function handle(LogEvent $event)
    {
        echo 123;
    }
}
