<?php

namespace App\Events;

use App\Models\DonationRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewDonationRequest implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public DonationRequest $donationRequest
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('donations.' . $this->donationRequest->donation_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'request_id'  => $this->donationRequest->id,
            'donation_id' => $this->donationRequest->donation_id,
            'charity_id'  => $this->donationRequest->charity_id,
            'status'      => $this->donationRequest->status,
        ];
    }
}
