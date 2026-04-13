@php $isRtl = app()->getLocale() === 'ar'; @endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('auth_page.register_title') }} — {{ config('app.name', 'Rebite') }}</title>

    @if($isRtl)
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --rb-green: #3a7d44;
            --rb-green-dark: #2d5f34;
            --rb-orange: #d4892a;
            --rb-orange-dark: #b8741f;
            --rb-cream: #faf7f2;
            --rb-dark: #2d3e30;
        }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            color: #333;
            margin: 0;
        }

        h1, h2, h3 {
            font-family: 'Playfair Display', Georgia, serif;
        }

        /* ── Navbar ── */
        .navbar {
            padding: .8rem 0;
            background: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,.06);
        }

        .navbar-brand img { height: 42px; }

        .navbar-brand .brand-text {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--rb-green) !important;
            margin-{{ $isRtl ? 'right' : 'left' }}: .5rem;
        }

        .nav-link {
            font-weight: 500;
            color: #444 !important;
            padding: .5rem 1rem !important;
        }

        .nav-link:hover { color: var(--rb-green) !important; }

        .btn-get-started {
            background-color: var(--rb-orange);
            border: none;
            color: #fff;
            border-radius: 2rem;
            padding: .5rem 1.5rem;
            font-weight: 600;
        }

        .btn-get-started:hover {
            background-color: var(--rb-orange-dark);
            color: #fff;
        }

        /* ── Register Page ── */
        .register-page {
            min-height: calc(100vh - 70px);
            background-image: url('{{ asset("images/Background-landing.png") }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 1rem;
            position: relative;
        }

        .register-page::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(255,255,255,.3);
        }

        /* ── Step 1: Role Selection ── */
        .role-selection-card {
            background: rgba(255,255,255,.92);
            backdrop-filter: blur(10px);
            border-radius: 1.5rem;
            padding: 3rem 2.5rem;
            max-width: 520px;
            width: 100%;
            position: relative;
            box-shadow: 0 10px 40px rgba(0,0,0,.1);
        }

        .role-selection-card h2 {
            text-align: center;
            font-size: 2rem;
            font-weight: 700;
            color: #2a2a2a;
            margin-bottom: .5rem;
        }

        .role-selection-card .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 2rem;
        }

        .role-option {
            display: flex;
            align-items: center;
            gap: 1.2rem;
            padding: 1rem 1.2rem;
            border: 2px solid #e8e3d9;
            border-radius: 1rem;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: all .3s;
            background: #fff;
        }

        .role-option:hover {
            border-color: var(--rb-green);
            box-shadow: 0 4px 15px rgba(58, 125, 68, .1);
            transform: translateY(-2px);
        }

        .role-option.selected {
            border-color: var(--rb-green);
            background: #f0f8f2;
        }

        .role-option img {
            width: 70px;
            height: 70px;
            object-fit: contain;
            border-radius: .5rem;
        }

        .role-option .role-info h5 {
            font-family: 'Inter', sans-serif;
            font-weight: 700;
            margin-bottom: .2rem;
            color: #2a2a2a;
            font-size: 1.1rem;
        }

        .role-option .role-info p {
            color: #777;
            margin-bottom: 0;
            font-size: .9rem;
        }

        /* ── Step 2: Registration Form ── */
        .register-form-card {
            background: rgba(255,255,255,.95);
            backdrop-filter: blur(10px);
            border-radius: 1.5rem;
            padding: 2.5rem 2rem;
            max-width: 550px;
            width: 100%;
            position: relative;
            box-shadow: 0 10px 40px rgba(0,0,0,.1);
        }

        .register-form-card h3 {
            text-align: center;
            font-size: 1.6rem;
            font-weight: 700;
            color: #2a2a2a;
            margin-bottom: .3rem;
        }

        .register-form-card .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 1.5rem;
        }

        .role-badge {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            background: #f0f8f2;
            color: var(--rb-green);
            border: 1px solid var(--rb-green);
            border-radius: 2rem;
            padding: .3rem 1rem;
            font-weight: 600;
            font-size: .85rem;
        }

        .btn-back {
            background: transparent;
            border: none;
            color: #888;
            font-size: .95rem;
            cursor: pointer;
            padding: .4rem .8rem;
            border-radius: .5rem;
            transition: all .2s;
        }

        .btn-back:hover {
            color: var(--rb-green);
            background: #f0f8f2;
        }

        .btn-register {
            background-color: var(--rb-green);
            border: none;
            color: #fff;
            border-radius: .5rem;
            padding: .75rem 2rem;
            font-weight: 600;
            width: 100%;
            font-size: 1.05rem;
            transition: all .2s;
        }

        .btn-register:hover {
            background-color: var(--rb-green-dark);
            color: #fff;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--rb-green);
            box-shadow: 0 0 0 .2rem rgba(58, 125, 68, .15);
        }

        .role-fields {
            background: #f8faf8;
            border-radius: .75rem;
            padding: 1rem;
            margin-bottom: .75rem;
            border: 1px dashed #c8e6c9;
        }

        /* ── Footer ── */
        .site-footer {
            background: var(--rb-dark);
            color: #b8c5ba;
            padding: 3rem 0 1.5rem;
        }

        .site-footer h6 { color: #fff; font-weight: 600; margin-bottom: 1rem; }

        .footer-brand {
            display: flex;
            align-items: center;
            gap: .5rem;
            margin-bottom: 1rem;
        }

        .footer-brand img {
            height: 36px;
            filter: brightness(0) invert(1);
        }

        .footer-brand span {
            font-family: 'Playfair Display', serif;
            font-size: 1.3rem;
            font-weight: 700;
            color: #fff;
        }

        .site-footer a { color: #b8c5ba; text-decoration: none; transition: color .2s; }
        .site-footer a:hover { color: var(--rb-orange); }

        .footer-social a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(255,255,255,.1);
            color: #fff;
            transition: all .2s;
        }

        .footer-social a:hover { background: var(--rb-orange); }

        a { color: var(--rb-green); }
        a:hover { color: var(--rb-green-dark); }
    </style>
</head>
<body>

{{-- ── Navbar ── --}}
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
            <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}"
                 onerror="this.style.display='none';this.nextElementSibling.style.display='inline';">
            <span class="brand-text" style="display:none;"><i class="fas fa-leaf me-1"></i>ReBite</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link" href="{{ url('/') }}">{{ __('Home') }}</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/#about') }}">{{ __('landing.about_title') }}</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('how-it-works') }}">{{ __('landing.how_it_works') }}</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('safety-guidelines') }}">{{ __('landing.safety_guidelines') }}</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/#contact') }}">{{ __('landing.contact') }}</a></li>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('language.switch', $isRtl ? 'en' : 'ar') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                    {{ $isRtl ? 'English' : 'العربية' }}
                </a>
                <a href="{{ route('register') }}" class="btn btn-get-started">{{ __('landing.get_started') }}</a>
            </div>
        </div>
    </div>
</nav>

{{-- ── Register Content ── --}}
<div class="register-page">

    {{-- STEP 1: Choose Role --}}
    <div id="step-role" class="role-selection-card">
        <h2>{{ __('Choose Your Role') }}</h2>
        <p class="subtitle">{{ __('Select your account type to continue.') }}</p>

        <div class="role-option" data-role="donor">
            <img src="{{ asset('images/Donor.png') }}" alt="Donor">
            <div class="role-info">
                <h5>{{ __('auth_page.donor') }}</h5>
                <p>{{ __('I have food to donate.') }}</p>
            </div>
        </div>

        <div class="role-option" data-role="volunteer">
            <img src="{{ asset('images/Volunteer.png') }}" alt="Volunteer">
            <div class="role-info">
                <h5>{{ __('auth_page.volunteer') }}</h5>
                <p>{{ __('I want to help with deliveries.') }}</p>
            </div>
        </div>

        <div class="role-option" data-role="charity">
            <img src="{{ asset('images/Charity.png') }}" alt="Charity">
            <div class="role-info">
                <h5>{{ __('auth_page.charity') }}</h5>
                <p>{{ __('We need food support.') }}</p>
            </div>
        </div>

        <p class="text-center mt-3 mb-0 small text-muted">
            {{ __('auth_page.already_have_account') }}
            <a href="{{ route('login') }}" class="fw-semibold">{{ __('general.login') }}</a>
        </p>
    </div>

    {{-- STEP 2: Registration Form --}}
    <div id="step-form" class="register-form-card" style="display:none;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <button type="button" class="btn-back" id="btn-back">
                <i class="fas fa-arrow-left me-1"></i> {{ __('Back') }}
            </button>
            <span class="role-badge" id="selected-role-badge">
                <i class="fas fa-user"></i> <span id="role-label"></span>
            </span>
        </div>

        <h3>{{ __('auth_page.register_title') }}</h3>
        <p class="subtitle">{{ __('Fill in your details to create an account.') }}</p>

        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" id="registerForm">
            @csrf
            <input type="hidden" name="role" id="role-input" value="{{ old('role') }}">

            <div class="row g-3">
                {{-- Name --}}
                <div class="col-12">
                    <label for="name" class="form-label">{{ __('auth_page.name') }} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" id="name" name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" placeholder="{{ __('auth_page.name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Email --}}
                <div class="col-md-6">
                    <label for="email" class="form-label">{{ __('auth_page.email') }} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" id="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" placeholder="{{ __('auth_page.email') }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Phone --}}
                <div class="col-md-6">
                    <label for="phone" class="form-label">{{ __('auth_page.phone') }} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        <input type="tel" id="phone" name="phone"
                               class="form-control @error('phone') is-invalid @enderror"
                               value="{{ old('phone') }}" placeholder="{{ __('auth_page.phone') }}" required>
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Password --}}
                <div class="col-md-6">
                    <label for="password" class="form-label">{{ __('auth_page.password') }} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" id="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="{{ __('auth_page.password') }}" required>
                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="#password">
                            <i class="fas fa-eye"></i>
                        </button>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Confirm Password --}}
                <div class="col-md-6">
                    <label for="password_confirmation" class="form-label">{{ __('auth_page.confirm_password') }} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               class="form-control" placeholder="{{ __('auth_page.confirm_password') }}" required>
                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="#password_confirmation">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                {{-- Role-specific fields --}}

                {{-- Donor: Health Certificate --}}
                <div class="col-12 role-section" id="donor-fields" style="display:none;">
                    <div class="role-fields">
                        <label for="health_certificate" class="form-label">{{ __('auth_page.health_certificate') }} <span class="text-danger">*</span></label>
                        <input type="file" id="health_certificate" name="health_certificate"
                               class="form-control @error('health_certificate') is-invalid @enderror"
                               accept=".pdf,.jpg,.jpeg,.png">
                        @error('health_certificate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Charity: Org name + license --}}
                <div class="col-12 role-section" id="charity-fields" style="display:none;">
                    <div class="role-fields">
                        <div class="mb-3">
                            <label for="organization_name" class="form-label">{{ __('auth_page.organization_name') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-building"></i></span>
                                <input type="text" id="organization_name" name="organization_name"
                                       class="form-control @error('organization_name') is-invalid @enderror"
                                       value="{{ old('organization_name') }}" placeholder="{{ __('auth_page.organization_name') }}">
                                @error('organization_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <label for="organization_license" class="form-label">{{ __('auth_page.organization_license') }} <span class="text-danger">*</span></label>
                        <input type="file" id="organization_license" name="organization_license"
                               class="form-control @error('organization_license') is-invalid @enderror"
                               accept=".pdf,.jpg,.jpeg,.png">
                        @error('organization_license')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Volunteer: ID file + default role_type --}}
                <div class="col-12 role-section" id="volunteer-fields" style="display:none;">
                    <div class="role-fields">
                        <label for="id_file" class="form-label">{{ __('auth_page.id_file') }} <span class="text-danger">*</span></label>
                        <input type="file" id="id_file" name="id_file"
                               class="form-control @error('id_file') is-invalid @enderror"
                               accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">{{ __('Upload a copy of your national ID or passport.') }}</small>
                        @error('id_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <input type="hidden" name="role_type" value="delivery">
                </div>

                {{-- City --}}
                <div class="col-md-6">
                    <label for="city_id" class="form-label">{{ __('auth_page.city') }}</label>
                    <select id="city_id" name="city_id" class="form-select @error('city_id') is-invalid @enderror">
                        <option value="">{{ __('Select City') }}</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                        @endforeach
                    </select>
                    @error('city_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Town --}}
                <div class="col-md-6">
                    <label for="town_id" class="form-label">{{ __('Town') }}</label>
                    <select id="town_id" name="town_id" class="form-select @error('town_id') is-invalid @enderror">
                        <option value="">{{ __('Select Town') }}</option>
                    </select>
                    @error('town_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Address --}}
                <div class="col-12">
                    <label for="address" class="form-label">{{ __('auth_page.address') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-location-dot"></i></span>
                        <input type="text" id="address" name="address"
                               class="form-control @error('address') is-invalid @enderror"
                               value="{{ old('address') }}" placeholder="{{ __('auth_page.address') }}">
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Safety Guidelines Checkbox --}}
                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input @error('safety_guidelines') is-invalid @enderror"
                               type="checkbox" id="safety_guidelines" name="safety_guidelines" value="1"
                               {{ old('safety_guidelines') ? 'checked' : '' }} required>
                        <label class="form-check-label" for="safety_guidelines">
                            {{ __('landing.safety_accept') }}
                            <a href="{{ route('safety-guidelines') }}" target="_blank" class="fw-semibold">{{ __('landing.safety_guidelines') }}</a>
                            <span class="text-danger">*</span>
                        </label>
                        @error('safety_guidelines')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                {{-- Submit --}}
                <div class="col-12 mt-2">
                    <button type="submit" class="btn btn-register">
                        <i class="fas fa-user-plus me-1"></i> {{ __('general.register') }}
                    </button>
                </div>
            </div>
        </form>

        <p class="text-center mt-3 mb-0 small text-muted">
            {{ __('auth_page.already_have_account') }}
            <a href="{{ route('login') }}" class="fw-semibold">{{ __('general.login') }}</a>
        </p>
    </div>
</div>

{{-- ── Footer ── --}}
<footer class="site-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="footer-brand">
                    <img src="{{ asset('images/logo.png') }}" alt="ReBite" onerror="this.style.display='none';">
                    <span>ReBite</span>
                </div>
                <ul class="list-unstyled small">
                    <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i>Amman, Jordan</li>
                    <li class="mb-2"><i class="fas fa-envelope me-2"></i>info@rebite.com</li>
                </ul>
            </div>
            <div class="col-lg-4">
                <h6>{{ __('landing.quick_links') }}</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="{{ url('/#about') }}">{{ __('landing.about_title') }}</a></li>
                    <li class="mb-2"><a href="{{ route('how-it-works') }}">{{ __('landing.how_it_works') }}</a></li>
                    <li class="mb-2"><a href="{{ route('safety-guidelines') }}">{{ __('landing.safety_guidelines') }}</a></li>
                    <li class="mb-2"><a href="{{ url('/#contact') }}">{{ __('landing.contact') }}</a></li>
                    <li class="mb-2"><a href="#">{{ __('Privacy Policy') }}</a></li>
                </ul>
            </div>
            <div class="col-lg-4">
                <h6>{{ __('Follow Us') }}</h6>
                <div class="footer-social d-flex gap-2 mb-3">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
                <ul class="list-unstyled small">
                    <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i>Amman, Jordan</li>
                    <li class="mb-2"><i class="fas fa-envelope me-2"></i>info@rebite.com</li>
                </ul>
            </div>
        </div>
        <hr class="border-secondary my-3">
        <div class="text-center small">
            <p class="mb-1">&copy; {{ date('Y') }} ReBite · All rights reserved</p>
            <a href="#" class="me-2">{{ __('Privacy Policy') }}</a> · <a href="#" class="ms-2">{{ __('Terms of Service') }}</a>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

var roleLabels = {
    donor: '{{ __("auth_page.donor") }}',
    volunteer: '{{ __("auth_page.volunteer") }}',
    charity: '{{ __("auth_page.charity") }}'
};

var roleIcons = {
    donor: 'fa-hand-holding-heart',
    volunteer: 'fa-people-carry-box',
    charity: 'fa-building-ngo'
};

function showStep(step) {
    if (step === 'role') {
        $('#step-form').fadeOut(200, function() { $('#step-role').fadeIn(300); });
    } else {
        $('#step-role').fadeOut(200, function() { $('#step-form').fadeIn(300); });
    }
}

function selectRole(role) {
    $('#role-input').val(role);
    $('#role-label').text(roleLabels[role] || role);
    $('#selected-role-badge i').attr('class', 'fas ' + (roleIcons[role] || 'fa-user'));

    $('.role-section').hide();
    if (role) {
        $('#' + role + '-fields').show();
    }

    showStep('form');
}

$(function () {
    // Role card click
    $('.role-option').on('click', function () {
        $('.role-option').removeClass('selected');
        $(this).addClass('selected');
        var role = $(this).data('role');
        selectRole(role);
    });

    // Back button
    $('#btn-back').on('click', function () {
        showStep('role');
    });

    // City -> Town dynamic loading
    $('#city_id').on('change', function () {
        var cityId = $(this).val();
        var $town = $('#town_id');
        $town.html('<option value="">{{ __("Select Town") }}</option>');
        if (!cityId) return;

        $.get('/api/cities/' + cityId + '/towns', function (towns) {
            towns.forEach(function (t) {
                var sel = ('{{ old("town_id") }}' == t.id) ? 'selected' : '';
                $town.append('<option value="' + t.id + '" ' + sel + '>' + t.name + '</option>');
            });
        });
    });

    // Toggle password visibility
    $('.toggle-password').on('click', function() {
        var input = $($(this).data('target'));
        var icon = $(this).find('i');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // If old role value exists (validation error), go straight to form
    var oldRole = '{{ old("role") }}';
    if (oldRole) {
        selectRole(oldRole);
        if ($('#city_id').val()) {
            $('#city_id').trigger('change');
        }
    }
});
</script>

@if(session('success'))
<script>Swal.fire({ icon: 'success', title: '{{ __("Success") }}', text: '{{ session("success") }}', confirmButtonColor: '#3a7d44' });</script>
@endif

@if(session('error'))
<script>Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ session("error") }}', confirmButtonColor: '#3a7d44' });</script>
@endif

</body>
</html>
