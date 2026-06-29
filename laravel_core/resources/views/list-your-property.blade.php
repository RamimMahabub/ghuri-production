<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>List Your Property on GHURI – Reach Thousands of Travelers</title>
    <meta name="description" content="Partner with GHURI and list your hotel, resort, or homestay. Reach thousands of travelers across Bangladesh and beyond. Get your first booking fast.">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; margin: 0; }

        /* ── NAVBAR ── */
        .lyp-nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 100;
            height: 68px;
            background: rgba(20, 2, 4, 0.97);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid rgba(255,255,255,0.07);
            display: flex;
            align-items: center;
        }
        .lyp-nav-inner {
            max-width: 1152px;
            margin: 0 auto;
            padding: 0 24px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .lyp-logo {
            font-family: 'Outfit', sans-serif;
            font-weight: 900;
            font-size: 1.6rem;
            letter-spacing: -0.04em;
            color: white;
            text-decoration: none;
        }
        .lyp-logo span { color: #ff4b55; }

        /* ── HERO ── */
        .lyp-hero {
            background: linear-gradient(135deg, #140103 0%, #5c0510 30%, #c40c13 65%, #e8353b 100%);
            padding-top: 110px;   /* clears 68px fixed navbar + extra breathing room */
            padding-bottom: 72px;
            padding-left: 24px;
            padding-right: 24px;
            position: relative;
            overflow: hidden;
            min-height: 92vh;
            display: flex;
            align-items: center;
        }
        .lyp-hero::before {
            content: '';
            position: absolute;
            width: 680px; height: 680px;
            background: radial-gradient(circle, rgba(255,70,80,0.22) 0%, transparent 70%);
            top: -180px; right: -140px;
            border-radius: 50%;
            animation: pulseglow 7s ease-in-out infinite;
            pointer-events: none;
        }
        .lyp-hero::after {
            content: '';
            position: absolute;
            width: 460px; height: 460px;
            background: radial-gradient(circle, rgba(255,255,255,0.06) 0%, transparent 70%);
            bottom: -80px; left: -90px;
            border-radius: 50%;
            animation: pulseglow 9s ease-in-out infinite reverse;
            pointer-events: none;
        }
        @keyframes pulseglow {
            0%,100%{ transform:scale(1); opacity:1; }
            50%{ transform:scale(1.14); opacity:0.65; }
        }

        /* buildings */
        .building { border-radius: 6px 6px 0 0; animation: floatbld 4s ease-in-out infinite; }
        @keyframes floatbld { 0%,100%{ transform:translateY(0); } 50%{ transform:translateY(-9px); } }

        /* ── STEP CARDS ── */
        .step-card {
            background: white;
            border: 1.5px solid #f1f5f9;
            border-radius: 20px;
            padding: 28px 22px 22px;
            transition: all 0.32s cubic-bezier(.4,0,.2,1);
            position: relative;
        }
        .step-card:hover {
            border-color: #d00e15;
            box-shadow: 0 18px 52px rgba(208,14,21,0.11);
            transform: translateY(-5px);
        }
        .step-number {
            width: 44px; height: 44px;
            background: #fff5f5;
            border: 2px solid #d00e15;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-family: 'Outfit', sans-serif;
            font-weight: 800; font-size: 16px; color: #d00e15;
            flex-shrink: 0;
            transition: all 0.32s;
        }
        .step-card:hover .step-number { background: #d00e15; color: white; }

        /* ── BENEFIT CARDS ── */
        .benefit-card {
            border-radius: 20px;
            padding: 30px 26px;
            border: 1.5px solid #f1f5f9;
            transition: all 0.32s cubic-bezier(.4,0,.2,1);
            background: white;
        }
        .benefit-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 18px 52px rgba(0,0,0,0.07);
            border-color: #fecdd3;
        }

        /* ── STATS ── */
        .stat-number {
            font-family: 'Outfit', sans-serif;
            font-size: 2.4rem; font-weight: 900;
            color: #d00e15; line-height: 1;
        }

        /* ── BUTTONS ── */
        .btn-white {
            background: white;
            color: #d00e15;
            font-weight: 700;
            padding: 15px 36px;
            border-radius: 50px;
            display: inline-flex; align-items: center; gap: 9px;
            font-size: 0.97rem;
            transition: all 0.28s;
            text-decoration: none;
            box-shadow: 0 4px 20px rgba(0,0,0,0.18);
            border: 2px solid white;
            white-space: nowrap;
        }
        .btn-white:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(0,0,0,0.28);
            background: #fff5f5;
        }
        .btn-outline-white {
            background: transparent;
            color: white;
            font-weight: 600;
            padding: 15px 32px;
            border-radius: 50px;
            border: 2px solid rgba(255,255,255,0.75);
            display: inline-flex; align-items: center; gap: 9px;
            font-size: 0.97rem;
            transition: all 0.28s;
            text-decoration: none;
            white-space: nowrap;
        }
        .btn-outline-white:hover {
            border-color: white;
            background: rgba(255,255,255,0.13);
            transform: translateY(-2px);
        }
        .btn-red {
            background: #d00e15;
            color: white;
            font-weight: 700;
            padding: 14px 36px;
            border-radius: 16px;
            display: inline-flex; align-items: center; gap: 9px;
            font-size: 0.97rem;
            transition: all 0.28s;
            text-decoration: none;
            box-shadow: 0 4px 16px rgba(208,14,21,0.3);
        }
        .btn-red:hover { background: #a90b11; transform: translateY(-2px); box-shadow: 0 8px 24px rgba(208,14,21,0.4); }

        /* ── FAQ ── */
        .faq-answer { overflow: hidden; transition: max-height 0.38s ease, padding 0.28s ease; }

        /* ── PROPERTY TYPE TILES ── */
        .prop-tile {
            background: white; border-radius: 18px; padding: 20px 12px;
            text-align: center; border: 1.5px solid #f1f5f9;
            transition: all 0.28s; cursor: default;
        }
        .prop-tile:hover { border-color: rgba(208,14,21,0.35); box-shadow: 0 8px 24px rgba(0,0,0,0.07); }
        .prop-icon {
            width: 44px; height: 44px; background: #fff5f5; border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 10px; transition: background 0.28s;
        }
        .prop-tile:hover .prop-icon { background: #d00e15; }
        .prop-tile:hover .prop-icon svg { color: white; }
    </style>
</head>
<body class="bg-white text-[#19100F]">

{{-- ============================================================
     NAVBAR
============================================================ --}}
<nav class="lyp-nav">
    <div class="lyp-nav-inner">
        <a href="{{ url('/') }}" class="lyp-logo">GHURI<span>.</span></a>

        <div style="display:flex; align-items:center; gap:12px;">
            @auth
                @if(auth()->user()->isPropertyOwner())
                    <a href="{{ route('property-owner.dashboard') }}"
                       style="color:rgba(255,255,255,0.8);font-size:0.875rem;font-weight:600;text-decoration:none;padding:8px 16px;border-radius:10px;transition:all 0.2s;"
                       onmouseover="this.style.background='rgba(255,255,255,0.1)';this.style.color='white'"
                       onmouseout="this.style.background='transparent';this.style.color='rgba(255,255,255,0.8)'">
                        My Dashboard
                    </a>
                @endif
                <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                    @csrf
                    <button type="submit"
                            style="background:none;border:none;cursor:pointer;color:rgba(255,255,255,0.55);font-size:0.875rem;font-weight:600;padding:8px 14px;border-radius:10px;transition:all 0.2s;"
                            onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(255,255,255,0.55)'">
                        Logout
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}"
                   style="color:rgba(255,255,255,0.8);font-size:0.875rem;font-weight:600;text-decoration:none;padding:8px 18px;border-radius:10px;transition:all 0.2s;"
                   onmouseover="this.style.background='rgba(255,255,255,0.1)';this.style.color='white'"
                   onmouseout="this.style.background='transparent';this.style.color='rgba(255,255,255,0.8)'">
                    Sign in
                </a>
                <a href="{{ route('register') }}"
                   style="background:#d00e15;color:white;font-size:0.875rem;font-weight:700;text-decoration:none;padding:10px 22px;border-radius:12px;transition:all 0.2s;box-shadow:0 2px 12px rgba(208,14,21,0.4);"
                   onmouseover="this.style.background='#a90b11'" onmouseout="this.style.background='#d00e15'">
                    List your property
                </a>
            @endauth
        </div>
    </div>
</nav>

{{-- ============================================================
     HERO
============================================================ --}}
<section class="lyp-hero">
    <div style="max-width:1152px; margin:0 auto; width:100%; position:relative; z-index:2;">
        <div style="display:grid; grid-template-columns:1fr; gap:48px; align-items:center;"
             class="lg:grid-cols-2-hero">

            {{-- LEFT: Copy --}}
            <div>
                {{-- Badge --}}
                <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.22);border-radius:50px;padding:7px 16px;margin-bottom:24px;backdrop-filter:blur(8px);">
                    <span style="width:8px;height:8px;background:#4ade80;border-radius:50%;display:inline-block;animation:pulseglow 2s ease-in-out infinite;"></span>
                    <span style="color:rgba(255,255,255,0.92);font-size:0.72rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">Now Open for Partners</span>
                </div>

                <h1 style="font-family:'Outfit',sans-serif;font-weight:900;font-size:clamp(2.4rem,5vw,3.8rem);line-height:1.08;color:white;margin:0 0 20px;">
                    List your<br>
                    <span style="background:linear-gradient(90deg,#ffb3b7,#ffd6d8);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">property</span><br>
                    on <span style="color:white;">GHURI</span><span style="color:#ff4b55;">.</span>
                </h1>

                <p style="color:rgba(255,255,255,0.82);font-size:1.05rem;line-height:1.7;margin:0 0 20px;max-width:440px;">
                    Join hundreds of hotels, resorts, and homestays across Bangladesh and beyond. Reach thousands of verified travelers every day.
                </p>

                <ul style="list-style:none;padding:0;margin:0 0 32px;display:flex;flex-direction:column;gap:10px;">
                    @php $checks = [
                        'Get your first booking within days',
                        'Full control over pricing, availability & rules',
                        'Real-time analytics & booking dashboard',
                        'Zero upfront cost — commission based only',
                    ]; @endphp
                    @foreach($checks as $check)
                    <li style="display:flex;align-items:center;gap:10px;color:rgba(255,255,255,0.9);font-size:0.9rem;font-weight:500;">
                        <svg style="width:20px;height:20px;color:#4ade80;flex-shrink:0;" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        {{ $check }}
                    </li>
                    @endforeach
                </ul>

                <div style="display:flex;flex-wrap:wrap;gap:14px;">
                    @auth
                        @if(auth()->user()->isPropertyOwner())
                            <a href="{{ route('property-owner.hotels.create') }}" class="btn-white">
                                <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Add Your Property
                            </a>
                            <a href="{{ route('property-owner.dashboard') }}" class="btn-outline-white">
                                Go to Dashboard →
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="btn-white">
                                <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                List your property
                            </a>
                            <a href="{{ route('login') }}" class="btn-outline-white">
                                I already have an account
                            </a>
                        @endif
                    @else
                        <a href="{{ route('register') }}" class="btn-white">
                            <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            List your property
                        </a>
                        <a href="{{ route('login') }}" class="btn-outline-white">
                            I already have an account
                        </a>
                    @endauth
                </div>
            </div>

            {{-- RIGHT: City illustration --}}
            <div style="display:flex;justify-content:flex-end;align-items:flex-end;position:relative;min-height:300px;" class="hidden lg:flex">
                <div style="display:flex;align-items:flex-end;gap:8px;position:relative;">
                    {{-- B1 small house --}}
                    <div class="building" style="width:58px;height:88px;background:linear-gradient(180deg,#ff8a92,#ec5060);position:relative;animation-delay:0s;">
                        <div style="position:absolute;top:-19px;left:0;right:0;height:19px;background:#ff6475;clip-path:polygon(50% 0%,100% 100%,0% 100%);"></div>
                        <div style="position:absolute;top:12px;left:9px;width:14px;height:16px;background:rgba(255,255,255,0.45);border-radius:2px;"></div>
                        <div style="position:absolute;top:12px;right:9px;width:14px;height:16px;background:rgba(255,255,255,0.45);border-radius:2px;"></div>
                        <div style="position:absolute;bottom:0;left:50%;transform:translateX(-50%);width:18px;height:28px;background:rgba(255,255,255,0.3);border-radius:2px 2px 0 0;"></div>
                    </div>
                    {{-- B2 medium --}}
                    <div class="building" style="width:78px;height:155px;background:linear-gradient(180deg,#e8b0b5,#c87b85);position:relative;animation-delay:-1.2s;">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:7px;padding:9px 9px 0;">
                            @for($w=0;$w<8;$w++)
                            <div style="height:13px;background:rgba(255,255,255,{{ $w%2==0 ? '0.5' : '0.28' }});border-radius:2px;"></div>
                            @endfor
                        </div>
                        <div style="position:absolute;bottom:0;left:50%;transform:translateX(-50%);width:26px;height:38px;background:rgba(255,255,255,0.25);border-radius:2px 2px 0 0;"></div>
                    </div>
                    {{-- B3 tall GHURI hotel --}}
                    <div class="building" style="width:98px;height:238px;background:linear-gradient(180deg,#c40c13,#7a0007);position:relative;border-radius:5px 5px 0 0;animation-delay:-0.6s;">
                        <div style="position:absolute;top:-14px;left:50%;transform:translateX(-50%);width:8px;height:14px;background:#ff4b55;border-radius:2px 2px 0 0;"></div>
                        <div style="position:absolute;top:10px;left:0;right:0;text-align:center;font-family:'Outfit',sans-serif;font-weight:900;color:rgba(255,255,255,0.88);font-size:10px;letter-spacing:2px;">GHURI</div>
                        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:5px;padding:28px 7px 0;">
                            @for($w=0;$w<18;$w++)
                            <div style="height:11px;background:rgba(255,255,255,{{ $w%3==1 ? '0.65' : '0.22' }});border-radius:1px;"></div>
                            @endfor
                        </div>
                        <div style="position:absolute;bottom:0;left:50%;transform:translateX(-50%);width:34px;height:48px;background:rgba(255,255,255,0.2);border-radius:2px 2px 0 0;"></div>
                    </div>
                    {{-- B4 medium --}}
                    <div class="building" style="width:73px;height:128px;background:linear-gradient(180deg,#fca5a8,#e07880);position:relative;animation-delay:-2.2s;">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;padding:8px 8px 0;">
                            @for($w=0;$w<6;$w++)
                            <div style="height:11px;background:rgba(255,255,255,{{ $w%2==1 ? '0.22' : '0.48' }});border-radius:2px;"></div>
                            @endfor
                        </div>
                        <div style="position:absolute;bottom:0;left:50%;transform:translateX(-50%);width:20px;height:30px;background:rgba(255,255,255,0.25);border-radius:2px 2px 0 0;"></div>
                    </div>
                    {{-- B5 small house --}}
                    <div class="building" style="width:54px;height:78px;background:linear-gradient(180deg,#fbbfc2,#e8a0a5);position:relative;animation-delay:-3.1s;">
                        <div style="position:absolute;top:-17px;left:0;right:0;height:17px;background:#f4939a;clip-path:polygon(50% 0%,100% 100%,0% 100%);"></div>
                        <div style="position:absolute;top:10px;left:7px;width:13px;height:15px;background:rgba(255,255,255,0.42);border-radius:2px;"></div>
                        <div style="position:absolute;top:10px;right:7px;width:13px;height:15px;background:rgba(255,255,255,0.42);border-radius:2px;"></div>
                        <div style="position:absolute;bottom:0;left:50%;transform:translateX(-50%);width:16px;height:24px;background:rgba(255,255,255,0.3);border-radius:2px 2px 0 0;"></div>
                    </div>
                </div>
                {{-- Ground --}}
                <div style="position:absolute;bottom:-2px;left:0;right:0;height:3px;background:rgba(255,255,255,0.14);border-radius:2px;"></div>
            </div>
        </div>
    </div>
</section>

{{-- ============================================================
     STATS STRIP
============================================================ --}}
<section style="background:white;border-bottom:1px solid #f1f5f9;padding:40px 24px;">
    <div style="max-width:960px;margin:0 auto;display:grid;grid-template-columns:repeat(2,1fr);gap:0;"
         class="md:grid-cols-4-stats">
        @php $stats = [
            ['n'=>'500+',   'l'=>'Partner Properties'],
            ['n'=>'50K+',   'l'=>'Monthly Travelers'],
            ['n'=>'3 Days', 'l'=>'Avg. First Booking'],
            ['n'=>'Free',   'l'=>'To Get Started'],
        ]; @endphp
        @foreach($stats as $stat)
        <div style="text-align:center;padding:20px 12px;border-right:1px solid #f1f5f9;">
            <div class="stat-number">{{ $stat['n'] }}</div>
            <div style="color:#6b7280;font-size:0.85rem;font-weight:500;margin-top:4px;">{{ $stat['l'] }}</div>
        </div>
        @endforeach
    </div>
</section>

{{-- ============================================================
     HOW IT WORKS — 4 STEPS
============================================================ --}}
<section style="padding:80px 24px;background:#f9fafb;">
    <div style="max-width:1152px;margin:0 auto;">
        <div style="text-align:center;margin-bottom:56px;">
            <div style="display:inline-block;background:#fff5f5;color:#d00e15;font-size:0.72rem;font-weight:700;padding:6px 16px;border-radius:50px;margin-bottom:14px;letter-spacing:0.08em;text-transform:uppercase;">Simple &amp; Fast</div>
            <h2 style="font-family:'Outfit',sans-serif;font-weight:900;font-size:clamp(1.8rem,4vw,2.4rem);color:#19100F;margin:0 0 10px;">All you have to do</h2>
            <p style="color:#6b7280;font-size:1rem;margin:0 auto;max-width:420px;">From signup to your first booking in just 4 easy steps. No tech skills needed.</p>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;position:relative;">
            {{-- Connector line (desktop) --}}
            <div style="display:none;" class="lg-connector"></div>

            @php $steps = [
                ['n'=>'1','title'=>'Create Account','desc'=>'Sign up for a free GHURI account. Select "Property Owner" role during registration. Your PMS dashboard is created instantly.','icon'=>'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                ['n'=>'2','title'=>'Add Your Property','desc'=>'Fill in your property details — name, location, description, amenities, and photos. Set up room types, pricing, and availability.','icon'=>'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                ['n'=>'3','title'=>'Get Approved','desc'=>'Submit your property for GHURI review. Our team verifies your listing quality. Approval typically takes 24–48 hours — you\'ll get an email.','icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['n'=>'4','title'=>'Go Live & Earn','desc'=>'Your property is published to thousands of travelers on GHURI. Manage bookings, respond to reviews, and grow your revenue.','icon'=>'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
            ]; @endphp

            @foreach($steps as $step)
            <div class="step-card">
                <div style="display:flex;align-items:flex-start;gap:14px;margin-bottom:14px;">
                    <div class="step-number">{{ $step['n'] }}</div>
                    <h3 style="font-family:'Outfit',sans-serif;font-weight:700;font-size:1rem;color:#19100F;margin:10px 0 0;">{{ $step['title'] }}</h3>
                </div>
                <p style="color:#6b7280;font-size:0.875rem;line-height:1.65;margin:0 0 18px;">{{ $step['desc'] }}</p>
                <div style="display:flex;justify-content:center;">
                    <div style="width:52px;height:52px;background:#fff5f5;border-radius:14px;display:flex;align-items:center;justify-content:center;">
                        <svg style="width:26px;height:26px;color:#d00e15;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $step['icon'] }}"/>
                        </svg>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div style="text-align:center;margin-top:44px;">
            @auth
                @if(auth()->user()->isPropertyOwner())
                    <a href="{{ route('property-owner.hotels.create') }}" class="btn-red">
                        <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Your Property Now
                    </a>
                @else
                    <a href="{{ route('register') }}" class="btn-red">
                        <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Get Started for Free
                    </a>
                @endif
            @else
                <a href="{{ route('register') }}" class="btn-red">
                    <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Get Started for Free
                </a>
            @endauth
            <p style="color:#9ca3af;font-size:0.82rem;margin-top:10px;">No credit card required. Free to list.</p>
        </div>
    </div>
</section>

{{-- ============================================================
     WHY GHURI — BENEFITS
============================================================ --}}
<section style="padding:80px 24px;background:white;">
    <div style="max-width:1152px;margin:0 auto;">
        <div style="text-align:center;margin-bottom:52px;">
            <div style="display:inline-block;background:#fff5f5;color:#d00e15;font-size:0.72rem;font-weight:700;padding:6px 16px;border-radius:50px;margin-bottom:14px;letter-spacing:0.08em;text-transform:uppercase;">Why GHURI?</div>
            <h2 style="font-family:'Outfit',sans-serif;font-weight:900;font-size:clamp(1.8rem,4vw,2.4rem);color:#19100F;margin:0 0 10px;">Everything you need to grow</h2>
            <p style="color:#6b7280;font-size:1rem;margin:0 auto;max-width:440px;">GHURI gives property owners powerful tools to manage, market, and maximize revenue — all in one place.</p>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:20px;">
            @php $benefits = [
                ['icon'=>'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z','title'=>'Data-Rich Analytics','desc'=>'Access detailed performance insights — occupancy rates, revenue trends, and guest reviews — to refine your strategy and stay competitive.'],
                ['icon'=>'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z','title'=>'Availability Calendar','desc'=>'Manage room availability with a bulk-update calendar. Block dates, set seasonal pricing, and sync across room types instantly.'],
                ['icon'=>'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z','title'=>'Flexible Pricing Rules','desc'=>'Set custom rate rules per room type — minimum stay, advance purchase, seasonal rates, and more. You\'re always in control.'],
                ['icon'=>'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z','title'=>'Guest Reviews & Reputation','desc'=>'Respond to guest reviews directly from your dashboard. Build trust and improve your property\'s ranking on GHURI.'],
                ['icon'=>'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z','title'=>'Promotions & Deals','desc'=>'Create special promotions and discount offers to attract more travelers during low season or for direct bookings.'],
                ['icon'=>'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z','title'=>'Dedicated Partner Support','desc'=>'Our GHURI partner team is here to help you onboard, optimize listings, and resolve any issues — quickly and professionally.'],
            ]; @endphp
            @foreach($benefits as $b)
            <div class="benefit-card">
                <div style="width:48px;height:48px;background:#fff5f5;border-radius:14px;display:flex;align-items:center;justify-content:center;margin-bottom:18px;">
                    <svg style="width:24px;height:24px;color:#d00e15;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $b['icon'] }}"/>
                    </svg>
                </div>
                <h3 style="font-family:'Outfit',sans-serif;font-weight:700;font-size:1.05rem;color:#19100F;margin:0 0 8px;">{{ $b['title'] }}</h3>
                <p style="color:#6b7280;font-size:0.875rem;line-height:1.65;margin:0;">{{ $b['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================================================
     PROPERTY TYPES
============================================================ --}}
<section style="padding:60px 24px;background:#f9fafb;">
    <div style="max-width:1152px;margin:0 auto;">
        <div style="text-align:center;margin-bottom:36px;">
            <h2 style="font-family:'Outfit',sans-serif;font-weight:900;font-size:clamp(1.6rem,3.5vw,2rem);color:#19100F;margin:0 0 8px;">What can you list?</h2>
            <p style="color:#6b7280;font-size:0.9rem;margin:0;">GHURI welcomes all types of accommodation.</p>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:14px;">
            @php $ptypes = [
                ['label'=>'Hotels',      'icon'=>'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
                ['label'=>'Resorts',     'icon'=>'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                ['label'=>'Apartments',  'icon'=>'M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z'],
                ['label'=>'Guesthouses', 'icon'=>'M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z'],
                ['label'=>'Homestays',   'icon'=>'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'],
                ['label'=>'& More',      'icon'=>'M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z'],
            ]; @endphp
            @foreach($ptypes as $pt)
            <div class="prop-tile">
                <div class="prop-icon">
                    <svg style="width:20px;height:20px;color:#d00e15;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $pt['icon'] }}"/>
                    </svg>
                </div>
                <span style="font-size:0.875rem;font-weight:700;color:#19100F;">{{ $pt['label'] }}</span>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================================================
     FAQ — pure JS accordion (no x-collapse dependency)
============================================================ --}}
<section style="padding:80px 24px;background:white;" id="faq-section">
    <div style="max-width:720px;margin:0 auto;">
        <div style="text-align:center;margin-bottom:48px;">
            <h2 style="font-family:'Outfit',sans-serif;font-weight:900;font-size:clamp(1.8rem,4vw,2.2rem);color:#19100F;margin:0 0 8px;">Frequently asked questions</h2>
            <p style="color:#6b7280;font-size:0.9rem;margin:0;">Everything you need to know before listing your property.</p>
        </div>

        <div style="border:1.5px solid #f1f5f9;border-radius:20px;overflow:hidden;box-shadow:0 2px 16px rgba(0,0,0,0.04);">
            @php $faqs = [
                ['q'=>'Is it free to list my property on GHURI?',     'a'=>'Yes! Listing your property on GHURI is completely free. We operate on a commission-only model, meaning we only earn when you earn. There are no upfront fees or monthly charges.'],
                ['q'=>'How long does approval take?',                  'a'=>'Our team reviews submissions within 24–48 business hours. We check property details, photos, and room information to ensure quality for our travelers. You\'ll receive an email notification once approved.'],
                ['q'=>'What commission does GHURI charge?',            'a'=>'Our standard commission rate is competitive with industry norms. The exact percentage is shared during your onboarding process. You keep the majority of each booking revenue.'],
                ['q'=>'Can I manage multiple properties?',             'a'=>'Absolutely! Your GHURI Property Dashboard supports managing multiple properties under one account. Each property has its own rooms, availability, and pricing settings.'],
                ['q'=>'How do I receive payment for bookings?',        'a'=>'GHURI processes guest payments and transfers your earnings (minus commission) directly to your registered bank account. Payout schedules and methods are configured in your dashboard settings.'],
                ['q'=>'What if I need help setting up my listing?',   'a'=>'Our dedicated partner support team is available to assist you. You can reach us via email or the support section in your dashboard. We\'re committed to helping you succeed on GHURI.'],
            ]; @endphp

            @foreach($faqs as $fi => $faq)
            <div class="faq-item-js" style="border-bottom:1px solid #f1f5f9;{{ $fi === count($faqs)-1 ? 'border-bottom:none;' : '' }}">
                <button
                    onclick="toggleFaq({{ $fi }})"
                    id="faq-btn-{{ $fi }}"
                    style="width:100%;text-align:left;background:none;border:none;cursor:pointer;padding:20px 24px;display:flex;align-items:center;justify-content:space-between;gap:16px;transition:background 0.2s;"
                    onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='none'"
                >
                    <span style="font-weight:600;color:#19100F;font-size:0.9rem;line-height:1.45;">{{ $faq['q'] }}</span>
                    <svg id="faq-icon-{{ $fi }}" style="width:20px;height:20px;color:#d00e15;flex-shrink:0;transition:transform 0.3s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </button>
                <div id="faq-answer-{{ $fi }}" class="faq-answer" style="max-height:0;padding:0 24px;overflow:hidden;transition:max-height 0.38s ease,padding 0.28s ease;">
                    <p style="color:#6b7280;font-size:0.875rem;line-height:1.7;margin:0 0 20px;">{{ $faq['a'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================================================
     FINAL CTA BANNER
============================================================ --}}
<section style="padding:80px 24px;background:linear-gradient(135deg,#140103 0%,#5c0510 40%,#c40c13 100%);">
    <div style="max-width:680px;margin:0 auto;text-align:center;">
        <h2 style="font-family:'Outfit',sans-serif;font-weight:900;font-size:clamp(2rem,5vw,3.2rem);color:white;margin:0 0 16px;line-height:1.15;">
            Ready to grow<br>your business?
        </h2>
        <p style="color:rgba(255,255,255,0.72);font-size:1rem;margin:0 auto 40px;max-width:420px;line-height:1.65;">
            Join GHURI's growing network of property partners. It takes less than 10 minutes to set up your first listing.
        </p>
        <div style="display:flex;flex-wrap:wrap;gap:14px;justify-content:center;">
            @auth
                @if(auth()->user()->isPropertyOwner())
                    <a href="{{ route('property-owner.hotels.create') }}" class="btn-white">
                        <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Your Property
                    </a>
                    <a href="{{ route('property-owner.dashboard') }}" class="btn-outline-white">
                        Go to Dashboard →
                    </a>
                @else
                    <a href="{{ route('register') }}" class="btn-white">
                        <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        List your property — it's free
                    </a>
                    <a href="{{ route('login') }}" class="btn-outline-white">
                        Sign in to existing account
                    </a>
                @endif
            @else
                <a href="{{ route('register') }}" class="btn-white">
                    <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    List your property — it's free
                </a>
                <a href="{{ route('login') }}" class="btn-outline-white">
                    Sign in to existing account
                </a>
            @endauth
        </div>
    </div>
</section>

{{-- ============================================================
     FOOTER
============================================================ --}}
<footer style="background:#0e0102;padding:28px 24px;">
    <div style="max-width:1152px;margin:0 auto;display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:16px;">
        <div style="font-family:'Outfit',sans-serif;font-weight:900;font-size:1.4rem;color:white;">
            GHURI<span style="color:#ff4b55;">.</span>
        </div>
        <div style="color:rgba(255,255,255,0.4);font-size:0.8rem;">
            &copy; {{ date('Y') }} GHURI. All rights reserved.
        </div>
        <div style="display:flex;gap:20px;">
            <a href="{{ url('/') }}"              style="color:rgba(255,255,255,0.45);font-size:0.8rem;text-decoration:none;transition:color 0.2s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(255,255,255,0.45)'">Home</a>
            <a href="{{ route('hotels.search') }}" style="color:rgba(255,255,255,0.45);font-size:0.8rem;text-decoration:none;transition:color 0.2s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(255,255,255,0.45)'">Find Hotels</a>
            <a href="{{ route('login') }}"         style="color:rgba(255,255,255,0.45);font-size:0.8rem;text-decoration:none;transition:color 0.2s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='rgba(255,255,255,0.45)'">Sign In</a>
        </div>
    </div>
</footer>

<script>
    // ── Pure JS FAQ accordion ────────────────────────────────────
    var faqOpen = null;
    function toggleFaq(idx) {
        var answer = document.getElementById('faq-answer-' + idx);
        var icon   = document.getElementById('faq-icon-'   + idx);

        if (faqOpen !== null && faqOpen !== idx) {
            var prevAnswer = document.getElementById('faq-answer-' + faqOpen);
            var prevIcon   = document.getElementById('faq-icon-'   + faqOpen);
            prevAnswer.style.maxHeight = '0';
            prevAnswer.style.padding   = '0 24px';
            prevIcon.style.transform   = 'rotate(0deg)';
        }

        if (faqOpen === idx) {
            answer.style.maxHeight = '0';
            answer.style.padding   = '0 24px';
            icon.style.transform   = 'rotate(0deg)';
            faqOpen = null;
        } else {
            answer.style.maxHeight = answer.scrollHeight + 'px';
            answer.style.padding   = '4px 24px';
            icon.style.transform   = 'rotate(45deg)';
            faqOpen = idx;
        }
    }

    // ── Responsive grid helpers via CSS media queries applied inline ──
    (function() {
        var style = document.createElement('style');
        style.textContent = [
            '@media(min-width:768px){',
            '  .md\\:grid-cols-4-stats { grid-template-columns: repeat(4,1fr) !important; }',
            '}',
            '@media(min-width:1024px){',
            '  .lg\\:grid-cols-2-hero { grid-template-columns: 1fr 1fr !important; }',
            '  .hidden.lg\\:flex { display:flex !important; }',
            '}'
        ].join('');
        document.head.appendChild(style);
    })();
</script>
</body>
</html>
