@extends('layouts.admin')

@section('title', __('Donation Requests'))

@section('content')
<div class="page-header d-flex flex-wrap align-items-center justify-content-between gap-2">
    <h1><i class="fas fa-clipboard-check me-2"></i>{{ __('Donation Requests') }}</h1>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="donation-requests-table" class="table table-hover w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>{{ __('Charity') }}</th>
                        <th>{{ __('Donation') }}</th>
                        <th>{{ __('Message') }}</th>
                        <th>{{ __('donations.status') }}</th>
                        <th>{{ __('Created At') }}</th>
                        <th>{{ __('general.actions') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.routes = {
        requestsDatatable: '{{ route("admin.donation-requests.datatable") }}',
        requestsApprove: '{{ route("admin.donation-requests.approve", ":id") }}',
        requestsReject: '{{ route("admin.donation-requests.reject", ":id") }}'
    };

    $(document).ready(function() {
        let table = initDataTable('donation-requests-table', window.routes.requestsDatatable, [
            { data: 'id', name: 'id' },
            { data: 'charity_name', name: 'charity.name', orderable: false },
            { data: 'donation_food', name: 'donation.food_type', orderable: false },
            { data: 'message', name: 'message' },
            { data: 'status', name: 'status' },
            { data: 'created_at', name: 'created_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ]);

        $(document).on('click', '.btn-approve-request', function() {
            let id = $(this).data('id');
            confirmAction({
                title: 'Approve this request?',
                text: 'The charity will be able to take this donation.',
                method: 'POST',
                url: window.routes.requestsApprove.replace(':id', id),
                onSuccess: function() { table.ajax.reload(); }
            });
        });

        $(document).on('click', '.btn-reject-request', function() {
            let id = $(this).data('id');
            confirmAction({
                title: 'Reject this request?',
                text: 'The charity request will be rejected.',
                icon: 'warning',
                method: 'POST',
                url: window.routes.requestsReject.replace(':id', id),
                onSuccess: function() { table.ajax.reload(); }
            });
        });
    });
</script>
@endpush
