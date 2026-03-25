@extends('layouts.auth')

@section('title', __('auth_page.register_title'))

@section('content')
    <h4 class="text-center fw-bold mb-4">{{ __('auth_page.register_title') }}</h4>

    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
        @csrf

        {{-- Name --}}
        <div class="mb-3">
            <label for="name" class="form-label">{{ __('auth_page.name') }}</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-user"></i></span>
                <input type="text"
                       id="name"
                       name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}"
                       placeholder="{{ __('auth_page.name') }}"
                       required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Email --}}
        <div class="mb-3">
            <label for="email" class="form-label">{{ __('auth_page.email') }}</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input type="email"
                       id="email"
                       name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}"
                       placeholder="{{ __('auth_page.email') }}"
                       required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Phone --}}
        <div class="mb-3">
            <label for="phone" class="form-label">{{ __('auth_page.phone') }}</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                <input type="tel"
                       id="phone"
                       name="phone"
                       class="form-control @error('phone') is-invalid @enderror"
                       value="{{ old('phone') }}"
                       placeholder="{{ __('auth_page.phone') }}"
                       required>
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Password --}}
        <div class="mb-3">
            <label for="password" class="form-label">{{ __('auth_page.password') }}</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password"
                       id="password"
                       name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       placeholder="{{ __('auth_page.password') }}"
                       required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Confirm Password --}}
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">{{ __('auth_page.confirm_password') }}</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password"
                       id="password_confirmation"
                       name="password_confirmation"
                       class="form-control"
                       placeholder="{{ __('auth_page.confirm_password') }}"
                       required>
            </div>
        </div>

        <hr class="my-3">

        {{-- Role Selection --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">{{ __('auth_page.role') }}</label>
            <div class="d-flex gap-3 flex-wrap">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="role" id="role_donor"
                           value="donor" {{ old('role') == 'donor' ? 'checked' : '' }} required>
                    <label class="form-check-label" for="role_donor">
                        <i class="fas fa-hand-holding-heart text-success me-1"></i>{{ __('auth_page.donor') }}
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="role" id="role_charity"
                           value="charity" {{ old('role') == 'charity' ? 'checked' : '' }}>
                    <label class="form-check-label" for="role_charity">
                        <i class="fas fa-building-ngo text-success me-1"></i>{{ __('auth_page.charity') }}
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="role" id="role_volunteer"
                           value="volunteer" {{ old('role') == 'volunteer' ? 'checked' : '' }}>
                    <label class="form-check-label" for="role_volunteer">
                        <i class="fas fa-people-carry-box text-success me-1"></i>{{ __('auth_page.volunteer') }}
                    </label>
                </div>
            </div>
            @error('role')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- Donor Fields --}}
        <div id="donor-fields" class="role-fields" style="display:none;">
            <div class="mb-3">
                <label for="health_certificate" class="form-label">{{ __('auth_page.health_certificate') }}</label>
                <input type="file"
                       id="health_certificate"
                       name="health_certificate"
                       class="form-control @error('health_certificate') is-invalid @enderror"
                       accept=".pdf,.jpg,.jpeg,.png">
                @error('health_certificate')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Charity Fields --}}
        <div id="charity-fields" class="role-fields" style="display:none;">
            <div class="mb-3">
                <label for="organization_name" class="form-label">{{ __('auth_page.organization_name') }}</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-building"></i></span>
                    <input type="text"
                           id="organization_name"
                           name="organization_name"
                           class="form-control @error('organization_name') is-invalid @enderror"
                           value="{{ old('organization_name') }}"
                           placeholder="{{ __('auth_page.organization_name') }}">
                    @error('organization_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="mb-3">
                <label for="organization_license" class="form-label">{{ __('auth_page.organization_license') }}</label>
                <input type="file"
                       id="organization_license"
                       name="organization_license"
                       class="form-control @error('organization_license') is-invalid @enderror"
                       accept=".pdf,.jpg,.jpeg,.png">
                @error('organization_license')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Volunteer Fields --}}
        <div id="volunteer-fields" class="role-fields" style="display:none;">
            <div class="mb-3">
                <label class="form-label">{{ __('auth_page.volunteer_type') }}</label>
                <div class="d-flex gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="volunteer_type" id="vt_delivery"
                               value="delivery" {{ old('volunteer_type') == 'delivery' ? 'checked' : '' }}>
                        <label class="form-check-label" for="vt_delivery">
                            <i class="fas fa-truck me-1"></i>{{ __('auth_page.delivery') }}
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="volunteer_type" id="vt_packaging"
                               value="packaging" {{ old('volunteer_type') == 'packaging' ? 'checked' : '' }}>
                        <label class="form-check-label" for="vt_packaging">
                            <i class="fas fa-box me-1"></i>{{ __('auth_page.packaging') }}
                        </label>
                    </div>
                </div>
                @error('volunteer_type')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <hr class="my-3">

        {{-- Address --}}
        <div class="mb-3">
            <label for="address" class="form-label">{{ __('auth_page.address') }}</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                <input type="text"
                       id="address"
                       name="address"
                       class="form-control @error('address') is-invalid @enderror"
                       value="{{ old('address') }}"
                       placeholder="{{ __('auth_page.address') }}">
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- City --}}
        <div class="mb-3">
            <label for="city" class="form-label">{{ __('auth_page.city') }}</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-city"></i></span>
                <input type="text"
                       id="city"
                       name="city"
                       class="form-control @error('city') is-invalid @enderror"
                       value="{{ old('city') }}"
                       placeholder="{{ __('auth_page.city') }}">
                @error('city')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
            <i class="fas fa-user-plus me-1"></i> {{ __('general.register') }}
        </button>
    </form>

    <p class="text-center mt-3 mb-0 small text-muted">
        {{ __('auth_page.already_have_account') }}
        <a href="{{ route('login') }}" class="fw-semibold">{{ __('general.login') }}</a>
    </p>
@endsection

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
<style>
    .auth-wrapper { max-width: 580px; }
    .role-fields {
        background: #f8faf8;
        border-radius: .5rem;
        padding: 1rem;
        margin-bottom: .5rem;
        border: 1px dashed #c8e6c9;
    }
</style>
@endpush

@push('scripts')
<script>
    $(function () {
        function toggleRoleFields() {
            var role = $('input[name="role"]:checked').val();
            $('.role-fields').slideUp(200);
            if (role) {
                $('#' + role + '-fields').slideDown(200);
            }
        }

        $('input[name="role"]').on('change', toggleRoleFields);
        toggleRoleFields();
    });
</script>
@endpush
