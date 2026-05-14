@extends('layouts.admin')

@section('title', __('dashboard.admin_dashboard'))

@section('content')
<div class="page-header d-flex flex-wrap align-items-center justify-content-between gap-2">
    <h1><i class="fas fa-tachometer-alt me-2"></i>{{ __('general.welcome') }}, {{ auth()->user()->name }}!</h1>
    <form method="get" action="{{ route('admin.dashboard') }}" class="d-flex flex-wrap gap-2 align-items-center">
        <label class="small text-muted mb-0">{{ __('Year') }}</label>
        <input type="number" name="year" class="form-control form-control-sm" style="width:90px" value="{{ $data['revenue_filter_year'] ?? now()->year }}" min="2000" max="2100">
        <label class="small text-muted mb-0">{{ __('Month') }}</label>
        <input type="number" name="month" class="form-control form-control-sm" style="width:70px" value="{{ $data['revenue_filter_month'] ?? now()->month }}" min="1" max="12">
        <button type="submit" class="btn btn-sm btn-outline-success">{{ __('Apply') }}</button>
    </form>
</div>

{{-- KPI cards (drill-down) --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <a href="{{ route('admin.users.index') }}" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm rb-kpi-card">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                         style="width:52px;height:52px;background:rgba(13,110,253,.12)">
                        <i class="fas fa-users fa-lg text-primary"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 small">{{ __('dashboard.total_users') }}</h6>
                        <h3 class="mb-0 fw-bold text-dark">{{ $data['total_users'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-sm-6 col-xl-3">
        <a href="{{ route('admin.donations.index') }}" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm rb-kpi-card">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                         style="width:52px;height:52px;background:rgba(25,135,84,.12)">
                        <i class="fas fa-hand-holding-heart fa-lg" style="color:#198754"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 small">{{ __('dashboard.total_donations') }}</h6>
                        <h3 class="mb-0 fw-bold text-dark">{{ $data['total_donations'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-sm-6 col-xl-3">
        <a href="{{ route('admin.users.index', ['status' => 'pending']) }}" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm rb-kpi-card">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                         style="width:52px;height:52px;background:rgba(255,193,7,.12)">
                        <i class="fas fa-clock fa-lg text-warning"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 small">{{ __('dashboard.pending_approvals') }}</h6>
                        <h3 class="mb-0 fw-bold text-dark">{{ $data['pending_approvals'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-sm-6 col-xl-3">
        <a href="{{ route('admin.donations.index', ['status' => 'pending']) }}" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm rb-kpi-card">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                         style="width:52px;height:52px;background:rgba(108,117,125,.12)">
                        <i class="fas fa-store fa-lg text-secondary"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 small">{{ __('Open marketplace donations') }}</h6>
                        <h3 class="mb-0 fw-bold text-dark">{{ $data['pending_donations'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <a href="{{ route('admin.donations.index', ['status' => 'accepted']) }}" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm rb-kpi-card">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                         style="width:52px;height:52px;background:rgba(13,202,240,.12)">
                        <i class="fas fa-check-double fa-lg" style="color:#0dcaf0"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 small">{{ __('Claimed by charity') }}</h6>
                        <h3 class="mb-0 fw-bold text-dark">{{ $data['accepted_donations'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                     style="width:52px;height:52px;background:rgba(13,202,240,.12)">
                    <i class="fas fa-weight-hanging fa-lg" style="color:#0dcaf0"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 small">{{ __('dashboard.food_saved') }}</h6>
                    <h3 class="mb-0 fw-bold">{{ $data['food_saved'] ?? '0 kg' }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card h-100 border-0 shadow-sm border-success border-opacity-50">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                     style="width:52px;height:52px;background:rgba(25,135,84,.15)">
                    <i class="fas fa-dollar-sign fa-lg text-success"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 small">{{ __('Subscription revenue (month)') }}</h6>
                    <h3 class="mb-0 fw-bold text-success">{{ $data['subscription_month_formatted'] ?? '$0.00' }}</h3>
                    <small class="text-muted">{{ __('All time') }}: {{ $data['subscription_total_formatted'] ?? '$0.00' }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <a href="{{ route('admin.charity-management.index') }}" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm rb-kpi-card">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                         style="width:52px;height:52px;background:rgba(111,66,193,.12)">
                        <i class="fas fa-hands-helping fa-lg" style="color:#6f42c1"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 small">{{ __('Charity claims (records)') }}</h6>
                        <h3 class="mb-0 fw-bold text-dark">{{ \App\Models\DonationRequest::count() }}</h3>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<style>
.rb-kpi-card { transition: transform .15s ease, box-shadow .15s ease; }
a:hover .rb-kpi-card { transform: translateY(-2px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.08) !important; }
</style>

{{-- Charts --}}
<div class="row g-3 mb-4">
    <div class="col-lg-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="fas fa-chart-pie me-1 text-success"></i> {{ __('Donations by status') }}
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="donationsByStatusChart" height="220"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="fas fa-chart-bar me-1 text-success"></i> {{ __('Subscription revenue') }} (USD)
            </div>
            <div class="card-body">
                <canvas id="subscriptionRevenueChart" height="220"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="fas fa-chart-pie me-1 text-success"></i> {{ __('Users by Role') }}
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="usersByRoleChart" height="220"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-12">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="fas fa-chart-bar me-1 text-success"></i> {{ __('dashboard.total_donations') }} — {{ __('Monthly') }}
            </div>
            <div class="card-body">
                <canvas id="monthlyDonationsChart" height="260"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const green      = '#198754';
    const greenLight = '#28a745';
    const gridColor  = 'rgba(0,0,0,.06)';

    const monthlyData = @json($data['monthly_donations'] ?? []);
    const monthLabels = Object.keys(monthlyData);
    const monthValues = Object.values(monthlyData);

    new Chart(document.getElementById('monthlyDonationsChart'), {
        type: 'bar',
        data: {
            labels: monthLabels,
            datasets: [{
                label: '{{ __("dashboard.total_donations") }}',
                data: monthValues,
                backgroundColor: green,
                borderRadius: 6,
                maxBarThickness: 40
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: gridColor }, ticks: { precision: 0 } },
                x: { grid: { display: false } }
            }
        }
    });

    const subData = @json($data['monthly_subscription_cents'] ?? []);
    const subLabels = Object.keys(subData);
    const subValues = Object.values(subData).map(c => Math.round(c) / 100);

    new Chart(document.getElementById('subscriptionRevenueChart'), {
        type: 'line',
        data: {
            labels: subLabels,
            datasets: [{
                label: '{{ __("Revenue (USD)") }}',
                data: subValues,
                borderColor: green,
                backgroundColor: 'rgba(25,135,84,.12)',
                fill: true,
                tension: 0.25
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: gridColor } },
                x: { grid: { display: false } }
            }
        }
    });

    const roleData   = @json($data['users_by_role'] ?? []);
    const roleLabels = Object.keys(roleData);
    const roleValues = Object.values(roleData);
    const roleColors = ['#0d6efd', '#198754', '#ffc107', '#0dcaf0', '#6f42c1', '#fd7e14'];

    new Chart(document.getElementById('usersByRoleChart'), {
        type: 'doughnut',
        data: {
            labels: roleLabels,
            datasets: [{
                data: roleValues,
                backgroundColor: roleColors.slice(0, roleLabels.length),
                borderWidth: 2,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { padding: 12, boxWidth: 10 } }
            }
        }
    });

    const dsByStatus = @json($data['donations_by_status'] ?? []);
    const dsLabels = Object.keys(dsByStatus);
    const dsValues = Object.values(dsByStatus);

    new Chart(document.getElementById('donationsByStatusChart'), {
        type: 'doughnut',
        data: {
            labels: dsLabels,
            datasets: [{
                data: dsValues,
                backgroundColor: roleColors.slice(0, dsLabels.length),
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { padding: 10, boxWidth: 10 } }
            }
        }
    });
});
</script>
@endpush
