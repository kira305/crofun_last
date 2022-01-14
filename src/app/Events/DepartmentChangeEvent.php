<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class DepartmentChangeEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $old_department_id;
    public $new_department_id;

    /**
     * Create a new event instance.
     *
     * @return void
     */
      public function __construct($old_department_id,$new_department_id)
    {
         
         $this->new_department_id = $new_department_id;
         $this->old_department_id = $old_department_id;
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
