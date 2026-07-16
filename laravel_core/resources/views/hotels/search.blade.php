<!DOCTYPE html>
<html lang="en-BD">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Hotels in Bangladesh | Compare Stays on Bookdei</title>
    <meta name="description" content="Search hotels, resorts, apartments, and guesthouses across Bangladesh. Compare available stays and book securely with Bookdei.">
    <meta name="robots" content="index, follow, max-image-preview:large">
    <link rel="canonical" href="{{ route('hotels.search') }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Bookdei">
    <meta property="og:title" content="Hotels in Bangladesh | Compare Stays on Bookdei">
    <meta property="og:description" content="Find and compare hotels, resorts, apartments, and guesthouses across Bangladesh.">
    <meta property="og:url" content="{{ route('hotels.search') }}">
    <meta property="og:image" content="{{ asset('hero-pc.webp') }}">
    <meta name="description" content="Find and book the perfect hotel. Compare prices, read reviews, and reserve rooms at the best rates.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:ital,wght@0,400;0,500;0,600;0,700;1,400&family=JetBrains+Mono:wght@400;500;600&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        /* Custom responsive classes for hPanel (no npm run build required) */
        @media (min-width: 768px) {
            .pc-card { padding: 0.75rem !important; }
            .pc-img-wrap { width: 260px !important; height: auto !important; border-radius: 0.5rem !important; }
            .pc-details { min-width: 250px !important; padding: 1rem !important; padding-left: 1.5rem !important; }
            .pc-pricing { width: 240px !important; padding: 1rem !important; border-top-width: 0 !important; border-left-width: 1px !important; }
        }
        @media (min-width: 1024px) {
            .pc-sidebar { width: 280px !important; }
        }
    </style>
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-900">
    <div class="min-h-screen">
        
        {{-- Header with Search --}}
        <header class="sticky top-0 z-40 border-b border-white/70 bg-white/75 backdrop-blur-xl shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-20">
                    {{-- Logo --}}
                    <a href="/" class="flex items-center gap-2">
                        <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center shadow-lg">
                            <i class="fas fa-plane-departure text-white text-lg"></i>
                        </div>
                        <span class="font-heading font-extrabold text-slate-900 text-xl tracking-tight">Bookdei</span>
                    </a>

                    {{-- Compact Search --}}
                    <form action="{{ route('hotels.search') }}" method="GET" class="hidden md:flex items-center gap-2 bg-white/80 rounded-full p-1.5 flex-1 max-w-3xl mx-8 border border-white/70 shadow-md backdrop-blur-md">
                        <div class="flex items-center flex-1 px-4 border-r border-slate-200">
                            <i class="fas fa-search text-[#1882FF] text-sm mr-3"></i>
                            <input type="text" name="destination" id="search-destination" value="{{ request('destination') }}" placeholder="Where are you going?" class="bg-transparent border-0 focus:ring-0 text-sm font-semibold flex-1 text-[#1a2b49] placeholder:text-slate-400 placeholder:font-medium p-0">
                        </div>
                        <div class="flex items-center px-4 border-r border-slate-200">
                            <i class="fas fa-calendar-alt text-[#1882FF] text-sm mr-3"></i>
                            <input type="date" name="check_in" value="{{ request('check_in', now()->addDay()->format('Y-m-d')) }}" class="bg-transparent border-0 focus:ring-0 text-sm font-semibold w-32 text-[#1a2b49] p-0 cursor-pointer">
                        </div>
                        <div class="flex items-center px-4">
                            <i class="fas fa-calendar-check text-[#1882FF] text-sm mr-3"></i>
                            <input type="date" name="check_out" value="{{ request('check_out', now()->addDays(2)->format('Y-m-d')) }}" class="bg-transparent border-0 focus:ring-0 text-sm font-semibold w-32 text-[#1a2b49] p-0 cursor-pointer">
                        </div>
                        <button type="submit" class="bg-[#1882FF] hover:bg-blue-700 text-white rounded-full px-6 py-2.5 font-bold text-sm transition shadow-md hover:shadow-lg flex items-center gap-2">
                            Search
                        </button>
                    </form>

                    {{-- Auth Links --}}
                    <div class="flex items-center gap-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-[#1a2b49] hover:text-[#1882FF] transition">Dashboard</a>
                            <a href="{{ route('dashboard') }}" class="w-10 h-10 rounded-full bg-[#1882FF] flex items-center justify-center text-white text-sm font-bold shadow-md hover:shadow-lg transition">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-semibold text-[#1a2b49] hover:text-[#1882FF] transition">Sign In</a>
                            <a href="{{ route('register') }}" class="bg-[#1a2b49] hover:bg-[#24385d] text-white rounded-full px-5 py-2 font-semibold text-sm transition shadow-md">Register</a>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-wrap gap-8">
                
                {{-- Left Sidebar Filters --}}
                <aside class="w-full shrink-0 pc-sidebar">
                    <form action="{{ route('hotels.search') }}" method="GET" class="sticky top-28 space-y-6">
                        <input type="hidden" name="destination" value="{{ request('destination') }}">
                        <input type="hidden" name="check_in" value="{{ request('check_in') }}">
                        <input type="hidden" name="check_out" value="{{ request('check_out') }}">

                        {{-- Main Filter Block --}}
                        <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-6">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="font-bold text-[#1a2b49] text-lg">Filters</h3>
                                <a href="{{ route('hotels.search', ['destination' => request('destination')]) }}" class="text-xs font-semibold text-[#1882FF] hover:text-blue-800 transition">Reset</a>
                            </div>

                            <div class="space-y-6">
                                {{-- Price per Night --}}
                                <div class="border-b border-slate-100 pb-5">
                                    <label class="block font-bold text-[#1a2b49] text-sm mb-3">Price per Night</label>
                                    <div class="flex items-center gap-3">
                                        <div class="relative flex-1">
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-medium">৳</span>
                                            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="w-full pl-6 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-[#1882FF] focus:border-[#1882FF] transition">
                                        </div>
                                        <span class="text-slate-400">-</span>
                                        <div class="relative flex-1">
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-medium">৳</span>
                                            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" class="w-full pl-6 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-[#1882FF] focus:border-[#1882FF] transition">
                                        </div>
                                    </div>
                                </div>

                                {{-- Star Rating --}}
                                <div class="border-b border-slate-100 pb-5">
                                    <label class="block font-bold text-[#1a2b49] text-sm mb-3">Star Category</label>
                                    <div class="flex flex-wrap gap-2">
                                        @for($s = 5; $s >= 1; $s--)
                                            <label class="relative cursor-pointer">
                                                <input type="checkbox" name="stars[]" value="{{ $s }}" {{ in_array($s, (array)request('stars', [])) ? 'checked' : '' }} class="peer sr-only">
                                                <div class="px-3 py-1.5 border border-slate-200 rounded-lg text-sm font-medium text-slate-600 bg-white hover:bg-slate-50 peer-checked:border-[#1882FF] peer-checked:bg-[#EEF6FF] peer-checked:text-[#1882FF] transition flex items-center gap-1 shadow-sm">
                                                    {{ $s }} <i class="fas fa-star text-[10px] {{ in_array($s, (array)request('stars', [])) ? 'text-[#1882FF]' : 'text-slate-400' }}"></i>
                                                </div>
                                            </label>
                                        @endfor
                                    </div>
                                </div>

                                {{-- Property Type --}}
                                <div>
                                    <label class="block font-bold text-[#1a2b49] text-sm mb-3">Property Type</label>
                                    <div class="space-y-3">
                                        @foreach(['hotel' => 'Hotel', 'resort' => 'Resort', 'villa' => 'Villa/Apartment', 'hostel' => 'Hostel'] as $type => $label)
                                            <label class="flex items-center gap-3 cursor-pointer group">
                                                <input type="checkbox" name="type" value="{{ $type }}" {{ request('type') === $type ? 'checked' : '' }} class="w-5 h-5 rounded border-slate-300 text-[#1882FF] focus:ring-[#1882FF] focus:ring-offset-0 transition shadow-sm">
                                                <span class="text-sm font-medium text-slate-600 group-hover:text-[#1a2b49] transition">{{ $label }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-6 pt-6 border-t border-slate-100">
                                <button type="submit" class="w-full bg-[#1a2b49] hover:bg-[#24385d] text-white font-bold py-3 rounded-xl shadow-md hover:shadow-lg transition flex items-center justify-center gap-2">
                                    <i class="fas fa-filter text-xs"></i> Apply Filters
                                </button>
                            </div>
                        </div>
                    </form>
                </aside>

                {{-- Main Results Area --}}
                <div class="flex-1 min-w-0">
                    
                    {{-- Top Bar --}}
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 bg-white/60 backdrop-blur-md border border-white/70 p-4 rounded-2xl shadow-sm">
                        <div class="mb-4 sm:mb-0">
                            <h1 class="text-xl font-bold text-[#1a2b49]">
                                {{ $properties->total() }} Available Properties
                            </h1>
                            <p class="text-xs font-semibold text-slate-500 mt-0.5">
                                @if(request('destination')) in <span class="text-[#1882FF]">{{ request('destination') }}</span> @endif
                                @if(request('check_in') && request('check_out'))
                                    · {{ \Carbon\Carbon::parse(request('check_in'))->format('M d') }} - {{ \Carbon\Carbon::parse(request('check_out'))->format('M d') }}
                                @endif
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Sort by</span>
                            <select onchange="window.location.href = this.value" class="appearance-none bg-white border border-slate-200 text-[#1a2b49] text-sm font-semibold rounded-xl px-4 py-2 pr-8 focus:ring-[#1882FF] focus:border-[#1882FF] shadow-sm cursor-pointer">
                                @foreach(['recommended' => 'Popularity', 'price_low' => 'Price: Low to High', 'price_high' => 'Price: High to Low', 'stars' => 'Star Rating', 'rating' => 'Guest Rating'] as $key => $label)
                                    <option value="{{ route('hotels.search', array_merge(request()->all(), ['sort' => $key])) }}" {{ request('sort') === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Property List --}}
                    <div class="space-y-6">
                        @forelse($properties as $index => $property)
                            
                            {{-- Inject Promotional Banner --}}
                            @if($index === 1)
                                <div class="rounded-3xl overflow-hidden bg-gradient-to-r from-slate-900 to-slate-800 shadow-lg relative flex items-center justify-between p-6 border border-slate-700">
                                    <div class="absolute right-0 top-0 bg-white text-slate-800 text-[10px] font-bold px-2 py-0.5 rounded-bl-lg">AD</div>
                                    <div class="flex items-center gap-6">
                                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-[#1882FF] to-[#FF4FD8] flex items-center justify-center shadow-inner opacity-90">
                                            <i class="fas fa-gem text-white text-2xl"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-white text-xl font-bold mb-1">Want Better Deals?</h3>
                                            <p class="text-slate-400 text-sm">Sign up now to unlock secret member-only prices.</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('register') }}" class="bg-white hover:bg-slate-100 text-[#0F172A] font-bold py-2.5 px-6 rounded-xl shadow-md transition flex items-center gap-2">
                                        Sign Up / Login <i class="fas fa-arrow-right text-sm"></i>
                                    </a>
                                </div>
                            @endif

                            {{-- Horizontal Property Card --}}
                            <div class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden flex flex-wrap items-stretch hover:shadow-md transition-shadow pc-card">
                                
                                {{-- Image Section --}}
                                <div class="relative w-full h-[200px] shrink-0 overflow-hidden pc-img-wrap">
                                    @php
                                        $coverUrl = $property->cover_photo_url;
                                        $isPlaceholder = str_contains($coverUrl, 'placeholder-hotel.jpg');
                                    @endphp
                                    @if(!$isPlaceholder)
                                        <img src="{{ $coverUrl }}" alt="{{ $property->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-slate-100 flex items-center justify-center">
                                            <i class="fas fa-hotel text-4xl text-slate-300"></i>
                                        </div>
                                    @endif

                                    {{-- Badges --}}
                                    <div class="absolute top-2 left-2 flex flex-col gap-2">
                                        @if($property->lowest_price && $property->lowest_price < 5000)
                                            <div class="bg-white/90 backdrop-blur text-[#000080] text-[10px] font-bold px-2 py-1 rounded shadow-sm flex items-center gap-1.5 border border-slate-100">
                                                <i class="fas fa-award"></i> Top Selling
                                            </div>
                                        @endif
                                    </div>
                                    
                                    {{-- Favorite Button --}}
                                    <button class="absolute bottom-2 left-2 w-7 h-7 bg-white/90 backdrop-blur rounded-full flex items-center justify-center text-slate-400 hover:text-red-500 shadow-sm transition">
                                        <i class="fas fa-heart text-xs"></i>
                                    </button>
                                </div>

                                {{-- Details Section --}}
                                <div class="flex-1 min-w-0 p-4 flex flex-col justify-start pc-details">
                                    {{-- Hotel Name --}}
                                    <h2 class="text-xl font-bold text-[#1a2b49] leading-tight mb-2">
                                        <a href="{{ route('hotels.show', ['property' => $property, 'check_in' => request('check_in'), 'check_out' => request('check_out')]) }}" class="hover:text-[#1882FF] transition">{{ $property->name }}</a>
                                    </h2>
                                    
                                    {{-- Stars & Location --}}
                                    <div class="flex items-center gap-3 mb-3">
                                        <div class="flex items-center gap-1 border border-yellow-200 bg-yellow-50 px-1.5 py-0.5 rounded text-[10px] font-semibold text-yellow-700">
                                            <i class="fas fa-star text-yellow-400"></i> {{ number_format($property->stars, 1) }} Star
                                        </div>
                                        <span class="text-[11px] text-slate-500 font-medium"><i class="fas fa-map-marker-alt text-slate-400 mr-1"></i>{{ $property->city }}</span>
                                    </div>

                                    {{-- Badges --}}
                                    <div class="flex flex-wrap gap-2 mb-4">
                                        <span class="border border-pink-300 text-pink-600 text-[10px] font-semibold px-2 py-0.5 rounded-full flex items-center gap-1">
                                            <i class="fas fa-user-friends"></i> Couple Friendly
                                        </span>
                                        @if($property->cancellation_policy && ($property->cancellation_policy['type'] ?? '') === 'free')
                                            <span class="border border-green-300 text-green-600 text-[10px] font-semibold px-2 py-0.5 rounded-full flex items-center gap-1">
                                                <i class="fas fa-check"></i> Free Cancellation
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Amenities Grid --}}
                                    @if($property->amenities)
                                        <div class="mt-auto pt-2">
                                            <div class="flex flex-wrap gap-x-4 gap-y-2">
                                                @php $flatAmenities = collect($property->amenities)->flatten()->take(4); @endphp
                                                @foreach($flatAmenities as $amenity)
                                                    <div class="flex items-center gap-1 text-[11px] font-medium text-slate-600">
                                                        @php
                                                            $icon = match(strtolower($amenity)) {
                                                                'wifi', 'internet' => 'fa-wifi',
                                                                'pool', 'swimming pool' => 'fa-swimming-pool',
                                                                'parking' => 'fa-parking',
                                                                'gym', 'fitness center' => 'fa-dumbbell',
                                                                'restaurant' => 'fa-utensils',
                                                                'air conditioning', 'ac' => 'fa-snowflake',
                                                                'spa' => 'fa-spa',
                                                                default => 'fa-check'
                                                            };
                                                        @endphp
                                                        <i class="fas {{ $icon }} text-slate-400 text-[10px] w-3 text-center"></i>
                                                        {{ str_replace('_', ' ', ucfirst($amenity)) }}
                                                    </div>
                                                @endforeach
                                                @if(count(collect($property->amenities)->flatten()) > 4)
                                                    <div class="text-[11px] font-semibold text-[#1882FF]">+ {{ count(collect($property->amenities)->flatten()) - 4 }} more</div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                {{-- Pricing & Action Section --}}
                                <div class="w-full shrink-0 p-4 flex flex-col justify-between border-t border-slate-100 pc-pricing">
                                    {{-- Rating Box --}}
                                    @if($property->average_rating)
                                        <div class="flex justify-end items-center gap-2 mb-4">
                                            <div class="text-right">
                                                <p class="text-sm font-bold text-slate-700 leading-tight">{{ $property->average_rating >= 8 ? 'Excellent' : ($property->average_rating >= 6 ? 'Good' : 'Review') }}</p>
                                                <p class="text-[11px] text-slate-500 font-medium">{{ $property->review_count }} Reviews</p>
                                            </div>
                                            <div class="w-10 h-10 bg-[#000080] rounded flex flex-col items-center justify-center text-white shadow-sm">
                                                <span class="font-bold text-sm leading-none">{{ number_format($property->average_rating, 1) }}</span>
                                                <span class="text-[8px] leading-none mt-0.5">out of 5</span>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="text-right mt-auto">
                                        @if($property->lowest_price && $property->lowest_price > 0)
                                            @php
                                                $activePromotion = $property->promotions->filter(fn($promo) => $promo->isValid())->first();
                                                $originalPrice = $property->lowest_price;
                                                $finalPrice = $originalPrice;
                                                $discountDisplay = null;
                                                
                                                if ($activePromotion) {
                                                    if ($activePromotion->discount_type === 'percent') {
                                                        $discountDisplay = round($activePromotion->discount_value) . '% off';
                                                        $finalPrice = $originalPrice * (1 - ($activePromotion->discount_value / 100));
                                                    } else {
                                                        $discountDisplay = \App\Helpers\Currency::format($activePromotion->discount_value) . ' off';
                                                        $finalPrice = $originalPrice - $activePromotion->discount_value;
                                                        if ($finalPrice < 0) $finalPrice = 0;
                                                    }
                                                }
                                            @endphp
                                            
                                            @if($activePromotion)
                                                <div class="flex justify-end mb-2">
                                                    <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full shadow-sm" style="background-color: #f27420; color: white;">{{ $discountDisplay }}</span>
                                                </div>
                                                <p class="text-[10px] font-bold mb-1 leading-tight text-right truncate" style="color: #00a651;">{{ $activePromotion->title ?? 'Special Offer' }}</p>
                                            @endif
                                            
                                            <div class="mt-3">
                                                <p class="text-[10px] text-slate-500 font-medium">Starts from</p>
                                                @if($activePromotion)
                                                    <p class="text-[11px] font-bold text-red-400 line-through mb-0.5">{{ \App\Helpers\Currency::format($originalPrice) }}</p>
                                                @endif
                                                <p class="text-xl font-bold text-[#1a2b49] leading-none mb-1">
                                                    {{ \App\Helpers\Currency::format($finalPrice) }}
                                                </p>
                                                <p class="text-[10px] text-slate-500 font-medium mb-3">for 1 Night , per room</p>
                                            </div>
                                        @else
                                            <p class="text-sm font-bold text-slate-500 mb-3">Price unavailable</p>
                                        @endif
                                        
                                        <a href="{{ route('hotels.show', ['property' => $property, 'check_in' => request('check_in'), 'check_out' => request('check_out')]) }}" class="block w-full font-bold py-2 rounded text-center shadow hover:shadow-md transition-all text-sm" style="background-color: #0066ff; color: white;">
                                            Select
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-3xl border border-white/70 bg-white/80 shadow-sm backdrop-blur-xl p-12 text-center">
                                <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-search text-3xl text-slate-300"></i>
                                </div>
                                <h3 class="text-xl font-bold text-[#1a2b49] mb-2">No properties found</h3>
                                <p class="text-slate-500 font-medium mb-6">We couldn't find any hotels matching your current filters and dates.</p>
                                <a href="{{ route('hotels.search', ['destination' => request('destination')]) }}" class="inline-flex items-center justify-center bg-[#1a2b49] hover:bg-[#24385d] text-white font-bold py-2.5 px-6 rounded-xl transition shadow-md">
                                    Clear all filters
                                </a>
                            </div>
                        @endforelse
                    </div>

                    {{-- Pagination --}}
                    @if($properties->hasPages())
                        <div class="mt-8 bg-white/60 backdrop-blur-md border border-white/70 rounded-2xl p-4 shadow-sm">
                            {{ $properties->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('search-destination');
        if (input) {
            new google.maps.places.Autocomplete(input, {
                types: ['(cities)'],
            });
            
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && document.querySelector('.pac-container')) {
                    const pacContainer = document.querySelector('.pac-container');
                    if (pacContainer.style.display !== 'none') {
                        e.preventDefault();
                    }
                }
            });
        }
    });
    </script>
</body>
</html>
