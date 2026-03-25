<?php

namespace App\Services;

use App\Models\Donation;
use App\Models\DonationAssignment;
use App\Models\DonationRequest;
use App\Models\User;
use Illuminate\Support\Str;

class NotificationService
{
    public function notifyUser(User $user, string $title, string $message, string $type, array $data = []): void
    {
        $user->notifications()->create([
            'id' => Str::uuid(),
            'type' => $type,
            'data' => json_encode(array_merge([
                'title' => $title,
                'message' => $message,
            ], $data)),
        ]);
    }

    public function notifyDonationStatusChanged(Donation $donation): void
    {
        $statusLabels = [
            'pending' => 'is pending review',
            'accepted' => 'has been accepted',
            'assigned' => 'has been assigned for delivery',
            'in_transit' => 'is in transit',
            'delivered' => 'has been delivered',
            'completed' => 'has been completed',
            'cancelled' => 'has been cancelled',
        ];

        $label = $statusLabels[$donation->status] ?? 'status updated';

        $this->notifyUser(
            $donation->user,
            'Donation Status Updated',
            "Your donation #{$donation->id} {$label}.",
            'donation_status',
            ['donation_id' => $donation->id, 'status' => $donation->status]
        );
    }

    public function notifyNewDonationRequest(DonationRequest $request): void
    {
        $donation = $request->donation;

        $this->notifyUser(
            $donation->user,
            'New Donation Request',
            "A charity has requested your donation #{$donation->id}.",
            'donation_request',
            ['donation_id' => $donation->id, 'request_id' => $request->id]
        );
    }

    public function notifyAssignment(DonationAssignment $assignment): void
    {
        if (!$assignment->volunteer_id) {
            return;
        }

        $this->notifyUser(
            $assignment->volunteer,
            'New Delivery Assignment',
            "You have been assigned to deliver donation #{$assignment->donation_id}.",
            'assignment',
            [
                'assignment_id' => $assignment->id,
                'donation_id' => $assignment->donation_id,
                'type' => $assignment->assignment_type,
            ]
        );
    }
}
