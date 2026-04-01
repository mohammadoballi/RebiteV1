@php
    $layout = 'layouts.donor';
    if(auth()->user()->hasRole('admin')) $layout = 'layouts.admin';
    elseif(auth()->user()->hasRole('charity')) $layout = 'layouts.charity';
    elseif(auth()->user()->hasRole('volunteer')) $layout = 'layouts.volunteer';
@endphp

@extends($layout)

@section('title', __('general.profile'))

@push('styles')
<style>
    .avatar-wrapper {
        position: relative;
        width: 120px;
        height: 120px;
        cursor: pointer;
    }
    .avatar-wrapper img,
    .avatar-placeholder {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #198754;
    }
    .avatar-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        background: #e9ecef;
        color: #6c757d;
    }
    .avatar-wrapper .avatar-overlay {
        position: absolute;
        inset: 0;
        border-radius: 50%;
        background: rgba(0,0,0,.45);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity .2s;
    }
    .avatar-wrapper:hover .avatar-overlay {
        opacity: 1;
    }
</style>
@endpush

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1><i class="fas fa-user me-2 text-success"></i>{{ __('general.profile') }}</h1>
</div>

<div class="row g-4">
    {{-- Card 1: Profile Information --}}
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-id-card me-2 text-success"></i>{{ __('Profile Information') }}</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="avatar-wrapper mx-auto" id="avatar-trigger" title="{{ __('Change Avatar') }}">
                        @if($user->avatar_url)
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" id="avatar-preview">
                        @else
                            <div class="avatar-placeholder" id="avatar-preview-placeholder">
                                <i class="fas fa-user fa-3x"></i>
                            </div>
                        @endif
                        <div class="avatar-overlay">
                            <i class="fas fa-camera fa-lg text-white"></i>
                        </div>
                    </div>
                    <input type="file" id="avatar-input" accept="image/*" class="d-none">
                    <small class="text-muted d-block mt-2">{{ __('Click to change avatar') }}</small>
                </div>

                <form id="profile-form">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">{{ __('Name') }}</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">{{ __('Email') }}</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" readonly disabled>
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">{{ __('Phone') }}</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ $user->phone }}">
                        </div>
                        <div class="col-md-6">
                            <label for="city_id" class="form-label">{{ __('City') }}</label>
                            <select class="form-select" id="city_id" name="city_id">
                                <option value="">{{ __('Select City') }}</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}" {{ $user->city_id == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="town_id" class="form-label">{{ __('Town') }}</label>
                            <select class="form-select" id="town_id" name="town_id">
                                <option value="">{{ __('Select Town') }}</option>
                                @if($user->city_id && $user->cityRelation)
                                    @foreach($user->cityRelation->towns as $town)
                                        <option value="{{ $town->id }}" {{ $user->town_id == $town->id ? 'selected' : '' }}>{{ $town->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="address" class="form-label">{{ __('Address') }}</label>
                            <textarea class="form-control" id="address" name="address" rows="3">{{ $user->address }}</textarea>
                        </div>
                        @if(auth()->user()->hasRole('volunteer'))
                        <div class="col-md-6">
                            <label for="role_type" class="form-label">{{ __('auth_page.volunteer_type') }}</label>
                            <select class="form-select" id="role_type" name="role_type">
                                <option value="delivery" {{ $user->role_type === 'delivery' ? 'selected' : '' }}>
                                    <i class="fas fa-truck me-1"></i>{{ __('auth_page.delivery') }}
                                </option>
                                <option value="packaging" {{ $user->role_type === 'packaging' ? 'selected' : '' }}>
                                    {{ __('auth_page.packaging') }}
                                </option>
                            </select>
                        </div>
                        @endif
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-success" id="btn-save-profile">
                                <i class="fas fa-save me-1"></i> {{ __('general.save') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Card 2: Change Password --}}
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-lock me-2 text-success"></i>{{ __('Change Password') }}</h5>
            </div>
            <div class="card-body">
                <form id="password-form">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="current_password" class="form-label">{{ __('Current Password') }}</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label">{{ __('New Password') }}</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">{{ __('Confirm New Password') }}</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-success" id="btn-change-password">
                                <i class="fas fa-key me-1"></i> {{ __('Change Password') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Card 3: Account Information --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2 text-success"></i>{{ __('Account Information') }}</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="d-flex justify-content-between align-items-center py-3 border-bottom">
                        <span class="text-muted"><i class="fas fa-user-tag me-2"></i>{{ __('Role') }}</span>
                        @if($user->hasRole('volunteer'))
                            <span class="badge bg-success fs-6">{{ $user->role_type === 'packaging' ? __('auth_page.packaging') : __('auth_page.delivery') }}</span>
                        @else
                            <span class="badge bg-success fs-6">{{ $user->roles->pluck('display_name')->filter()->implode(', ') ?: '-' }}</span>
                        @endif
                    </li>
                    <li class="d-flex justify-content-between align-items-center py-3 border-bottom">
                        <span class="text-muted"><i class="fas fa-circle-check me-2"></i>{{ __('general.status') }}</span>
                        @switch($user->status)
                            @case('approved')
                                <span class="badge bg-success">{{ __('general.approved') }}</span>
                                @break
                            @case('pending')
                                <span class="badge bg-warning">{{ __('general.pending') }}</span>
                                @break
                            @case('rejected')
                                <span class="badge bg-danger">{{ __('general.rejected') }}</span>
                                @break
                            @default
                                <span class="badge bg-secondary">{{ ucfirst($user->status) }}</span>
                        @endswitch
                    </li>
                    <li class="d-flex justify-content-between align-items-center py-3 {{ $user->organization_name ? 'border-bottom' : '' }}">
                        <span class="text-muted"><i class="fas fa-calendar me-2"></i>{{ __('Member Since') }}</span>
                        <span>{{ $user->created_at->format('M d, Y') }}</span>
                    </li>
                    @if($user->organization_name)
                        <li class="d-flex justify-content-between align-items-center py-3">
                            <span class="text-muted"><i class="fas fa-building me-2"></i>{{ __('Organization') }}</span>
                            <span>{{ $user->organization_name }}</span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Avatar upload trigger
    $('#avatar-trigger').on('click', function() {
        $('#avatar-input').trigger('click');
    });

    $('#avatar-input').on('change', function() {
        var file = this.files[0];
        if (!file) return;

        if (file.size > 2 * 1024 * 1024) {
            showError('{{ __("Avatar must be less than 2MB.") }}');
            return;
        }

        var formData = new FormData();
        formData.append('avatar', file);

        $.ajax({
            url: '{{ route("profile.avatar") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                showSuccess(response.message);
                // Replace avatar display
                var wrapper = $('#avatar-trigger');
                wrapper.find('#avatar-preview, #avatar-preview-placeholder').remove();
                wrapper.prepend('<img src="' + response.path + '" alt="" id="avatar-preview" style="width:120px;height:120px;border-radius:50%;object-fit:cover;border:4px solid #198754;">');
            },
            error: function(xhr) {
                var msg = xhr.responseJSON?.message || '{{ __("Failed to upload avatar.") }}';
                showError(msg);
            }
        });
    });

    // City -> Town dynamic loading
    $('#city_id').on('change', function () {
        var cityId = $(this).val();
        var $town = $('#town_id');
        $town.html('<option value="">{{ __("Select Town") }}</option>');
        if (!cityId) return;

        $.get('/api/cities/' + cityId + '/towns', function (towns) {
            towns.forEach(function (t) {
                $town.append('<option value="' + t.id + '">' + t.name + '</option>');
            });
        });
    });

    // Profile update
    $('#profile-form').on('submit', function(e) {
        e.preventDefault();
        clearFormErrors('#profile-form');

        var $btn = $('#btn-save-profile');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> {{ __("general.loading") }}');

        $.ajax({
            url: '{{ route("profile.update") }}',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                showSuccess('{{ __("Profile updated successfully.") }}');
            },
            error: function(xhr) {
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    displayFormErrors('#profile-form', xhr.responseJSON.errors);
                } else {
                    showError(xhr.responseJSON?.message || '{{ __("general.error") }}');
                }
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> {{ __("general.save") }}');
            }
        });
    });

    // Password change
    $('#password-form').on('submit', function(e) {
        e.preventDefault();
        clearFormErrors('#password-form');

        var $btn = $('#btn-change-password');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> {{ __("general.loading") }}');

        $.ajax({
            url: '{{ route("profile.password") }}',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                showSuccess('{{ __("Password changed successfully.") }}');
                $('#password-form')[0].reset();
            },
            error: function(xhr) {
                if (xhr.status === 422 && xhr.responseJSON?.errors) {
                    displayFormErrors('#password-form', xhr.responseJSON.errors);
                } else {
                    showError(xhr.responseJSON?.message || '{{ __("general.error") }}');
                }
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="fas fa-key me-1"></i> {{ __("Change Password") }}');
            }
        });
    });
});
</script>
@endpush
