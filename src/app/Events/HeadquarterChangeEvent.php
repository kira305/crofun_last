<?php
namespace App\Events;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class HeadquarterChangeEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $old_headquarter_id;
    public $new_headquarter_id;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    /*app/http/Listeners を呼び出し*/
    public function __construct($old_headquarter_id,$new_headquarter_id)
    {
         $this->new_headquarter_id = $new_headquarter_id;
         $this->old_headquarter_id = $old_headquarter_id;
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
