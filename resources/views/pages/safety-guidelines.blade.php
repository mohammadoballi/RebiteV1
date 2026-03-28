@php $isRtl = app()->getLocale() === 'ar'; @endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('landing.safety_guidelines') }} — {{ config('app.name', 'Rebite') }}</title>

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
            --rb-teal: #4a9d6e;
            --rb-cream: #faf7f2;
            --rb-dark: #2d3e30;
        }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            color: #333;
            margin: 0;
        }

        h1, h2, h3 { font-family: 'Playfair Display', Georgia, serif; }

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
            transition: color .2s;
        }

        .nav-link:hover, .nav-link.active { color: var(--rb-green) !important; }

        .btn-get-started {
            background-color: var(--rb-orange);
            border: none;
            color: #fff;
            border-radius: 2rem;
            padding: .5rem 1.5rem;
            font-weight: 600;
            transition: all .2s;
        }

        .btn-get-started:hover {
            background-color: var(--rb-orange-dark);
            color: #fff;
        }

        .lang-btn {
            border: 1px solid #dee2e6;
            background: transparent;
            border-radius: .375rem;
            padding: .25rem .6rem;
            font-size: .85rem;
            color: #555;
            text-decoration: none;
            transition: all .2s;
        }

        .lang-btn:hover { border-color: var(--rb-green); color: var(--rb-green); }

        .page-hero {
            background-image: url('{{ asset("images/Background-landing.png") }}');
            background-size: cover;
            background-position: center;
            padding: 7rem 1rem 4rem;
            text-align: center;
        }

        .page-hero h1 {
            font-size: 2.6rem;
            font-weight: 700;
            color: #2a2a2a;
            margin-bottom: .75rem;
        }

        .page-hero p {
            font-size: 1.1rem;
            color: #555;
            max-width: 600px;
            margin: 0 auto;
        }

        .safety-icon-circle {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--rb-green), var(--rb-teal));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.2rem;
            color: #fff;
            font-size: 1.8rem;
            box-shadow: 0 4px 15px rgba(58,125,68,.25);
        }

        .safety-content {
            padding: 4rem 0 5rem;
        }

        .safety-card {
            background: #fff;
            border-radius: 1.25rem;
            box-shadow: 0 4px 20px rgba(0,0,0,.06);
            border: 1px solid #e8e3d9;
            overflow: hidden;
        }

        .safety-card-body { padding: 2.5rem; }

        .safety-intro {
            font-size: 1.05rem;
            line-height: 1.8;
            color: #444;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #eee;
        }

        .safety-point {
            display: flex;
            gap: 1.2rem;
            margin-bottom: 1.5rem;
            align-items: flex-start;
        }

        .safety-point:last-child { margin-bottom: 0; }

        .safety-point-icon {
            flex: 0 0 50px;
            width: 50px;
            height: 50px;
            background: #f0f8f2;
            border-radius: .75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--rb-green);
            font-size: 1.3rem;
        }

        .safety-point h5 {
            font-family: 'Inter', sans-serif;
            font-size: 1.05rem;
            font-weight: 700;
            color: #2a2a2a;
            margin-bottom: .3rem;
        }

        .safety-point p {
            color: #555;
            font-size: .95rem;
            line-height: 1.7;
            margin-bottom: 0;
        }

        .cta-section {
            background: linear-gradient(135deg, var(--rb-green) 0%, var(--rb-teal) 100%);
            color: #fff;
            padding: 4rem 0;
            text-align: center;
        }

        .cta-section h2 { font-size: 2rem; margin-bottom: 1rem; }

        .cta-section p {
            opacity: .85;
            max-width: 500px;
            margin: 0 auto 2rem;
            font-size: 1.05rem;
        }

        .btn-cta-light {
            background: #fff;
            color: var(--rb-green);
            border: none;
            border-radius: 2rem;
            padding: .75rem 2.5rem;
            font-weight: 600;
            font-size: 1.05rem;
            transition: all .3s;
        }

        .btn-cta-light:hover {
            background: var(--rb-cream);
            color: var(--rb-green-dark);
            transform: translateY(-2px);
        }

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

        .footer-brand img { height: 36px; filter: brightness(0) invert(1); }

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

        .fade-up {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity .6s ease-out, transform .6s ease-out;
        }

        .fade-up.visible { opacity: 1; transform: translateY(0); }

        @media (max-width: 768px) {
            .safety-point { flex-direction: column; text-align: center; align-items: center; }
            .safety-card-body { padding: 1.5rem; }
            .page-hero h1 { font-size: 2rem; }
        }
    </style>
</head>
<body>

{{-- Navbar --}}
<nav class="navbar navbar-expand-lg fixed-top">
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
                <li class="nav-item"><a class="nav-link active" href="{{ route('safety-guidelines') }}">{{ __('landing.safety_guidelines') }}</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('/#contact') }}">{{ __('landing.contact') }}</a></li>
            </ul>

            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('language.switch', $isRtl ? 'en' : 'ar') }}" class="lang-btn">
                    {{ $isRtl ? 'English' : 'العربية' }}
                </a>
                @auth
                    <a href="{{ route('home') }}" class="btn btn-get-started">
                        <i class="fas fa-tachometer-alt me-1"></i>{{ __('general.dashboard') }}
                    </a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-get-started">{{ __('landing.get_started') }}</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

{{-- Hero --}}
<div class="page-hero">
    <div class="safety-icon-circle fade-up">
        <i class="fas fa-shield-alt"></i>
    </div>
    <h1 class="fade-up">{{ __('landing.safety_guidelines') }}</h1>
    <p class="fade-up">{{ __('landing.safety_subtitle') }}</p>
</div>

{{-- Content --}}
<section class="safety-content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 fade-up">
                <div class="safety-card">
                    <div class="safety-card-body">
                        <p class="safety-intro">{{ __('landing.safety_intro') }}</p>

                        <div class="safety-point">
                            <div class="safety-point-icon"><i class="fas fa-hand-sparkles"></i></div>
                            <div>
                                <h5>{{ __('landing.safety_hygiene_title') }}</h5>
                                <p>{{ __('landing.safety_hygiene_text') }}</p>
                            </div>
                        </div>

                        <div class="safety-point">
                            <div class="safety-point-icon"><i class="fas fa-calendar-check"></i></div>
                            <div>
                                <h5>{{ __('landing.safety_freshness_title') }}</h5>
                                <p>{{ __('landing.safety_freshness_text') }}</p>
                            </div>
                        </div>

                        <div class="safety-point">
                            <div class="safety-point-icon"><i class="fas fa-check-double"></i></div>
                            <div>
                                <h5>{{ __('landing.safety_compliance_title') }}</h5>
                                <p>{{ __('landing.safety_compliance_text') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="cta-section fade-up">
    <div class="container">
        <h2>{{ __('landing.cta_title') }}</h2>
        <p>{{ __('landing.cta_text') }}</p>
        <a href="{{ route('register') }}" class="btn btn-cta-light">
            <i class="fas fa-user-plus me-1"></i> {{ __('general.register') }}
        </a>
    </div>
</section>

{{-- Footer --}}
<footer class="site-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="footer-brand">
                    <img src="{{ asset('images/logo.png') }}" alt="ReBite" onerror="this.style.display='none';">
                    <span>ReBite</span>
                </div>
                <ul class="list-unstyled small">
                    <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i>{{ __('landing.location') }}</li>
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
                    <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i>{{ __('landing.location') }}</li>
                    <li class="mb-2"><i class="fas fa-envelope me-2"></i>info@rebite.com</li>
                </ul>
            </div>
        </div>
        <hr class="border-secondary my-3">
        <div class="text-center small">
            <p class="mb-1">&copy; {{ date('Y') }} ReBite · {{ __('landing.all_rights') }}</p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) entry.target.classList.add('visible');
        });
    }, { threshold: 0.15 });
    document.querySelectorAll('.fade-up').forEach(function (el) { observer.observe(el); });
</script>
</body>
</html>
