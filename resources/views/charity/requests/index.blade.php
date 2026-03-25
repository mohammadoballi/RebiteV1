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

@push('scripts')
<script>
    window.routes = {
        myRequestsDatatable: '{{ route("charity.my-requests.datatable") }}'
    };
</script>
<script src="{{ asset('js/charity/donations.js') }}"></script>
@endpush
