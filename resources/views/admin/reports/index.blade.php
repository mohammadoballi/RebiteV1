@extends('layouts.admin')

@section('title', __('Reports & Analytics'))

@section('content')
<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <h1><i class="fas fa-chart-bar me-2"></i>{{ __('Reports & Analytics') }}</h1>
    <span class="text-muted small"><i class="fas fa-clock me-1"></i>{{ __('Last updated') }}: {{ now()->format('M d, Y H:i') }}</span>
</div>

{{-- Overview Stats Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width:48px;height:48px;background:rgba(13,110,253,.12)">
                    <i class="fas fa-users fa-lg text-primary"></i>
                </div>
                <h3 class="fw-bold mb-0">{{ $data['user_stats']['total'] }}</h3>
                <small class="text-muted">{{ __('Total Users') }}</small>
                <div class="mt-1">
                    <span class="badge bg-success">{{ $data['user_stats']['approved'] }} {{ __('general.approved') }}</span>
                    <span class="badge bg-warning text-dark">{{ $data['user_stats']['pending'] }} {{ __('general.pending') }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width:48px;height:48px;background:rgba(25,135,84,.12)">
                    <i class="fas fa-hand-holding-heart fa-lg text-success"></i>
                </div>
                <h3 class="fw-bold mb-0">{{ $data['donation_stats']['total'] }}</h3>
                <small class="text-muted">{{ __('Total Donations') }}</small>
                <div class="mt-1">
                    <span class="badge bg-success">{{ $data['donation_stats']['completed'] }} {{ __('donations.completed') }}</span>
                    <span class="badge bg-warning text-dark">{{ $data['donation_stats']['pending'] }} {{ __('donations.pending') }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width:48px;height:48px;background:rgba(13,202,240,.12)">
                    <i class="fas fa-truck fa-lg" style="color:#0dcaf0"></i>
                </div>
                <h3 class="fw-bold mb-0">{{ $data['delivery_stats']['total'] }}</h3>
                <small class="text-muted">{{ __('Total Deliveries') }}</small>
                <div class="mt-1">
                    <span class="badge bg-success">{{ $data['delivery_stats']['completed'] }} {{ __('donations.completed') }}</span>
                    <span class="badge bg-info">{{ $data['delivery_stats']['external'] }} {{ __('External') }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width:48px;height:48px;background:rgba(255,193,7,.12)">
                    <i class="fas fa-weight-hanging fa-lg text-warning"></i>
                </div>
                <h3 class="fw-bold mb-0">{{ $data['food_saved']['formatted'] }}</h3>
                <small class="text-muted">{{ __('Food Saved') }}</small>
                <div class="mt-1">
                    <span class="badge bg-light text-dark border">{{ $data['food_saved']['items_count'] }} {{ __('items') }}</span>
                    <span class="badge bg-warning text-dark"><i class="fas fa-star me-1"></i>{{ $data['average_rating'] }}/5</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Charts Row 1: Monthly + Status --}}
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold border-0">
                <i class="fas fa-chart-line me-1 text-success"></i> {{ __('Monthly Donations Trend') }}
            </div>
            <div class="card-body">
                <canvas id="monthlyTrendChart" height="300"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold border-0">
                <i class="fas fa-chart-pie me-1 text-success"></i> {{ __('Donations by Status') }}
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="donationStatusChart" height="280"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Charts Row 2: Users by Role + Food Types --}}
<div class="row g-3 mb-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold border-0">
                <i class="fas fa-user-tag me-1 text-success"></i> {{ __('Users by Role') }}
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="usersByRoleChart" height="280"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold border-0">
                <i class="fas fa-utensils me-1 text-success"></i> {{ __('Top Food Types') }}
            </div>
            <div class="card-body">
                <canvas id="foodTypesChart" height="280"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold border-0">
                <i class="fas fa-clipboard-list me-1 text-success"></i> {{ __('Requests & Deliveries') }}
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h6 class="fw-bold small text-muted mb-2">{{ __('Charity Requests') }}</h6>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small">{{ __('general.pending') }}</span>
                        <span class="badge bg-warning text-dark">{{ $data['request_stats']['pending'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small">{{ __('general.approved') }}</span>
                        <span class="badge bg-success">{{ $data['request_stats']['approved'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small">{{ __('general.rejected') }}</span>
                        <span class="badge bg-danger">{{ $data['request_stats']['rejected'] }}</span>
                    </div>
                    @if($data['request_stats']['total'] > 0)
                    <div class="progress mt-2" style="height:6px">
                        <div class="progress-bar bg-success" style="width:{{ ($data['request_stats']['approved'] / $data['request_stats']['total']) * 100 }}%"></div>
                        <div class="progress-bar bg-warning" style="width:{{ ($data['request_stats']['pending'] / $data['request_stats']['total']) * 100 }}%"></div>
                        <div class="progress-bar bg-danger" style="width:{{ ($data['request_stats']['rejected'] / $data['request_stats']['total']) * 100 }}%"></div>
                    </div>
                    @endif
                </div>
                <hr>
                <div>
                    <h6 class="fw-bold small text-muted mb-2">{{ __('Delivery Assignments') }}</h6>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small">{{ __('general.pending') }}</span>
                        <span class="badge bg-warning text-dark">{{ $data['delivery_stats']['pending'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small">{{ __('In Progress') }}</span>
                        <span class="badge bg-info">{{ $data['delivery_stats']['in_progress'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small">{{ __('donations.completed') }}</span>
                        <span class="badge bg-success">{{ $data['delivery_stats']['completed'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small">{{ __('donations.cancelled') }}</span>
                        <span class="badge bg-danger">{{ $data['delivery_stats']['cancelled'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Leaderboard Tables --}}
<div class="row g-3 mb-4">
    {{-- Top Donors --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold border-0">
                <i class="fas fa-trophy text-warning me-1"></i> {{ __('Top Donors') }}
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>{{ __('users.name') }}</th>
                                <th class="text-center">{{ __('donations.title') }}</th>
                                <th class="text-end">{{ __('donations.quantity') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data['top_donors'] as $i => $donor)
                            <tr>
                                <td>
                                    @if($i < 3)
                                        <span class="badge bg-{{ ['warning','secondary','info'][$i] }} rounded-pill">{{ $i + 1 }}</span>
                                    @else
                                        {{ $i + 1 }}
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $donor['name'] }}</strong>
                                    @if($donor['city'])
                                        <br><small class="text-muted">{{ $donor['city'] }}</small>
                                    @endif
                                </td>
                                <td class="text-center"><span class="badge bg-success">{{ $donor['donations_count'] }}</span></td>
                                <td class="text-end">{{ number_format($donor['total_quantity'] ?? 0) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">{{ __('general.no_data') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Top Charities --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold border-0">
                <i class="fas fa-building text-primary me-1"></i> {{ __('Top Charities') }}
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>{{ __('users.name') }}</th>
                                <th class="text-center">{{ __('Requests') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data['top_charities'] as $i => $ch)
                            <tr>
                                <td>
                                    @if($i < 3)
                                        <span class="badge bg-{{ ['warning','secondary','info'][$i] }} rounded-pill">{{ $i + 1 }}</span>
                                    @else
                                        {{ $i + 1 }}
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $ch['name'] }}</strong>
                                    @if($ch['organization_name'])
                                        <br><small class="text-muted">{{ $ch['organization_name'] }}</small>
                                    @endif
                                </td>
                                <td class="text-center"><span class="badge bg-primary">{{ $ch['requests_count'] }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">{{ __('general.no_data') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Top Volunteers --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white fw-semibold border-0">
                <i class="fas fa-medal text-success me-1"></i> {{ __('Top Volunteers') }}
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>{{ __('users.name') }}</th>
                                <th class="text-center">{{ __('Type') }}</th>
                                <th class="text-center">{{ __('donations.completed') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data['top_volunteers'] as $i => $vol)
                            <tr>
                                <td>
                                    @if($i < 3)
                                        <span class="badge bg-{{ ['warning','secondary','info'][$i] }} rounded-pill">{{ $i + 1 }}</span>
                                    @else
                                        {{ $i + 1 }}
                                    @endif
                                </td>
                                <td><strong>{{ $vol['name'] }}</strong></td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $vol['role_type'] === 'delivery' ? 'info' : 'secondary' }}">
                                        {{ ucfirst($vol['role_type'] ?? '-') }}
                                    </span>
                                </td>
                                <td class="text-center"><span class="badge bg-success">{{ $vol['completed_count'] }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">{{ __('general.no_data') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const colors = {
        green: '#198754', greenLight: '#20c997', blue: '#0d6efd', cyan: '#0dcaf0',
        yellow: '#ffc107', red: '#dc3545', purple: '#6f42c1', orange: '#fd7e14',
        teal: '#20c997', gray: '#6c757d'
    };
    const gridColor = 'rgba(0,0,0,.06)';

    // Monthly Trend (All vs Completed)
    const monthlyAll = @json($data['monthly_donations']);
    const monthlyCompleted = @json($data['monthly_completed']);
    const allMonths = [...new Set([...Object.keys(monthlyAll), ...Object.keys(monthlyCompleted)])].sort();

    new Chart(document.getElementById('monthlyTrendChart'), {
        type: 'line',
        data: {
            labels: allMonths,
            datasets: [
                {
                    label: 'All Donations',
                    data: allMonths.map(m => monthlyAll[m] || 0),
                    borderColor: colors.green,
                    backgroundColor: 'rgba(25,135,84,.1)',
                    fill: true,
                    tension: 0.3,
                    borderWidth: 2,
                    pointRadius: 4,
                    pointBackgroundColor: colors.green
                },
                {
                    label: 'Completed',
                    data: allMonths.map(m => monthlyCompleted[m] || 0),
                    borderColor: colors.cyan,
                    backgroundColor: 'rgba(13,202,240,.08)',
                    fill: true,
                    tension: 0.3,
                    borderWidth: 2,
                    pointRadius: 4,
                    pointBackgroundColor: colors.cyan
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'top' } },
            scales: {
                y: { beginAtZero: true, grid: { color: gridColor }, ticks: { precision: 0 } },
                x: { grid: { display: false } }
            }
        }
    });

    // Donations by Status
    const statusData = @json($data['donations_by_status']);
    const statusColors = {
        'pending': colors.yellow, 'accepted': colors.blue, 'assigned': colors.purple,
        'in_transit': colors.orange, 'delivered': colors.teal, 'completed': colors.green,
        'cancelled': colors.red
    };

    new Chart(document.getElementById('donationStatusChart'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(statusData).map(s => s.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())),
            datasets: [{
                data: Object.values(statusData),
                backgroundColor: Object.keys(statusData).map(s => statusColors[s] || colors.gray),
                borderWidth: 2,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { padding: 12, usePointStyle: true } } }
        }
    });

    // Users by Role
    const roleData = @json($data['users_by_role']);
    const roleColors = [colors.blue, colors.green, colors.yellow, colors.cyan, colors.purple, colors.orange];

    new Chart(document.getElementById('usersByRoleChart'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(roleData),
            datasets: [{
                data: Object.values(roleData),
                backgroundColor: roleColors.slice(0, Object.keys(roleData).length),
                borderWidth: 2,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { padding: 12, usePointStyle: true } } }
        }
    });

    // Top Food Types (horizontal bar)
    const foodData = @json($data['top_food_types']);

    new Chart(document.getElementById('foodTypesChart'), {
        type: 'bar',
        data: {
            labels: Object.keys(foodData),
            datasets: [{
                label: 'Count',
                data: Object.values(foodData),
                backgroundColor: colors.green,
                borderRadius: 4,
                maxBarThickness: 24
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: {
                x: { beginAtZero: true, grid: { color: gridColor }, ticks: { precision: 0 } },
                y: { grid: { display: false } }
            }
        }
    });
});
</script>
@endpush
