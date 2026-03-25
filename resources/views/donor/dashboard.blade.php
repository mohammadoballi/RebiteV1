@extends('layouts.donor')

@section('title', __('dashboard.donor_dashboard'))

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1><i class="fas fa-tachometer-alt me-2"></i>{{ __('dashboard.donor_dashboard') }}</h1>
</div>

{{-- Welcome Card --}}
<div class="card mb-4">
    <div class="card-body d-flex align-items-center">
        <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
            <i class="fas fa-hand-holding-heart fa-2x text-success"></i>
        </div>
        <div>
            <h5 class="mb-1">{{ __('general.welcome') }}, {{ auth()->user()->name }}!</h5>
            <p class="text-muted mb-0">{{ __('dashboard.donor_dashboard') }} — Rebite</p>
        </div>
    </div>
</div>

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                    <i class="fas fa-gift fa-lg text-success"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ $totalDonations ?? 0 }}</h3>
                    <small class="text-muted">{{ __('dashboard.my_donations') }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                    <i class="fas fa-clock fa-lg text-warning"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ $pendingCount ?? 0 }}</h3>
                    <small class="text-muted">{{ __('donations.pending') }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                    <i class="fas fa-check-circle fa-lg text-primary"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ $completedCount ?? 0 }}</h3>
                    <small class="text-muted">{{ __('donations.completed') }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                    <i class="fas fa-thumbs-up fa-lg text-info"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ $acceptedCount ?? 0 }}</h3>
                    <small class="text-muted">{{ __('donations.accepted') }}</small>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Recent Donations & Quick Action --}}
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="fas fa-history me-2"></i>{{ __('dashboard.recent_donations') }}</h6>
                <a href="{{ route('donor.donations.index') }}" class="btn btn-sm btn-outline-primary">
                    {{ __('general.view') }} {{ __('general.actions') }}
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('donations.food_type') }}</th>
                                <th>{{ __('donations.quantity') }}</th>
                                <th>{{ __('donations.status') }}</th>
                                <th>{{ __('donations.pickup_time') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentDonations ?? [] as $donation)
                                <tr>
                                    <td>{{ $donation->food_type }}</td>
                                    <td>{{ $donation->quantity }} {{ __('donations.' . ($donation->quantity_unit ?? 'kg')) }}</td>
                                    <td><x-status-badge :status="$donation->status" /></td>
                                    <td>{{ $donation->pickup_time?->format('M d, Y H:i') ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                        {{ __('general.no_data') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card text-center">
            <div class="card-body py-5">
                <div class="rounded-circle bg-success bg-opacity-10 d-inline-flex p-4 mb-3">
                    <i class="fas fa-plus-circle fa-3x text-success"></i>
                </div>
                <h5>{{ __('donations.add_donation') }}</h5>
                <p class="text-muted">{{ __('Share surplus food with those in need') }}</p>
                <a href="{{ route('donor.donations.index') }}#add" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> {{ __('donations.add_donation') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
