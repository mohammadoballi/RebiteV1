@extends('layouts.admin')

@section('title', __('dashboard.admin_dashboard'))

@section('content')
<div class="page-header d-flex align-items-center justify-content-between">
    <h1><i class="fas fa-tachometer-alt me-2"></i>{{ __('general.welcome') }}, {{ auth()->user()->name }}!</h1>
</div>

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                     style="width:52px;height:52px;background:rgba(13,110,253,.12)">
                    <i class="fas fa-users fa-lg text-primary"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 small">{{ __('dashboard.total_users') }}</h6>
                    <h3 class="mb-0 fw-bold">{{ $data['total_users'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                     style="width:52px;height:52px;background:rgba(25,135,84,.12)">
                    <i class="fas fa-hand-holding-heart fa-lg" style="color:#198754"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 small">{{ __('dashboard.total_donations') }}</h6>
                    <h3 class="mb-0 fw-bold">{{ $data['total_donations'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                     style="width:52px;height:52px;background:rgba(255,193,7,.12)">
                    <i class="fas fa-clock fa-lg text-warning"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 small">{{ __('dashboard.pending_approvals') }}</h6>
                    <h3 class="mb-0 fw-bold">{{ $data['pending_approvals'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card h-100">
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
</div>

{{-- Charts --}}
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold">
                <i class="fas fa-chart-bar me-1 text-success"></i> {{ __('dashboard.total_donations') }} — {{ __('Monthly') }}
            </div>
            <div class="card-body">
                <canvas id="monthlyDonationsChart" height="280"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header bg-white fw-semibold">
                <i class="fas fa-chart-pie me-1 text-success"></i> {{ __('Users by Role') }}
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="usersByRoleChart" height="260"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Recent Donations --}}
<div class="card">
    <div class="card-header bg-white fw-semibold">
        <i class="fas fa-history me-1 text-success"></i> {{ __('dashboard.recent_donations') }}
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>{{ __('donations.donor') }}</th>
                        <th>{{ __('donations.food_type') }}</th>
                        <th>{{ __('donations.quantity') }}</th>
                        <th>{{ __('donations.status') }}</th>
                        <th>{{ __('donations.pickup_time') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data['recent_donations'] ?? [] as $donation)
                        <tr>
                            <td>{{ $donation->id }}</td>
                            <td>{{ $donation->donor->name ?? '-' }}</td>
                            <td>{{ $donation->food_type }}</td>
                            <td>{{ $donation->quantity }} {{ $donation->quantity_unit ?? '' }}</td>
                            <td>
                                @php
                                    $statusColors = [
                                        'pending'   => 'warning',
                                        'accepted'  => 'info',
                                        'assigned'  => 'primary',
                                        'in_transit' => 'secondary',
                                        'delivered' => 'success',
                                        'completed' => 'success',
                                        'cancelled' => 'danger',
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$donation->status] ?? 'secondary' }}">
                                    {{ __('donations.' . $donation->status) }}
                                </span>
                            </td>
                            <td>{{ $donation->pickup_time ? \Carbon\Carbon::parse($donation->pickup_time)->format('M d, Y H:i') : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">{{ __('general.no_data') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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

    // Monthly Donations Bar Chart
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

    // Users by Role Pie Chart
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
                legend: { position: 'bottom', labels: { padding: 16 } }
            }
        }
    });
});
</script>
@endpush
