<!DOCTYPE html>
<html lang="en-BD">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Page' }} - Bookdei OTA</title>
    <meta name="description" content="Learn about {{ $title ?? 'Bookdei' }} and how Bookdei helps travelers book flights and stays across Bangladesh.">
    <meta name="robots" content="noindex, follow">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:site_name" content="Bookdei">
    <meta property="og:title" content="{{ $title ?? 'Bookdei' }} | Bookdei">
    <meta property="og:description" content="Information from Bookdei, Bangladesh's flight and accommodation booking platform.">
    <meta property="og:url" content="{{ url()->current() }}">
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:ital,wght@0,400;0,500;0,600;0,700;1,400&family=JetBrains+Mono:wght@400;500;600&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .booking-nav {
            background-color: #d00e15 !important; /* Ensure nav is solid blue since it's not over an image */
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50 text-dark selection:bg-[#d00e15] selection:text-white flex flex-col min-h-screen">
    <!-- Navigation -->
    <nav class="booking-nav fixed top-0 inset-x-0 z-50" style="height: 75px;">
        <div class="booking-nav-inner">
            <a href="{{ url('/') }}" class="booking-nav-logo flex items-center" aria-label="bookdei home">
                <img src="{{ asset('images/logo.png') }}" alt="bookdei" class="booking-nav-logo-image object-contain drop-shadow-sm transition-transform duration-300 hover:scale-105" style="height: 48px; width: auto; max-width: 190px;">
            </a>
            <div class="booking-nav-actions">
                <div x-data="{ currencyOpen: false }" class="relative">
                    <button type="button" @click="currencyOpen = !currencyOpen" class="booking-nav-currency flex items-center" aria-label="Currency: {{ session('currency', 'BDT') }}; region: Bangladesh" title="Currency and region">
                        {{ session('currency', 'BDT') }}
                        <span class="booking-nav-separator" aria-hidden="true"></span>
                        <span class="booking-nav-flag" aria-hidden="true"></span>
                    </button>
                    <div x-show="currencyOpen" @click.away="currencyOpen = false" x-transition style="display:none;" class="absolute right-0 mt-2 w-32 bg-white border border-gray-100 rounded-xl shadow-xl overflow-hidden z-50">
                        <form action="{{ route('currency.set') }}" method="POST">
                            @csrf
                            <input type="hidden" name="currency" value="BDT">
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm {{ session('currency', 'BDT') === 'BDT' ? 'font-bold text-[#d00e15] bg-red-50' : 'text-gray-700 hover:bg-gray-50' }}">
                                BDT (৳)
                            </button>
                        </form>
                        <form action="{{ route('currency.set') }}" method="POST" class="border-t border-gray-50">
                            @csrf
                            <input type="hidden" name="currency" value="USD">
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm {{ session('currency') === 'USD' ? 'font-bold text-[#d00e15] bg-red-50' : 'text-gray-700 hover:bg-gray-50' }}">
                                USD ($)
                            </button>
                        </form>
                    </div>
                </div>
                {{-- ✦ List your Property Link — shown when Hotel tab is active --}}
                <a
                    href="{{ route('list-your-property') }}"
                    id="nav-list-property-link"
                    class="booking-nav-property inline-flex items-center"
                >
                    List your property
                </a>

                <a href="{{ route('navbar.support') }}" class="booking-nav-link">Support</a>
                <a href="{{ route('my-bookings.index') }}" class="booking-nav-link" title="Booking history">Trips</a>
                <a href="{{ route('pages.contact') }}" class="booking-nav-icon-button booking-nav-messages" aria-label="Messages" title="Messages">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                        <path d="M7 18.5 3.5 21v-5A8.5 8.5 0 0 1 3 13V7.5A3.5 3.5 0 0 1 6.5 4h11A3.5 3.5 0 0 1 21 7.5v6a3.5 3.5 0 0 1-3.5 3.5H8.8L7 18.5Z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M8 10.5h.01M12 10.5h.01M16 10.5h.01" stroke-width="2.2" stroke-linecap="round"/>
                    </svg>
                </a>

                @if (Route::has('login'))
                    @auth
                        @php
                            $user = auth()->user();
                            $isInternalUser = $user && $user->isInternalUser();
                        @endphp
                        @if ($isInternalUser)
                            <a href="{{ route('admin.dashboard') }}" class="booking-nav-auth-button">Dashboard</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="booking-nav-auth-button">Logout</button>
                            </form>
                        @else
                            <div x-data="{ open:false }" class="relative">
                                <button @click="open = !open" type="button" class="booking-nav-account" aria-label="Open account menu">
                                    <span class="booking-nav-avatar">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </span>
                                    <svg class="w-4 h-4 transition" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <div
                                    x-show="open"
                                    @click.away="open = false"
                                    x-transition
                                    style="display:none;"
                                    class="absolute right-0 mt-2 w-64 bg-white border border-gray-100 rounded-xl shadow-xl overflow-hidden"
                                >
                                    <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                                        <p class="text-sm font-bold text-[#19100F] truncate">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                                    </div>

                                    @if($user->isPropertyOwner())
                                        <a href="{{ route('property-owner.dashboard') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">PMS Dashboard</a>
                                    @else
                                        <a href="{{ route('dashboard') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">Dashboard</a>
                                        <a href="{{ route('my-bookings.index') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">Booking History</a>
                                    @endif
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">Account</a>
                                    <button type="button" class="w-full text-left px-4 py-2.5 text-sm text-gray-400 cursor-default">My Wishlist</button>
                                    <button type="button" class="w-full text-left px-4 py-2.5 text-sm text-gray-400 cursor-default">Settings</button>

                                    <form method="POST" action="{{ route('logout') }}" class="border-t border-gray-100">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">Logout</button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    @else
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="booking-nav-auth-button">Register</a>
                        @endif
                        <a href="{{ route('login') }}" class="booking-nav-auth-button">Sign in</a>
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main class="flex-grow pt-32 pb-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 md:p-12">
                <h1 class="text-4xl font-extrabold text-[#19100F] mb-6 tracking-tight">{{ $title ?? 'Page' }}</h1>
                <div class="prose prose-lg prose-blue max-w-none text-gray-600">
                    <p class="text-xl mb-6">Welcome to the {{ $title ?? 'Page' }}. We are currently updating this section to provide you with the most up-to-date and comprehensive information.</p>
                    <p class="mb-4">At Bookdei, we are committed to ensuring your travel experience is smooth, reliable, and perfectly tailored to your needs. This page will soon feature detailed insights and helpful resources.</p>
                    <p>In the meantime, if you have any urgent inquiries or require assistance, please do not hesitate to contact our 24/7 support team.</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-[#19100F] text-gray-300 py-16 border-t border-gray-800 font-sans mt-auto" style="background-color: #19100F;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Main Footer Links Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-12 mb-16">
                <!-- Brand & Slogan -->
                <div class="lg:col-span-2">
                    <img src="{{ asset('images/logo.png') }}" alt="bookdei" class="mb-6" style="height: 100px; width: auto; transform: scale(1.5); transform-origin: left center;">
                    <p class="text-sm text-gray-400 max-w-sm leading-relaxed mb-8">
                        Your trusted global travel platform. Smart bookings, competitive deals, and reliable support - all in one place. Experience seamless travel with Bookdei.
                    </p>
                    <div class="flex items-center gap-4">
                        <!-- Social Icons -->
                        <a href="#" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-[#d00e15] hover:text-white transition-colors duration-300">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-[#d00e15] hover:text-white transition-colors duration-300">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-[#d00e15] hover:text-white transition-colors duration-300">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Column 1 -->
                <div>
                    <h4 class="text-white font-bold text-sm mb-6 uppercase tracking-wider">Company</h4>
                    <ul class="space-y-4">
                        <li><a href="{{ route('pages.about') }}" class="text-sm hover:text-white transition-colors">About Us</a></li>
                        <li><a href="{{ route('pages.careers') }}" class="text-sm hover:text-white transition-colors">Careers</a></li>
                        <li><a href="{{ route('pages.press') }}" class="text-sm hover:text-white transition-colors">Press Center</a></li>
                        <li><a href="{{ route('pages.sustainability') }}" class="text-sm hover:text-white transition-colors">Sustainability</a></li>
                        <li><a href="{{ route('pages.investors') }}" class="text-sm hover:text-white transition-colors">Investor Relations</a></li>
                    </ul>
                </div>

                <!-- Column 2 -->
                <div>
                    <h4 class="text-white font-bold text-sm mb-6 uppercase tracking-wider">Support</h4>
                    <ul class="space-y-4">
                        <li><a href="{{ route('pages.contact') }}" class="text-sm hover:text-white transition-colors">Contact Us</a></li>
                        <li><a href="{{ route('pages.help') }}" class="text-sm hover:text-white transition-colors">Help Center & FAQ</a></li>
                        <li><a href="{{ route('pages.cancellation') }}" class="text-sm hover:text-white transition-colors">Cancellation Policy</a></li>
                        <li><a href="{{ route('pages.trust-safety') }}" class="text-sm hover:text-white transition-colors">Trust & Safety</a></li>
                        <li><a href="{{ route('pages.complaint') }}" class="text-sm hover:text-white transition-colors">Submit a Complaint</a></li>
                    </ul>
                </div>

                <!-- Column 3 -->
                <div>
                    <h4 class="text-white font-bold text-sm mb-6 uppercase tracking-wider">Partners</h4>
                    <ul class="space-y-4">
                        <li><a href="{{ route('list-your-property') }}" class="text-sm hover:text-white transition-colors text-[#d00e15] font-medium">List your Property</a></li>
                        <li><a href="{{ route('pages.affiliates') }}" class="text-sm hover:text-white transition-colors">Affiliate Network</a></li>
                        <li><a href="{{ route('pages.travel-agencies') }}" class="text-sm hover:text-white transition-colors">Travel Agencies</a></li>
                        <li><a href="{{ route('pages.corporate') }}" class="text-sm hover:text-white transition-colors">Corporate Travel</a></li>
                        <li><a href="{{ route('pages.partner-portal') }}" class="text-sm hover:text-white transition-colors">Partner Portal</a></li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Footer Area -->
            <div class="pt-8 border-t border-gray-800 flex flex-col md:flex-row items-center justify-between gap-6">
                <!-- Legal Links & Copyright -->
                <div class="flex flex-col gap-4 text-center md:text-left">
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-4 md:gap-6 text-xs text-gray-400">
                        <a href="{{ route('pages.privacy') }}" class="hover:text-white transition-colors">Privacy Policy</a>
                        <a href="{{ route('pages.terms') }}" class="hover:text-white transition-colors">Terms of Service</a>
                        <a href="{{ route('pages.cookies') }}" class="hover:text-white transition-colors">Cookie Settings</a>
                        <a href="{{ route('pages.sitemap') }}" class="hover:text-white transition-colors">Sitemap</a>
                    </div>
                    <p class="text-[11px] text-gray-500">
                        &copy; {{ date('Y') }} Bookdei OTA. All rights reserved. Registered travel agency.
                    </p>
                </div>

                <!-- Badges -->
                <div class="flex flex-wrap justify-center items-center gap-6">
                    <div class="flex flex-col items-center gap-1.5 opacity-80 hover:opacity-100 transition-opacity">
                        <span class="text-[8px] text-gray-500 uppercase tracking-widest font-bold">Verified By</span>
                        <div class="flex items-center gap-1.5 text-gray-300">
                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            <span class="font-bold text-[10px] tracking-wider uppercase">DigiCert</span>
                        </div>
                    </div>
                    <div class="flex flex-col items-center gap-1.5 opacity-80 hover:opacity-100 transition-opacity">
                        <span class="text-[8px] text-gray-500 uppercase tracking-widest font-bold">Authorized By</span>
                        <div class="flex items-center gap-1 text-gray-300">
                            <span class="font-black text-sm italic tracking-wider">IATA</span>
                        </div>
                    </div>
                    <div class="flex flex-col items-center gap-1.5 opacity-80 hover:opacity-100 transition-opacity">
                        <span class="text-[8px] text-gray-500 uppercase tracking-widest font-bold">Member of</span>
                        <div class="flex items-center gap-1 text-gray-300">
                            <span class="font-black text-xs tracking-wider">BASIS</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
