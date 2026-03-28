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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --rb-green: #3a7d44;
            --rb-green-dark: #2d5f34;
            --rb-orange: #d4892a;
            --rb-cream: #faf7f2;
            --rb-dark: #2d3e30;
        }

        body {
            min-height: 100vh;
            background-image: url('{{ asset("images/Background-landing.png") }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', 'Segoe UI', sans-serif;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: rgba(255,255,255,.3);
            z-index: 0;
        }

        h1, h2, h3, h4 {
            font-family: 'Playfair Display', Georgia, serif;
        }

        .auth-wrapper {
            width: 100%;
            max-width: 480px;
            padding: 1rem;
            position: relative;
            z-index: 1;
        }

        .auth-card {
            background: rgba(255,255,255,.93);
            backdrop-filter: blur(10px);
            border-radius: 1.5rem;
            box-shadow: 0 10px 40px rgba(0,0,0,.1);
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
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 800;
            color: var(--rb-green);
            letter-spacing: 2px;
        }

        .btn-primary {
            background-color: var(--rb-green);
            border-color: var(--rb-green);
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background-color: var(--rb-green-dark);
            border-color: var(--rb-green-dark);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--rb-green);
            box-shadow: 0 0 0 .2rem rgba(58, 125, 68, .15);
        }

        a { color: var(--rb-green); }
        a:hover { color: var(--rb-green-dark); }

        .lang-switcher {
            text-align: center;
            margin-top: 1.5rem;
        }

        .lang-switcher a {
            color: #555;
            text-decoration: none;
            font-weight: 500;
            background: rgba(255,255,255,.7);
            padding: .3rem .8rem;
            border-radius: 1rem;
            font-size: .85rem;
            transition: all .2s;
        }

        .lang-switcher a:hover {
            background: rgba(255,255,255,.9);
            color: var(--rb-green);
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
<script>Swal.fire({ icon: 'success', title: '{{ __("Success") }}', text: '{{ session("success") }}', confirmButtonColor: '#3a7d44' });</script>
@endif

@if(session('error'))
<script>Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ session("error") }}', confirmButtonColor: '#3a7d44' });</script>
@endif

@stack('scripts')
</body>
</html>
