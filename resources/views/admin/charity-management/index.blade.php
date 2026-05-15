@extends('layouts.admin')

@section('title', __('navigation.charity_management'))

@section('content')
<div class="page-header d-flex flex-wrap align-items-center justify-content-between gap-2">
    <h1><i class="fas fa-hands-helping me-2"></i>{{ __('navigation.charity_management') }}</h1>
    <p class="text-muted small mb-0">{{ __('charity_management.description') }}</p>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table id="charity-management-table" class="table table-hover w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>{{ __('charity_management.charity') }}</th>
                        <th>{{ __('charity_management.donation') }} #</th>
                        <th>{{ __('donations.food_type') }}</th>
                        <th>{{ __('charity_management.donation_status') }}</th>
                        <th>{{ __('charity_management.request') }}</th>
                        <th>{{ __('charity_management.message') }}</th>
                        <th>{{ __('charity_management.created_at') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        initDataTable('charity-management-table', '{{ route("admin.charity-management.datatable") }}', [
            { data: 'id', name: 'id' },
            { data: 'charity_name', name: 'charity.name', orderable: false },
            { data: 'donation_id', name: 'donation_id' },
            { data: 'donation_food', name: 'donation.food_type', orderable: false },
            { data: 'donation_status', name: 'donation.status' },
            { data: 'status_badge', name: 'status', orderable: false, searchable: false },
            { data: 'message', name: 'message' },
            { data: 'created_at', name: 'created_at' },
        ]);
    });
</script>
@endpush
