<?php

use App\Credit_check;
use App\Service\MailService;
class CreditMail extends Thread{

    private $mail_service;
    private $client_id;
    private $receivable;
    public function __construct($client_id,$receivable,$mail_service){

        $this->client_id    = $client_id;
        $this->mail_service = $mail_service;
        $this->receivable   = $receivable;

    }

    public function run(){

          $credit     = Credit_check::where('client_id',$this->client_id)->first();

          if($credit){

                      $credit     = (int)$credit->credit_limit;

                      $receivable = (int)$receivable;
                      
                      if(($credit - $this->receivable) < 0){

                          $this->mail_service->sendCreditMail($this->client_id);
                          // $job = (new SendCreditMail($client_id,$this->mail_service))->delay(Carbon::now()->addMinutes(1));
                          // dispatch($job)->onQueue('processing');

                      }

          }

    }
}

?>