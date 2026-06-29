<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GHURI - OTA Platform</title>
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:ital,wght@0,400;0,500;0,600;0,700;1,400&family=JetBrains+Mono:wght@400;500;600&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="preload" as="image" href="{{ asset('hero-pic-optimized.webp') }}" media="(min-width: 768px)">
    <link rel="preload" as="image" href="{{ asset('hero-pic-mobile.webp') }}" media="(max-width: 767px)">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-white text-dark selection:bg-[#d00e15] selection:text-white">
    <!-- Navigation -->
    <nav x-data="{ scrolled: false }" 
         @scroll.window="scrolled = (window.pageYOffset > 20)"
         :class="scrolled ? 'bg-white/95 backdrop-blur-md shadow-sm border-b border-gray-100' : 'bg-transparent border-transparent'"
         class="fixed top-0 inset-x-0 z-50 transition-colors duration-300">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative flex justify-between h-20 items-center">
            <div class="flex items-center gap-2 cursor-pointer">
                <div class="font-poppins font-extrabold text-3xl tracking-tighter text-[#d00e15]" data-text="GHURI">
                    GHURI<span class="logo-dot text-[#d00e15]">.</span>
                </div>
            </div>
            <div class="flex items-center space-x-4">

                {{-- ✦ List your Property Link — shown when Hotel tab is active --}}
                <a
                    href="{{ route('list-your-property') }}"
                    id="nav-list-property-link"
                    class="hidden items-center gap-1.5 text-sm font-semibold text-[#d00e15] hover:text-[#A90B16] px-3 py-2 rounded-xl border border-[#d00e15]/30 hover:bg-[#d00e15]/5 transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    List your Property
                </a>

                @if (Route::has('login'))
                    @auth
                        @php
                            $user = auth()->user();
                            $isInternalUser = $user && $user->isInternalUser();
                        @endphp
                        @if ($isInternalUser)
                            <a href="{{ route('admin.dashboard') }}" class="text-sm font-semibold text-[#d00e15] hover:text-[#A90B16] px-4 py-2 transition border border-[#d00e15]/30 rounded-md">Dashboard</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-sm font-semibold text-[#d00e15] hover:text-[#A90B16] px-4 py-2 transition border border-[#d00e15]/30 rounded-md">Logout</button>
                            </form>
                        @else
                            <div x-data="{ open:false }" class="relative">
                                <button @click="open = !open" type="button" class="flex items-center gap-2 rounded-full border border-[#d00e15]/30 bg-[#d00e15]/5 px-2 py-1.5 shadow-sm hover:border-[#d00e15]/60 transition">
                                    <span class="w-8 h-8 rounded-full bg-[#d00e15] text-white font-bold text-sm flex items-center justify-center">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </span>
                                    <svg class="w-4 h-4 text-[#d00e15] transition" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                            <a href="{{ route('register') }}" class="text-sm font-bold text-[#d00e15] hover:bg-red-50 px-6 py-2 transition border border-[#d00e15] rounded-xl">Sign Up</a>
                        @endif
                        <a href="{{ route('login') }}" class="bg-[#d00e15] hover:bg-[#A90B16] text-white text-sm font-bold py-2 px-6 rounded-xl transition shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                            Login
                        </a>
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative isolate overflow-hidden w-full bg-[#fafafa]" style="aspect-ratio: 21/9; min-height: 450px; max-height: 750px;">
        <picture class="absolute inset-0 w-full h-full">
            <source srcset="{{ asset('hero-optimized.webp') }}" type="image/webp">
            <img
                src="{{ asset('hero.png') }}"
                alt="Travel hero background"
                class="w-full h-full object-cover object-center"
                loading="eager"
                fetchpriority="high"
                decoding="async"
            >
        </picture>
    </div>

    <!-- Search Component Container -->
    <div class="-mt-16 md:-mt-24 lg:-mt-32 mb-8 md:mb-12 lg:mb-16 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-40">
        <div class="neon-search-container" x-data="{ tab: 'hotels', tripType: 'one_way' }" id="search-box">

            <div class="neon-search-bg"></div>
            <div class="neon-search-inner pt-12 pb-6 px-4 md:px-8">
            
            <!-- Tabs floating on top border -->
            <div class="absolute left-1/2 -translate-x-1/2 -top-6">
                <div class="flex bg-white rounded-full shadow-md border border-gray-100 text-sm font-bold p-1 gap-1">
                    <button @click="tab = 'hotels'; $dispatch('tab-changed', { tab: 'hotels' })" :class="tab === 'hotels' ? 'text-[#d00e15] bg-red-50/50' : 'text-gray-500 hover:bg-gray-50'" class="px-6 py-2.5 rounded-full transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg> Hotel
                    </button>
                    <button @click="tab = 'flights'; $dispatch('tab-changed', { tab: 'flights' })" :class="tab === 'flights' ? 'text-[#d00e15] bg-red-50/50' : 'text-gray-500 hover:bg-gray-50'" class="px-6 py-2.5 rounded-full transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 104 0 2 2 0 012-2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Flight
                    </button>
                </div>
            </div>

            <!-- Tab Contents -->
            <div>
                <!-- Flights Search Form -->
                @include('partials.flights_coming_soon')
                <!-- Hotel Search Form -->
                <div x-show="tab === 'hotels'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                    <form action="{{ route('hotels.search') }}" method="GET">
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 relative z-10">
                            
                            <!-- Destination -->
                            <div class="col-span-1 lg:col-span-4 relative border border-[#d00e15]/40 rounded-2xl bg-white p-3 hover:border-[#d00e15] focus-within:border-[#d00e15] focus-within:ring-1 focus-within:ring-[#d00e15] transition-all group flex flex-col justify-center">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Destination</label>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400 group-focus-within:text-[#d00e15] shrink-0 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <input type="text" name="destination" placeholder="City, region, or specific hotel" class="w-full border-none p-0 focus:ring-0 font-bold text-[13px] text-[#19100F] bg-transparent truncate" required autocomplete="off">
                                </div>
                            </div>

                            <!-- Check-in Date -->
                            <div class="col-span-1 lg:col-span-2 border border-[#d00e15]/40 rounded-2xl bg-white p-3 hover:border-[#d00e15] focus-within:border-[#d00e15] focus-within:ring-1 focus-within:ring-[#d00e15] transition-all group cursor-text" @click="$refs.inputCheckIn.showPicker ? $refs.inputCheckIn.showPicker() : $refs.inputCheckIn.focus()">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Check-in</label>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-[#d00e15] group-focus-within:text-[#d00e15] shrink-0 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <input x-ref="inputCheckIn" type="date" name="check_in" class="w-full border-none p-0 focus:ring-0 font-bold text-[13px] text-[#19100F] bg-transparent leading-none [&::-webkit-calendar-picker-indicator]:hidden" required value="{{ \Carbon\Carbon::tomorrow()->toDateString() }}">
                                </div>
                            </div>

                            <!-- Check-out Date -->
                            <div class="col-span-1 lg:col-span-2 border border-[#d00e15]/40 rounded-2xl bg-white p-3 hover:border-[#d00e15] focus-within:border-[#d00e15] focus-within:ring-1 focus-within:ring-[#d00e15] transition-all group cursor-text" @click="$refs.inputCheckOut.showPicker ? $refs.inputCheckOut.showPicker() : $refs.inputCheckOut.focus()">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Check-out</label>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-[#d00e15] group-focus-within:text-[#d00e15] shrink-0 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <input x-ref="inputCheckOut" type="date" name="check_out" class="w-full border-none p-0 focus:ring-0 font-bold text-[13px] text-[#19100F] bg-transparent leading-none [&::-webkit-calendar-picker-indicator]:hidden" required value="{{ \Carbon\Carbon::tomorrow()->addDays(2)->toDateString() }}">
                                </div>
                            </div>

                            <!-- Guests -->
                            <div class="col-span-1 lg:col-span-2 border border-[#d00e15]/40 rounded-2xl bg-white p-3 hover:border-[#d00e15] focus-within:border-[#d00e15] focus-within:ring-1 focus-within:ring-[#d00e15] transition-all group flex flex-col justify-center">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 hidden lg:block opacity-0">&nbsp;</label>
                                <div class="flex items-center gap-2 h-full lg:mt-3">
                                    <svg class="w-4 h-4 text-gray-400 group-focus-within:text-[#d00e15] shrink-0 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                    <select name="guests" class="flex-1 border-none p-0 focus:ring-0 text-[13px] font-bold text-[#19100F] bg-transparent cursor-pointer bg-none">
                                        <option value="1">1 Guest</option>
                                        <option value="2" selected>2 Guests</option>
                                        <option value="3">3 Guests</option>
                                        <option value="4">4 Guests</option>
                                        <option value="5">5 Guests</option>
                                        <option value="6">6+ Guests</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Search Button -->
                            <div class="col-span-1 lg:col-span-2 flex">
                                <button type="submit" class="w-full bg-[#d00e15] hover:bg-[#A90B16] text-white font-bold text-[14px] rounded-2xl transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2 p-3">
                                    <span>SEARCH</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </button>
                            </div>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- GHURI Service Banners -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-16 pt-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Promo 1 - Flight -->
            <div class="rounded-2xl overflow-hidden shadow-md relative group cursor-pointer h-56 md:h-72 bg-[#0f172a]">
                <img src="/flight-card.webp" alt="Airplane wing in the sky" loading="lazy" decoding="async" class="absolute inset-0 h-full w-full object-cover object-[70%_50%] transition-transform duration-700 group-hover:scale-105">
                <div class="absolute inset-0 bg-gradient-to-r from-[#0f172a]/20 via-transparent to-transparent"></div>
                <div class="relative p-6 h-full flex flex-col justify-between">
                    <div>
                        <h3 class="text-white text-xl md:text-3xl font-extrabold mb-1 drop-shadow-lg">Fly to Your Dream Destinations</h3>
                        <p class="text-white/95 text-sm md:text-base font-bold drop-shadow-md">Explore hundreds of routes at the best prices.</p>
                    </div>
                    <div>
                        <a href="#" class="inline-block bg-white text-[#19100F] px-5 py-2.5 rounded-full font-semibold shadow-sm">Book Flights</a>
                    </div>
                </div>
            </div>

            <!-- Promo 2 - Hotel -->
            <div class="rounded-2xl overflow-hidden shadow-md relative group cursor-pointer h-56 md:h-72 bg-[#0f172a]">
                <img src="/hotel-card.webp" alt="Comfortable hotel room" loading="lazy" decoding="async" class="absolute inset-0 h-full w-full object-cover object-center transition-transform duration-700 group-hover:scale-105">
                <div class="absolute inset-0 bg-gradient-to-r from-[#0f172a]/25 via-transparent to-transparent"></div>
                <div class="relative p-6 h-full flex flex-col justify-between">
                    <div>
                        <h3 class="text-white text-xl md:text-3xl font-extrabold mb-1 drop-shadow-lg">Comfortable Stays, <span class="bg-[#d00e15] text-white px-2 py-1 rounded">Unforgettable</span> Memories</h3>
                        <p class="text-white text-sm md:text-base font-bold drop-shadow-md">Find the perfect hotel for every trip.</p>
                    </div>
                    <div>
                        <a href="#" class="inline-block bg-white text-[#19100F] px-5 py-2.5 rounded-full font-semibold shadow-sm">Book Hotels</a>
                    </div>
                </div>
            </div>

            <!-- Promo 3 - Discount -->
            <div class="rounded-2xl overflow-hidden shadow-md relative group cursor-pointer h-56 md:h-72 bg-[#dbeafe]">
                <img src="/discount-card.webp" alt="Discount ticket illustration" loading="lazy" decoding="async" class="absolute inset-0 h-full w-full object-cover object-center transition-transform duration-700 group-hover:scale-105">
                <div class="absolute inset-0 bg-gradient-to-r from-white/30 via-white/10 to-transparent"></div>
                <div class="relative p-6 h-full flex flex-col justify-between">
                    <div>
                        <h3 class="text-[#19100F] text-xl md:text-3xl font-extrabold mb-1 drop-shadow-2xl">Best Prices Every Time</h3>
                        <p class="text-[#19100F] text-sm md:text-base font-bold drop-shadow-lg">We bring you the best deals so you can travel more.</p>
                    </div>
                    <div>
                        <span class="inline-block bg-white text-[#19100F] px-5 py-2.5 rounded-full font-semibold shadow-sm">Learn More</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Airlines Showcase Section -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="bg-white rounded-[28px] shadow-[0_4px_30px_rgba(15,23,42,0.04)] border border-gray-50 p-8 md:p-12">
            <div class="text-center max-w-3xl mx-auto mb-10">
                <h2 class="text-2xl md:text-[28px] font-poppins font-bold text-[#19100F] mb-4 tracking-tight">Search Top Airlines</h2>
                <p class="text-sm text-gray-500 leading-relaxed font-medium">
                    GHURI's user-friendly platform, powered by GHURI technology, connects you to top airlines instantly. Enjoy a comfortable and hassle-free journey on any destination and get tickets of top airlines easily.
                </p>
            </div>
            
            @php
            $topAirlines = [
                ['name' => 'Biman Bangladesh Airlines', 'iata' => 'BG'],
                ['name' => 'US-Bangla Airlines', 'iata' => 'BS'],
                ['name' => 'NOVOAIR', 'iata' => 'VQ'],
                ['name' => 'Air Astra', 'iata' => '2A'],
                ['name' => 'Emirates', 'iata' => 'EK'],
                ['name' => 'Singapore Airlines', 'iata' => 'SQ'],
                ['name' => 'Malaysia Airlines', 'iata' => 'MH'],
                ['name' => 'Qatar Airways', 'iata' => 'QR'],
                ['name' => 'Saudia Airlines', 'iata' => 'SV'],
                ['name' => 'Air India', 'iata' => 'AI'],
                ['name' => 'Gulf Air', 'iata' => 'GF'],
                ['name' => 'Turkish Airlines', 'iata' => 'TK'],
                ['name' => 'Thai Airways International', 'iata' => 'TG'],
                ['name' => 'Cathay Pacific Airways', 'iata' => 'CX'],
                ['name' => 'China Southern Airlines', 'iata' => 'CZ'],
                ['name' => 'SriLankan Airlines', 'iata' => 'UL'],
                ['name' => 'AirAsia', 'iata' => 'AK'],
                ['name' => 'Batik Air', 'iata' => 'ID'],
                ['name' => 'IndiGo', 'iata' => '6E'],
                ['name' => 'Air Arabia', 'iata' => 'G9'],
            ];
            @endphp
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-8 gap-y-6">
                @foreach($topAirlines as $airline)
                <a href="javascript:void(0)" class="flex items-center justify-between py-2 px-1 hover:bg-gray-50/50 rounded-lg transition-colors group">
                    <div class="flex items-center gap-4">
                        <img src="https://images.kiwi.com/airlines/64/{{ $airline['iata'] }}.png" alt="{{ $airline['name'] }}" class="w-8 h-8 object-contain">
                        <span class="text-sm font-bold text-[#19100F] group-hover:text-[#d00e15] transition-colors">{{ $airline['name'] }}</span>
                    </div>
                    <svg class="w-3.5 h-3.5 text-gray-300 group-hover:text-[#d00e15] transition-colors shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="pb-24 bg-gray-50 relative overflow-hidden">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-12">
                <h2 class="text-2xl md:text-3xl font-poppins font-bold text-[#19100F] mb-2">Why Choose Us</h2>
                <p class="text-sm text-gray-500">Powered exclusively by GHURI technology - global content, real-time pricing, and seamless booking.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Feature 1 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition duration-300 group text-center">
                    <div class="w-12 h-12 mx-auto text-[#d00e15] mb-4 transform group-hover:scale-110 transition duration-300">
                        <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 104 0 2 2 0 012-2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-base font-bold mb-2 text-[#19100F]">Global Flight Content</h3>
                    <p class="text-gray-500 text-xs leading-relaxed">Access worldwide airline inventory through GHURI's GDS-connected availability search.</p>
                </div>
                <!-- Feature 2 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition duration-300 group text-center">
                    <div class="w-12 h-12 mx-auto text-[#d00e15] mb-4 transform group-hover:scale-110 transition duration-300">
                        <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-base font-bold mb-2 text-[#19100F]">Real-Time Revalidation</h3>
                    <p class="text-gray-500 text-xs leading-relaxed">GHURI revalidates fares before payment, ensuring price accuracy and reducing failed bookings.</p>
                </div>
                <!-- Feature 3 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition duration-300 group text-center">
                    <div class="w-12 h-12 mx-auto text-[#d00e15] mb-4 transform group-hover:scale-110 transition duration-300">
                        <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                    </div>
                    <h3 class="text-base font-bold mb-2 text-[#19100F]">Instant E-Ticketing</h3>
                    <p class="text-gray-500 text-xs leading-relaxed">Book and receive PNR/UniqueID confirmation via GHURI's booking endpoint instantly.</p>
                </div>
                <!-- Feature 4 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition duration-300 group text-center">
                    <div class="w-12 h-12 mx-auto text-[#d00e15] mb-4 transform group-hover:scale-110 transition duration-300">
                        <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <h3 class="text-base font-bold mb-2 text-[#19100F]">Sandbox + Production</h3>
                    <p class="text-gray-500 text-xs leading-relaxed">GHURI supports both sandbox testing and live production environments for safer deployments.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-[#d00e15] text-white py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex flex-col items-center md:items-start gap-1">
                <div class="flex items-center gap-2">
                    <div class="font-poppins font-extrabold text-2xl tracking-tight text-white">GHURI<span class="text-white">.</span></div>
                </div>
                <p class="text-xs text-white/60 max-w-xs text-center md:text-left mt-2 leading-relaxed">
                    Smart bookings, competitive deals, and reliable support - all in one platform.
                </p>
                <p class="text-[10px] text-white/40 mt-4">
                    &copy; {{ date('Y') }} GHURI OTA. All rights reserved.
                </p>
            </div>
            
            <div class="flex flex-wrap justify-center md:justify-end items-center gap-8">
                <div class="flex flex-col items-center gap-2">
                    <span class="text-[9px] text-white/50 uppercase tracking-widest font-bold">Verified By</span>
                    <div class="flex items-center gap-2 bg-white/10 px-3 py-2 rounded-full border border-white/5">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        <span class="font-bold text-xs">DigiCert</span>
                    </div>
                </div>
                <div class="flex flex-col items-center gap-2">
                    <span class="text-[9px] text-white/50 uppercase tracking-widest font-bold">Authorized By</span>
                    <div class="flex items-center gap-2 bg-white/10 px-4 py-2 rounded-full border border-white/5">
                        <span class="font-black text-xs italic tracking-wider">IATA</span>
                    </div>
                </div>
                <div class="flex flex-col items-center gap-2">
                    <span class="text-[9px] text-white/50 uppercase tracking-widest font-bold">Member of</span>
                    <div class="flex items-center gap-2 bg-white/10 px-4 py-2 rounded-full border border-white/5">
                        <span class="font-black text-xs tracking-wider">BASIS</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('airportSearch', (initialCode) => ({
                open: false,
                search: '',
                selectedCode: initialCode,
                selectedDisplay: '',
                filtered: [],
                loading: false,

                clearSelection() {
                    this.open = true;
                    this.search = '';
                    this.selectedCode = '';
                    this.selectedDisplay = '';
                    this.filtered = [];
                    this.loading = false;
                },

                init() {
                    if (initialCode) {
                        fetch(`/ajax/airports/search?q=${encodeURIComponent(initialCode)}`)
                            .then(res => res.json())
                            .then(data => {
                                if(data.length > 0) {
                                    this.search = data[0].display_name;
                                    this.selectedDisplay = data[0].display_name;
                                    this.selectedCode = data[0].code;
                                    this.filtered = [];
                                }
                            })
                            .catch(() => {
                                this.search = initialCode;
                            });
                    }
                },

                filter() {
                    this.open = true;
                    if (this.search.length < 1) {
                        this.filtered = [];
                        return;
                    }
                    this.loading = true;
                    fetch(`/ajax/airports/search?q=${encodeURIComponent(this.search)}`)
                        .then(res => res.json())
                        .then(data => {
                            this.filtered = data;
                            this.loading = false;
                        })
                        .catch(() => {
                            this.filtered = [];
                            this.loading = false;
                        });
                },

                select(airport) {
                    this.selectedCode = airport.code;
                    this.search = airport.display_name;
                    this.selectedDisplay = airport.display_name;
                    this.open = false;
                }
            }));
        });
    </script>
    <script>
        // Show/hide "List your Property" link based on active search tab
        (function () {
            var link = document.getElementById('nav-list-property-link');
            if (!link) return;

            function showLink(tab) {
                if (tab === 'hotels') {
                    link.classList.remove('hidden');
                    link.classList.add('inline-flex');
                } else {
                    link.classList.add('hidden');
                    link.classList.remove('inline-flex');
                }
            }

            // Show on page load (default tab is hotels)
            showLink('hotels');

            // Listen for tab change events dispatched by Alpine.js
            window.addEventListener('tab-changed', function (e) {
                showLink(e.detail.tab);
            });
        })();
    </script>
</body>
</html>
