<?php

namespace App\Services;

use App\Models\Donation;
use App\Models\DonationRequest;
use App\Repositories\DonationRequestRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DonationRequestService
{
    public function __construct(
        protected DonationRequestRepository $donationRequestRepository
    ) {}

    public function create(array $data): DonationRequest
    {
        return $this->donationRequestRepository->create($data);
    }

    /**
     * Charity claims a donation: serializes on the donation row, rejects competing charities,
     * marks this request approved, and moves the donation out of the marketplace (pending → accepted).
     */
    public function charityAcceptDonation(int $donationId, int $charityId, ?string $message = null): DonationRequest
    {
        return DB::transaction(function () use ($donationId, $charityId, $message) {
            /** @var Donation $donation */
            $donation = Donation::query()->lockForUpdate()->findOrFail($donationId);

            if ($donation->status !== Donation::STATUS_PENDING) {
                throw new RuntimeException(__('This donation is no longer available.'));
            }

            if (DonationRequest::query()
                ->where('donation_id', $donationId)
                ->where('status', DonationRequest::STATUS_APPROVED)
                ->exists()) {
                throw new RuntimeException(__('This donation has already been accepted by another charity.'));
            }

            $existing = DonationRequest::query()
                ->where('donation_id', $donationId)
                ->where('charity_id', $charityId)
                ->whereIn('status', [DonationRequest::STATUS_PENDING, DonationRequest::STATUS_APPROVED])
                ->first();

            if ($existing && $existing->status === DonationRequest::STATUS_APPROVED) {
                throw new RuntimeException(__('You have already accepted this donation.'));
            }

            DonationRequest::query()
                ->where('donation_id', $donationId)
                ->where('status', DonationRequest::STATUS_PENDING)
                ->where('charity_id', '!=', $charityId)
                ->update(['status' => DonationRequest::STATUS_REJECTED]);

            if ($existing) {
                $existing->update([
                    'status' => DonationRequest::STATUS_APPROVED,
                    'message' => $message ?? $existing->message,
                ]);
                $req = $existing->fresh();
            } else {
                $req = DonationRequest::create([
                    'donation_id' => $donationId,
                    'charity_id' => $charityId,
                    'status' => DonationRequest::STATUS_APPROVED,
                    'message' => $message,
                ]);
            }

            $donation->update([
                'status' => Donation::STATUS_ACCEPTED,
                'accepted_charity_id' => $charityId,
            ]);

            return $req;
        });
    }

    public function getByCharity(int $charityId): Collection
    {
        return $this->donationRequestRepository->getByCharity($charityId);
    }
}
