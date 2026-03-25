@extends('layouts.charity')

@section('title', __('dashboard.charity_dashboard'))

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1><i class="fas fa-tachometer-alt me-2"></i>{{ __('dashboard.charity_dashboard') }}</h1>
</div>

{{-- Welcome Card --}}
<div class="card mb-4">
    <div class="card-body d-flex align-items-center">
        <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
            <i class="fas fa-building-ngo fa-2x text-success"></i>
        </div>
        <div>
            <h5 class="mb-1">{{ __('general.welcome') }}, {{ auth()->user()->name }}!</h5>
            <p class="text-muted mb-0">{{ __('dashboard.charity_dashboard') }} — Rebite</p>
        </div>
    </div>
</div>

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-4">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                    <i class="fas fa-search fa-lg text-success"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ $availableDonations ?? 0 }}</h3>
                    <small class="text-muted">{{ __('dashboard.available_donations') }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-4">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                    <i class="fas fa-clipboard-list fa-lg text-primary"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ $myRequestsCount ?? 0 }}</h3>
                    <small class="text-muted">{{ __('dashboard.my_requests') }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-4">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                    <i class="fas fa-check fa-lg text-info"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ $approvedRequests ?? 0 }}</h3>
                    <small class="text-muted">{{ __('general.approved') }}</small>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Recent Requests --}}
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="fas fa-history me-2"></i>{{ __('dashboard.my_requests') }}</h6>
        <a href="{{ route('charity.my-requests') }}" class="btn btn-sm btn-outline-primary">
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
                        <th>{{ __('Created At') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentRequests ?? [] as $request)
                        <tr>
                            <td>{{ $request->donation->food_type ?? '—' }}</td>
                            <td>{{ $request->donation->quantity ?? '—' }} {{ __('donations.' . ($request->donation->quantity_unit ?? 'kg')) }}</td>
                            <td><x-status-badge :status="$request->status" /></td>
                            <td>{{ $request->created_at?->format('M d, Y H:i') ?? '—' }}</td>
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
@endsection
