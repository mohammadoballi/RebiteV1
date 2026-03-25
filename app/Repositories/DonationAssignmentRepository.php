<?php

namespace App\Repositories;

use App\Models\DonationAssignment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class DonationAssignmentRepository extends BaseRepository
{
    public function __construct(DonationAssignment $model)
    {
        parent::__construct($model);
    }

    public function getByVolunteer(int $volunteerId): Collection
    {
        return $this->query()
            ->where('volunteer_id', $volunteerId)
            ->with(['donation', 'donation.user:id,name,phone'])
            ->latest()
            ->get();
    }

    public function getPendingDeliveries(): Collection
    {
        return $this->query()
            ->where('assignment_type', 'delivery')
            ->whereIn('status', ['pending', 'accepted', 'in_progress'])
            ->with(['donation', 'volunteer:id,name,phone'])
            ->latest()
            ->get();
    }

    public function assignVolunteer(int $donationId, int $volunteerId, string $type): Model
    {
        return $this->model->create([
            'donation_id' => $donationId,
            'volunteer_id' => $volunteerId,
            'assignment_type' => $type,
            'status' => 'pending',
        ]);
    }
}
