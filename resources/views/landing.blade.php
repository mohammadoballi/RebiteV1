@php $isRtl = app()->getLocale() === 'ar'; @endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Rebite') }} — {{ __('landing.hero_title') }}</title>

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

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            color: #333;
            overflow-x: hidden;
            margin: 0;
        }

        h1, h2, h3, .hero-title {
            font-family: 'Playfair Display', Georgia, serif;
        }

        /* ── Navbar ── */
        .navbar {
            padding: .8rem 0;
            transition: all .3s;
            background: #fff;
        }

        .navbar.scrolled {
            box-shadow: 0 2px 20px rgba(0,0,0,.08);
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

        .nav-link:hover { color: var(--rb-green) !important; }

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
            transform: translateY(-1px);
        }

        /* ── Hero Photo ── */
        .hero-photo {
            position: relative;
            overflow: hidden;
            padding-top: 70px;
        }

        .hero-photo img {
            width: 100%;
            height: 550px;
            object-fit: cover;
            display: block;
        }

        .hero-photo .hero-curve {
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 80px;
        }

        /* ── Main content area with background image ── */
        .landing-bg-section {
            background-image: url('{{ asset("images/Background-landing.png") }}');
            background-size: cover;
            background-position: center top;
            background-repeat: no-repeat;
        }

        /* ── Hero Text ── */
        .hero-text {
            text-align: center;
            padding: 3.5rem 1rem 3rem;
        }

        .hero-title {
            font-size: 2.8rem;
            font-weight: 700;
            color: #2a2a2a;
            margin-bottom: 1rem;
        }

        .hero-title em {
            font-style: italic;
            color: var(--rb-green);
        }

        .hero-subtitle {
            font-size: 1.15rem;
            color: #666;
            max-width: 500px;
            margin: 0 auto 2rem;
        }

        .btn-hero {
            background-color: var(--rb-orange);
            border: none;
            color: #fff;
            border-radius: 2rem;
            padding: .75rem 2.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            transition: all .3s;
            box-shadow: 0 4px 15px rgba(212, 137, 42, .3);
        }

        .btn-hero:hover {
            background-color: var(--rb-orange-dark);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(212, 137, 42, .4);
        }

        /* ── About Section ── */
        .about-section {
            padding: 4rem 0 5rem;
        }

        .about-title {
            font-size: 2.2rem;
            font-weight: 700;
            color: #2a2a2a;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .about-text {
            max-width: 700px;
            margin: 0 auto;
            text-align: center;
            font-size: 1.05rem;
            line-height: 1.8;
            color: #555;
        }

        .about-text strong {
            color: #2a2a2a;
        }

        /* ── How It Works ── */
        .how-section {
            padding: 4rem 0 5rem;
        }

        .how-title {
            font-size: 2.2rem;
            font-weight: 700;
            text-align: center;
            color: #2a2a2a;
            margin-bottom: 3rem;
        }

        .steps-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0;
            flex-wrap: wrap;
        }

        .step-item {
            text-align: center;
            flex: 0 0 auto;
            max-width: 200px;
        }

        .step-item img {
            width: 140px;
            height: 140px;
            object-fit: contain;
            margin-bottom: 1rem;
        }

        .step-item p {
            font-weight: 600;
            color: #2a2a2a;
            font-size: .95rem;
            line-height: 1.3;
        }

        .step-arrow {
            color: var(--rb-green);
            font-size: 2rem;
            padding: 0 .5rem;
            align-self: flex-start;
            margin-top: 3.5rem;
        }

        @media (max-width: 768px) {
            .steps-row { flex-direction: column; gap: 1.5rem; }
            .step-arrow { transform: rotate(90deg); margin-top: 0; }
            .hero-title { font-size: 2rem; }
            .hero-photo img { height: 350px; }
        }

        /* ── CTA Section ── */
        .cta-section {
            background: linear-gradient(135deg, var(--rb-green) 0%, var(--rb-teal) 100%);
            color: #fff;
            padding: 5rem 0;
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

        /* ── Contact Section ── */
        .contact-section {
            padding: 5rem 0;
            background: #fff;
        }

        .contact-title {
            font-size: 2.2rem;
            font-weight: 700;
            text-align: center;
            color: #2a2a2a;
            margin-bottom: 3rem;
        }

        .contact-card {
            text-align: center;
            padding: 2rem 1.5rem;
            border-radius: 1rem;
            background: var(--rb-cream);
            border: 1px solid #e8e3d9;
            transition: transform .3s;
        }

        .contact-card:hover { transform: translateY(-4px); }

        .contact-card i {
            font-size: 2rem;
            color: var(--rb-green);
            margin-bottom: 1rem;
        }

        /* ── Footer ── */
        .site-footer {
            background: var(--rb-dark);
            color: #b8c5ba;
            padding: 3.5rem 0 1.5rem;
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

        /* ── Fade-in ── */
        .fade-up {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity .6s ease-out, transform .6s ease-out;
        }

        .fade-up.visible {
            opacity: 1;
            transform: translateY(0);
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

        .lang-btn:hover {
            border-color: var(--rb-green);
            color: var(--rb-green);
        }
    </style>
</head>
<body>

{{-- ── Navbar ── --}}
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
                <li class="nav-item"><a class="nav-link" href="#home">{{ __('Home') }}</a></li>
                <li class="nav-item"><a class="nav-link" href="#about">{{ __('landing.about_title') }}</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('how-it-works') }}">{{ __('landing.how_it_works') }}</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('safety-guidelines') }}">{{ __('landing.safety_guidelines') }}</a></li>
                <li class="nav-item"><a class="nav-link" href="#contact">{{ __('landing.contact') }}</a></li>
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

{{-- ── Hero Photo ── --}}
<div id="home" class="hero-photo">
    <img src="{{ asset('images/Hero-image.png') }}" alt="ReBite — Turning surplus food into hope">
    <svg class="hero-curve" viewBox="0 0 1440 80" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M0,80 C360,0 1080,0 1440,80 L1440,80 L0,80 Z" fill="#e8e4dc"/>
    </svg>
</div>

{{-- ── One continuous background section: Hero Text + About + How It Works ── --}}
<div class="landing-bg-section">

    {{-- Hero Text --}}
    <div class="hero-text">
        <h1 class="hero-title fade-up"><em>Turning Surplus Food</em> Into <strong>Hope</strong></h1>
        <p class="hero-subtitle fade-up">{{ __('landing.hero_subtitle') }}</p>
        <a href="{{ route('register') }}" class="btn btn-hero fade-up">{{ __('landing.get_started') }}</a>
    </div>

    {{-- About Section --}}
    <section id="about" class="about-section">
        <div class="container fade-up">
            <h2 class="about-title">{{ __('landing.about_title') }}</h2>
            <p class="about-text">
                At <strong>ReBite</strong>, we believe no food should <strong>go to waste</strong> while people are in need.
                Our platform bridges the gap between surplus and shortage by enabling efficient
                food donation and distribution. We aim to create a smarter, more sustainable
                way to ensure surplus food reaches those who need it most.
            </p>
        </div>
    </section>

    {{-- How It Works Section --}}
    <section id="how-it-works" class="how-section">
        <div class="container">
            <h2 class="how-title fade-up">{{ __('landing.how_it_works') }}</h2>

            <div class="steps-row fade-up">
                <div class="step-item">
                    <img src="{{ asset('images/How-it-work-1.png') }}" alt="{{ __('landing.step1_title') }}">
                    <p>{{ __('landing.step1_title') }}</p>
                </div>
                <div class="step-arrow"><i class="fas fa-arrow-{{ $isRtl ? 'left' : 'right' }}"></i></div>
                <div class="step-item">
                    <img src="{{ asset('images/How-it-work-2.png') }}" alt="{{ __('landing.step2_title') }}">
                    <p>{{ __('landing.step2_title') }}</p>
                </div>
                <div class="step-arrow"><i class="fas fa-arrow-{{ $isRtl ? 'left' : 'right' }}"></i></div>
                <div class="step-item">
                    <img src="{{ asset('images/How-it-work-3.png') }}" alt="{{ __('landing.step3_title') }}">
                    <p>{{ __('landing.step3_title') }}</p>
                </div>
                <div class="step-arrow"><i class="fas fa-arrow-{{ $isRtl ? 'left' : 'right' }}"></i></div>
                <div class="step-item">
                    <img src="{{ asset('images/How-it-work-4.png') }}" alt="{{ __('landing.step4_title') }}">
                    <p>{{ __('landing.step4_title') }}</p>
                </div>
            </div>

            <div class="text-center mt-4 fade-up">
                <a href="{{ route('how-it-works') }}" class="btn btn-outline-success rounded-pill px-4">
                    {{ __('landing.learn_more') }} <i class="fas fa-arrow-{{ $isRtl ? 'left' : 'right' }} ms-1"></i>
                </a>
            </div>
        </div>
    </section>

</div>

{{-- ── CTA Section ── --}}
<section class="cta-section fade-up">
    <div class="container">
        <h2>{{ __('landing.cta_title') }}</h2>
        <p>{{ __('landing.cta_text') }}</p>
        <a href="{{ route('register') }}" class="btn btn-cta-light">
            <i class="fas fa-user-plus me-1"></i> {{ __('general.register') }}
        </a>
    </div>
</section>

{{-- ── Contact Section ── --}}
<section id="contact" class="contact-section">
    <div class="container">
        <h2 class="contact-title fade-up">{{ __('landing.contact') }}</h2>
        <div class="row g-4 justify-content-center">
            <div class="col-md-4 fade-up">
                <div class="contact-card">
                    <i class="fas fa-envelope"></i>
                    <h6>{{ __('Email') }}</h6>
                    <p class="text-muted mb-0">info@rebite.com</p>
                </div>
            </div>
            <div class="col-md-4 fade-up">
                <div class="contact-card">
                    <i class="fas fa-map-marker-alt"></i>
                    <h6>{{ __('Location') }}</h6>
                    <p class="text-muted mb-0">{{ __('landing.location') }}</p>
                </div>
            </div>
            <div class="col-md-4 fade-up">
                <div class="contact-card">
                    <i class="fas fa-phone"></i>
                    <h6>{{ __('Phone') }}</h6>
                    <p class="text-muted mb-0">+962 7XX XXX XXX</p>
                </div>
            </div>
        </div>
    </div>
</section>

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
                    <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i>{{ __('landing.location') }}</li>
                    <li class="mb-2"><i class="fas fa-envelope me-2"></i>info@rebite.com</li>
                </ul>
            </div>
            <div class="col-lg-4">
                <h6>{{ __('landing.quick_links') }}</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="#about">{{ __('landing.about_title') }}</a></li>
                    <li class="mb-2"><a href="{{ route('how-it-works') }}">{{ __('landing.how_it_works') }}</a></li>
                    <li class="mb-2"><a href="{{ route('safety-guidelines') }}">{{ __('landing.safety_guidelines') }}</a></li>
                    <li class="mb-2"><a href="#contact">{{ __('landing.contact') }}</a></li>
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
                    <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i>{{ __('landing.location') }}</li>
                    <li class="mb-2"><i class="fas fa-envelope me-2"></i>info@rebite.com</li>
                </ul>
            </div>
        </div>
        <hr class="border-secondary my-3">
        <div class="text-center small">
            <p class="mb-1">&copy; {{ date('Y') }} ReBite · {{ __('landing.all_rights') }}</p>
            <a href="#" class="me-2">{{ __('Privacy Policy') }}</a> · <a href="#" class="ms-2">{{ __('Terms of Service') }}</a>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    window.addEventListener('scroll', function () {
        document.querySelector('.navbar').classList.toggle('scrolled', window.scrollY > 50);
    });

    const observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) entry.target.classList.add('visible');
        });
    }, { threshold: 0.15 });

    document.querySelectorAll('.fade-up').forEach(function (el) { observer.observe(el); });

    document.querySelectorAll('a[href^="#"]').forEach(function (link) {
        link.addEventListener('click', function (e) {
            var target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                var top = target.getBoundingClientRect().top + window.pageYOffset - 80;
                window.scrollTo({ top: top, behavior: 'smooth' });
                var toggler = document.querySelector('.navbar-collapse.show');
                if (toggler) { new bootstrap.Collapse(toggler).hide(); }
            }
        });
    });
</script>
</body>
</html>
