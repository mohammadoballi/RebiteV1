<?php

namespace App\Events;

use App\Models\DonationAssignment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VolunteerAssigned implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public DonationAssignment $assignment
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('assignments.' . $this->assignment->volunteer_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'assignment_id'   => $this->assignment->id,
            'donation_id'     => $this->assignment->donation_id,
            'volunteer_id'    => $this->assignment->volunteer_id,
            'assignment_type' => $this->assignment->assignment_type,
            'status'          => $this->assignment->status,
        ];
    }
}
