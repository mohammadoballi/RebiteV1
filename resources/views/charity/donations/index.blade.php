@extends('layouts.charity')

@section('title', __('dashboard.available_donations'))

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h1><i class="fas fa-store me-2"></i>{{ __('dashboard.available_donations') }}</h1>
    <span class="badge bg-success fs-6">{{ $donations->total() }} {{ __('donations.title') }}</span>
</div>

{{-- Filters --}}
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body">
        <form method="GET" action="{{ route('charity.donations.index') }}" id="filterForm">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-semibold"><i class="fas fa-search me-1"></i> {{ __('general.search') }}</label>
                    <input type="text" name="search" class="form-control" placeholder="{{ __('Search food, address...') }}" value="{{ $filters['search'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold"><i class="fas fa-city me-1"></i> {{ __('City') }}</label>
                    <select name="city" class="form-select">
                        <option value="">{{ __('All Cities') }}</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}" {{ ($filters['city'] ?? '') == $city ? 'selected' : '' }}>{{ $city }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold"><i class="fas fa-utensils me-1"></i> {{ __('donations.food_type') }}</label>
                    <input type="text" name="food_type" class="form-control" placeholder="{{ __('e.g. Rice, Bread') }}" value="{{ $filters['food_type'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold"><i class="fas fa-calendar me-1"></i> {{ __('From') }}</label>
                    <input type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold">{{ __('To') }}</label>
                    <input type="date" name="date_to" class="form-control" value="{{ $filters['date_to'] ?? '' }}">
                </div>
                <div class="col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-success flex-grow-1" title="{{ __('general.search') }}"><i class="fas fa-filter"></i></button>
                    <a href="{{ route('charity.donations.index') }}" class="btn btn-outline-secondary" title="{{ __('Reset') }}"><i class="fas fa-times"></i></a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Donation Cards Grid --}}
<div class="row g-4">
    @forelse($donations as $donation)
    <div class="col-sm-6 col-lg-4 col-xl-3">
        <div class="card h-100 border-0 shadow-sm donation-card" style="cursor:pointer; transition: transform .2s, box-shadow .2s;" 
             onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,.12)'"
             onmouseout="this.style.transform=''; this.style.boxShadow=''"
             data-id="{{ $donation->id }}">
            {{-- Image --}}
            <div class="position-relative">
                @if($donation->image)
                    <img src="{{ asset('storage/' . $donation->image) }}" class="card-img-top" alt="{{ $donation->food_type }}" style="height: 180px; object-fit: cover;">
                @else
                    <div class="card-img-top d-flex align-items-center justify-content-center text-white" style="height: 180px; background: linear-gradient(135deg, #198754, #20c997);">
                        <i class="fas fa-utensils fa-3x opacity-75"></i>
                    </div>
                @endif
                {{-- Volunteer badge --}}
                <span class="position-absolute top-0 end-0 m-2 badge {{ $donation->is_full ? 'bg-secondary' : 'bg-success' }} rounded-pill">
                    <i class="fas fa-users me-1"></i>{{ $donation->volunteers_count }}/{{ $donation->volunteers_needed }}
                </span>
                {{-- Pickup time badge --}}
                <span class="position-absolute bottom-0 start-0 m-2 badge bg-dark bg-opacity-75 rounded-pill">
                    <i class="fas fa-clock me-1"></i>{{ $donation->pickup_time ? $donation->pickup_time->format('M d, H:i') : '-' }}
                </span>
            </div>

            <div class="card-body pb-2">
                {{-- Food items tags --}}
                <div class="mb-2">
                    @if($donation->items && $donation->items->count() > 0)
                        @foreach($donation->items->take(3) as $item)
                            <span class="badge bg-light text-dark border me-1 mb-1">
                                {{ $item->food_type }} <small class="text-muted">({{ $item->quantity }} {{ $item->quantity_unit }})</small>
                            </span>
                        @endforeach
                        @if($donation->items->count() > 3)
                            <span class="badge bg-light text-success border mb-1">+{{ $donation->items->count() - 3 }} more</span>
                        @endif
                    @else
                        <span class="badge bg-light text-dark border">{{ $donation->food_type }}</span>
                    @endif
                </div>

                {{-- Location --}}
                <p class="card-text text-muted small mb-1">
                    <i class="fas fa-map-marker-alt text-danger me-1"></i>{{ Str::limit($donation->pickup_address, 40) }}
                </p>

                {{-- Donor --}}
                <p class="card-text small mb-0">
                    <i class="fas fa-user text-success me-1"></i>
                    <strong>{{ $donation->donor->name ?? '-' }}</strong>
                    @if($donation->donor && $donation->donor->city)
                        <span class="text-muted">· {{ $donation->donor->city }}</span>
                    @endif
                </p>
            </div>

            <div class="card-footer bg-transparent border-top-0 pt-0">
                <button class="btn btn-success btn-sm w-100 btn-request-donation" data-id="{{ $donation->id }}">
                    <i class="fas fa-hand-holding-heart me-1"></i> {{ __('donations.request_donation') }}
                </button>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">{{ __('general.no_data') }}</h4>
                <p class="text-muted">{{ __('No donations available at the moment. Check back later!') }}</p>
            </div>
        </div>
    </div>
    @endforelse
</div>

{{-- Pagination --}}
<div class="d-flex justify-content-center mt-4">
    {{ $donations->withQueryString()->links() }}
</div>
@endsection

@section('modals')
{{-- View & Request Donation Modal --}}
<div class="modal fade" id="donationDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header" style="background: linear-gradient(135deg, #198754, #20c997); color: white;">
                <h5 class="modal-title"><i class="fas fa-hand-holding-heart me-2"></i>{{ __('Donation Details') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-5">
                        <div id="modal-image-container">
                            <div class="rounded d-flex align-items-center justify-content-center text-white" id="modal-image-placeholder" style="height: 220px; background: linear-gradient(135deg, #198754, #20c997);">
                                <i class="fas fa-utensils fa-3x opacity-75"></i>
                            </div>
                            <img id="modal-image" src="" class="img-fluid rounded d-none" style="max-height: 220px; width: 100%; object-fit: cover;">
                        </div>
                        <div class="mt-3">
                            <h6 class="fw-bold"><i class="fas fa-user me-1 text-success"></i> {{ __('donations.donor') }}</h6>
                            <p id="modal-donor" class="mb-1">-</p>
                            <p id="modal-donor-city" class="text-muted small mb-0">-</p>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="mb-3">
                            <h6 class="fw-bold"><i class="fas fa-utensils me-1 text-success"></i> {{ __('Food Items') }}</h6>
                            <div id="modal-items-list"></div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="p-2 rounded bg-light">
                                    <small class="text-muted d-block">{{ __('donations.pickup_time') }}</small>
                                    <strong id="modal-pickup-time">-</strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 rounded bg-light">
                                    <small class="text-muted d-block">{{ __('Volunteers') }}</small>
                                    <strong id="modal-volunteers">-</strong>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">{{ __('donations.pickup_address') }}</small>
                            <span id="modal-address">-</span>
                        </div>
                        <div class="mb-3" id="modal-description-wrap" style="display:none">
                            <small class="text-muted d-block">{{ __('donations.description') }}</small>
                            <span id="modal-description">-</span>
                        </div>
                        <div class="mb-3" id="modal-notes-wrap" style="display:none">
                            <small class="text-muted d-block">{{ __('donations.notes') }}</small>
                            <span id="modal-notes">-</span>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="mt-3">
                    <h6 class="fw-bold"><i class="fas fa-paper-plane me-1 text-success"></i> {{ __('donations.request_donation') }}</h6>
                    <input type="hidden" id="request-donation-id">
                    <div class="mb-3">
                        <textarea class="form-control" id="request-message" rows="2" maxlength="500" placeholder="{{ __('Optional message to the donor...') }}"></textarea>
                    </div>
                    <button type="button" class="btn btn-success w-100" id="btn-submit-request">
                        <i class="fas fa-hand-holding-heart me-1"></i> {{ __('donations.request_donation') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.routes = {
        donationsShow: '{{ route("charity.donations.show", ":id") }}',
        donationsRequest: '{{ route("charity.donations.request", ":id") }}'
    };

    // Open detail modal on card click or request button
    $(document).on('click', '.donation-card, .btn-request-donation', function(e) {
        e.stopPropagation();
        let donationId = $(this).closest('[data-id]').data('id') || $(this).data('id');
        openDonationModal(donationId);
    });

    function openDonationModal(id) {
        $.get(window.routes.donationsShow.replace(':id', id), function(data) {
            let modal = $('#donationDetailModal');

            // Image
            if (data.image) {
                modal.find('#modal-image').attr('src', '/storage/' + data.image).removeClass('d-none');
                modal.find('#modal-image-placeholder').addClass('d-none');
            } else {
                modal.find('#modal-image').addClass('d-none');
                modal.find('#modal-image-placeholder').removeClass('d-none');
            }

            // Donor
            modal.find('#modal-donor').text(data.donor ? data.donor.name : '-');
            modal.find('#modal-donor-city').text(data.donor && data.donor.city ? data.donor.city : '');

            // Items list
            let itemsHtml = '';
            if (data.items && data.items.length > 0) {
                data.items.forEach(function(item) {
                    itemsHtml += '<div class="d-flex justify-content-between align-items-center border-bottom py-2">';
                    itemsHtml += '<span><i class="fas fa-check-circle text-success me-1"></i> ' + item.food_type + '</span>';
                    itemsHtml += '<span class="badge bg-light text-dark border">' + item.quantity + ' ' + item.quantity_unit + '</span>';
                    itemsHtml += '</div>';
                    if (item.description) {
                        itemsHtml += '<small class="text-muted d-block mb-1 ps-4">' + item.description + '</small>';
                    }
                });
            } else {
                itemsHtml = '<span class="badge bg-light text-dark border">' + (data.food_type || '-') + ' — ' + (data.quantity || '') + ' ' + (data.quantity_unit || '') + '</span>';
            }
            modal.find('#modal-items-list').html(itemsHtml);

            // Details
            modal.find('#modal-pickup-time').text(data.pickup_time ? new Date(data.pickup_time).toLocaleString() : '-');
            modal.find('#modal-volunteers').html(data.volunteers_count + '/' + data.volunteers_needed + ' <i class="fas fa-users text-success"></i>');
            modal.find('#modal-address').text(data.pickup_address || '-');

            if (data.description) {
                modal.find('#modal-description').text(data.description);
                modal.find('#modal-description-wrap').show();
            } else {
                modal.find('#modal-description-wrap').hide();
            }

            if (data.notes) {
                modal.find('#modal-notes').text(data.notes);
                modal.find('#modal-notes-wrap').show();
            } else {
                modal.find('#modal-notes-wrap').hide();
            }

            modal.find('#request-donation-id').val(data.id);
            modal.find('#request-message').val('');
            modal.modal('show');
        });
    }

    // Submit request
    $(document).on('click', '#btn-submit-request', function() {
        let donationId = $('#request-donation-id').val();
        let message = $('#request-message').val();
        let btn = $(this);
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> ...');

        $.ajax({
            url: window.routes.donationsRequest.replace(':id', donationId),
            type: 'POST',
            data: { message: message },
            success: function(response) {
                showSuccess(response.message || 'Requested successfully!');
                $('#donationDetailModal').modal('hide');
                setTimeout(function() { location.reload(); }, 1500);
            },
            error: function(xhr) {
                showError(xhr.responseJSON?.message || 'Request failed');
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="fas fa-hand-holding-heart me-1"></i> {{ __("donations.request_donation") }}');
            }
        });
    });
</script>
@endpush
