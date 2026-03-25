<?php

namespace App\Events;

use App\Models\Donation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DonationStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Donation $donation
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('donations.' . $this->donation->id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'donation_id' => $this->donation->id,
            'status'      => $this->donation->status,
            'food_type'   => $this->donation->food_type,
            'donor_id'    => $this->donation->user_id,
        ];
    }
}
