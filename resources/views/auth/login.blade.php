@extends('layouts.auth')

@section('title', __('auth_page.login_title'))

@section('content')
    <h4 class="text-center fw-bold mb-4">{{ __('auth_page.login_title') }}</h4>

    @if(session('status'))
        <div class="alert alert-success small">{{ session('status') }}</div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning small">{{ session('warning') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

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
                       required
                       autofocus>
                @error('email')
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

        {{-- Remember + Forgot --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input type="checkbox"
                       class="form-check-input"
                       id="remember"
                       name="remember"
                       {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label small" for="remember">{{ __('auth_page.remember_me') }}</label>
            </div>
            <a href="{{ route('password.request') }}" class="small">{{ __('auth_page.forgot_password') }}</a>
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
            <i class="fas fa-sign-in-alt me-1"></i> {{ __('general.login') }}
        </button>
    </form>

    <p class="text-center mt-3 mb-0 small text-muted">
        {{ __('auth_page.dont_have_account') }}
        <a href="{{ route('register') }}" class="fw-semibold">{{ __('general.register') }}</a>
    </p>
@endsection

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
@endpush
