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
    <link rel="preload" as="image" href="{{ asset('hero-pc.webp') }}" type="image/webp" media="(min-width: 768px)">
    <link rel="preload" as="image" href="{{ asset('hero-mobile.webp') }}" type="image/webp" media="(max-width: 767px)">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-white text-dark selection:bg-[#d00e15] selection:text-white">
    <!-- Navigation -->
    <nav class="booking-nav fixed top-0 inset-x-0 z-50">
        <div class="booking-nav-inner">
            <a href="{{ url('/') }}" class="booking-nav-logo" aria-label="GHURI home">
                GHURI<span>.</span>
            </a>
            <div class="booking-nav-actions">
                <button type="button" class="booking-nav-currency" aria-label="Select currency">BDT</button>
                <button type="button" class="booking-nav-icon-button" aria-label="Select language" title="Language">
                    <span class="booking-nav-flag" aria-hidden="true"></span>
                </button>
                <button type="button" class="booking-nav-icon-button booking-nav-help" aria-label="Help and support" title="Help and support">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke-width="1.8"/><path d="M9.7 9a2.45 2.45 0 1 1 3.15 2.35c-.7.25-.85.75-.85 1.65" stroke-width="1.8" stroke-linecap="round"/><circle cx="12" cy="16.8" r=".8" fill="currentColor" stroke="none"/></svg>
                </button>

                {{-- ✦ List your Property Link — shown when Hotel tab is active --}}
                <a
                    href="{{ route('list-your-property') }}"
                    id="nav-list-property-link"
                    class="booking-nav-property hidden items-center"
                >
                    List your property
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
    <div class="relative isolate overflow-hidden w-full bg-[#fafafa] h-[550px] md:h-auto md:aspect-[21/9] md:min-h-[550px] lg:max-h-[750px]">
        <picture class="absolute inset-0 w-full h-full">
            <source media="(max-width: 767px)" srcset="{{ asset('hero-mobile.webp') }}" type="image/webp">
            <source media="(min-width: 768px)" srcset="{{ asset('hero-pc.webp') }}" type="image/webp">
            <img
                src="{{ asset('hero-pc.png') }}"
                alt="Travel hero background"
                class="w-full h-full object-cover object-bottom md:object-center"
                loading="eager"
                fetchpriority="high"
                decoding="async"
            >
        </picture>
    </div>

    <!-- Search Component Container -->
    <div class="travel-search-wrap -mt-16 md:-mt-24 lg:-mt-28 mb-10 md:mb-14 relative z-40">
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
                <button type="button" @click="tab = 'flights'; $dispatch('tab-changed', { tab: 'flights' })" :class="tab === 'flights' ? 'is-active' : ''" class="travel-search-tab" role="tab" :aria-selected="tab === 'flights'">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21 16v-2l-8-5V3.5a1.5 1.5 0 0 0-3 0V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5L21 16Z" stroke-width="1.8" stroke-linejoin="round"/></svg>
                    <span>Flight</span>
                </button>
                <button type="button" @click="tab = 'hotels'; $dispatch('tab-changed', { tab: 'hotels' })" :class="tab === 'hotels' ? 'is-active' : ''" class="travel-search-tab" role="tab" :aria-selected="tab === 'hotels'">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 21V6h7v15M11 10h9v11M2 21h20M7 9h1m-1 4h1m-1 4h1m7-4h1m-1 4h1" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>Hotel</span>
                </button>
                <button type="button" @click="tab = 'tours'; $dispatch('tab-changed', { tab: 'tours' })" :class="tab === 'tours' ? 'is-active' : ''" class="travel-search-tab" role="tab" :aria-selected="tab === 'tours'">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 20V9m16 11V9M2 20h20M3 9h18M6 9c0-3.3 2.7-6 6-6s6 2.7 6 6M8 20v-5h8v5M12 3v6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>Tour</span>
                </button>
                <button type="button" @click="tab = 'visa'; $dispatch('tab-changed', { tab: 'visa' })" :class="tab === 'visa' ? 'is-active' : ''" class="travel-search-tab" role="tab" :aria-selected="tab === 'visa'">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M5 3h14v18H5zM8 7h8M8 17h8M12 9a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <span>Visa</span>
                </button>
            </div>

            <!-- Hotel Search Form -->
            <div x-show="tab === 'hotels'" x-transition.opacity>
                <form action="{{ route('hotels.search') }}" method="GET" class="travel-hotel-form">
                    <div class="travel-search-main-row">
                        <label class="travel-field travel-destination-field">
                            <span class="travel-field-label">Destination/Property Name</span>
                            <span class="travel-destination-input">
                                <span class="travel-destination-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z" stroke-width="1.8"/><circle cx="12" cy="10" r="2.7" stroke-width="1.8"/></svg>
                                </span>
                                <input type="text" name="destination" placeholder="Where would you like to stay?" required autocomplete="off">
                                <span class="travel-destination-type">Hotels</span>
                            </span>
                        </label>

                        <div class="travel-field travel-date-field">
                            <div class="travel-date-half">
                                <button type="button" class="travel-date-trigger" @click="$refs.checkInPicker.showPicker ? $refs.checkInPicker.showPicker() : $refs.checkInPicker.click()" aria-label="Choose check-in date">
                                    <span class="travel-field-label">Check-in</span>
                                    <span class="travel-date-value">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 3v13m0 0-4-4m4 4 4-4M5 9V5h14v4M5 19v2h14v-2" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        <strong x-text="formatDate(checkIn)"></strong>
                                    </span>
                                    <span class="travel-weekday" x-text="weekday(checkIn)"></span>
                                </button>
                                <input x-ref="checkInPicker" type="date" name="check_in" x-model="checkIn" @change="updateCheckIn()" min="{{ now()->toDateString() }}" required aria-label="Check-in date" tabindex="-1">
                            </div>

                            <span class="travel-night-pill" x-text="nights() + (nights() === 1 ? ' Night' : ' Nights')"></span>

                            <div class="travel-date-half">
                                <button type="button" class="travel-date-trigger" @click="$refs.checkOutPicker.showPicker ? $refs.checkOutPicker.showPicker() : $refs.checkOutPicker.click()" aria-label="Choose check-out date">
                                    <span class="travel-field-label">Check-out</span>
                                    <span class="travel-date-value">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 17V4m0 0-4 4m4-4 4 4M5 15v4h14v-4" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        <strong x-text="formatDate(checkOut)"></strong>
                                    </span>
                                    <span class="travel-weekday" x-text="weekday(checkOut)"></span>
                                </button>
                                <input x-ref="checkOutPicker" type="date" name="check_out" x-model="checkOut" :min="checkIn" required aria-label="Check-out date" tabindex="-1">
                            </div>
                        </div>

                        <div class="travel-field travel-guests-field" @click.away="guestOpen = false">
                            <button type="button" @click="guestOpen = !guestOpen" :aria-expanded="guestOpen" aria-haspopup="dialog">
                                <span class="travel-field-label">Rooms and Guests</span>
                                <strong x-text="rooms + (rooms === 1 ? ' Room' : ' Rooms') + ', ' + adults + (adults === 1 ? ' Adult' : ' Adults') + ', ' + children + (children === 1 ? ' Child' : ' Children')"></strong>
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

                        <button type="submit" class="travel-search-submit">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="11" cy="11" r="7" stroke-width="2"/><path d="m16 16 5 5" stroke-width="2" stroke-linecap="round"/></svg>
                            <span>Search</span>
                        </button>
                    </div>

                    <div class="travel-search-filter-row">
                        <label class="travel-filter-check">
                            <input type="checkbox" name="free_cancellation" value="1">
                            <span>Free cancellation</span>
                        </label>
                        <label class="travel-star-filter">
                            <select name="stars" aria-label="Hotel star rating">
                                <option value="">Hotel Star Rating</option>
                                <option value="5">5 Star</option>
                                <option value="4">4 Star</option>
                                <option value="3">3 Star</option>
                                <option value="2">2 Star</option>
                                <option value="1">1 Star</option>
                            </select>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m7 10 5 5 5-5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </label>
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

    <!-- GHURI Service Banners (Hidden for now) -->
    <!--
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-16 pt-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            ... content hidden ...
        </div>
    </div>
    -->

    <!-- Recent Searches Section -->
    @if(isset($recentSearches) && count($recentSearches) > 0)
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-8 pt-4">
        <div class="mb-4">
            <h2 class="text-2xl font-poppins font-bold text-[#19100F]">Your recent searches</h2>
        </div>
        <div class="flex gap-4 overflow-x-auto pb-4 hide-scrollbar snap-x">
            @foreach($recentSearches as $search)
            @php
                $cin = \Carbon\Carbon::parse($search['check_in']);
                $cout = \Carbon\Carbon::parse($search['check_out']);
            @endphp
            <a href="{{ route('hotels.search', ['destination' => $search['destination'], 'check_in' => $search['check_in'], 'check_out' => $search['check_out'], 'guests' => $search['guests'], 'rooms' => $search['rooms']]) }}" 
               class="flex flex-row bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition duration-300 shrink-0 snap-start"
               style="width: 360px; height: 120px; max-height: 120px;">
                <div class="shrink-0 relative h-full" style="width: 140px;">
                    <img src="{{ $search['image_url'] }}" alt="{{ $search['destination'] }}" class="w-full h-full object-cover">
                    <div class="absolute top-2 left-2 bg-white px-1.5 py-1 rounded shadow-sm flex items-center justify-center">
                        <svg class="w-4 h-4 text-[#006e5b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 14V7a2 2 0 012-2h4a2 2 0 012 2v7m4-7a2 2 0 012-2h4a2 2 0 012 2v7M4 14h16v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4z"></path>
                        </svg>
                    </div>
                </div>
                <div class="p-3.5 flex flex-col justify-center flex-1 min-w-0 h-full overflow-hidden">
                    <div class="text-xs text-gray-500 mb-0.5 truncate">{{ $search['destination'] }}</div>
                    <h3 class="font-bold text-[#19100F] text-[15px] leading-tight mb-2 truncate" title="{{ $search['property_name'] ?: $search['destination'] }}">
                        {{ $search['property_name'] ?: $search['destination'] }}
                    </h3>
                    
                    <div class="text-xs text-gray-600 flex items-center gap-2 mb-1.5">
                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span class="truncate">{{ $cin->format('D, M j') }} &ndash; {{ $cout->format('D, M j') }}</span>
                    </div>
                    <div class="text-xs text-gray-600 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <span class="truncate">{{ $search['guests'] }} {{ Str::plural('guest', $search['guests']) }}, {{ $search['rooms'] }} {{ Str::plural('room', $search['rooms']) }}</span>
                    </div>
                </div>
            </a>
            @endforeach
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
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="mb-6">
            <h2 class="text-2xl font-poppins font-bold text-[#19100F]">Offers</h2>
            <p class="text-gray-500 text-sm mt-1">Promotions, deals, and special offers for you</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse(isset($offers) ? $offers : [] as $offer)
            <div class="flex flex-col sm:flex-row bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition duration-300 p-4 gap-4 items-center">
                <div class="flex-1 flex flex-col justify-center">
                    <div class="text-xs text-gray-500 font-medium mb-1">{{ $offer->property ? $offer->property->name : 'Special Offer' }}</div>
                    <h3 class="font-bold text-[#19100F] text-xl leading-tight mb-2">{{ $offer->title }}</h3>
                    <p class="text-sm text-gray-600 line-clamp-2 mb-4">{{ $offer->description }}</p>
                    <div>
                        <span class="inline-block bg-[#006ce4] hover:bg-[#0057b8] text-white text-sm font-semibold px-4 py-2 rounded transition">
                            Save with {{ $offer->code ?: 'a Deal' }}
                        </span>
                    </div>
                </div>
                <div class="w-full shrink-0 relative" style="max-width: 130px; height: 130px;">
                    <img src="{{ $offer->image_url }}" alt="{{ $offer->title }}" class="w-full h-full object-cover rounded-lg">
                </div>
            </div>
            @empty
            <div class="col-span-1 md:col-span-2">
                <div class="flex flex-col sm:flex-row bg-white rounded-xl border border-gray-200 hover:shadow-md transition duration-300 p-5 gap-6 items-center">
                    <div class="flex-1">
                        <div class="text-xs text-gray-500 font-medium mb-1">Escape for less with our Getaway Deals</div>
                        <h3 class="font-bold text-[#19100F] text-xl leading-tight mb-2">No catch. Just getaways.</h3>
                        <p class="text-sm text-gray-600 mb-4">At least 15% off select stays worldwide &ndash; just book and go.</p>
                        <a href="#" class="inline-block bg-[#006ce4] hover:bg-[#0057b8] text-white text-sm font-semibold px-4 py-2 rounded transition">
                            Save with a Getaway Deal
                        </a>
                    </div>
                    <div class="w-full shrink-0" style="max-width: 140px; height: 140px;">
                        <img src="https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=400&q=80" alt="Getaway Deals" class="w-full h-full object-cover rounded-lg shadow-sm">
                    </div>
                </div>
            </div>
            @endforelse
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
