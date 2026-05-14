@extends('layouts.admin')

@section('title', __('Charity Management'))

@section('content')
<div class="page-header d-flex flex-wrap align-items-center justify-content-between gap-2">
    <h1><i class="fas fa-hands-helping me-2"></i>{{ __('Charity Management') }}</h1>
    <p class="text-muted small mb-0">{{ __('Read-only overview of charity claims. Charities accept donations directly without admin approval.') }}</p>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table id="charity-management-table" class="table table-hover w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>{{ __('Charity') }}</th>
                        <th>{{ __('Donation') }} #</th>
                        <th>{{ __('donations.food_type') }}</th>
                        <th>{{ __('Donation status') }}</th>
                        <th>{{ __('Request') }}</th>
                        <th>{{ __('Message') }}</th>
                        <th>{{ __('Created At') }}</th>
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
