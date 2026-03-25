@extends('layouts.volunteer')

@section('title', __('dashboard.volunteer_dashboard'))

@section('content')
@php
    $user = auth()->user();
    $assignments = $user->assignments;
    $totalCount = $assignments->count();
    $pendingCount = $assignments->where('status', 'pending')->count();
    $inProgressCount = $assignments->whereIn('status', ['accepted', 'in_transit', 'in_progress'])->count();
    $completedCount = $assignments->whereIn('status', ['delivered', 'completed'])->count();
    $recentAssignments = $user->assignments()->with('donation')->latest()->take(5)->get();
@endphp

<div class="page-header d-flex justify-content-between align-items-center">
    <h1><i class="fas fa-tachometer-alt me-2 text-success"></i>{{ __('dashboard.volunteer_dashboard') }}</h1>
</div>

{{-- Welcome Card --}}
<div class="card mb-4 border-0" style="background: linear-gradient(135deg, #198754 0%, #20c997 100%);">
    <div class="card-body text-white py-4">
        <div class="row align-items-center">
            <div class="col-auto">
                @if($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="rounded-circle" width="64" height="64" style="object-fit: cover;">
                @else
                    <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                        <i class="fas fa-user fa-2x text-white"></i>
                    </div>
                @endif
            </div>
            <div class="col">
                <h3 class="mb-1">{{ __('general.welcome') }}, {{ $user->name }}!</h3>
                <p class="mb-0 opacity-75">{{ __('dashboard.volunteer_dashboard') }} &mdash; {{ now()->translatedFormat('l, d F Y') }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 56px; height: 56px; background: rgba(25,135,84,.1);">
                    <i class="fas fa-tasks fa-lg text-success"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold text-success">{{ $totalCount }}</h3>
                    <small class="text-muted">{{ __('Total Assignments') }}</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 56px; height: 56px; background: rgba(255,193,7,.1);">
                    <i class="fas fa-clock fa-lg text-warning"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold text-warning">{{ $pendingCount }}</h3>
                    <small class="text-muted">{{ __('Pending') }}</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 56px; height: 56px; background: rgba(13,110,253,.1);">
                    <i class="fas fa-spinner fa-lg text-primary"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold text-primary">{{ $inProgressCount }}</h3>
                    <small class="text-muted">{{ __('In Progress') }}</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 56px; height: 56px; background: rgba(32,201,151,.1);">
                    <i class="fas fa-check-double fa-lg" style="color: #20c997;"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold" style="color: #20c997;">{{ $completedCount }}</h3>
                    <small class="text-muted">{{ __('Completed') }}</small>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Recent Assignments --}}
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-history me-2 text-success"></i>{{ __('Recent Assignments') }}</h5>
        <a href="{{ route('volunteer.assignments.index') }}" class="btn btn-sm btn-outline-success">
            {{ __('general.view') }} {{ __('All') }} <i class="fas fa-arrow-right ms-1"></i>
        </a>
    </div>
    <div class="card-body p-0">
        @if($recentAssignments->isEmpty())
            <div class="text-center text-muted py-5">
                <i class="fas fa-inbox fa-3x mb-3 d-block opacity-50"></i>
                <p>{{ __('general.no_data') }}</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('Food Type') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('general.status') }}</th>
                            <th>{{ __('Date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentAssignments as $assignment)
                            <tr>
                                <td>{{ $assignment->id }}</td>
                                <td>{{ $assignment->donation->food_type ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ $assignment->assignment_type === 'delivery' ? 'bg-info' : 'bg-secondary' }}">
                                        {{ ucfirst($assignment->assignment_type) }}
                                    </span>
                                </td>
                                <td>
                                    @switch($assignment->status)
                                        @case('pending')
                                            <span class="badge bg-warning">{{ __('Pending') }}</span>
                                            @break
                                        @case('accepted')
                                            <span class="badge bg-info">{{ __('Accepted') }}</span>
                                            @break
                                        @case('in_transit')
                                        @case('in_progress')
                                            <span class="badge bg-primary">{{ __('In Progress') }}</span>
                                            @break
                                        @case('delivered')
                                        @case('completed')
                                            <span class="badge bg-success">{{ __('Completed') }}</span>
                                            @break
                                        @case('cancelled')
                                            <span class="badge bg-danger">{{ __('Cancelled') }}</span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ ucfirst($assignment->status) }}</span>
                                    @endswitch
                                </td>
                                <td>{{ $assignment->created_at->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
