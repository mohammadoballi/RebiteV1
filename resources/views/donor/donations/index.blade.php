@extends('layouts.donor')

@section('title', __('dashboard.my_donations'))

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h1><i class="fas fa-gift me-2"></i>{{ __('dashboard.my_donations') }}</h1>
    <button class="btn btn-success" id="btn-add-donation">
        <i class="fas fa-plus me-1"></i> {{ __('donations.add_donation') }}
    </button>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <x-datatable id="donations-table" :columns="[
            '#',
            __('Food Items'),
            __('Volunteers'),
            __('donations.status'),
            __('donations.pickup_time'),
            __('Created At'),
            __('general.actions')
        ]" />
    </div>
</div>
@endsection

@section('modals')
{{-- Create / Edit Donation Modal --}}
<div class="modal fade" id="donationModal" tabindex="-1" aria-labelledby="donationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header" style="background: linear-gradient(135deg, #198754, #20c997); color: white;">
                <h5 class="modal-title" id="donationModalLabel">{{ __('donations.add_donation') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="donationForm" enctype="multipart/form-data">
                <div class="modal-body">
                    {{-- Food Items Section --}}
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0"><i class="fas fa-utensils me-1 text-success"></i> {{ __('Food Items') }}</h6>
                            <button type="button" class="btn btn-sm btn-outline-success" id="btn-add-item">
                                <i class="fas fa-plus me-1"></i> {{ __('Add Item') }}
                            </button>
                        </div>
                        <div id="items-container">
                            {{-- Item row template will be added by JS --}}
                        </div>
                    </div>

                    <hr>

                    {{-- Donation Details --}}
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="description" class="form-label">{{ __('donations.description') }}</label>
                            <textarea class="form-control" id="description" name="description" rows="2" placeholder="{{ __('General description of the donation...') }}"></textarea>
                        </div>

                        <div class="col-md-8">
                            <label for="pickup_address" class="form-label">{{ __('donations.pickup_address') }} <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="pickup_address" name="pickup_address" rows="2" required></textarea>
                        </div>

                        <div class="col-md-4">
                            <label for="volunteers_needed" class="form-label">{{ __('Volunteers Needed') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-users"></i></span>
                                <input type="number" class="form-control" id="volunteers_needed" name="volunteers_needed" min="1" max="50" value="1" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="pickup_time" class="form-label">{{ __('donations.pickup_time') }} <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="pickup_time" name="pickup_time" required>
                        </div>

                        <div class="col-md-6">
                            <label for="expiry_time" class="form-label">{{ __('donations.expiry_time') }}</label>
                            <input type="datetime-local" class="form-control" id="expiry_time" name="expiry_time">
                        </div>

                        <div class="col-12">
                            <label for="notes" class="form-label">{{ __('donations.notes') }}</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                        </div>

                        <div class="col-12">
                            <label for="image" class="form-label">{{ __('donations.image') }}</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('general.cancel') }}</button>
                    <button type="button" class="btn btn-success" id="btn-save-donation">
                        <i class="fas fa-save me-1"></i> {{ __('general.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- View Donation Modal --}}
<div class="modal fade" id="viewDonationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header" style="background: linear-gradient(135deg, #198754, #20c997); color: white;">
                <h5 class="modal-title"><i class="fas fa-eye me-2"></i>{{ __('Donation Details') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12">
                        <h6 class="fw-bold"><i class="fas fa-utensils me-1 text-success"></i> {{ __('Food Items') }}</h6>
                        <div id="view-items-list"></div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-2 rounded bg-light">
                            <small class="text-muted d-block">{{ __('donations.status') }}</small>
                            <span id="view-status">-</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-2 rounded bg-light">
                            <small class="text-muted d-block">{{ __('Volunteers') }}</small>
                            <span id="view-volunteers">-</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-2 rounded bg-light">
                            <small class="text-muted d-block">{{ __('donations.pickup_time') }}</small>
                            <span id="view-pickup-time">-</span>
                        </div>
                    </div>
                    <div class="col-12">
                        <small class="text-muted d-block">{{ __('donations.pickup_address') }}</small>
                        <span id="view-address">-</span>
                    </div>
                    <div class="col-12" id="view-desc-wrap" style="display:none">
                        <small class="text-muted d-block">{{ __('donations.description') }}</small>
                        <span id="view-description">-</span>
                    </div>
                    <div class="col-12" id="view-notes-wrap" style="display:none">
                        <small class="text-muted d-block">{{ __('donations.notes') }}</small>
                        <span id="view-notes">-</span>
                    </div>
                    <div class="col-12" id="view-image-container" style="display:none">
                        <img id="view-image" src="" class="img-fluid rounded" style="max-height: 250px;">
                    </div>
                    <div class="col-12" id="view-volunteers-section" style="display:none">
                        <hr>
                        <h6 class="fw-bold"><i class="fas fa-users me-1 text-success"></i> {{ __('Assigned Volunteers') }}</h6>
                        <div id="view-volunteers-list"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('general.close') }}</button>
            </div>
        </div>
    </div>
</div>

@include('components.rate-volunteer-modal')
@endsection

@push('scripts')
<script>
    window.routes = {
        donationsDatatable: '{{ route("donor.donations.datatable") }}',
        donationsStore: '{{ route("donor.donations.store") }}',
        donationsShow: '{{ route("donor.donations.show", ":id") }}',
        donationsUpdate: '{{ route("donor.donations.update", ":id") }}',
        donationsDestroy: '{{ route("donor.donations.destroy", ":id") }}'
    };
</script>
<script src="{{ asset('js/donor/donations.js') }}"></script>
@endpush
