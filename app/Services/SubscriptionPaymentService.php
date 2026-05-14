<?php

namespace App\Services;

use App\Models\SubscriptionPayment;
use App\Models\User;
use Carbon\Carbon;

class SubscriptionPaymentService
{
    public function recordFromStripeInvoice(
        string $stripeInvoiceId,
        string $stripeCustomerId,
        int $amountCents,
        string $currency,
        ?int $periodStartTs = null,
        ?int $periodEndTs = null
    ): ?SubscriptionPayment {
        $user = User::where('stripe_customer_id', $stripeCustomerId)->first();
        if (!$user || $amountCents <= 0) {
            return null;
        }

        return SubscriptionPayment::updateOrCreate(
            ['stripe_invoice_id' => $stripeInvoiceId],
            [
                'user_id' => $user->id,
                'amount_cents' => $amountCents,
                'currency' => strtolower($currency),
                'paid_at' => now(),
                'period_start' => $periodStartTs ? Carbon::createFromTimestamp($periodStartTs) : null,
                'period_end' => $periodEndTs ? Carbon::createFromTimestamp($periodEndTs) : null,
            ]
        );
    }
}
