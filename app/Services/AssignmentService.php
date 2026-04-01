<?php

namespace App\Services;

use App\Models\DonationAssignment;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Repositories\DonationAssignmentRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class AssignmentService
{
    public function __construct(
        protected DonationAssignmentRepository $assignmentRepository,
        protected NotificationService $notificationService
    ) {}

    public function assignVolunteer(int $donationId, int $volunteerId, string $type): Model
    {
        return $this->assignmentRepository->assignVolunteer($donationId, $volunteerId, $type);
    }

    public function autoAssign(int $donationId): Model
    {
        $volunteer = User::whereHas('roles', fn ($q) => $q->where('name', Role::VOLUNTEER))
            ->where('status', User::STATUS_APPROVED)
            ->where('role_type', 'delivery')
            ->whereDoesntHave('assignments', function ($q) {
                $q->whereIn('status', ['pending', 'accepted', 'in_progress']);
            })
            ->first();

        if ($volunteer) {
            return $this->assignmentRepository->assignVolunteer(
                $donationId,
                $volunteer->id,
                'delivery'
            );
        }

        return $this->assignmentRepository->create([
            'donation_id' => $donationId,
            'assignment_type' => 'delivery',
            'status' => 'pending',
            'is_external_delivery' => true,
        ]);
    }

    public function updateStatus(int $id, string $status): bool
    {
        return $this->assignmentRepository->update($id, ['status' => $status]);
    }

    public function getByVolunteer(int $volunteerId): Collection
    {
        return $this->assignmentRepository->getByVolunteer($volunteerId);
    }

    public function markPickedUp(int $id): bool
    {
        $assignment = $this->assignmentRepository->findOrFail($id);
        $result = $this->assignmentRepository->update($id, [
            'status' => 'in_progress',
            'pickup_at' => now(),
        ]);

        if ($result && $assignment->volunteer_id) {
            User::find($assignment->volunteer_id)?->addPoints(Setting::getInt('volunteer_pickup_points', 5));
            $this->notificationService->notifyPickedUp($assignment);
        }

        return $result;
    }

    public function markDelivered(int $id): bool
    {
        $assignment = $this->assignmentRepository->findOrFail($id);
        $result = $this->assignmentRepository->update($id, [
            'status' => 'completed',
            'delivered_at' => now(),
        ]);

        if ($result && $assignment->volunteer_id) {
            $volunteer = User::find($assignment->volunteer_id);
            $volunteer?->addPoints(Setting::getInt('volunteer_delivery_points', 25));
            $this->notificationService->notifyDelivered($assignment);
        }

        return $result;
    }
}
