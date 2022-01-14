<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class GroupChangeEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $old_group_id;
    public $new_group_id;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($old_group_id,$new_group_id)
    {
         $this->new_group_id = $new_group_id;
         $this->old_group_id = $old_group_id;
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
