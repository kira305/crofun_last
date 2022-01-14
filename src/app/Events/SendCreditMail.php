<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SendCreditMail
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $mail_service;
    public $client_id;
    public $receivable;

    public function __construct($client_id,$receivable,$mail_service){

        $this->client_id    = $client_id;
        $this->mail_service = $mail_service;
        $this->receivable   = $receivable;

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
