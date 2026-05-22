<?php

namespace App\Services;

use App\Models\Donation;
use App\Models\Setting;
use App\Models\User;
use App\Repositories\DonationRepository;
use App\Support\DonationItemAggregator;
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

            $data['volunteers_needed'] = ($data['delivery_volunteers_needed'] ?? 0)
                                       + ($data['packaging_volunteers_needed'] ?? 0);

            if (!empty($items)) {
                $data = array_merge($data, DonationItemAggregator::aggregateForDonation($items));
            }

            $donation = $this->donationRepository->create($data);

            foreach ($items as $item) {
                $donation->items()->create($item);
            }

            $pointsAwarded = $this->awardDonorPoints($donation, count($items));
            $donation->setAttribute('points_awarded', $pointsAwarded);

            return $donation->load('items');
        });
    }

    public function update(int $id, array $data): Donation
    {
        return DB::transaction(function () use ($id, $data) {
            $items = $data['items'] ?? null;
            unset($data['items']);

            if (isset($data['delivery_volunteers_needed']) || isset($data['packaging_volunteers_needed'])) {
                $donation = $this->donationRepository->findOrFail($id);
                $data['volunteers_needed'] = ($data['delivery_volunteers_needed'] ?? $donation->delivery_volunteers_needed)
                                           + ($data['packaging_volunteers_needed'] ?? $donation->packaging_volunteers_needed);
            }

            $donation = $this->donationRepository->findOrFail($id);

            if ($items !== null) {
                if (!empty($items)) {
                    $data = array_merge($data, DonationItemAggregator::aggregateForDonation($items));
                }

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

    public function getMarketplaceData(array $filters = [], bool $forVolunteerBrowse = false)
    {
        $with = ['donor:id,name,city,city_id,town_id,avatar', 'items', 'cityRelation:id,name', 'town:id,name', 'foodCategory.parent:id,name'];

        if ($forVolunteerBrowse) {
            $with[] = 'assignments:id,donation_id,assignment_type,status';
            $query = Donation::query()
                ->with($with)
                ->whereIn('status', [Donation::STATUS_PENDING, Donation::STATUS_IN_PROGRESS])
                ->where(function ($q) {
                    $q->whereNull('expiry_time')
                        ->orWhere('expiry_time', '>', now());
                })
                ->whereColumn('volunteers_count', '<', 'volunteers_needed')
                ->forVolunteerMarketplace()
                ->withExists('approvedCharityRequest')
                ->withExists('charityLinkedAssignments')
                ->latest();
        } else {
            $query = Donation::query()
                ->with($with)
                ->available()
                ->latest();
        }

        if (!empty($filters['city_id'])) {
            $query->where('city_id', $filters['city_id']);
        }

        if (!empty($filters['town_id'])) {
            $query->where('town_id', $filters['town_id']);
        }

        if (!empty($filters['food_category_id'])) {
            $query->where('food_category_id', $filters['food_category_id']);
        }

        if (!empty($filters['food_category_parent_id'])) {
            $parentId = $filters['food_category_parent_id'];
            $query->whereHas('foodCategory', fn ($q) => $q->where('parent_id', $parentId));
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

        if ($forVolunteerBrowse) {
            if (! empty($filters['volunteer_type'])) {
                $query->needsVolunteerType($filters['volunteer_type']);
            } else {
                $query->needsAnyVolunteerType();
            }
        } elseif (! empty($filters['volunteer_type'])) {
            $query->needsVolunteerType($filters['volunteer_type']);
        }

        return $query->paginate(12);
    }

    public function incrementVolunteerCount(int $id): void
    {
        $donation = $this->donationRepository->findOrFail($id);
        $donation->increment('volunteers_count');
    }

    public function awardDonorPoints(Donation $donation, int $itemCount): int
    {
        $donor = User::find($donation->user_id);
        if (!$donor) {
            return 0;
        }

        $perDonation = Setting::getInt('donor_points_per_donation', 10);
        $perItem = Setting::getInt('donor_points_per_item', 2);
        $points = $perDonation + ($itemCount * $perItem);

        if ($points > 0) {
            $donor->addPoints($points);
        }

        return $points;
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
