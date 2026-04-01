@extends('layouts.charity')

@section('title', __('Subscription'))

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1><i class="fas fa-credit-card me-2"></i>{{ __('Subscription') }}</h1>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card border-0 shadow">
            <div class="card-body text-center p-5">
                @if($user->hasActiveSubscription())
                    <div class="mb-4">
                        <i class="fas fa-check-circle fa-4x text-success"></i>
                    </div>
                    <h3 class="fw-bold text-success">{{ __('Active Subscription') }}</h3>
                    <p class="text-muted mb-3">
                        {{ __('Your subscription is active.') }}
                        @if($user->subscription_ends_at)
                            {{ __('Renews on') }}: <strong>{{ $user->subscription_ends_at->format('M d, Y') }}</strong>
                        @endif
                    </p>
                    <span class="badge bg-success fs-6 px-4 py-2">
                        <i class="fas fa-crown me-1"></i> {{ __('Subscribed') }}
                    </span>
                @else
                    <div class="mb-4">
                        <i class="fas fa-lock fa-4x text-warning"></i>
                    </div>
                    <h3 class="fw-bold">{{ __('Subscribe to Rebite') }}</h3>
                    <p class="text-muted mb-4">
                        {{ __('A monthly subscription is required to request and take donations.') }}
                    </p>

                    <div class="card bg-light border-0 mb-4 mx-auto" style="max-width: 300px;">
                        <div class="card-body">
                            <h2 class="fw-bold text-success mb-0">{{ $price }} JD</h2>
                            <small class="text-muted">/ {{ __('month') }}</small>
                        </div>
                    </div>

                    <ul class="list-unstyled text-start mb-4" style="max-width: 350px; margin: 0 auto;">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i> {{ __('Browse available donations') }}</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i> {{ __('Request donations from donors') }}</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i> {{ __('Rate donors and volunteers') }}</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i> {{ __('Full access to all features') }}</li>
                    </ul>

                    <form action="{{ route('charity.subscription.checkout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success btn-lg px-5">
                            <i class="fas fa-credit-card me-2"></i> {{ __('Subscribe Now') }}
                        </button>
                    </form>

                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="fas fa-lock me-1"></i> {{ __('Secure payment powered by Stripe') }}
                        </small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
