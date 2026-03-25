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

    <style>
        :root {
            --rb-green: #28a745;
            --rb-green-dark: #198754;
            --rb-teal: #20c997;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            overflow-x: hidden;
        }

        /* ── Navbar ── */
        .navbar-brand {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--rb-green) !important;
            letter-spacing: 1px;
        }

        .navbar {
            transition: box-shadow .3s;
        }

        .navbar.scrolled {
            box-shadow: 0 2px 20px rgba(0,0,0,.1) !important;
        }

        .nav-link {
            font-weight: 500;
            color: #444 !important;
            transition: color .2s;
        }

        .nav-link:hover { color: var(--rb-green) !important; }

        .btn-green {
            background-color: var(--rb-green);
            border-color: var(--rb-green);
            color: #fff;
        }

        .btn-green:hover, .btn-green:focus {
            background-color: var(--rb-green-dark);
            border-color: var(--rb-green-dark);
            color: #fff;
        }

        .btn-outline-green {
            border-color: var(--rb-green);
            color: var(--rb-green);
        }

        .btn-outline-green:hover, .btn-outline-green:focus {
            background-color: var(--rb-green);
            color: #fff;
        }

        /* ── Hero ── */
        .hero-section {
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 40%, #a5d6a7 100%);
            padding: 8rem 0 5rem;
            position: relative;
            overflow: hidden;
        }

        .hero-section::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 80px;
            background: #fff;
            clip-path: ellipse(55% 100% at 50% 100%);
        }

        .hero-emoji {
            font-size: 4rem;
            animation: float 3s ease-in-out infinite;
            display: inline-block;
        }

        .hero-emoji:nth-child(2) { animation-delay: .5s; }
        .hero-emoji:nth-child(3) { animation-delay: 1s; }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        /* ── Sections ── */
        section { padding: 5rem 0; }

        .section-title {
            font-weight: 700;
            color: var(--rb-green-dark);
            margin-bottom: 1rem;
        }

        .section-subtitle {
            color: #666;
            max-width: 600px;
            margin: 0 auto 3rem;
        }

        /* ── Step cards ── */
        .step-card {
            text-align: center;
            padding: 2.5rem 1.5rem;
            border-radius: 1rem;
            background: #fff;
            border: 1px solid #e9ecef;
            transition: transform .3s, box-shadow .3s;
            position: relative;
        }

        .step-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 30px rgba(40, 167, 69, .15);
        }

        .step-number {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--rb-green);
            color: #fff;
            font-weight: 700;
            font-size: 1.3rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .step-icon {
            font-size: 2.5rem;
            color: var(--rb-teal);
            margin-bottom: 1rem;
        }

        /* ── Feature cards ── */
        .feature-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 15px rgba(0,0,0,.06);
            transition: transform .3s, box-shadow .3s;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 10px 30px rgba(40, 167, 69, .15);
        }

        .feature-card .card-body { padding: 2rem; }

        .feature-icon {
            width: 60px;
            height: 60px;
            border-radius: .75rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #fff;
            margin-bottom: 1rem;
        }

        .feature-icon.donor-bg   { background: var(--rb-green); }
        .feature-icon.charity-bg { background: var(--rb-green-dark); }
        .feature-icon.volunteer-bg { background: var(--rb-teal); }

        .feature-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .feature-list li {
            padding: .4rem 0;
            color: #555;
        }

        .feature-list li::before {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            color: var(--rb-green);
            margin-{{ $isRtl ? 'left' : 'right' }}: .5rem;
        }

        /* ── CTA ── */
        .cta-section {
            background: linear-gradient(135deg, var(--rb-green) 0%, var(--rb-teal) 100%);
            color: #fff;
        }

        /* ── About ── */
        .about-icon {
            font-size: 5rem;
            color: var(--rb-teal);
            opacity: .8;
        }

        /* ── Footer ── */
        .site-footer {
            background: #1a1a2e;
            color: #ccc;
            padding: 3rem 0 1.5rem;
        }

        .site-footer a {
            color: #aaa;
            text-decoration: none;
            transition: color .2s;
        }

        .site-footer a:hover { color: var(--rb-teal); }

        .footer-brand {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--rb-teal);
        }

        /* ── Fade-in animation ── */
        .fade-up {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity .6s ease-out, transform .6s ease-out;
        }

        .fade-up.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* ── Lang switcher ── */
        .lang-btn {
            border: 1px solid #dee2e6;
            background: transparent;
            border-radius: .375rem;
            padding: .25rem .6rem;
            font-size: .85rem;
            color: #555;
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
<nav class="navbar navbar-expand-lg bg-white fixed-top shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            <i class="fas fa-leaf me-1"></i>REBITE
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#about">{{ __('landing.about_title') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#how-it-works">{{ __('landing.how_it_works') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#features">{{ __('landing.features_title') }}</a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('language.switch', $isRtl ? 'en' : 'ar') }}" class="lang-btn text-decoration-none">
                    {{ $isRtl ? 'English' : 'العربية' }}
                </a>
                @auth
                    <a href="{{ route(auth()->user()->roles->first()?->name . '.dashboard') }}" class="btn btn-outline-green btn-sm px-3">
                        <i class="fas fa-tachometer-alt me-1"></i>{{ __('general.dashboard') }}
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-green btn-sm px-3">
                            <i class="fas fa-sign-out-alt me-1"></i>{{ __('general.logout') }}
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-green btn-sm px-3">{{ __('general.login') }}</a>
                    <a href="{{ route('register') }}" class="btn btn-green btn-sm px-3">{{ __('general.register') }}</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

{{-- ── Hero Section ── --}}
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <h1 class="display-4 fw-bold text-dark mb-3">{{ __('landing.hero_title') }}</h1>
                <p class="lead text-muted mb-4" style="max-width:520px;">{{ __('landing.hero_subtitle') }}</p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="{{ route('register') }}" class="btn btn-green btn-lg px-4">
                        <i class="fas fa-rocket me-1"></i> {{ __('landing.get_started') }}
                    </a>
                    <a href="#about" class="btn btn-outline-green btn-lg px-4">
                        <i class="fas fa-arrow-down me-1"></i> {{ __('landing.learn_more') }}
                    </a>
                </div>
            </div>
            <div class="col-lg-5 text-center mt-4 mt-lg-0">
                <div class="hero-emoji">🍽️</div>
                <div class="hero-emoji">💚</div>
                <div class="hero-emoji">🤝</div>
                <div class="mt-3">
                    <svg viewBox="0 0 400 250" width="320" xmlns="http://www.w3.org/2000/svg">
                        <ellipse cx="200" cy="230" rx="180" ry="20" fill="#c8e6c9" opacity=".5"/>
                        <rect x="120" y="80" width="160" height="120" rx="16" fill="#fff" stroke="#28a745" stroke-width="2"/>
                        <circle cx="170" cy="130" r="18" fill="#ffcc02"/>
                        <circle cx="210" cy="140" r="14" fill="#ff6b6b"/>
                        <circle cx="240" cy="125" r="12" fill="#28a745"/>
                        <path d="M150 160 Q200 185 250 160" stroke="#20c997" stroke-width="3" fill="none" stroke-linecap="round"/>
                        <path d="M185 80 L200 50 L215 80" fill="none" stroke="#198754" stroke-width="2" stroke-linecap="round"/>
                        <line x1="200" y1="55" x2="200" y2="40" stroke="#198754" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ── About Section ── --}}
<section id="about" class="bg-white">
    <div class="container">
        <div class="row align-items-center fade-up">
            <div class="col-lg-5 text-center mb-4 mb-lg-0">
                <div class="about-icon">
                    <i class="fas fa-hand-holding-heart"></i>
                </div>
            </div>
            <div class="col-lg-7">
                <h2 class="section-title">{{ __('landing.about_title') }}</h2>
                <p class="text-muted fs-5 lh-lg">{{ __('landing.about_text') }}</p>
                <div class="d-flex gap-4 mt-4">
                    <div class="text-center">
                        <div class="fw-bold fs-3 text-success">♻️</div>
                        <small class="text-muted">{{ __('landing.reduce_waste') }}</small>
                    </div>
                    <div class="text-center">
                        <div class="fw-bold fs-3 text-success">🍎</div>
                        <small class="text-muted">{{ __('landing.feed_community') }}</small>
                    </div>
                    <div class="text-center">
                        <div class="fw-bold fs-3 text-success">🌍</div>
                        <small class="text-muted">{{ __('landing.make_impact') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ── How It Works ── --}}
<section id="how-it-works" style="background: #f8faf8;">
    <div class="container text-center">
        <h2 class="section-title">{{ __('landing.how_it_works') }}</h2>
        <p class="section-subtitle">{{ __('landing.how_it_works_subtitle') }}</p>

        <div class="row g-4">
            <div class="col-md-4 fade-up">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <div class="step-icon"><i class="fas fa-utensils"></i></div>
                    <h5 class="fw-bold">{{ __('landing.step1_title') }}</h5>
                    <p class="text-muted">{{ __('landing.step1_text') }}</p>
                </div>
            </div>
            <div class="col-md-4 fade-up">
                <div class="step-card">
                    <div class="step-number">2</div>
                    <div class="step-icon"><i class="fas fa-handshake"></i></div>
                    <h5 class="fw-bold">{{ __('landing.step2_title') }}</h5>
                    <p class="text-muted">{{ __('landing.step2_text') }}</p>
                </div>
            </div>
            <div class="col-md-4 fade-up">
                <div class="step-card">
                    <div class="step-number">3</div>
                    <div class="step-icon"><i class="fas fa-truck"></i></div>
                    <h5 class="fw-bold">{{ __('landing.step3_title') }}</h5>
                    <p class="text-muted">{{ __('landing.step3_text') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ── Features Section ── --}}
<section id="features" class="bg-white">
    <div class="container text-center">
        <h2 class="section-title">{{ __('landing.features_title') }}</h2>
        <p class="section-subtitle">{{ __('landing.features_subtitle') }}</p>

        <div class="row g-4">
            {{-- Donor Features --}}
            <div class="col-lg-4 fade-up">
                <div class="card feature-card">
                    <div class="card-body text-start">
                        <div class="feature-icon donor-bg"><i class="fas fa-burger"></i></div>
                        <h5 class="fw-bold mb-3">{{ __('landing.donor_features') }}</h5>
                        <ul class="feature-list">
                            <li>{{ __('landing.donor_f1') }}</li>
                            <li>{{ __('landing.donor_f2') }}</li>
                            <li>{{ __('landing.donor_f3') }}</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Charity Features --}}
            <div class="col-lg-4 fade-up">
                <div class="card feature-card">
                    <div class="card-body text-start">
                        <div class="feature-icon charity-bg"><i class="fas fa-building-ngo"></i></div>
                        <h5 class="fw-bold mb-3">{{ __('landing.charity_features') }}</h5>
                        <ul class="feature-list">
                            <li>{{ __('landing.charity_f1') }}</li>
                            <li>{{ __('landing.charity_f2') }}</li>
                            <li>{{ __('landing.charity_f3') }}</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Volunteer Features --}}
            <div class="col-lg-4 fade-up">
                <div class="card feature-card">
                    <div class="card-body text-start">
                        <div class="feature-icon volunteer-bg"><i class="fas fa-people-carry-box"></i></div>
                        <h5 class="fw-bold mb-3">{{ __('landing.volunteer_features') }}</h5>
                        <ul class="feature-list">
                            <li>{{ __('landing.volunteer_f1') }}</li>
                            <li>{{ __('landing.volunteer_f2') }}</li>
                            <li>{{ __('landing.volunteer_f3') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ── CTA Section ── --}}
<section class="cta-section text-center">
    <div class="container fade-up">
        <h2 class="display-6 fw-bold mb-3">{{ __('landing.cta_title') }}</h2>
        <p class="lead mb-4 opacity-75" style="max-width:550px;margin:0 auto;">{{ __('landing.cta_text') }}</p>
        <a href="{{ route('register') }}" class="btn btn-light btn-lg px-5 text-success fw-semibold">
            <i class="fas fa-user-plus me-1"></i> {{ __('general.register') }}
        </a>
    </div>
</section>

{{-- ── Footer ── --}}
<footer class="site-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="footer-brand mb-2"><i class="fas fa-leaf me-1"></i>REBITE</div>
                <p class="small">{{ __('landing.footer_tagline') }}</p>
            </div>
            <div class="col-lg-4">
                <h6 class="text-white mb-3">{{ __('landing.quick_links') }}</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="#about">{{ __('landing.about_title') }}</a></li>
                    <li class="mb-2"><a href="#how-it-works">{{ __('landing.how_it_works') }}</a></li>
                    <li class="mb-2"><a href="#features">{{ __('landing.features_title') }}</a></li>
                    <li class="mb-2"><a href="{{ route('register') }}">{{ __('general.register') }}</a></li>
                </ul>
            </div>
            <div class="col-lg-4">
                <h6 class="text-white mb-3">{{ __('landing.contact') }}</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><i class="fas fa-envelope me-2"></i>info@rebite.com</li>
                    <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i>{{ __('landing.location') }}</li>
                </ul>
                <div class="mt-3 d-flex gap-3">
                    <a href="#"><i class="fab fa-twitter fs-5"></i></a>
                    <a href="#"><i class="fab fa-instagram fs-5"></i></a>
                    <a href="#"><i class="fab fa-facebook fs-5"></i></a>
                </div>
            </div>
        </div>
        <hr class="border-secondary my-3">
        <p class="text-center small mb-0">&copy; {{ date('Y') }} Rebite. {{ __('landing.all_rights') }}</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Navbar shadow on scroll
    window.addEventListener('scroll', function () {
        document.querySelector('.navbar').classList.toggle('scrolled', window.scrollY > 50);
    });

    // Fade-up on scroll
    const observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.15 });

    document.querySelectorAll('.fade-up').forEach(function (el) {
        observer.observe(el);
    });

    // Smooth scroll for anchor links & close mobile menu
    document.querySelectorAll('a[href^="#"]').forEach(function (link) {
        link.addEventListener('click', function (e) {
            var target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                var offset = 80;
                var top = target.getBoundingClientRect().top + window.pageYOffset - offset;
                window.scrollTo({ top: top, behavior: 'smooth' });

                var toggler = document.querySelector('.navbar-collapse.show');
                if (toggler) {
                    new bootstrap.Collapse(toggler).hide();
                }
            }
        });
    });
</script>
</body>
</html>
