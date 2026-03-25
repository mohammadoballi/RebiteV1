@php $isRtl = app()->getLocale() === 'ar'; @endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('general.dashboard')) — {{ config('app.name', 'Rebite') }}</title>

    @if($isRtl)
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif

    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --rb-primary: #28a745;
            --rb-primary-dark: #218838;
            --rb-secondary: #20c997;
            --rb-sidebar-bg: #198754;
            --rb-sidebar-width: 250px;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            overflow-x: hidden;
        }

        .rb-navbar {
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,.08);
            z-index: 1040;
            border-bottom: 3px solid var(--rb-primary);
        }
        .rb-navbar .navbar-brand { font-weight: 700; font-size: 1.35rem; color: var(--rb-primary); }
        .rb-navbar .nav-link { color: #555; }
        .rb-navbar .nav-link:hover { color: var(--rb-primary); }
        .rb-navbar .badge-notify {
            position: absolute; top: 2px;
            {{ $isRtl ? 'left' : 'right' }}: 2px;
            font-size: .6rem; padding: 3px 5px;
        }

        .rb-sidebar {
            position: fixed; top: 0;
            {{ $isRtl ? 'right' : 'left' }}: 0;
            width: var(--rb-sidebar-width); height: 100vh;
            background: var(--rb-sidebar-bg);
            padding-top: 70px; z-index: 1030;
            transition: transform .3s ease; overflow-y: auto;
        }
        .rb-sidebar .nav-link {
            color: rgba(255,255,255,.8); padding: .85rem 1.5rem;
            font-size: .925rem; display: flex; align-items: center; gap: .75rem;
            border-{{ $isRtl ? 'right' : 'left' }}: 3px solid transparent; transition: all .2s;
        }
        .rb-sidebar .nav-link i { width: 20px; text-align: center; }
        .rb-sidebar .nav-link:hover { background: rgba(255,255,255,.1); color: #fff; }
        .rb-sidebar .nav-link.active {
            background: rgba(255,255,255,.15); color: #fff;
            border-{{ $isRtl ? 'right' : 'left' }}-color: #fff; font-weight: 600;
        }
        .rb-sidebar .sidebar-header {
            padding: 1rem 1.5rem .5rem; color: rgba(255,255,255,.5);
            font-size: .75rem; text-transform: uppercase; letter-spacing: 1px;
        }

        .rb-main {
            {{ $isRtl ? 'margin-right' : 'margin-left' }}: var(--rb-sidebar-width);
            padding-top: 80px; min-height: 100vh; transition: margin .3s ease;
        }
        .rb-main .content-wrapper { padding: 1.5rem; }

        .notification-dropdown { width: 320px; max-height: 400px; overflow-y: auto; }
        .notification-dropdown .dropdown-item { white-space: normal; padding: .75rem 1rem; border-bottom: 1px solid #f0f0f0; }
        .notification-dropdown .dropdown-item.unread { background-color: #f0fdf4; }
        .notification-dropdown .notif-time { font-size: .75rem; color: #999; }

        .btn-primary { background-color: var(--rb-primary); border-color: var(--rb-primary); }
        .btn-primary:hover, .btn-primary:focus { background-color: var(--rb-primary-dark); border-color: #1e7e34; }
        .btn-outline-primary { color: var(--rb-primary); border-color: var(--rb-primary); }
        .btn-outline-primary:hover { background-color: var(--rb-primary); border-color: var(--rb-primary); }
        .form-control:focus, .form-select:focus { border-color: var(--rb-secondary); box-shadow: 0 0 0 .2rem rgba(32,201,151,.25); }

        .page-header { margin-bottom: 1.5rem; }
        .page-header h1 { font-size: 1.5rem; font-weight: 700; color: #333; }
        .card { border: none; border-radius: .75rem; box-shadow: 0 2px 10px rgba(0,0,0,.06); }

        .sidebar-toggler { display: none; }
        @media (max-width: 991.98px) {
            .rb-sidebar { transform: translate{{ $isRtl ? 'X(100%)' : 'X(-100%)' }}; }
            .rb-sidebar.show { transform: translateX(0); }
            .rb-main { {{ $isRtl ? 'margin-right' : 'margin-left' }}: 0; }
            .sidebar-toggler { display: inline-flex; }
            .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.4); z-index: 1025; }
            .sidebar-overlay.show { display: block; }
        }

        table.dataTable thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid var(--rb-primary) !important;
            font-weight: 600;
        }
    </style>

    @stack('styles')
</head>
<body>

{{-- Sidebar --}}
<aside class="rb-sidebar" id="sidebar">
    <div class="sidebar-header">{{ __('Navigation') }}</div>
    <nav class="nav flex-column">
        <a class="nav-link {{ request()->routeIs('charity.dashboard') ? 'active' : '' }}"
           href="{{ route('charity.dashboard') }}">
            <i class="fas fa-tachometer-alt"></i> {{ __('general.dashboard') }}
        </a>
        <a class="nav-link {{ request()->routeIs('charity.donations.*') ? 'active' : '' }}"
           href="{{ route('charity.donations.index') }}">
            <i class="fas fa-search"></i> {{ __('Available Donations') }}
        </a>
        <a class="nav-link {{ request()->routeIs('charity.my-requests*') ? 'active' : '' }}"
           href="{{ route('charity.my-requests') }}">
            <i class="fas fa-clipboard-list"></i> {{ __('My Requests') }}
        </a>
        <a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}"
           href="{{ route('profile.show') }}">
            <i class="fas fa-user"></i> {{ __('Profile') }}
        </a>
    </nav>
</aside>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

{{-- Navbar --}}
<nav class="navbar rb-navbar fixed-top px-3">
    <div class="d-flex align-items-center">
        <button class="btn sidebar-toggler me-2" type="button" id="sidebarToggle">
            <i class="fas fa-bars fa-lg"></i>
        </button>
        <a class="navbar-brand mb-0" href="{{ route('charity.dashboard') }}">
            <i class="fas fa-leaf me-1"></i> Rebite
        </a>
    </div>

    <div class="d-flex align-items-center gap-2">
        <x-notification-bell />

        <div class="dropdown">
            <button class="btn btn-link nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-globe"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('language.switch', 'en') }}">English</a></li>
                <li><a class="dropdown-item" href="{{ route('language.switch', 'ar') }}">العربية</a></li>
            </ul>
        </div>

        <div class="dropdown">
            <button class="btn btn-link nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user-circle me-1"></i> {{ auth()->user()->name ?? '' }}
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="{{ route('profile.show') }}">
                        <i class="fas fa-user me-2"></i> {{ __('Profile') }}
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="dropdown-item text-danger" type="submit">
                            <i class="fas fa-sign-out-alt me-2"></i> {{ __('Logout') }}
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main class="rb-main">
    <div class="content-wrapper">
        @yield('content')
    </div>
</main>

@yield('modals')

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    document.getElementById('sidebarToggle')?.addEventListener('click', function () {
        document.getElementById('sidebar').classList.toggle('show');
        document.getElementById('sidebarOverlay').classList.toggle('show');
    });
    document.getElementById('sidebarOverlay')?.addEventListener('click', function () {
        document.getElementById('sidebar').classList.remove('show');
        this.classList.remove('show');
    });
</script>

@if(session('success'))
<script>Swal.fire({ icon: 'success', title: '{{ __("Success") }}', text: '{{ session("success") }}', confirmButtonColor: '#28a745' });</script>
@endif
@if(session('error'))
<script>Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ session("error") }}', confirmButtonColor: '#28a745' });</script>
@endif

<script src="{{ asset('js/common.js') }}"></script>
@stack('scripts')
</body>
</html>
