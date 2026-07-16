<!DOCTYPE html>
<html lang="en-BD">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bookdei | Flights, Hotels & Travel Deals</title>
    <meta name="description" content="Book flights and hotels with Bookdei. Discover competitive travel deals, simple booking, and reliable support for journeys across Bangladesh and beyond.">
    <meta name="robots" content="index, follow, max-image-preview:large">
    <meta name="geo.region" content="BD">
    <meta name="geo.placename" content="Bangladesh">
    <link rel="canonical" href="{{ url('/') }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Bookdei">
    <meta property="og:title" content="Bookdei | Flights, Hotels & Travel Deals">
    <meta property="og:description" content="Book flights and hotels with Bookdei. Discover competitive travel deals, simple booking, and reliable support.">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:image" content="{{ asset('hero-pc.webp') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Bookdei | Flights, Hotels & Travel Deals">
    <meta name="twitter:description" content="Book flights and hotels with Bookdei. Discover competitive travel deals, simple booking, and reliable support.">
    <meta name="twitter:image" content="{{ asset('hero-pc.webp') }}">
    <script type="application/ld+json">{!! json_encode([
        '@context' => 'https://schema.org',
        '@graph' => [[
            '@type' => 'WebSite',
            '@id' => url('/').'#website',
            'name' => 'Bookdei',
            'alternateName' => 'Bookdei Bangladesh',
            'url' => url('/'),
            'inLanguage' => 'en-BD',
        ], [
            '@type' => ['Organization', 'TravelAgency'],
            '@id' => url('/').'#organization',
            'name' => 'Bookdei',
            'url' => url('/'),
            'logo' => ['@type' => 'ImageObject', 'url' => asset('images/logo.png')],
            'areaServed' => ['@type' => 'Country', 'name' => 'Bangladesh'],
            'currenciesAccepted' => 'BDT',
            'description' => 'Bangladesh-based platform for booking flights, hotels, resorts, and travel stays.',
        ]],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:ital,wght@0,400;0,500;0,600;0,700;1,400&family=JetBrains+Mono:wght@400;500;600&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="preload" as="image" href="{{ asset('hero-pc.webp') }}" type="image/webp" media="(min-width: 768px)">
    <link rel="preload" as="image" href="{{ asset('hero-mobile.webp') }}" type="image/webp" media="(max-width: 767px)">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-white text-dark selection:bg-[#d00e15] selection:text-white">
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
                    class="booking-nav-property hidden items-center"
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

    <!-- Hero Section -->
    <div class="homepage-hero relative isolate overflow-hidden w-full bg-white">
        <picture class="absolute inset-0 block w-full h-full">
            <source media="(max-width: 767px)" srcset="{{ asset('hero-mobile.webp') }}" type="image/webp">
            <source media="(min-width: 768px)" srcset="{{ asset('hero-pc.webp') }}" type="image/webp">
            <img
                src="{{ asset('hero-pc.png') }}"
                alt="Travel hero background"
                class="homepage-hero-image block w-full h-full"
                loading="eager"
                fetchpriority="high"
                decoding="async"
            >
        </picture>
        <!-- Gradient overlay to blend image into white background -->
        <div class="absolute inset-x-0 bottom-0 h-64 bg-gradient-to-t from-white to-transparent"></div>
    </div>

    <!-- Search Component Container -->
    <div class="travel-search-wrap mb-10 md:mb-14 relative z-40">
        <div
            class="travel-search-card"
            x-data="{
                tab: 'hotels',
                checkIn: '{{ \Carbon\Carbon::tomorrow()->toDateString() }}',
                checkOut: '{{ \Carbon\Carbon::tomorrow()->addDay()->toDateString() }}',
                rooms: 1,
                adults: 2,
                children: 0,
                guestOpen: false,
                formatDate(value) {
                    return new Intl.DateTimeFormat('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }).format(new Date(value + 'T00:00:00'));
                },
                weekday(value) {
                    return new Intl.DateTimeFormat('en-US', { weekday: 'long' }).format(new Date(value + 'T00:00:00'));
                },
                nights() {
                    return Math.max(1, Math.round((new Date(this.checkOut + 'T00:00:00') - new Date(this.checkIn + 'T00:00:00')) / 86400000));
                },
                updateCheckIn() {
                    const nextDay = new Date(this.checkIn + 'T00:00:00');
                    nextDay.setDate(nextDay.getDate() + 1);
                    if (new Date(this.checkOut + 'T00:00:00') <= new Date(this.checkIn + 'T00:00:00')) {
                        this.checkOut = nextDay.toISOString().slice(0, 10);
                    }
                }
            }"
            id="search-box"
        >
            <!-- Floating service tabs -->
            <div class="travel-search-tabs" role="tablist" aria-label="Travel services">
                <button type="button" @click="tab = 'hotels'; $dispatch('tab-changed', { tab: 'hotels' })" :class="tab === 'hotels' ? 'is-active' : ''" class="travel-search-tab" role="tab" :aria-selected="tab === 'hotels'">
                    <span class="travel-tab-art travel-tab-art-stays" aria-hidden="true"></span>
                    <span>Stays</span>
                </button>
                <button type="button" @click="tab = 'flights'; $dispatch('tab-changed', { tab: 'flights' })" :class="tab === 'flights' ? 'is-active' : ''" class="travel-search-tab" role="tab" :aria-selected="tab === 'flights'">
                    <span class="travel-tab-art travel-tab-art-flight" aria-hidden="true"></span>
                    <span>Flights</span>
                </button>
                <button type="button" @click="tab = 'visa'; $dispatch('tab-changed', { tab: 'visa' })" :class="tab === 'visa' ? 'is-active' : ''" class="travel-search-tab" role="tab" :aria-selected="tab === 'visa'">
                    <span class="travel-tab-art travel-tab-art-visa" aria-hidden="true"></span>
                    <span>Visa</span>
                </button>
                <button type="button" @click="tab = 'packages'; $dispatch('tab-changed', { tab: 'packages' })" :class="tab === 'packages' ? 'is-active' : ''" class="travel-search-tab" role="tab" :aria-selected="tab === 'packages'">
                    <span class="travel-tab-art travel-tab-art-packages" aria-hidden="true"></span>
                    <span>Packages</span>
                </button>
            </div>

            <!-- Hotel Search Form -->
            <div x-show="tab === 'hotels'" x-transition.opacity>
                <form action="{{ route('hotels.search') }}" method="GET" class="travel-hotel-form">
                    <div class="travel-search-main-row">
                        <label class="travel-field travel-destination-field">
                            <span class="travel-destination-input">
                                <span class="travel-destination-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z" stroke-width="1.8"/><circle cx="12" cy="10" r="2.7" stroke-width="1.8"/></svg>
                                </span>
                                <input type="text" name="destination" placeholder="Where to?" required autocomplete="off">
                            </span>
                        </label>

                        <div class="travel-field travel-date-field">
                            <span class="travel-field-leading" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="5" width="18" height="16" rx="2" stroke-width="1.8"/><path d="M7 3v4m10-4v4M3 10h18" stroke-width="1.8" stroke-linecap="round"/></svg></span>
                            <button type="button" class="travel-date-trigger" @click="$refs.checkInPicker.showPicker ? $refs.checkInPicker.showPicker() : $refs.checkInPicker.click()" aria-label="Choose stay dates">
                                <span class="travel-field-label">Dates</span>
                                <strong><span x-text="formatDate(checkIn)"></span> - <span x-text="formatDate(checkOut)"></span></strong>
                            </button>
                            <input x-ref="checkInPicker" type="date" name="check_in" x-model="checkIn" @change="updateCheckIn()" min="{{ now()->toDateString() }}" required aria-label="Check-in date" tabindex="-1">
                            <input x-ref="checkOutPicker" type="date" name="check_out" x-model="checkOut" :min="checkIn" required aria-label="Check-out date" tabindex="-1">
                        </div>

                        <div class="travel-field travel-guests-field" @click.away="guestOpen = false">
                            <button type="button" @click="guestOpen = !guestOpen" :aria-expanded="guestOpen" aria-haspopup="dialog">
                                <span class="travel-field-leading" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="7" r="3.5" stroke-width="1.8"/><path d="M5 21v-2a7 7 0 0 1 14 0v2H5Z" stroke-width="1.8" stroke-linejoin="round"/></svg></span>
                                <span><span class="travel-field-label">Travelers</span><strong x-text="(adults + children) + ((adults + children) === 1 ? ' traveler, ' : ' travelers, ') + rooms + (rooms === 1 ? ' room' : ' rooms')"></strong></span>
                            </button>
                            <input type="hidden" name="rooms" :value="rooms">
                            <input type="hidden" name="guests" :value="adults + children">
                            <input type="hidden" name="children" :value="children">

                            <div x-show="guestOpen" x-transition.origin.top.right class="travel-guests-popover" style="display:none;">
                                <div class="travel-counter-row">
                                    <div><strong>Rooms</strong><span>Number of rooms</span></div>
                                    <div class="travel-counter"><button type="button" @click="rooms = Math.max(1, rooms - 1)" aria-label="Remove room">−</button><b x-text="rooms"></b><button type="button" @click="rooms = Math.min(8, rooms + 1)" aria-label="Add room">+</button></div>
                                </div>
                                <div class="travel-counter-row">
                                    <div><strong>Adults</strong><span>Ages 13 or above</span></div>
                                    <div class="travel-counter"><button type="button" @click="adults = Math.max(1, adults - 1)" aria-label="Remove adult">−</button><b x-text="adults"></b><button type="button" @click="adults = Math.min(16, adults + 1)" aria-label="Add adult">+</button></div>
                                </div>
                                <div class="travel-counter-row">
                                    <div><strong>Children</strong><span>Ages 0–12</span></div>
                                    <div class="travel-counter"><button type="button" @click="children = Math.max(0, children - 1)" aria-label="Remove child">−</button><b x-text="children"></b><button type="button" @click="children = Math.min(8, children + 1)" aria-label="Add child">+</button></div>
                                </div>
                                <button type="button" class="travel-guests-done" @click="guestOpen = false">Done</button>
                            </div>
                        </div>

                    </div>

                    <div class="travel-search-filter-row">
                        <label class="travel-filter-check">
                            <input type="checkbox" name="free_cancellation" value="1">
                            <span>Free cancellation</span>
                        </label>
                        <button type="submit" class="travel-search-submit"><span>Search</span></button>
                    </div>
                </form>
            </div>

            <div x-show="tab !== 'hotels'" x-transition.opacity class="travel-service-placeholder" style="display:none;">
                <div>
                    <span x-text="tab.charAt(0).toUpperCase() + tab.slice(1)"></span> booking is coming soon.
                </div>
                <button type="button" @click="tab = 'hotels'; $dispatch('tab-changed', { tab: 'hotels' })">Search hotels instead</button>
            </div>
        </div>
    </div>

    @if(\App\Models\Setting::get('promo_banner_enabled', '0') === '1')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mb-10 relative z-30">
        <div class="bg-white rounded-[24px] shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-6 md:p-8 flex flex-col md:flex-row items-center justify-between gap-6 border border-gray-100">
            <div class="flex items-center gap-6">
                <!-- Icon -->
                <div class="hidden md:flex items-center justify-center bg-red-50 w-14 h-14 rounded-2xl shrink-0">
                    <svg class="w-8 h-8 text-[#C0112C]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                </div>
                <div class="flex flex-col text-left">
                    <h3 class="text-2xl font-poppins font-bold text-[#19100F] mb-1">
                        {{ \App\Models\Setting::get('promo_banner_title', 'Big Summer Sale: Save up to 40%') }}
                    </h3>
                    <p class="text-gray-500 text-[15px]">
                        {{ \App\Models\Setting::get('promo_banner_subtitle', 'Book memorable stays, tours and experiences with ease.') }}
                    </p>
                </div>
            </div>
            <a href="{{ \App\Models\Setting::get('promo_banner_button_url', '#') }}" class="w-full md:w-auto text-center bg-[#C0112C] hover:bg-[#E8424D] text-white font-semibold py-3 px-8 rounded-xl transition duration-300 whitespace-nowrap shrink-0 shadow-[0_4px_14px_0_rgba(192,17,44,0.39)] hover:shadow-[0_6px_20px_rgba(192,17,44,0.23)] hover:-translate-y-0.5">
                {{ \App\Models\Setting::get('promo_banner_button_text', 'Search') }}
            </a>
        </div>
    </div>
    @endif
    <!-- Recent Searches Section -->
    @if(isset($recentSearches) && count($recentSearches) > 0)
    <div class="w-full bg-[#f0f3f6] py-10 my-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-5">
                <h2 class="text-2xl font-poppins font-bold text-[#19100F]">Your recent activity</h2>
            </div>
            <div class="flex gap-4 overflow-x-auto pb-2 hide-scrollbar snap-x">
                @foreach($recentSearches as $search)
                @php
                    $cin = \Carbon\Carbon::parse($search['check_in']);
                    $cout = \Carbon\Carbon::parse($search['check_out']);
                @endphp
                <a href="{{ route('hotels.search', ['destination' => $search['destination'], 'check_in' => $search['check_in'], 'check_out' => $search['check_out'], 'guests' => $search['guests'], 'rooms' => $search['rooms']]) }}" 
                   class="flex items-center bg-white rounded-2xl border border-gray-100 hover:shadow-md transition duration-300 shrink-0 snap-start p-3 gap-4"
                   style="width: 340px; height: 96px;">
                    <div class="w-[72px] h-[72px] rounded-xl bg-[#f5f7f9] overflow-hidden shrink-0 flex items-center justify-center relative">
                        @if(!empty($search['image_url']))
                            <img src="{{ $search['image_url'] }}" alt="{{ $search['destination'] }}" class="w-full h-full object-cover">
                        @else
                            <svg class="w-8 h-8 text-[#006e5b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0 flex flex-col justify-center">
                        <h3 class="font-bold text-[#19100F] text-[15px] leading-tight truncate mb-1" title="{{ $search['property_name'] ?: $search['destination'] }}">
                            {{ $search['property_name'] ?: $search['destination'] }}
                        </h3>
                        
                        <div class="text-[13px] text-gray-500 truncate mb-0.5 font-medium">
                            {{ $search['property_name'] ? $search['destination'] : ($search['guests'] . ' ' . Str::plural('guest', $search['guests']) . ', ' . $search['rooms'] . ' ' . Str::plural('room', $search['rooms'])) }}
                        </div>
                        <div class="text-[13px] text-gray-500 truncate font-medium">
                            {{ $cin->format('M j') }} - {{ $cout->format('M j') }}
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <style>
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    <!-- Offers Section -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-16 pt-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-8 gap-y-12">
            @forelse(isset($offers) ? $offers : [] as $offer)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 relative mt-4 mb-4 mx-2 md:ml-6 md:mr-6">
                <!-- Overlapping Image (Top Left) -->
                <div class="w-[calc(100%-2rem)] mx-auto h-[200px] md:absolute md:-left-5 md:-top-5 md:w-[160px] md:h-[160px] -mt-6 md:mt-0 rounded-xl overflow-hidden shadow-lg z-10 bg-gray-100">
                    <img src="{{ $offer->image_url }}" alt="{{ $offer->title }}" class="w-full h-full object-cover">
                </div>
                
                <!-- Text Content -->
                <div class="p-5 md:pl-[180px] md:pr-6 md:pt-6 md:pb-[60px] md:min-h-[160px] flex flex-col justify-between h-full">
                    <div>
                        <h3 class="font-bold text-[#19100F] text-lg md:text-xl leading-tight mb-2.5">{{ $offer->title }}</h3>
                        <p class="text-sm text-gray-500 line-clamp-3">{{ $offer->description }}</p>
                    </div>
                    
                    <div class="mt-5 md:mt-0">
                        <div class="md:absolute md:bottom-6 md:left-[180px] inline-flex items-center gap-2 bg-[#f0f4f8] rounded-lg px-3 py-1.5 border border-[#e2e8f0]">
                            <svg class="w-4 h-4 text-[#d00e15]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                            <span class="text-[13px] font-bold text-[#0f172a] uppercase tracking-wide">{{ $offer->code ?: 'DEAL' }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Overlapping Button (Bottom Right) -->
                <a href="#" class="absolute -bottom-4 -right-2 md:-bottom-5 md:-right-5 flex items-center gap-1.5 bg-[#d00e15] hover:bg-[#a00b10] text-white text-[14px] font-bold px-5 py-2.5 md:px-6 md:py-3 rounded-xl shadow-lg transition-all hover:-translate-y-1 z-10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 17L17 7m0 0H8m9 0v9"></path></svg>
                    Learn More
                </a>
            </div>
            @empty
            <div class="col-span-1 lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 relative mt-4 mb-4 mx-2 md:ml-6 md:mr-6">
                    <!-- Overlapping Image (Top Left) -->
                    <div class="w-[calc(100%-2rem)] mx-auto h-[200px] md:absolute md:-left-6 md:-top-6 md:w-[240px] md:h-[240px] -mt-6 md:mt-0 rounded-xl overflow-hidden shadow-lg z-10 bg-gray-100">
                        <img src="https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=400&q=80" alt="Getaway Deals" class="w-full h-full object-cover">
                    </div>
                    
                    <!-- Text Content -->
                    <div class="p-5 md:pl-[250px] md:pr-6 md:pt-6 md:pb-[60px] md:min-h-[220px] flex flex-col justify-between h-full">
                        <div>
                            <h3 class="font-bold text-[#19100F] text-lg md:text-xl leading-tight mb-2.5">No catch. Just getaways.</h3>
                            <p class="text-sm text-gray-500 line-clamp-3">At least 15% off select stays worldwide &ndash; just book and go.</p>
                        </div>
                        
                        <div class="mt-5 md:mt-0">
                            <div class="md:absolute md:bottom-6 md:left-[180px] inline-flex items-center gap-2 bg-[#f0f4f8] rounded-lg px-3 py-1.5 border border-[#e2e8f0]">
                                <svg class="w-4 h-4 text-[#d00e15]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                <span class="text-[13px] font-bold text-[#0f172a] uppercase tracking-wide">GETAWAY15</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Overlapping Button (Bottom Right) -->
                    <a href="#" class="absolute -bottom-4 -right-2 md:-bottom-5 md:-right-5 flex items-center gap-1.5 bg-[#d00e15] hover:bg-[#a00b10] text-white text-[14px] font-bold px-5 py-2.5 md:px-6 md:py-3 rounded-xl shadow-lg transition-all hover:-translate-y-1 z-10">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 17L17 7m0 0H8m9 0v9"></path></svg>
                        Learn More
                    </a>
                </div>
            </div>
            @endforelse
            @if(isset($offers) && count($offers) == 1)
            <!-- Add a fallback dummy offer to fill the second column so it doesn't look empty -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 relative mt-4 mb-4 mx-2 md:ml-6 md:mr-6">
                <!-- Overlapping Image (Top Left) -->
                <div class="w-[calc(100%-2rem)] mx-auto h-[200px] md:absolute md:-left-5 md:-top-5 md:w-[160px] md:h-[160px] -mt-6 md:mt-0 rounded-xl overflow-hidden shadow-lg z-10 bg-gray-100">
                    <img src="https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=400&q=80" alt="Getaway Deals" class="w-full h-full object-cover">
                </div>
                
                <!-- Text Content -->
                <div class="p-5 md:pl-[180px] md:pr-6 md:pt-6 md:pb-[60px] md:min-h-[160px] flex flex-col justify-between h-full">
                    <div>
                        <h3 class="font-bold text-[#19100F] text-lg md:text-xl leading-tight mb-2.5">No catch. Just getaways.</h3>
                        <p class="text-sm text-gray-500 line-clamp-3">At least 15% off select stays worldwide &ndash; just book and go.</p>
                    </div>
                    
                    <div class="mt-5 md:mt-0">
                        <div class="md:absolute md:bottom-6 md:left-[180px] inline-flex items-center gap-2 bg-[#f0f4f8] rounded-lg px-3 py-1.5 border border-[#e2e8f0]">
                            <svg class="w-4 h-4 text-[#d00e15]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                            <span class="text-[13px] font-bold text-[#0f172a] uppercase tracking-wide">GETAWAY15</span>
                        </div>
                    </div>
                </div>
                
                <!-- Overlapping Button (Bottom Right) -->
                <a href="#" class="absolute -bottom-4 -right-2 md:-bottom-5 md:-right-5 flex items-center gap-1.5 bg-[#d00e15] hover:bg-[#a00b10] text-white text-[14px] font-bold px-5 py-2.5 md:px-6 md:py-3 rounded-xl shadow-lg transition-all hover:-translate-y-1 z-10">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 17L17 7m0 0H8m9 0v9"></path></svg>
                    Learn More
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Homes guests love -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="flex justify-between items-end mb-5">
            <div>
                <h2 class="text-2xl font-poppins font-bold text-[#19100F]">Homes guests love</h2>
                <p class="text-gray-500 text-sm mt-1">Top rated properties from real guests</p>
            </div>
            <a href="{{ route('hotels.search') }}" class="text-[#d00e15] hover:text-[#a00b10] text-sm font-semibold hidden sm:block">Discover homes</a>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            @forelse($topProperties as $property)
            <a href="{{ route('hotels.show', $property) }}" class="group block relative flex flex-col h-full cursor-pointer">
                <div class="aspect-[4/3] w-full overflow-hidden relative rounded-2xl mb-3 shrink-0">
                    <img src="{{ $property->cover_photo_url }}" alt="{{ $property->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    <div class="absolute top-3 right-3 bg-white rounded-full p-2 text-[#d00e15] shadow-sm hover:bg-gray-50 transition">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    </div>
                </div>
                
                <div class="flex flex-col flex-1">
                    <h3 class="font-bold text-[#19100F] text-base leading-tight mb-0.5 truncate">{{ $property->name }}</h3>
                    <p class="text-sm text-gray-500 mb-2 truncate">{{ $property->city }}{{ $property->country ? ', ' . $property->country : '' }}</p>
                    
                    @if($property->average_rating)
                    <div class="flex items-center gap-1.5 mb-2 mt-0.5">
                        <div class="bg-[#006e5b] text-white text-[11px] font-bold px-1.5 py-0.5 rounded">{{ number_format($property->average_rating, 1) }}</div>
                        <div class="text-[13px] text-[#19100F] font-bold">{{ $property->average_rating >= 9 ? 'Exceptional' : ($property->average_rating >= 8 ? 'Excellent' : ($property->average_rating >= 7 ? 'Very Good' : 'Good')) }}</div>
                        <div class="text-[13px] text-gray-500">({{ $property->reviews_count }} {{ Str::plural('review', $property->reviews_count) }})</div>
                    </div>
                    @else
                    <div class="flex items-center mb-2 mt-0.5">
                        <div class="text-[13px] text-gray-500 italic">No reviews yet</div>
                    </div>
                    @endif
                    
                    <div class="mt-auto pt-1.5 flex flex-col">
                        <span class="text-xs text-gray-500 mb-0.5">Starting from</span>
                        <span class="font-bold text-[#19100F] text-[16px]">{{ \App\Helpers\Currency::format($property->lowest_price ?? 0) }}</span>
                    </div>
                </div>
            </a>
            @empty
            <div class="col-span-full text-center py-8 text-gray-500">No properties available yet.</div>
            @endforelse
        </div>
        <a href="{{ route('hotels.search') }}" class="text-[#d00e15] hover:text-[#a00b10] text-sm font-semibold block sm:hidden mt-4 text-center">Discover homes</a>
    </div>

    <!-- Promoted Properties -->
    @if(isset($promotedProperties) && $promotedProperties->count() > 0)
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="flex justify-between items-end mb-5">
            <div>
                <h2 class="text-2xl font-poppins font-bold text-[#19100F]">Save on select properties</h2>
                <p class="text-gray-500 text-sm mt-1">Exclusive deals and promotions</p>
            </div>
            <a href="{{ route('hotels.search', ['has_promotions' => true]) }}" class="text-[#d00e15] hover:text-[#a00b10] hover:bg-[#d00e15]/5 text-sm font-semibold hidden sm:block border border-[#d00e15] px-5 py-2 rounded-full transition">See more deals</a>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($promotedProperties as $property)
            <a href="{{ route('hotels.show', $property) }}" class="group block relative flex flex-col h-full cursor-pointer">
                <div class="aspect-[4/3] w-full overflow-hidden relative rounded-2xl mb-3 shrink-0">
                    <img src="{{ $property->cover_photo_url }}" alt="{{ $property->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    <div class="absolute top-3 right-3 bg-white rounded-full p-2 text-[#d00e15] shadow-sm hover:bg-gray-50 transition">
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    </div>
                </div>
                
                <div class="flex flex-col flex-1">
                    <h3 class="font-bold text-[#19100F] text-base leading-tight mb-0.5 truncate">{{ $property->name }}</h3>
                    <p class="text-sm text-gray-500 mb-2 truncate">{{ $property->city }}{{ $property->country ? ', ' . $property->country : '' }}</p>
                    
                    @if($property->average_rating)
                    <div class="flex items-center gap-1.5 mb-2 mt-0.5">
                        <div class="bg-[#006e5b] text-white text-[11px] font-bold px-1.5 py-0.5 rounded">{{ number_format($property->average_rating, 1) }}</div>
                        <div class="text-[13px] text-[#19100F] font-bold">{{ $property->average_rating >= 9 ? 'Exceptional' : ($property->average_rating >= 8 ? 'Excellent' : ($property->average_rating >= 7 ? 'Very Good' : 'Good')) }}</div>
                        <div class="text-[13px] text-gray-500">({{ $property->reviews_count }} {{ Str::plural('review', $property->reviews_count) }})</div>
                    </div>
                    @else
                    <div class="flex items-center mb-2 mt-0.5">
                        <div class="text-[13px] text-gray-500 italic">No reviews yet</div>
                    </div>
                    @endif
                    
                    <div class="mt-auto pt-1 flex flex-col items-start">
                        @if($property->promotions->first())
                        <div class="bg-[#006e5b] text-white text-[11px] font-bold px-1.5 py-0.5 rounded mb-1.5">
                            {{ strtoupper($property->promotions->first()->discount_display) }}
                        </div>
                        @endif
                        <span class="text-xs text-gray-500 mb-0.5">Starting from</span>
                        <span class="font-bold text-[#19100F] text-[16px]">{{ \App\Helpers\Currency::format($property->lowest_price ?? 0) }}</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        <a href="{{ route('hotels.search', ['has_promotions' => true]) }}" class="text-[#d00e15] hover:text-[#a00b10] hover:bg-[#d00e15]/5 text-sm font-semibold block sm:hidden mt-4 text-center border border-[#d00e15] px-5 py-2 rounded-full transition">See more deals</a>
    </div>
    @endif

    <!-- Info Cards -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-10 pt-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 lg:gap-8">
            <!-- Card 1 -->
            <div class="group bg-white rounded-[20px] p-7 flex flex-col items-start border border-gray-100 shadow-[0_2px_15px_rgba(0,0,0,0.03)] hover:shadow-[0_10px_30px_rgba(0,0,0,0.08)] transition-all duration-400 hover:-translate-y-1 relative overflow-hidden">
                <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" class="drop-shadow-sm mb-5 group-hover:scale-110 transition-transform duration-500">
                    <path d="M16 22V14C16 9.58 19.58 6 24 6C28.42 6 32 9.58 32 14V22" stroke="#EAB308" stroke-width="4.5" stroke-linecap="round"/>
                    <rect x="10" y="22" width="28" height="20" rx="4" fill="url(#lock-grad)"/>
                    <circle cx="24" cy="30" r="3" fill="#1C1C1E"/>
                    <path d="M23 32L22 36H26L25 32H23Z" fill="#1C1C1E"/>
                    <defs>
                        <linearGradient id="lock-grad" x1="10" y1="22" x2="38" y2="42" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#FF3B30"/>
                            <stop offset="1" stop-color="#D00E15"/>
                        </linearGradient>
                    </defs>
                </svg>
                <h3 class="font-serif font-bold text-[#111827] text-[1.5rem] leading-tight mb-2.5 tracking-tight" style="font-family: Georgia, 'Times New Roman', serif;">Members save up to 20%</h3>
                <p class="text-gray-500 text-[15px] mb-6 flex-1">Get instant discounts on hotels today.</p>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="inline-block mt-auto text-[14px] font-semibold text-[#4F46E5] hover:text-[#3730A3] underline decoration-[#4F46E5]/30 hover:decoration-[#3730A3] underline-offset-4 transition-colors">Join for free</a>
                @else
                    <a href="#" class="inline-block mt-auto text-[14px] font-semibold text-[#4F46E5] hover:text-[#3730A3] underline decoration-[#4F46E5]/30 hover:decoration-[#3730A3] underline-offset-4 transition-colors">Join for free</a>
                @endif
            </div>
            
            <!-- Card 2 -->
            <div class="group bg-white rounded-[20px] p-7 flex flex-col items-start border border-gray-100 shadow-[0_2px_15px_rgba(0,0,0,0.03)] hover:shadow-[0_10px_30px_rgba(0,0,0,0.08)] transition-all duration-400 hover:-translate-y-1 relative overflow-hidden">
                <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" class="drop-shadow-sm mb-5 group-hover:scale-110 transition-transform duration-500">
                    <circle cx="24" cy="12" r="5" fill="#FBBF24"/>
                    <circle cx="24" cy="12" r="3" stroke="#D97706" stroke-width="1.5"/>
                    <path d="M38 28C38 20.268 31.732 14 24 14C16.268 14 10 20.268 10 28C10 32 12 36 24 36C36 36 38 32 38 28Z" fill="url(#pig-grad)"/>
                    <ellipse cx="12" cy="28" rx="4" ry="5" fill="#FDA4AF"/>
                    <path d="M22 14L18 6L26 10" fill="url(#pig-grad)"/>
                    <rect x="16" y="34" width="4" height="6" rx="2" fill="#BE123C"/>
                    <rect x="28" y="34" width="4" height="6" rx="2" fill="#BE123C"/>
                    <rect x="22" y="14" width="4" height="2" rx="1" fill="#4C0519"/>
                    <circle cx="18" cy="24" r="2.5" fill="#1C1C1E"/>
                    <defs>
                        <linearGradient id="pig-grad" x1="10" y1="14" x2="38" y2="36" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#F43F5E"/>
                            <stop offset="1" stop-color="#E11D48"/>
                        </linearGradient>
                    </defs>
                </svg>
                <h3 class="font-serif font-bold text-[#111827] text-[1.5rem] leading-tight mb-2.5 tracking-tight" style="font-family: Georgia, 'Times New Roman', serif;">Choose how you save</h3>
                <p class="text-gray-500 text-[15px] mb-6 flex-1">Get an instant discount or turn it into future travel rewards.</p>
                <a href="#" class="inline-block mt-auto text-[14px] font-semibold text-[#4F46E5] hover:text-[#3730A3] underline decoration-[#4F46E5]/30 hover:decoration-[#3730A3] underline-offset-4 transition-colors">How it works</a>
            </div>
            
            <!-- Card 3 -->
            <div class="group bg-white rounded-[20px] p-7 flex flex-col items-start border border-gray-100 shadow-[0_2px_15px_rgba(0,0,0,0.03)] hover:shadow-[0_10px_30px_rgba(0,0,0,0.08)] transition-all duration-400 hover:-translate-y-1 relative overflow-hidden">
                <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" class="drop-shadow-sm mb-5 group-hover:scale-110 transition-transform duration-500">
                    <rect x="10" y="10" width="28" height="28" rx="6" fill="url(#box-grad)" stroke="#F43F5E" stroke-width="2.5"/>
                    <path d="M18 24L22 28L30 18" stroke="#1C1C1E" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <defs>
                        <linearGradient id="box-grad" x1="10" y1="10" x2="38" y2="38" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#A78BFA"/>
                            <stop offset="1" stop-color="#6366F1"/>
                        </linearGradient>
                    </defs>
                </svg>
                <h3 class="font-serif font-bold text-[#111827] text-[1.5rem] leading-tight mb-2.5 tracking-tight" style="font-family: Georgia, 'Times New Roman', serif;">Plans change. We get it.</h3>
                <p class="text-gray-500 text-[15px] mb-6 flex-1">Free cancellation on thousands of hotels.</p>
                <a href="{{ route('hotels.search') }}" class="inline-block mt-auto text-[14px] font-semibold text-[#4F46E5] hover:text-[#3730A3] underline decoration-[#4F46E5]/30 hover:decoration-[#3730A3] underline-offset-4 transition-colors">Book now</a>
            </div>
        </div>
    </div>

    <!-- Stay like a local -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-10 pt-6">
        <h2 class="font-serif font-bold text-[#111827] text-[1.5rem] md:text-2xl mb-5 tracking-tight" style="font-family: Georgia, 'Times New Roman', serif;">Stay like a local in Bangladesh</h2>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 md:gap-4">
            <a href="{{ route('hotels.search', ['type' => 'Resort']) }}" class="group relative rounded-[16px] overflow-hidden cursor-pointer block aspect-[3/4]">
                <img src="{{ asset('resort-card.webp') }}" alt="Resort" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition duration-700 ease-out">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent opacity-80 group-hover:opacity-100 transition-opacity duration-500"></div>
                <div class="absolute bottom-4 left-4 right-4">
                    <h3 class="text-white font-bold text-[15px] leading-tight drop-shadow-md">Resort</h3>
                </div>
            </a>
            
            <a href="{{ route('hotels.search', ['type' => 'Hotel']) }}" class="group relative rounded-[16px] overflow-hidden cursor-pointer block aspect-[3/4]">
                <img src="{{ asset('hotel-card.webp') }}" alt="Hotel" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition duration-700 ease-out">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent opacity-80 group-hover:opacity-100 transition-opacity duration-500"></div>
                <div class="absolute bottom-4 left-4 right-4">
                    <h3 class="text-white font-bold text-[15px] leading-tight drop-shadow-md">Hotel</h3>
                </div>
            </a>
            
            <a href="{{ route('hotels.search', ['type' => 'Apartment']) }}" class="group relative rounded-[16px] overflow-hidden cursor-pointer block aspect-[3/4]">
                <img src="{{ asset('apartment-card.webp') }}" alt="Apartment" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition duration-700 ease-out">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent opacity-80 group-hover:opacity-100 transition-opacity duration-500"></div>
                <div class="absolute bottom-4 left-4 right-4">
                    <h3 class="text-white font-bold text-[15px] leading-tight drop-shadow-md">Apartment</h3>
                </div>
            </a>
            
            <a href="{{ route('hotels.search', ['type' => 'House']) }}" class="group relative rounded-[16px] overflow-hidden cursor-pointer block aspect-[3/4]">
                <img src="https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=400&q=80" alt="House" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition duration-700 ease-out">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent opacity-80 group-hover:opacity-100 transition-opacity duration-500"></div>
                <div class="absolute bottom-4 left-4 right-4">
                    <h3 class="text-white font-bold text-[15px] leading-tight drop-shadow-md">House</h3>
                </div>
            </a>
            
            <a href="{{ route('hotels.search', ['type' => 'Houseboat']) }}" class="group relative rounded-[16px] overflow-hidden cursor-pointer block aspect-[3/4]">
                <img src="https://images.unsplash.com/photo-1542385150-1369ce681dcb?auto=format&fit=crop&w=400&q=80" alt="Houseboat" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition duration-700 ease-out">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent opacity-80 group-hover:opacity-100 transition-opacity duration-500"></div>
                <div class="absolute bottom-4 left-4 right-4">
                    <h3 class="text-white font-bold text-[15px] leading-tight drop-shadow-md">Houseboat</h3>
                </div>
            </a>
        </div>
    </div>

    <!-- Stays for every travel style -->
    @if(!empty($travelStyles))
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-16 pt-6" x-data="{ activeStyleTab: '{{ array_key_first($travelStyles) }}' }">
        <div class="mb-4">
            <h2 class="text-[#0a1128] font-bold text-2xl md:text-[28px] mb-1 font-poppins">Stays for every travel style</h2>
            <p class="text-gray-500 text-[15px]">Average prices based on current calendar month</p>
        </div>

        <!-- Tabs -->
        <div class="flex items-center gap-6 overflow-x-auto border-b border-gray-100 mb-6 hide-scrollbar relative">
            @foreach(array_keys($travelStyles) as $tab)
                <button type="button" @click="activeStyleTab = '{{ $tab }}'" 
                        :class="activeStyleTab === '{{ $tab }}' ? 'text-[#2563eb] font-bold border-[#2563eb]' : 'text-[#4A5568] border-transparent hover:text-gray-900 font-semibold'" 
                        class="pb-3 border-b-2 text-[15px] whitespace-nowrap transition-colors">{{ $tab }}</button>
            @endforeach
        </div>

        <!-- Carousel / Cards Container -->
        <div class="relative group">
            @foreach($travelStyles as $tabName => $properties)
            <div x-show="activeStyleTab === '{{ $tabName }}'" style="display: none;" x-transition.opacity.duration.300ms>
                <div class="flex gap-4 overflow-x-auto hide-scrollbar snap-x pb-4 scroll-smooth travelStyleContainer">
                    
                    @foreach($properties as $property)
                    <!-- Card -->
                    <a href="{{ route('hotels.show', $property->id) }}" class="block snap-start shrink-0 w-[260px] md:w-[280px] rounded-[12px] bg-white hover:shadow-md transition-shadow duration-300 group/card">
                        <div class="relative h-[180px] w-full rounded-[12px] overflow-hidden mb-3">
                            <img src="{{ $property->cover_photo_url }}" alt="{{ $property->name }}" class="w-full h-full object-cover group-hover/card:scale-105 transition-transform duration-700">
                            <div class="absolute top-3 left-3 bg-[#0a1128]/90 text-white text-[12px] font-bold px-2.5 py-1.5 rounded-[6px]">
                                {{ $tabName }} vibes
                            </div>
                        </div>
                        <div>
                            <h3 class="font-bold text-[#19100F] text-[16px] mb-0.5 leading-tight font-poppins truncate">{{ $property->name }}</h3>
                            <p class="text-gray-500 text-[13px] mb-3 font-medium truncate">{{ $property->city }}, {{ $property->country ?? 'Bangladesh' }}</p>
                            <div class="flex flex-col">
                                <span class="font-bold text-[#19100F] text-[17px] leading-none mb-0.5">BDT {{ number_format($property->lowest_price ?? 5000, 0) }}</span>
                                <span class="text-gray-500 text-[12px] font-medium">avg per night</span>
                            </div>
                        </div>
                    </a>
                    @endforeach

                </div>
            </div>
            @endforeach
            
            <!-- Scroll Button -->
            <button type="button" @click="document.querySelector('.travelStyleContainer:not([style*=\'display: none\'])').scrollBy({left: 300, behavior: 'smooth'})" class="absolute -right-4 top-[90px] -mt-5 bg-white w-10 h-10 rounded-full shadow-[0_2px_10px_rgba(0,0,0,0.1)] border border-gray-100 flex items-center justify-center text-[#2563eb] hover:bg-gray-50 transition z-10 hidden md:flex opacity-0 group-hover:opacity-100 duration-300" aria-label="Scroll right">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
            </button>
        </div>
    </div>
    @endif

    <!-- Trending destinations -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-10 pt-6">
        <div class="mb-5">
            <h2 class="font-serif font-bold text-[#111827] text-[1.5rem] md:text-2xl tracking-tight" style="font-family: Georgia, 'Times New Roman', serif;">Trending destinations</h2>
            <p class="text-gray-500 text-sm mt-1">Most popular choices for travelers from Bangladesh</p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 md:gap-4">
            @foreach([
                ['name' => "Cox's Bazar", 'image' => 'coxsbazar-card.webp'],
                ['name' => 'Chittagong', 'image' => 'chittagong-card.webp'],
                ['name' => 'Sreemangal', 'image' => 'sreemangal-card.webp'],
                ['name' => 'Dhaka', 'image' => 'dhaka-card.webp'],
                ['name' => 'Rangamati', 'image' => 'rangamati-card.webp'],
            ] as $destination)
                <a href="{{ route('hotels.search', ['destination' => $destination['name']]) }}" class="group relative rounded-[16px] overflow-hidden cursor-pointer block aspect-[3/4]">
                    <img src="{{ asset($destination['image']) }}" alt="{{ $destination['name'] }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition duration-700 ease-out">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent opacity-80 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="absolute bottom-4 left-4 right-4">
                        <h3 class="text-white font-bold text-[15px] leading-tight drop-shadow-md">{{ $destination['name'] }}</h3>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Top Airlines Showcase Section removed temporarily -->

    <!-- Features Section -->
    <!-- Features Section -->
    <div class="py-20 bg-gradient-to-b from-gray-50 to-white relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl md:text-4xl font-poppins font-extrabold text-[#19100F] mb-4 tracking-tight">Why Choose Us</h2>
                <p class="text-[17px] text-gray-600 font-medium">Discover carefully curated stays across Bangladesh with guaranteed best rates, authentic reviews, and dedicated local support.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white p-8 rounded-[24px] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 hover:shadow-[0_8px_30px_rgb(208,14,21,0.08)] hover:-translate-y-1 transition-all duration-300 group text-center relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-red-50 rounded-bl-[100px] -mr-8 -mt-8 transition-transform duration-500 group-hover:scale-110"></div>
                    <div class="w-16 h-16 mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 flex items-center justify-center text-[#d00e15] mb-6 relative z-10 transform group-hover:rotate-6 transition-transform duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold mb-3 text-[#19100F] relative z-10">Extensive Selection</h3>
                    <p class="text-gray-500 text-[14px] leading-relaxed relative z-10">Choose from thousands of properties, from luxury resorts to cozy local homestays across the country.</p>
                </div>
                <!-- Feature 2 -->
                <div class="bg-white p-8 rounded-[24px] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 hover:shadow-[0_8px_30px_rgb(208,14,21,0.08)] hover:-translate-y-1 transition-all duration-300 group text-center relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-red-50 rounded-bl-[100px] -mr-8 -mt-8 transition-transform duration-500 group-hover:scale-110"></div>
                    <div class="w-16 h-16 mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 flex items-center justify-center text-[#d00e15] mb-6 relative z-10 transform group-hover:-rotate-6 transition-transform duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold mb-3 text-[#19100F] relative z-10">Verified Reviews</h3>
                    <p class="text-gray-500 text-[14px] leading-relaxed relative z-10">Make informed decisions with authentic reviews and detailed ratings from verified guests.</p>
                </div>
                <!-- Feature 3 -->
                <div class="bg-white p-8 rounded-[24px] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 hover:shadow-[0_8px_30px_rgb(208,14,21,0.08)] hover:-translate-y-1 transition-all duration-300 group text-center relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-red-50 rounded-bl-[100px] -mr-8 -mt-8 transition-transform duration-500 group-hover:scale-110"></div>
                    <div class="w-16 h-16 mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 flex items-center justify-center text-[#d00e15] mb-6 relative z-10 transform group-hover:rotate-6 transition-transform duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold mb-3 text-[#19100F] relative z-10">Best Price Guarantee</h3>
                    <p class="text-gray-500 text-[14px] leading-relaxed relative z-10">Enjoy exclusive deals and our promise to always match the lowest hotel rates available online.</p>
                </div>
                <!-- Feature 4 -->
                <div class="bg-white p-8 rounded-[24px] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 hover:shadow-[0_8px_30px_rgb(208,14,21,0.08)] hover:-translate-y-1 transition-all duration-300 group text-center relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-red-50 rounded-bl-[100px] -mr-8 -mt-8 transition-transform duration-500 group-hover:scale-110"></div>
                    <div class="w-16 h-16 mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 flex items-center justify-center text-[#d00e15] mb-6 relative z-10 transform group-hover:-rotate-6 transition-transform duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold mb-3 text-[#19100F] relative z-10">Local Expertise</h3>
                    <p class="text-gray-500 text-[14px] leading-relaxed relative z-10">Our dedicated support team provides personalized recommendations and assistance whenever you need it.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer support callout -->
    <section class="bg-white px-4 py-14 md:py-20">
        <a href="{{ route('navbar.support') }}" class="mx-auto flex max-w-[480px] items-center gap-5 rounded-[28px] bg-[#f4f4f4] px-7 py-8 md:gap-7 md:px-10 hover:bg-[#eeeeee] transition-colors" aria-label="Open Help and Support">
            <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-full bg-[#d00e15] text-white shadow-sm">
                <svg class="h-12 w-12" viewBox="0 0 64 64" fill="none" aria-hidden="true">
                    <circle cx="32" cy="28" r="14" fill="#F5B77A"/>
                    <path d="M18 28a14 14 0 0 1 28 0v5h-5v-6a9 9 0 0 0-18 0v6h-5v-5Z" fill="currentColor"/>
                    <path d="M18 31h6v12h-3a6 6 0 0 1-6-6v-3a3 3 0 0 1 3-3Zm28 0h-6v12h3a6 6 0 0 0 6-6v-3a3 3 0 0 0-3-3Z" fill="#111827"/>
                    <path d="M23 47c3-4 15-4 18 0l5 9H18l5-9Z" fill="#111827"/>
                    <path d="M38 43c0 3-3 5-7 5" stroke="#111827" stroke-width="3" stroke-linecap="round"/>
                </svg>
            </div>
            <div>
                <h2 class="text-xl md:text-2xl font-bold text-[#111827] mb-1">We're here for you</h2>
                <p class="text-[15px] md:text-lg leading-snug text-gray-600">Customer support whenever you need us</p>
            </div>
        </a>
    </section>

    <!-- Footer -->
    <footer class="bg-[#f8f4ec] text-[#101a3b] rounded-t-[16px] font-sans">
        <div class="max-w-7xl mx-auto px-5 sm:px-8 lg:px-10 pt-12 md:pt-14 pb-8">
            <img src="{{ asset('images/logo-dark.png') }}" onerror="this.onerror=null; this.src='{{ asset('images/logo.png') }}';" alt="Bookdei" class="h-11 w-auto object-contain mb-12">

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-x-8 gap-y-10 mb-12">
                <div>
                    <h3 class="font-bold text-[15px] mb-5">Top destinations</h3>
                    <ul class="space-y-4 text-sm font-semibold underline underline-offset-2">
                        <li><a href="{{ route('hotels.search', ['destination' => "Cox's Bazar"]) }}" class="hover:text-[#d00e15]">Hotels in Cox's Bazar</a></li>
                        <li><a href="{{ route('hotels.search', ['destination' => 'Dhaka']) }}" class="hover:text-[#d00e15]">Hotels in Dhaka</a></li>
                        <li><a href="{{ route('hotels.search', ['destination' => 'Chittagong']) }}" class="hover:text-[#d00e15]">Hotels in Chittagong</a></li>
                        <li><a href="{{ route('hotels.search', ['destination' => 'Sreemangal']) }}" class="hover:text-[#d00e15]">Hotels in Sreemangal</a></li>
                        <li><a href="{{ route('hotels.search', ['destination' => 'Rangamati']) }}" class="hover:text-[#d00e15]">Hotels in Rangamati</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-bold text-[15px] mb-5">Support & FAQs</h3>
                    <ul class="space-y-4 text-sm font-semibold underline underline-offset-2">
                        <li><a href="{{ route('my-bookings.index') }}" class="hover:text-[#d00e15]">Your bookings</a></li>
                        <li><a href="{{ route('pages.help') }}" class="hover:text-[#d00e15]">FAQs</a></li>
                        <li><a href="{{ route('pages.contact') }}" class="hover:text-[#d00e15]">Contact us</a></li>
                        <li><a href="{{ route('pages.complaint') }}" class="hover:text-[#d00e15]">Submit a complaint</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-bold text-[15px] leading-tight mb-5">For suppliers, affiliates and partners</h3>
                    <ul class="space-y-4 text-sm font-semibold underline underline-offset-2">
                        <li><a href="{{ route('pages.affiliates') }}" class="hover:text-[#d00e15]">Affiliate with us</a></li>
                        <li><a href="{{ route('list-your-property') }}" class="hover:text-[#d00e15]">List your property</a></li>
                        <li><a href="{{ route('pages.travel-agencies') }}" class="hover:text-[#d00e15]">Travel agencies</a></li>
                        <li><a href="{{ route('pages.partner-portal') }}" class="hover:text-[#d00e15]">Partner portal</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-bold text-[15px] mb-5">Policies</h3>
                    <ul class="space-y-4 text-sm font-semibold underline underline-offset-2">
                        <li><a href="{{ route('pages.terms') }}" class="hover:text-[#d00e15]">Terms & Conditions</a></li>
                        <li><a href="{{ route('pages.privacy') }}" class="hover:text-[#d00e15]">Privacy</a></li>
                        <li><a href="{{ route('pages.cookies') }}" class="hover:text-[#d00e15]">Cookies</a></li>
                        <li><a href="{{ route('pages.cancellation') }}" class="hover:text-[#d00e15]">Cancellation policy</a></li>
                        <li><a href="{{ route('pages.trust-safety') }}" class="hover:text-[#d00e15]">Trust & safety</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-bold text-[15px] mb-5">Other information</h3>
                    <ul class="space-y-4 text-sm font-semibold underline underline-offset-2">
                        <li><a href="{{ route('pages.about') }}" class="hover:text-[#d00e15]">About us</a></li>
                        <li><a href="{{ route('pages.careers') }}" class="hover:text-[#d00e15]">Careers</a></li>
                        <li><a href="{{ route('pages.press') }}" class="hover:text-[#d00e15]">Press center</a></li>
                        <li><a href="{{ route('pages.corporate') }}" class="hover:text-[#d00e15]">Corporate travel</a></li>
                        <li><a href="{{ route('pages.sitemap') }}" class="hover:text-[#d00e15]">Sitemap</a></li>
                    </ul>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row items-center justify-between gap-7 pt-7 border-t border-[#101a3b]/10">
                <div class="w-full max-w-[700px] bg-white rounded-md p-2">
                    <img src="{{ asset('SSLCommerz-Pay-With-logo-All-Size-01.png') }}" onerror="this.onerror=null; this.src='/SSLCommerz-Pay-With-logo-All-Size-01.png';" alt="Accepted payment methods" class="w-full h-auto object-contain">
                </div>
                <p class="shrink-0 text-sm text-[#4b5563] text-center lg:text-right">Copyright &copy; {{ date('Y') }} <span class="text-[#d00e15]">Bookdei</span>. All rights reserved.</p>
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
