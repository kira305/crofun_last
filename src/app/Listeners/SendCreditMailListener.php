<?php

namespace App\Listeners;

use App\Events\LogEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\SendCreditMail;
use App\Credit_check;
class SendCreditMailListener
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
    public function handle(SendCreditMail $event)
    {
          $credit     = Credit_check::where('client_id',$event->client_id)->first();

          if($credit){

                      $credit     = (int)$credit->credit_limit;

                      $receivable = (int)$event->receivable;
                      
                      if(($credit - $receivable) < 0){

                          $event->mail_service->sendCreditMail($event->client_id);
                          // $job = (new SendCreditMail($client_id,$this->mail_service))->delay(Carbon::now()->addMinutes(1));
                          // dispatch($job)->onQueue('processing');

                      }

          }
    }
}