@extends('layouts.admin')

@section('title', __('donations.title'))

@section('content')
<div class="page-header d-flex flex-wrap align-items-center justify-content-between gap-2">
    <h1><i class="fas fa-hand-holding-heart me-2"></i>{{ __('Manage Donations') }}</h1>
    <div class="d-flex align-items-center gap-2">
        <label for="donationStatusFilter" class="form-label mb-0 small text-muted">{{ __('donations.status') }}:</label>
        <select id="donationStatusFilter" class="form-select form-select-sm" style="width:auto">
            <option value="">{{ __('All') }}</option>
            <option value="pending">{{ __('donations.pending') }}</option>
            <option value="accepted">{{ __('donations.accepted') }}</option>
            <option value="assigned">{{ __('donations.assigned') }}</option>
            <option value="in_transit">{{ __('donations.in_transit') }}</option>
            <option value="delivered">{{ __('donations.delivered') }}</option>
            <option value="completed">{{ __('donations.completed') }}</option>
            <option value="cancelled">{{ __('donations.cancelled') }}</option>
        </select>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="donations-table" class="table table-hover w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>{{ __('donations.donor') }}</th>
                        <th>{{ __('Food Items') }}</th>
                        <th>{{ __('donations.quantity') }}</th>
                        <th>{{ __('donations.status') }}</th>
                        <th>{{ __('donations.pickup_time') }}</th>
                        <th>{{ __('Created At') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('modals')
{{-- View Donation Modal --}}
<div class="modal fade" id="viewDonationModal" tabindex="-1" aria-labelledby="viewDonationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success bg-opacity-10 border-success border-opacity-25">
                <h5 class="modal-title" id="viewDonationModalLabel">
                    <i class="fas fa-hand-holding-heart me-1"></i> {{ __('Donation Details') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('general.close') }}"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-0">{{ __('donations.donor') }}</label>
                        <p class="fw-semibold mb-2" id="donation-donor">-</p>
                    </div>
                    <div class="col-12">
                        <label class="form-label text-muted small mb-0">{{ __('Food Items') }}</label>
                        <div id="donation-items-list" class="fw-semibold mb-2">-</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-0">{{ __('donations.status') }}</label>
                        <p class="mb-2"><span class="badge" id="donation-status-badge">-</span></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-0">{{ __('City') }} / {{ __('Town') }}</label>
                        <p class="fw-semibold mb-2" id="donation-city-town">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-0">{{ __('Food category') }}</label>
                        <p class="fw-semibold mb-2" id="donation-food-category">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-0">{{ __('donations.pickup_address') }}</label>
                        <p class="fw-semibold mb-2" id="donation-pickup-address">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-0">{{ __('donations.pickup_time') }}</label>
                        <p class="fw-semibold mb-2" id="donation-pickup-time">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-0">{{ __('donations.expiry_time') }}</label>
                        <p class="fw-semibold mb-2" id="donation-expiry-time">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small mb-0">{{ __('Created At') }}</label>
                        <p class="fw-semibold mb-2" id="donation-created-at">-</p>
                    </div>
                    <div class="col-12">
                        <label class="form-label text-muted small mb-0">{{ __('donations.description') }}</label>
                        <p class="fw-semibold mb-2" id="donation-description">-</p>
                    </div>
                    <div class="col-12">
                        <label class="form-label text-muted small mb-0">{{ __('donations.notes') }}</label>
                        <p class="fw-semibold mb-2" id="donation-notes">-</p>
                    </div>
                    <div class="col-12" id="donation-image-row" style="display:none">
                        <label class="form-label text-muted small mb-0">{{ __('donations.image') }}</label>
                        <div>
                            <img id="donation-image" src="" alt="" class="img-thumbnail mt-1" style="max-height:200px">
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('general.close') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.routes = {
        donationsDatatable: '{{ route("admin.donations.datatable") }}',
        donationsShow:      '{{ route("admin.donations.show", ":id") }}'
    };
    window.unitLabels = {
        kg: @json(__('donations.kg')),
        pieces: @json(__('donations.pieces')),
        boxes: @json(__('donations.boxes')),
        bags: @json(__('donations.bags')),
        plates: @json(__('donations.plates'))
    };
</script>
<script src="{{ asset('js/admin/donations.js') }}"></script>
@endpush
