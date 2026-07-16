<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bookdei - OTA Platform</title>
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
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm {{ session('currency', 'BDT') === 'BDT' ? 'font-bold text-[#1882FF] bg-blue-50' : 'text-gray-700 hover:bg-gray-50' }}">
                                BDT (৳)
                            </button>
                        </form>
                        <form action="{{ route('currency.set') }}" method="POST" class="border-t border-gray-50">
                            @csrf
                            <input type="hidden" name="currency" value="USD">
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm {{ session('currency') === 'USD' ? 'font-bold text-[#1882FF] bg-blue-50' : 'text-gray-700 hover:bg-gray-50' }}">
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

                <a href="{{ route('pages.help') }}" class="booking-nav-link">Support</a>
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
    <div class="homepage-hero relative isolate overflow-hidden w-full bg-[#fffaf8]">
        <picture class="absolute inset-0 w-full h-full">
            <source media="(max-width: 767px)" srcset="{{ asset('hero-mobile.webp') }}" type="image/webp">
            <source media="(min-width: 768px)" srcset="{{ asset('hero-pc.webp') }}" type="image/webp">
            <img
                src="{{ asset('hero-pc.png') }}"
                alt="Travel hero background"
                class="homepage-hero-image w-full h-full object-cover"
                loading="eager"
                fetchpriority="high"
                decoding="async"
            >
        </picture>
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
                <button type="button" @click="tab = 'visa'; $dispatch('tab-changed', { tab: 'visa' })" :class="tab === 'visa' ? 'is-active' : ''" class="travel-search-tab" role="tab" :aria-selected="tab === 'visa'">
                    <span class="travel-tab-art travel-tab-art-visa" aria-hidden="true"></span>
                    <span>Visa</span>
                </button>
                <button type="button" @click="tab = 'flights'; $dispatch('tab-changed', { tab: 'flights' })" :class="tab === 'flights' ? 'is-active' : ''" class="travel-search-tab" role="tab" :aria-selected="tab === 'flights'">
                    <span class="travel-tab-art travel-tab-art-flight" aria-hidden="true"></span>
                    <span>Flight</span>
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

    <!-- Bookdei Service Banners (Hidden for now) -->
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
                    <h3 class="font-bold text-[#19100F] text-sm leading-tight mb-2 truncate" title="{{ $search['property_name'] ?: $search['destination'] }}">
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
            @if(isset($offers) && count($offers) == 1)
            <!-- Add a fallback dummy offer to fill the second column so it doesn't look empty -->
            <div class="flex flex-col sm:flex-row bg-white rounded-xl border border-gray-200 hover:shadow-md transition duration-300 p-4 gap-4 items-center">
                <div class="flex-1 flex flex-col justify-center">
                    <div class="text-xs text-gray-500 font-medium mb-1">Escape for less with our Getaway Deals</div>
                    <h3 class="font-bold text-[#19100F] text-xl leading-tight mb-2">No catch. Just getaways.</h3>
                    <p class="text-sm text-gray-600 line-clamp-2 mb-4">At least 15% off select stays worldwide &ndash; just book and go.</p>
                    <div>
                        <a href="#" class="inline-block bg-[#006ce4] hover:bg-[#0057b8] text-white text-sm font-semibold px-4 py-2 rounded transition">
                            Save with a Getaway Deal
                        </a>
                    </div>
                </div>
                <div class="w-full shrink-0 relative" style="max-width: 130px; height: 130px;">
                    <img src="https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=400&q=80" alt="Getaway Deals" class="w-full h-full object-cover rounded-lg shadow-sm">
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Browse by property type -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-10 pt-6">
        <h2 class="text-2xl font-poppins font-bold text-[#19100F] mb-4">Browse by property type</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 lg:gap-6">
            <a href="{{ route('hotels.search', ['type' => 'Resort']) }}" class="group block cursor-pointer">
                <div class="rounded-xl overflow-hidden mb-3" style="aspect-ratio: 4/3;">
                    <img src="{{ asset('resort-card.webp') }}" alt="Resort" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-6 h-6 text-[#006e5b]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 104 0 2 2 0 012-2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="font-bold text-[#19100F] text-lg">Resort</span>
                </div>
            </a>
            <a href="{{ route('hotels.search', ['type' => 'Hotel']) }}" class="group block cursor-pointer">
                <div class="rounded-xl overflow-hidden mb-3" style="aspect-ratio: 4/3;">
                    <img src="{{ asset('hotel-card.webp') }}" alt="Hotel" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-6 h-6 text-[#006e5b]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    <span class="font-bold text-[#19100F] text-lg">Hotel</span>
                </div>
            </a>
            <a href="{{ route('hotels.search', ['type' => 'Apartment']) }}" class="group block cursor-pointer">
                <div class="rounded-xl overflow-hidden mb-3" style="aspect-ratio: 4/3;">
                    <img src="{{ asset('apartment-card.webp') }}" alt="Apartment" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-6 h-6 text-[#006e5b]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span class="font-bold text-[#19100F] text-lg">Apartment</span>
                </div>
            </a>
        </div>
    </div>

    <!-- Trending destinations -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="mb-5">
            <h2 class="text-2xl font-poppins font-bold text-[#19100F]">Trending destinations</h2>
            <p class="text-gray-500 text-sm mt-1">Most popular choices for travelers from Bangladesh</p>
        </div>
        
        <div class="flex flex-col gap-4">
            <!-- Top row: 2 items -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="{{ route('hotels.search', ['destination' => 'Cox\'s Bazar']) }}" class="group relative rounded-[2rem] overflow-hidden cursor-pointer block" style="aspect-ratio: 2/1;">
                    <img src="{{ asset('coxsbazar-card.webp') }}" alt="Cox's Bazar" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition duration-1000 ease-in-out">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent opacity-80 group-hover:opacity-100 transition-opacity duration-500"></div>
                    
                    <div class="absolute bottom-6 left-6 right-6 flex items-center gap-4">
                        <div class="h-[2px] w-0 bg-white group-hover:w-12 transition-all duration-500 ease-out"></div>
                        <h3 class="text-white font-['Space_Grotesk'] font-bold text-2xl md:text-4xl tracking-wide group-hover:tracking-widest transition-all duration-500 ease-out">COX'S BAZAR</h3>
                    </div>
                </a>
                <a href="{{ route('hotels.search', ['destination' => 'Chittagong']) }}" class="group relative rounded-[2rem] overflow-hidden cursor-pointer block" style="aspect-ratio: 2/1;">
                    <img src="{{ asset('chittagong-card.webp') }}" alt="Chittagong" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition duration-1000 ease-in-out">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent opacity-80 group-hover:opacity-100 transition-opacity duration-500"></div>
                    
                    <div class="absolute bottom-6 left-6 right-6 flex items-center gap-4">
                        <div class="h-[2px] w-0 bg-white group-hover:w-12 transition-all duration-500 ease-out"></div>
                        <h3 class="text-white font-['Space_Grotesk'] font-bold text-2xl md:text-4xl tracking-wide group-hover:tracking-widest transition-all duration-500 ease-out">CHITTAGONG</h3>
                    </div>
                </a>
            </div>
            <!-- Bottom row: 3 items -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('hotels.search', ['destination' => 'Sreemangal']) }}" class="group relative rounded-[2rem] overflow-hidden cursor-pointer block" style="aspect-ratio: 3/2;">
                    <img src="{{ asset('sreemangal-card.webp') }}" alt="Sreemangal" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition duration-1000 ease-in-out">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent opacity-80 group-hover:opacity-100 transition-opacity duration-500"></div>
                    
                    <div class="absolute bottom-6 left-6 right-6 flex items-center gap-3">
                        <div class="h-[2px] w-0 bg-white group-hover:w-8 transition-all duration-500 ease-out"></div>
                        <h3 class="text-white font-['Space_Grotesk'] font-bold text-xl md:text-3xl tracking-wide group-hover:tracking-widest transition-all duration-500 ease-out">SREEMANGAL</h3>
                    </div>
                </a>
                <a href="{{ route('hotels.search', ['destination' => 'Dhaka']) }}" class="group relative rounded-[2rem] overflow-hidden cursor-pointer block" style="aspect-ratio: 3/2;">
                    <img src="{{ asset('dhaka-card.webp') }}" alt="Dhaka" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition duration-1000 ease-in-out">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent opacity-80 group-hover:opacity-100 transition-opacity duration-500"></div>
                    
                    <div class="absolute bottom-6 left-6 right-6 flex items-center gap-3">
                        <div class="h-[2px] w-0 bg-white group-hover:w-8 transition-all duration-500 ease-out"></div>
                        <h3 class="text-white font-['Space_Grotesk'] font-bold text-xl md:text-3xl tracking-wide group-hover:tracking-widest transition-all duration-500 ease-out">DHAKA</h3>
                    </div>
                </a>
                <a href="{{ route('hotels.search', ['destination' => 'Rangamati']) }}" class="group relative rounded-[2rem] overflow-hidden cursor-pointer block" style="aspect-ratio: 3/2;">
                    <img src="{{ asset('rangamati-card.webp') }}" alt="Rangamati" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition duration-1000 ease-in-out">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/10 to-transparent opacity-80 group-hover:opacity-100 transition-opacity duration-500"></div>
                    
                    <div class="absolute bottom-6 left-6 right-6 flex items-center gap-3">
                        <div class="h-[2px] w-0 bg-white group-hover:w-8 transition-all duration-500 ease-out"></div>
                        <h3 class="text-white font-['Space_Grotesk'] font-bold text-xl md:text-3xl tracking-wide group-hover:tracking-widest transition-all duration-500 ease-out">RANGAMATI</h3>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Homes guests love -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="flex justify-between items-end mb-5">
            <div>
                <h2 class="text-2xl font-poppins font-bold text-[#19100F]">Homes guests love</h2>
                <p class="text-gray-500 text-sm mt-1">Top rated properties from real guests</p>
            </div>
            <a href="{{ route('hotels.search') }}" class="text-[#006ce4] hover:text-[#0057b8] text-sm font-semibold hidden sm:block">Discover homes</a>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            @forelse($topProperties as $property)
            <a href="{{ route('hotels.show', $property) }}" class="group block border border-gray-100 rounded-xl overflow-hidden hover:shadow-md transition duration-300 relative bg-white flex flex-col h-full">
                <div class="aspect-[4/3] w-full overflow-hidden relative shrink-0">
                    <img src="{{ $property->cover_photo_url }}" alt="{{ $property->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    <div class="absolute top-3 right-3 bg-white rounded-full p-1.5 text-gray-400 hover:text-[#d00e15] shadow-sm cursor-pointer transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    </div>
                </div>
                <div class="p-4 flex flex-col flex-1">
                    <h3 class="font-bold text-[#19100F] text-base mb-1 truncate">{{ $property->name }}</h3>
                    <p class="text-xs text-gray-500 mb-2 truncate">{{ $property->city }}{{ $property->country ? ', ' . $property->country : '' }}</p>
                    
                    @if($property->average_rating)
                    <div class="flex items-center gap-2 mb-3 mt-auto">
                        <div class="bg-[#003b95] text-white text-xs font-bold px-1.5 py-0.5 rounded flex items-center justify-center min-w-[1.75rem] h-6">{{ number_format($property->average_rating, 1) }}</div>
                        <div class="text-xs text-[#19100F] font-medium">{{ $property->average_rating >= 9 ? 'Exceptional' : ($property->average_rating >= 8 ? 'Excellent' : ($property->average_rating >= 7 ? 'Very Good' : 'Good')) }}</div>
                        <div class="text-xs text-gray-500">{{ $property->reviews_count }} {{ Str::plural('review', $property->reviews_count) }}</div>
                    </div>
                    @else
                    <div class="flex items-center gap-2 mb-3 mt-auto">
                        <div class="text-xs text-gray-500 italic">No reviews yet</div>
                    </div>
                    @endif
                    
                    <div class="text-right mt-2 border-t border-gray-50 pt-2">
                        <span class="text-xs text-gray-500">Starting from</span>
                        <span class="font-bold text-[#19100F] text-base ml-1">{{ \App\Helpers\Currency::format($property->lowest_price ?? 0) }}</span>
                    </div>
                </div>
            </a>
            @empty
            <div class="col-span-full text-center py-8 text-gray-500">No properties available yet.</div>
            @endforelse
        </div>
        <a href="{{ route('hotels.search') }}" class="text-[#006ce4] hover:text-[#0057b8] text-sm font-semibold block sm:hidden mt-4 text-center">Discover homes</a>
    </div>

    <!-- Top Airlines Showcase Section -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="bg-white rounded-[28px] shadow-[0_4px_30px_rgba(15,23,42,0.04)] border border-gray-50 p-8 md:p-12">
            <div class="text-center max-w-3xl mx-auto mb-10">
                <h2 class="text-2xl md:text-[28px] font-poppins font-bold text-[#19100F] mb-4 tracking-tight">Search Top Airlines</h2>
                <p class="text-sm text-gray-500 leading-relaxed font-medium">
                    Bookdei's user-friendly platform, powered by Bookdei technology, connects you to top airlines instantly. Enjoy a comfortable and hassle-free journey on any destination and get tickets of top airlines easily.
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
                <p class="text-sm text-gray-500">Powered exclusively by Bookdei technology - global content, real-time pricing, and seamless booking.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Hotel Feature 1 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition duration-300 group text-center">
                    <div class="w-12 h-12 mx-auto text-[#006ce4] mb-4 transform group-hover:scale-110 transition duration-300">
                        <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <h3 class="text-base font-bold mb-2 text-[#19100F]">Extensive Selection</h3>
                    <p class="text-gray-500 text-xs leading-relaxed">Choose from over a million properties worldwide, from luxury resorts to cozy local stays.</p>
                </div>
                <!-- Hotel Feature 2 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition duration-300 group text-center">
                    <div class="w-12 h-12 mx-auto text-[#006ce4] mb-4 transform group-hover:scale-110 transition duration-300">
                        <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                    </div>
                    <h3 class="text-base font-bold mb-2 text-[#19100F]">Verified Reviews</h3>
                    <p class="text-gray-500 text-xs leading-relaxed">Make informed decisions with authentic reviews and detailed ratings from real travelers.</p>
                </div>
                <!-- Hotel Feature 3 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition duration-300 group text-center">
                    <div class="w-12 h-12 mx-auto text-[#006ce4] mb-4 transform group-hover:scale-110 transition duration-300">
                        <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-base font-bold mb-2 text-[#19100F]">Best Price Guarantee</h3>
                    <p class="text-gray-500 text-xs leading-relaxed">Enjoy exclusive deals and our promise to always match the lowest hotel rates available online.</p>
                </div>
                <!-- Flight Feature -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition duration-300 group text-center">
                    <div class="w-12 h-12 mx-auto text-[#006ce4] mb-4 transform group-hover:scale-110 transition duration-300">
                        <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 104 0 2 2 0 012-2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-base font-bold mb-2 text-[#19100F]">Global Flight Content</h3>
                    <p class="text-gray-500 text-xs leading-relaxed">Access worldwide airline inventory and seamlessly compare the best fares for your destination instantly.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Trust & Payment Section -->
    <div class="border-t border-gray-100 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-12">
            <!-- Payment Methods -->
            <div class="mb-10 pb-8 border-b border-gray-100/60 flex justify-center">
                <img src="{{ asset('SSLCommerz-Pay-With-logo-All-Size-01.png') }}" 
                     onerror="this.onerror=null; this.src='/SSLCommerz-Pay-With-logo-All-Size-01.png';"
                     alt="Payment Methods" 
                     class="w-full h-auto object-contain px-4">
            </div>

            <!-- Trust Badges Removed -->
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-[#0B1120] text-gray-300 py-16 border-t border-gray-800 font-sans" style="background-color: #0B1120;">
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
                        <a href="#" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-[#006ce4] hover:text-white transition-colors duration-300">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-[#006ce4] hover:text-white transition-colors duration-300">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-[#006ce4] hover:text-white transition-colors duration-300">
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
                        <li><a href="{{ route('list-your-property') }}" class="text-sm hover:text-white transition-colors text-[#006ce4] font-medium">List your Property</a></li>
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
