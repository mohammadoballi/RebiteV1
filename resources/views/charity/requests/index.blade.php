@extends('layouts.charity')

@section('title', __('dashboard.my_requests'))

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1><i class="fas fa-clipboard-list me-2"></i>{{ __('dashboard.my_requests') }}</h1>
</div>

<div class="card">
    <div class="card-body">
        <x-datatable id="my-requests-table" :columns="[
            '#',
            __('donations.food_type'),
            __('donations.quantity'),
            __('donations.status'),
            __('Created At'),
            __('general.actions')
        ]" />
    </div>
</div>
@endsection

@section('modals')
{{-- View Completed Donation - Rate Modal --}}
<div class="modal fade" id="viewRequestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header" style="background: linear-gradient(135deg, #198754, #20c997); color: white;">
                <h5 class="modal-title"><i class="fas fa-eye me-2"></i>{{ __('Donation Details') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="req-donation-details"></div>

                <div id="req-rate-section" style="display:none">
                    <hr>
                    <h6 class="fw-bold"><i class="fas fa-star me-1 text-warning"></i> {{ __('Rate') }}</h6>
                    <div id="req-rateable-list"></div>
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
        myRequestsDatatable: '{{ route("charity.my-requests.datatable") }}',
        donationsShow: '{{ route("charity.donations.show", ":id") }}'
    };
</script>
<script src="{{ asset('js/charity/donations.js') }}"></script>
@endpush
