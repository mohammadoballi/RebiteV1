@extends('layouts.auth')

@section('title', __('auth_page.forgot_password'))

@section('content')
    <h4 class="text-center fw-bold mb-2">{{ __('auth_page.reset_password') }}</h4>
    <p class="text-center text-muted small mb-4">{{ __('auth_page.reset_instructions') }}</p>

    @if(session('status'))
        <div class="alert alert-success small">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        {{-- Email --}}
        <div class="mb-4">
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

        {{-- Submit --}}
        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
            <i class="fas fa-paper-plane me-1"></i> {{ __('auth_page.send_reset_link') }}
        </button>
    </form>

    <p class="text-center mt-3 mb-0 small text-muted">
        <a href="{{ route('login') }}" class="fw-semibold">
            <i class="fas fa-arrow-left me-1"></i>{{ __('general.back') }} {{ __('general.login') }}
        </a>
    </p>
@endsection

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
@endpush
