<?php

namespace App\Services;

use App\Models\Donation;
use App\Models\DonationAssignment;
use App\Models\DonationRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;

class NotificationService
{
    public function notifyUser(User $user, string $title, string $message, string $type, array $data = []): void
    {
        $user->notifications()->create([
            'id'   => Str::uuid(),
            'type' => $type,
            'data' => array_merge([
                'title'   => $title,
                'message' => $message,
            ], $data),
        ]);
    }

    // ── Donation Status ──

    public function notifyDonationStatusChanged(Donation $donation): void
    {
        $donation->loadMissing('donor');
        if (!$donation->donor) return;

        $statusLabels = [
            'pending'    => 'is pending review',
            'accepted'   => 'has been approved and is now visible',
            'assigned'   => 'has been assigned for delivery',
            'in_transit' => 'is in transit',
            'delivered'  => 'has been delivered',
            'completed'  => 'has been completed',
            'cancelled'  => 'has been cancelled',
        ];

        $label = $statusLabels[$donation->status] ?? 'status updated';

        $this->notifyUser(
            $donation->donor,
            'Donation Status Updated',
            "Your donation #{$donation->id} {$label}.",
            'donation_status',
            ['donation_id' => $donation->id, 'status' => $donation->status]
        );
    }

    // ── Donation Request (charity → donor) ──

    public function notifyNewDonationRequest(DonationRequest $request): void
    {
        $request->loadMissing(['donation.donor', 'charity']);
        $donation = $request->donation;
        if (!$donation?->donor) return;

        $charityName = $request->charity->organization_name ?? $request->charity->name ?? 'A charity';

        $this->notifyUser(
            $donation->donor,
            'New Donation Request',
            "{$charityName} has requested your donation #{$donation->id}.",
            'donation_request',
            ['donation_id' => $donation->id, 'request_id' => $request->id]
        );
    }

    // ── Charity request approved/rejected by admin ──

    public function notifyCharityRequestApproved(DonationRequest $request): void
    {
        $request->loadMissing(['charity', 'donation']);
        if (!$request->charity) return;

        $this->notifyUser(
            $request->charity,
            'Donation Request Approved',
            "Your request for donation #{$request->donation_id} has been approved.",
            'request_approved',
            ['donation_id' => $request->donation_id, 'request_id' => $request->id]
        );
    }

    public function notifyCharityRequestRejected(DonationRequest $request): void
    {
        $request->loadMissing(['charity', 'donation']);
        if (!$request->charity) return;

        $this->notifyUser(
            $request->charity,
            'Donation Request Rejected',
            "Your request for donation #{$request->donation_id} has been rejected.",
            'request_rejected',
            ['donation_id' => $request->donation_id, 'request_id' => $request->id]
        );
    }

    // ── Volunteer Assignment ──

    public function notifyVolunteerAssigned(DonationAssignment $assignment): void
    {
        $assignment->loadMissing('volunteer');
        if (!$assignment->volunteer_id || !$assignment->volunteer) return;

        $this->notifyUser(
            $assignment->volunteer,
            'New Assignment',
            "You have been assigned to donation #{$assignment->donation_id} as {$assignment->assignment_type}.",
            'assignment',
            [
                'assignment_id' => $assignment->id,
                'donation_id'   => $assignment->donation_id,
                'type'          => $assignment->assignment_type,
            ]
        );
    }

    public function notifyDonorVolunteerAssigned(DonationAssignment $assignment): void
    {
        $assignment->loadMissing(['donation.donor', 'volunteer']);
        $donor = $assignment->donation?->donor;
        if (!$donor) return;

        $volName = $assignment->volunteer->name ?? 'A volunteer';

        $this->notifyUser(
            $donor,
            'Volunteer Assigned',
            "{$volName} has volunteered for your donation #{$assignment->donation_id}.",
            'volunteer_assigned',
            ['donation_id' => $assignment->donation_id, 'assignment_id' => $assignment->id]
        );
    }

    // ── Assignment lifecycle (pickup / delivery) ──

    public function notifyPickedUp(DonationAssignment $assignment): void
    {
        $assignment->loadMissing(['donation.donor', 'volunteer']);
        $donor = $assignment->donation?->donor;
        if (!$donor) return;

        $volName = $assignment->volunteer->name ?? 'The volunteer';

        $this->notifyUser(
            $donor,
            'Donation Picked Up',
            "{$volName} has picked up your donation #{$assignment->donation_id}.",
            'pickup',
            ['donation_id' => $assignment->donation_id, 'assignment_id' => $assignment->id]
        );
    }

    public function notifyDelivered(DonationAssignment $assignment): void
    {
        $assignment->loadMissing(['donation.donor', 'volunteer']);
        $donor = $assignment->donation?->donor;
        if (!$donor) return;

        $volName = $assignment->volunteer->name ?? 'The volunteer';

        $this->notifyUser(
            $donor,
            'Donation Delivered',
            "{$volName} has delivered your donation #{$assignment->donation_id}.",
            'delivery',
            ['donation_id' => $assignment->donation_id, 'assignment_id' => $assignment->id]
        );
    }

    // ── User approval / rejection ──

    public function notifyUserApproved(User $user): void
    {
        $this->notifyUser(
            $user,
            'Account Approved',
            'Your account has been approved! You can now use all features.',
            'account_approved'
        );
    }

    public function notifyUserRejected(User $user, string $reason = ''): void
    {
        $msg = 'Your account has been rejected.';
        if ($reason) {
            $msg .= " Reason: {$reason}";
        }

        $this->notifyUser($user, 'Account Rejected', $msg, 'account_rejected');
    }

    // ── New donation created (notify admins) ──

    public function notifyAdminsNewDonation(Donation $donation): void
    {
        $donation->loadMissing('donor');
        $donorName = $donation->donor->name ?? 'A donor';

        $admins = User::whereHas('roles', fn ($q) => $q->where('name', 'admin'))->get();

        foreach ($admins as $admin) {
            $this->notifyUser(
                $admin,
                'New Donation',
                "{$donorName} created donation #{$donation->id}. Pending your approval.",
                'new_donation',
                ['donation_id' => $donation->id]
            );
        }
    }
}
