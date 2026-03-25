<?php

namespace App\Services;

use App\Models\Donation;
use App\Models\DonationItem;
use App\Repositories\DonationRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DonationService
{
    public function __construct(
        protected DonationRepository $donationRepository
    ) {}

    public function create(array $data): Donation
    {
        return DB::transaction(function () use ($data) {
            $items = $data['items'] ?? [];
            unset($data['items']);

            // Set food_type from first item for backward compat
            if (!empty($items)) {
                $data['food_type'] = collect($items)->pluck('food_type')->implode(', ');
                $data['quantity'] = collect($items)->pluck('quantity')->first();
                $data['quantity_unit'] = collect($items)->pluck('quantity_unit')->first();
            }

            $donation = $this->donationRepository->create($data);

            foreach ($items as $item) {
                $donation->items()->create($item);
            }

            return $donation->load('items');
        });
    }

    public function update(int $id, array $data): Donation
    {
        return DB::transaction(function () use ($id, $data) {
            $items = $data['items'] ?? null;
            unset($data['items']);

            $donation = $this->donationRepository->findOrFail($id);

            if ($items !== null) {
                $data['food_type'] = collect($items)->pluck('food_type')->implode(', ');
                $data['quantity'] = collect($items)->pluck('quantity')->first();
                $data['quantity_unit'] = collect($items)->pluck('quantity_unit')->first();

                $donation->items()->delete();
                foreach ($items as $item) {
                    $donation->items()->create($item);
                }
            }

            $donation->update(array_filter($data, fn ($v) => $v !== null));

            return $donation->load('items');
        });
    }

    public function delete(int $id): bool
    {
        $donation = $this->donationRepository->findOrFail($id);

        if ($donation->image) {
            Storage::disk('public')->delete($donation->image);
        }

        return $this->donationRepository->delete($id);
    }

    public function getByDonor(int $userId): Collection
    {
        return $this->donationRepository->getByDonor($userId);
    }

    public function getAvailable(): Collection
    {
        return $this->donationRepository->getAvailable();
    }

    public function updateStatus(int $id, string $status): bool
    {
        return $this->donationRepository->update($id, ['status' => $status]);
    }

    public function getDatatableData(?int $donorId = null)
    {
        return $this->donationRepository->getDatatableQuery($donorId);
    }

    public function getMarketplaceData(array $filters = [])
    {
        $query = Donation::available()
            ->with(['donor:id,name,city,avatar', 'items'])
            ->latest();

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('food_type', 'like', "%{$search}%")
                  ->orWhere('pickup_address', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('items', fn ($q2) => $q2->where('food_type', 'like', "%{$search}%"));
            });
        }

        if (!empty($filters['city'])) {
            $query->whereHas('donor', fn ($q) => $q->where('city', $filters['city']));
        }

        if (!empty($filters['food_type'])) {
            $ft = $filters['food_type'];
            $query->where(function ($q) use ($ft) {
                $q->where('food_type', 'like', "%{$ft}%")
                  ->orWhereHas('items', fn ($q2) => $q2->where('food_type', 'like', "%{$ft}%"));
            });
        }

        if (!empty($filters['date_from'])) {
            $query->where('pickup_time', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('pickup_time', '<=', $filters['date_to'] . ' 23:59:59');
        }

        return $query->paginate(12);
    }

    public function incrementVolunteerCount(int $id): void
    {
        $donation = $this->donationRepository->findOrFail($id);
        $donation->increment('volunteers_count');
    }

    public function uploadImage(int $id, UploadedFile $file): string
    {
        $donation = $this->donationRepository->findOrFail($id);

        if ($donation->image) {
            Storage::disk('public')->delete($donation->image);
        }

        $path = $file->store('donations', 'public');
        $donation->update(['image' => $path]);

        return $path;
    }
}
