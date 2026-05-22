@extends('layouts.admin')

@section('title', __('dashboard.admin_dashboard'))

@section('content')
<div class="page-header d-flex flex-wrap align-items-center justify-content-between gap-2">
    <h1><i class="fas fa-tachometer-alt me-2"></i>{{ __('general.welcome') }}, {{ auth()->user()->name }}!</h1>
    <form method="get" action="{{ route('admin.dashboard') }}" class="d-flex flex-wrap gap-2 align-items-center">
        <label class="small text-muted mb-0">{{ __('dashboard.year') }}</label>
        <input type="number" name="year" class="form-control form-control-sm" style="width:90px" value="{{ $data['revenue_filter_year'] ?? now()->year }}" min="2000" max="2100">
        <label class="small text-muted mb-0">{{ __('dashboard.month') }}</label>
        <input type="number" name="month" class="form-control form-control-sm" style="width:70px" value="{{ $data['revenue_filter_month'] ?? now()->month }}" min="1" max="12">
        <button type="submit" class="btn btn-sm btn-outline-success">{{ __('dashboard.apply') }}</button>
    </form>
</div>

{{-- KPI cards (drill-down) --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-4">
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

    <div class="col-sm-6 col-xl-4">
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

    <div class="col-sm-6 col-xl-4">
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

</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-4">
        <a href="{{ route('admin.donations.index', ['status' => 'accepted']) }}" class="text-decoration-none">
            <div class="card h-100 border-0 shadow-sm rb-kpi-card">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                         style="width:52px;height:52px;background:rgba(13,202,240,.12)">
                        <i class="fas fa-check-double fa-lg" style="color:#0dcaf0"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1 small">{{ __('dashboard.claimed_by_charity') }}</h6>
                        <h3 class="mb-0 fw-bold text-dark">{{ $data['accepted_donations'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-sm-6 col-xl-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-2"
                         style="width:42px;height:42px;background:rgba(13,202,240,.12)">
                        <i class="fas fa-weight-hanging" style="color:#0dcaf0"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0 small">{{ __('dashboard.food_saved') }}</h6>
                        <h5 class="mb-0 fw-bold">{{ $data['food_saved'] ?? '0 items' }}</h5>
                    </div>
                </div>
                <div class="position-relative" style="height: 160px;">
                    <canvas id="foodSavedPieChart"></canvas>
                    <div id="foodSavedEmptyState" class="position-absolute top-50 start-50 translate-middle text-center text-muted small d-none">
                        <i class="fas fa-chart-pie d-block mb-1"></i>{{ __('dashboard.no_food_saved_data') }}
                    </div>
                </div>
                <div class="small mt-2" id="foodSavedLegend"></div>
                <div class="d-flex justify-content-between small text-muted mt-2">
                    <span>{{ __('dashboard.saved_qty') }}: {{ number_format($data['food_saved_total_items'] ?? 0) }} {{ __('items') }}</span>
                    <span>{{ __('dashboard.unit_types') }}: {{ count($data['food_saved_chart'] ?? []) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-4">
        <div class="card h-100 border-0 shadow-sm border-success border-opacity-50">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                     style="width:52px;height:52px;background:rgba(25,135,84,.15)">
                    <i class="fas fa-dollar-sign fa-lg text-success"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 small">{{ __('dashboard.subscription_revenue_month') }}</h6>
                    <h3 class="mb-0 fw-bold text-success">{{ $data['subscription_month_formatted'] ?? '$0.00' }}</h3>
                    <small class="text-muted">{{ __('dashboard.all_time') }}: {{ $data['subscription_total_formatted'] ?? '$0.00' }}</small>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
.rb-kpi-card { transition: transform .15s ease, box-shadow .15s ease; }
a:hover .rb-kpi-card { transform: translateY(-2px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.08) !important; }
</style>

{{-- Charts --}}
<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="fas fa-chart-bar me-1 text-success"></i> {{ __('dashboard.subscription_revenue') }} (USD)
            </div>
            <div class="card-body">
                <canvas id="subscriptionRevenueChart" height="220"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="fas fa-chart-pie me-1 text-success"></i> {{ __('dashboard.users_by_role') }}
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
                <i class="fas fa-chart-bar me-1 text-success"></i> {{ __('dashboard.total_donations') }} — {{ __('dashboard.monthly') }}
            </div>
            <div class="card-body">
                <canvas id="monthlyDonationsChart" height="260"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@php
    $monthlyDonations = $data['monthly_donations'] ?? [];
    $monthlySubscriptionCents = $data['monthly_subscription_cents'] ?? [];
    $usersByRole = $data['users_by_role'] ?? [];
    $foodSavedChart = $data['food_saved_chart'] ?? [];
@endphp
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const green      = '#198754';
    const greenLight = '#28a745';
    const gridColor  = 'rgba(0,0,0,.06)';

    const monthlyData = @json($monthlyDonations);
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

    const subData = @json($monthlySubscriptionCents);
    const subLabels = Object.keys(subData);
    const subValues = Object.values(subData).map(c => Math.round(c) / 100);

    new Chart(document.getElementById('subscriptionRevenueChart'), {
        type: 'line',
        data: {
            labels: subLabels,
            datasets: [{
                label: '{{ __("dashboard.revenue_usd") }}',
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

    const roleData   = @json($usersByRole);
    const roleLabels = Object.keys(roleData);
    const roleValues = Object.values(roleData);
    const roleColors = ['#0d6efd', '#198754', '#ffc107', '#0dcaf0', '#6f42c1', '#fd7e14'];
    const unitLabels = {
        kg: @json(__('donations.kg')),
        pieces: @json(__('donations.pieces')),
        boxes: @json(__('donations.boxes')),
        bags: @json(__('donations.bags')),
        plates: @json(__('donations.plates'))
    };

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

    const foodSavedData = @json($foodSavedChart);

    const foodSavedCanvas = document.getElementById('foodSavedPieChart');
    const foodSavedEmptyState = document.getElementById('foodSavedEmptyState');
    const foodSavedLegend = document.getElementById('foodSavedLegend');
    const unitRows = Array.isArray(foodSavedData) ? foodSavedData : [];
    const pieLabels = unitRows.map(function(row) {
        const key = String(row.unit || '').toLowerCase();
        return unitLabels[key] || key.toUpperCase() || '-';
    });
    const pieValues = unitRows.map(function(row) { return Number(row.quantity || 0); });
    const pieCounts = unitRows.map(function(row) { return Number(row.count || 0); });
    const piePalette = ['#198754', '#0d6efd', '#ffc107', '#0dcaf0', '#6f42c1', '#fd7e14', '#20c997', '#6c757d', '#6610f2', '#d63384'];
    const pieColors = unitRows.map(function(_, idx) { return piePalette[idx % piePalette.length]; });
    const totalQty = pieValues.reduce(function(acc, v) { return acc + v; }, 0);

    if (foodSavedCanvas) {
        if (unitRows.length === 0 || totalQty <= 0) {
            foodSavedCanvas.classList.add('d-none');
            foodSavedEmptyState?.classList.remove('d-none');
            if (foodSavedLegend) {
                foodSavedLegend.innerHTML = '';
            }
        } else {
            const foodSavedChart = new Chart(foodSavedCanvas, {
                type: 'pie',
                data: {
                    labels: pieLabels,
                    datasets: [{
                        data: pieValues,
                        backgroundColor: pieColors,
                        borderColor: '#ffffff',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const val = Number(context.raw || 0);
                                    const count = Number(pieCounts[context.dataIndex] || 0);
                                    const pct = totalQty > 0 ? ((val / totalQty) * 100).toFixed(1) : '0.0';
                                    return context.label + ': ' + val.toFixed(1) + ' (' + count + ' {{ __('items') }}, ' + pct + '%)';
                                }
                            }
                        }
                    }
                }
            });

            if (foodSavedLegend) {
                const labels = foodSavedChart.data.labels || [];
                const values = foodSavedChart.data.datasets[0].data || [];
                const colors = foodSavedChart.data.datasets[0].backgroundColor || [];
                foodSavedLegend.innerHTML = labels.map(function(label, idx) {
                    const value = Number(values[idx] || 0);
                    const count = Number(pieCounts[idx] || 0);
                    const pct = totalQty > 0 ? ((value / totalQty) * 100).toFixed(1) : '0.0';
                    return '<div class="d-flex align-items-center justify-content-between mb-1">'
                        + '<span><i class="fas fa-circle me-1" style="color:' + colors[idx] + '"></i>' + label + '</span>'
                        + '<span>' + value.toFixed(1) + ' · ' + count + ' · ' + pct + '%</span>'
                        + '</div>';
                }).join('');
            }
        }
    }
});
</script>
@endpush
