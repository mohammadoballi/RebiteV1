<?php

namespace App\Repositories;

use App\Models\Donation;
use Illuminate\Database\Eloquent\Collection;

class DonationRepository extends BaseRepository
{
    public function __construct(Donation $model)
    {
        parent::__construct($model);
    }

    public function getByDonor(int $userId): Collection
    {
        return $this->query()->where('user_id', $userId)->latest()->get();
    }

    public function getAvailable(): Collection
    {
        return $this->query()
            ->where('status', 'pending')
            ->where(function ($q) {
                $q->whereNull('expiry_time')
                  ->orWhere('expiry_time', '>', now());
            })
            ->latest()
            ->get();
    }

    public function getByStatus(string $status): Collection
    {
        return $this->query()->where('status', $status)->latest()->get();
    }

    public function getDatatableQuery(?int $donorId = null)
    {
        $query = $this->query()->with(['donor:id,name', 'items']);

        if ($donorId) {
            $query->where('user_id', $donorId);
        }

        return $query->select([
            'id', 'user_id', 'food_type', 'quantity', 'quantity_unit',
            'pickup_address', 'pickup_time', 'status', 'created_at',
            'volunteers_needed', 'volunteers_count',
        ]);
    }

    public function getForCharity(): Collection
    {
        return $this->query()
            ->whereIn('status', ['pending', 'accepted'])
            ->where(function ($q) {
                $q->whereNull('expiry_time')
                  ->orWhere('expiry_time', '>', now());
            })
            ->with('user:id,name,city')
            ->latest()
            ->get();
    }
}
