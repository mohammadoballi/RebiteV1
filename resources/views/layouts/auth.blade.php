@php $isRtl = app()->getLocale() === 'ar'; @endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Rebite') — {{ config('app.name', 'Rebite') }}</title>

    @if($isRtl)
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif

    <style>
        :root {
            --rb-primary: #28a745;
            --rb-secondary: #20c997;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #28a745 0%, #20c997 50%, #e8f5e9 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .auth-wrapper {
            width: 100%;
            max-width: 500px;
            padding: 1rem;
        }

        .auth-card {
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, .15);
            overflow: hidden;
        }

        .auth-card .card-body {
            padding: 2.5rem 2rem;
        }

        .auth-logo {
            text-align: center;
            padding: 2rem 2rem 0;
        }

        .auth-logo img {
            max-height: 64px;
        }

        .auth-logo .logo-fallback {
            font-size: 2rem;
            font-weight: 800;
            color: var(--rb-primary);
            letter-spacing: 2px;
        }

        .btn-primary {
            background-color: var(--rb-primary);
            border-color: var(--rb-primary);
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background-color: #218838;
            border-color: #1e7e34;
        }

        .form-control:focus {
            border-color: var(--rb-secondary);
            box-shadow: 0 0 0 .2rem rgba(32, 201, 151, .25);
        }

        a {
            color: var(--rb-primary);
        }

        a:hover {
            color: #1e7e34;
        }

        .lang-switcher {
            text-align: center;
            margin-top: 1.5rem;
        }

        .lang-switcher a {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            opacity: .85;
            transition: opacity .2s;
        }

        .lang-switcher a:hover {
            opacity: 1;
            color: #fff;
        }
    </style>

    @stack('styles')
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-card">
        <div class="auth-logo">
            <a href="{{ url('/') }}">
                <img src="{{ asset('images/logo.png') }}"
                     alt="{{ config('app.name') }}"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='block';">
                <span class="logo-fallback" style="display:none;">REBITE</span>
            </a>
        </div>

        <div class="card-body">
            @yield('content')
        </div>
    </div>

    <div class="lang-switcher">
        @if($isRtl)
            <a href="{{ route('language.switch', 'en') }}">English</a>
        @else
            <a href="{{ route('language.switch', 'ar') }}">العربية</a>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
</script>

@if(session('success'))
<script>Swal.fire({ icon: 'success', title: '{{ __("Success") }}', text: '{{ session("success") }}', confirmButtonColor: '#28a745' });</script>
@endif

@if(session('error'))
<script>Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ session("error") }}', confirmButtonColor: '#28a745' });</script>
@endif

@stack('scripts')
</body>
</html>
