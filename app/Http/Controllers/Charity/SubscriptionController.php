<?php

namespace App\Http\Controllers\Charity;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Customer;
use Stripe\Stripe;
use Stripe\Webhook;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function index()
    {
        $user = auth()->user();
        $price = Setting::get('charity_subscription_price', 2);

        return view('charity.subscription.index', [
            'user'  => $user,
            'price' => $price,
        ]);
    }

    public function checkout()
    {
        $user = auth()->user();
        $price = (float) Setting::get('charity_subscription_price', 2);

        if (!$user->stripe_customer_id) {
            $customer = Customer::create([
                'email' => $user->email,
                'name'  => $user->organization_name ?? $user->name,
                'metadata' => ['user_id' => $user->id],
            ]);
            $user->update(['stripe_customer_id' => $customer->id]);
        }

        $session = StripeSession::create([
            'customer'    => $user->stripe_customer_id,
            'mode'        => 'subscription',
            'line_items'  => [[
                'price_data' => [
                    'currency'     => 'usd',
                    'unit_amount'  => (int) ($price * 100 * 1.31),
                    'recurring'    => ['interval' => 'month'],
                    'product_data' => [
                        'name' => 'Rebite Charity Monthly Subscription',
                    ],
                ],
                'quantity' => 1,
            ]],
            'success_url' => route('charity.subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('charity.subscription.index'),
        ]);

        return redirect($session->url);
    }

    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');
        if (!$sessionId) {
            return redirect()->route('charity.subscription.index');
        }

        $session = StripeSession::retrieve(['id' => $sessionId, 'expand' => ['subscription']]);

        $user = auth()->user();
        $user->update([
            'stripe_subscription_id' => $session->subscription->id ?? $session->subscription,
            'subscription_status'    => 'active',
            'subscription_ends_at'   => now()->addMonth(),
        ]);

        return redirect()->route('charity.subscription.index')
            ->with('success', __('Subscription activated successfully!'));
    }

    public static function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\Exception $e) {
            return response('Invalid signature', 400);
        }

        switch ($event->type) {
            case 'invoice.payment_succeeded':
                $subscription = $event->data->object;
                $customerId = $subscription->customer;
                $user = \App\Models\User::where('stripe_customer_id', $customerId)->first();
                if ($user) {
                    $user->update([
                        'subscription_status'  => 'active',
                        'subscription_ends_at' => now()->addMonth(),
                    ]);
                }
                break;

            case 'customer.subscription.deleted':
            case 'invoice.payment_failed':
                $subscription = $event->data->object;
                $customerId = $subscription->customer;
                $user = \App\Models\User::where('stripe_customer_id', $customerId)->first();
                if ($user) {
                    $user->update(['subscription_status' => 'cancelled']);
                }
                break;
        }

        return response('OK', 200);
    }
}
