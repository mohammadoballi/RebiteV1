<?php

namespace App\Http\Requests;

use App\Models\DonationRequest;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreRatingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'rateable_id'   => ['required', 'integer', 'exists:users,id'],
            'rateable_type' => ['required', 'string', 'in:user'],
            'rating'        => ['required', 'integer', 'min:1', 'max:5'],
            'comment'       => ['nullable', 'string', 'max:500'],
        ];

        $rules['donation_id'] = ['required', 'integer', 'exists:donations,id'];

        return $rules;
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $user = $this->user();
            $donationId = $this->input('donation_id');

            $approved = DonationRequest::query()
                ->where('donation_id', $donationId)
                ->where('charity_id', $user->id)
                ->where('status', DonationRequest::STATUS_APPROVED)
                ->exists();

            if (!$approved) {
                $validator->errors()->add('donation_id', __('You can only rate participants on donations you have accepted.'));
            }

            $existingQuery = Rating::query()
                ->where('rater_id', $user->id)
                ->where('rateable_id', $this->input('rateable_id'))
                ->where('rateable_type', User::class);

            if ($donationId) {
                $existingQuery->where('donation_id', $donationId);
            }

            if ($existingQuery->exists()) {
                $validator->errors()->add('rating', __('You have already submitted a rating for this person on this donation.'));
            }
        });
    }
}
