<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class SeizeSystem implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $employeeId;
    public $action;

    public function __construct($employeeId)
    {
        $this->employeeId = $employeeId;
        $this->action = 'seize_system';
    }

    public function broadcastOn()
    {
        return new Channel('employee.' . $this->employeeId);
    }
}
