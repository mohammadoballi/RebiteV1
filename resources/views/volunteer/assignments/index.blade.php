@extends('layouts.volunteer')

@section('title', __('dashboard.my_assignments'))

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1><i class="fas fa-truck me-2 text-success"></i>{{ __('dashboard.my_assignments') }}</h1>
</div>

<div class="card">
    <div class="card-body">
        <x-datatable id="assignments-table" :columns="[
            'ID',
            'Food Type',
            'Type',
            'Status',
            'Pickup Time',
            'Delivered At',
            'Actions'
        ]" />
    </div>
</div>
@endsection

@section('modals')
<x-modal id="viewAssignmentModal" :title="__('Assignment Details')">
    <input type="hidden" id="current-assignment-id">

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label fw-semibold text-muted">{{ __('Food Type') }}</label>
            <p class="mb-0" id="assign-food-type">-</p>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold text-muted">{{ __('Quantity') }}</label>
            <p class="mb-0" id="assign-quantity">-</p>
        </div>
        <div class="col-md-12">
            <label class="form-label fw-semibold text-muted">{{ __('Pickup Address') }}</label>
            <p class="mb-0" id="assign-address">-</p>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold text-muted">{{ __('Type') }}</label>
            <p class="mb-0" id="assign-type">-</p>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold text-muted">{{ __('general.status') }}</label>
            <p class="mb-0" id="assign-status">-</p>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold text-muted">{{ __('Pickup Time') }}</label>
            <p class="mb-0" id="assign-pickup-at">-</p>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold text-muted">{{ __('Delivered At') }}</label>
            <p class="mb-0" id="assign-delivered-at">-</p>
        </div>
        <div class="col-md-12">
            <label class="form-label fw-semibold text-muted">{{ __('Notes') }}</label>
            <p class="mb-0" id="assign-notes">-</p>
        </div>
    </div>

    <hr>

    <div class="action-buttons d-flex gap-2 justify-content-end">
        <button type="button" id="btn-accept-assignment" class="btn btn-success" style="display:none;">
            <i class="fas fa-check me-1"></i> {{ __('Accept') }}
        </button>
        <button type="button" id="btn-pickup-assignment" class="btn btn-primary" style="display:none;">
            <i class="fas fa-box me-1"></i> {{ __('Mark Picked Up') }}
        </button>
        <button type="button" id="btn-deliver-assignment" class="btn btn-info text-white" style="display:none;">
            <i class="fas fa-check-double me-1"></i> {{ __('Mark Delivered') }}
        </button>
    </div>
</x-modal>
@endsection

@push('scripts')
<script>
    window.routes = {
        assignmentsDatatable: '{{ route("volunteer.assignments.datatable") }}',
        assignmentsShow: '{{ route("volunteer.assignments.show", ":id") }}',
        assignmentsAccept: '{{ route("volunteer.assignments.accept", ":id") }}',
        assignmentsPickup: '{{ route("volunteer.assignments.pickup", ":id") }}',
        assignmentsDeliver: '{{ route("volunteer.assignments.deliver", ":id") }}'
    };
</script>
<script src="{{ asset('js/volunteer/assignments.js') }}"></script>
@endpush
