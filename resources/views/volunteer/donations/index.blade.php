@extends('layouts.volunteer')

@section('title', __('Browse Donations'))

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h1><i class="fas fa-store me-2"></i>{{ __('Browse Donations') }}</h1>
    <div>
        <span class="badge bg-success fs-6">{{ $donations->total() }} {{ __('Available') }}</span>
        <span class="badge bg-warning text-dark fs-6 ms-1"><i class="fas fa-coins me-1"></i>{{ auth()->user()->points }} {{ __('Points') }}</span>
    </div>
</div>

{{-- Filters --}}
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body">
        <form method="GET" action="{{ route('volunteer.donations.index') }}" id="filterForm">
            <div class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label fw-semibold"><i class="fas fa-search me-1"></i> {{ __('general.search') }}</label>
                    <input type="text" name="search" class="form-control" placeholder="{{ __('Search food, address...') }}" value="{{ $filters['search'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold"><i class="fas fa-city me-1"></i> {{ __('City') }}</label>
                    <select name="city_id" id="filter_city_id" class="form-select">
                        <option value="">{{ __('All Cities') }}</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ ($filters['city_id'] ?? '') == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold"><i class="fas fa-map-marker-alt me-1"></i> {{ __('Town') }}</label>
                    <select name="town_id" id="filter_town_id" class="form-select">
                        <option value="">{{ __('All Towns') }}</option>
                        @if(isset($towns) && count($towns) > 0)
                            @foreach($towns as $town)
                                <option value="{{ $town->id }}" {{ ($filters['town_id'] ?? '') == $town->id ? 'selected' : '' }}>{{ $town->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold"><i class="fas fa-utensils me-1"></i> {{ __('donations.food_type') }}</label>
                    <input type="text" name="food_type" class="form-control" placeholder="{{ __('e.g. Rice') }}" value="{{ $filters['food_type'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold"><i class="fas fa-calendar me-1"></i> {{ __('From') }}</label>
                    <input type="date" name="date_from" class="form-control" value="{{ $filters['date_from'] ?? '' }}">
                </div>
                <div class="col-md-1">
                    <label class="form-label fw-semibold">{{ __('To') }}</label>
                    <input type="date" name="date_to" class="form-control" value="{{ $filters['date_to'] ?? '' }}">
                </div>
                <div class="col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-success flex-grow-1" title="{{ __('general.search') }}"><i class="fas fa-filter"></i></button>
                    <a href="{{ route('volunteer.donations.index') }}?city_id=&town_id=" class="btn btn-outline-secondary" title="{{ __('Reset') }}"><i class="fas fa-times"></i></a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Cards --}}
<div class="row g-4">
    @forelse($donations as $donation)
    <div class="col-sm-6 col-lg-4 col-xl-3">
        <div class="card h-100 border-0 shadow-sm donation-card" style="cursor:pointer; transition: transform .2s, box-shadow .2s;"
             onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 25px rgba(0,0,0,.12)'"
             onmouseout="this.style.transform=''; this.style.boxShadow=''"
             data-id="{{ $donation->id }}">
            <div class="position-relative">
                @if($donation->image)
                    <img src="{{ asset('storage/' . $donation->image) }}" class="card-img-top" alt="{{ $donation->food_type }}" style="height: 180px; object-fit: cover;">
                @else
                    <div class="card-img-top d-flex align-items-center justify-content-center text-white" style="height: 180px; background: linear-gradient(135deg, #198754, #20c997);">
                        <i class="fas fa-utensils fa-3x opacity-75"></i>
                    </div>
                @endif
                <span class="position-absolute top-0 end-0 m-2">
                    <span class="badge {{ $donation->is_full ? 'bg-secondary' : 'bg-success' }} rounded-pill">
                        <i class="fas fa-users me-1"></i>{{ $donation->volunteers_count }}/{{ $donation->volunteers_needed }}
                    </span>
                    <br>
                    <span class="badge bg-dark bg-opacity-75 rounded-pill mt-1" style="font-size: .65rem">
                        <i class="fas fa-truck"></i> {{ $donation->delivery_volunteers_needed }}
                        · <i class="fas fa-box"></i> {{ $donation->packaging_volunteers_needed }}
                    </span>
                </span>
                <span class="position-absolute bottom-0 start-0 m-2 badge bg-dark bg-opacity-75 rounded-pill">
                    <i class="fas fa-clock me-1"></i>{{ $donation->pickup_time ? $donation->pickup_time->format('M d, H:i') : '-' }}
                </span>
            </div>

            <div class="card-body pb-2">
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
                <p class="card-text text-muted small mb-1">
                    <i class="fas fa-map-marker-alt text-danger me-1"></i>{{ Str::limit($donation->pickup_address, 40) }}
                </p>
                <p class="card-text small mb-0">
                    <i class="fas fa-user text-success me-1"></i>
                    <strong>{{ $donation->donor->name ?? '-' }}</strong>
                    @if($donation->cityRelation)
                        <span class="text-muted">· {{ $donation->cityRelation->name }}</span>
                        @if($donation->town)
                            <span class="text-muted">/ {{ $donation->town->name }}</span>
                        @endif
                    @endif
                </p>
            </div>

            <div class="card-footer bg-transparent border-top-0 pt-0">
                <button class="btn btn-success btn-sm w-100 btn-assign-me" data-id="{{ $donation->id }}">
                    <i class="fas fa-hand-paper me-1"></i> {{ __('Assign Me') }}
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
                <p class="text-muted">{{ __('No donations available at the moment.') }}</p>
            </div>
        </div>
    </div>
    @endforelse
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $donations->withQueryString()->links() }}
</div>
@endsection

@section('modals')
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
                        <div id="modal-image-placeholder" class="rounded d-flex align-items-center justify-content-center text-white" style="height: 220px; background: linear-gradient(135deg, #198754, #20c997);">
                            <i class="fas fa-utensils fa-3x opacity-75"></i>
                        </div>
                        <img id="modal-image" src="" class="img-fluid rounded d-none" style="max-height: 220px; width: 100%; object-fit: cover;">
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
                                    <div class="small text-muted mt-1" id="modal-volunteer-types">-</div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted d-block">{{ __('donations.pickup_address') }}</small>
                            <span id="modal-address">-</span>
                        </div>
                    </div>
                </div>

                <hr>
                <input type="hidden" id="assign-donation-id">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="badge bg-success"><i class="fas fa-coins me-1"></i> +5 {{ __('Points') }}</span>
                        <small class="text-muted ms-1">{{ __('for volunteering') }}</small>
                    </div>
                    <button type="button" class="btn btn-success" id="btn-confirm-assign">
                        <i class="fas fa-hand-paper me-1"></i> {{ __('Assign Me') }}
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
        donationsShow: '{{ route("volunteer.donations.show", ":id") }}',
        donationsAssign: '{{ route("volunteer.donations.assign", ":id") }}'
    };

    // City -> Town dynamic filter
    $('#filter_city_id').on('change', function () {
        var cityId = $(this).val();
        var $town = $('#filter_town_id');
        $town.html('<option value="">{{ __("All Towns") }}</option>');
        if (!cityId) return;

        $.get('/api/cities/' + cityId + '/towns', function (towns) {
            towns.forEach(function (t) {
                $town.append('<option value="' + t.id + '">' + t.name + '</option>');
            });
        });
    });

    $(document).on('click', '.donation-card, .btn-assign-me', function(e) {
        e.stopPropagation();
        let id = $(this).closest('[data-id]').data('id') || $(this).data('id');
        openModal(id);
    });

    function openModal(id) {
        $.get(window.routes.donationsShow.replace(':id', id), function(data) {
            let m = $('#donationDetailModal');
            if (data.image) {
                m.find('#modal-image').attr('src', '/storage/' + data.image).removeClass('d-none');
                m.find('#modal-image-placeholder').addClass('d-none');
            } else {
                m.find('#modal-image').addClass('d-none');
                m.find('#modal-image-placeholder').removeClass('d-none');
            }
            m.find('#modal-donor').text(data.donor ? data.donor.name : '-');
            var cityText = '';
            if (data.city_relation) cityText = data.city_relation.name;
            if (data.town) cityText += ' / ' + data.town.name;
            m.find('#modal-donor-city').text(cityText);

            let html = '';
            if (data.items && data.items.length > 0) {
                data.items.forEach(function(i) {
                    html += '<div class="d-flex justify-content-between border-bottom py-2">';
                    html += '<span><i class="fas fa-check-circle text-success me-1"></i> ' + i.food_type + '</span>';
                    html += '<span class="badge bg-light text-dark border">' + i.quantity + ' ' + i.quantity_unit + '</span>';
                    html += '</div>';
                });
            } else {
                html = '<span>' + (data.food_type || '-') + '</span>';
            }
            m.find('#modal-items-list').html(html);
            m.find('#modal-pickup-time').text(data.pickup_time ? new Date(data.pickup_time).toLocaleString() : '-');
            m.find('#modal-volunteers').html(data.volunteers_count + '/' + data.volunteers_needed + ' <i class="fas fa-users text-success"></i>');
            m.find('#modal-volunteer-types').html('<i class="fas fa-truck"></i> ' + (data.delivery_volunteers_needed || 0) + ' · <i class="fas fa-box"></i> ' + (data.packaging_volunteers_needed || 0));
            m.find('#modal-address').text(data.pickup_address || '-');
            m.find('#assign-donation-id').val(data.id);
            m.modal('show');
        });
    }

    $(document).on('click', '#btn-confirm-assign', function() {
        let id = $('#assign-donation-id').val();
        let btn = $(this);
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>...');

        $.post(window.routes.donationsAssign.replace(':id', id), function(res) {
            showSuccess(res.message || 'Assigned!');
            $('#donationDetailModal').modal('hide');
            setTimeout(function() { location.reload(); }, 1500);
        }).fail(function(xhr) {
            showError(xhr.responseJSON?.message || 'Failed');
        }).always(function() {
            btn.prop('disabled', false).html('<i class="fas fa-hand-paper me-1"></i> Assign Me');
        });
    });
</script>
@endpush
