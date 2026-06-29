<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Search Hotels — {{ config('app.name', 'GhuriTravel') }}</title>
    <meta name="description" content="Find and book the perfect hotel. Compare prices, read reviews, and reserve rooms at the best rates.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:ital,wght@0,400;0,500;0,600;0,700;1,400&family=JetBrains+Mono:wght@400;500;600&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-body antialiased bg-brand-surface">

    {{-- Header with Search --}}
    <header class="bg-white border-b border-brand-border sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                {{-- Logo --}}
                <a href="/" class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-brand-primary flex items-center justify-center">
                        <i class="fas fa-plane-departure text-white text-sm"></i>
                    </div>
                    <span class="font-heading font-bold text-brand-black text-lg">GhuriTravel</span>
                </a>

                {{-- Compact Search --}}
                <form action="{{ route('hotels.search') }}" method="GET" class="hidden md:flex items-center gap-2 bg-brand-surface rounded-full px-4 py-2 flex-1 max-w-2xl mx-8 border border-brand-border hover:border-brand-muted transition-colors">
                    <i class="fas fa-search text-brand-muted text-sm"></i>
                    <input type="text" name="destination" id="search-destination" value="{{ request('destination') }}" placeholder="Where are you going?" class="bg-transparent border-0 focus:ring-0 text-sm flex-1 text-brand-black placeholder:text-brand-muted">
                    <input type="date" name="check_in" value="{{ request('check_in', now()->addDay()->format('Y-m-d')) }}" class="bg-transparent border-0 focus:ring-0 text-sm w-32 text-brand-text">
                    <input type="date" name="check_out" value="{{ request('check_out', now()->addDays(2)->format('Y-m-d')) }}" class="bg-transparent border-0 focus:ring-0 text-sm w-32 text-brand-text">
                    <button type="submit" class="bg-brand-primary text-white rounded-full w-8 h-8 flex items-center justify-center hover:bg-brand-dark transition-colors">
                        <i class="fas fa-search text-xs"></i>
                    </button>
                </form>

                {{-- Auth Links --}}
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-sm text-brand-text hover:text-brand-primary">My Bookings</a>
                        <div class="w-8 h-8 rounded-full bg-brand-primary flex items-center justify-center text-white text-xs font-bold">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-brand-text hover:text-brand-primary">Sign In</a>
                        <a href="{{ route('register') }}" class="btn-primary btn-sm">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex gap-6">
            {{-- Filters Sidebar --}}
            <aside class="hidden lg:block w-72 flex-shrink-0">
                <form action="{{ route('hotels.search') }}" method="GET" class="card card-body space-y-5 sticky top-24">
                    <input type="hidden" name="destination" value="{{ request('destination') }}">
                    <input type="hidden" name="check_in" value="{{ request('check_in') }}">
                    <input type="hidden" name="check_out" value="{{ request('check_out') }}">

                    <h3 class="font-heading font-bold text-brand-black text-sm">Filters</h3>

                    {{-- Price Range --}}
                    <div>
                        <label class="form-label text-xs">Price per Night</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="form-input-styled text-xs w-full">
                            <span class="text-brand-muted">–</span>
                            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" class="form-input-styled text-xs w-full">
                        </div>
                    </div>

                    {{-- Star Rating --}}
                    <div>
                        <label class="form-label text-xs">Star Rating</label>
                        <div class="space-y-1.5">
                            @for($s = 5; $s >= 1; $s--)
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="stars[]" value="{{ $s }}" {{ in_array($s, (array)request('stars', [])) ? 'checked' : '' }} class="rounded border-brand-border text-brand-primary focus:ring-brand-primary">
                                    <span class="flex items-center gap-0.5">
                                        @for($i = 0; $i < $s; $i++)
                                            <i class="fas fa-star text-yellow-400 text-xs"></i>
                                        @endfor
                                    </span>
                                </label>
                            @endfor
                        </div>
                    </div>

                    {{-- Property Type --}}
                    <div>
                        <label class="form-label text-xs">Property Type</label>
                        <div class="space-y-1.5">
                            @foreach(['hotel', 'resort', 'villa', 'hostel', 'apartment', 'guesthouse'] as $type)
                                <label class="flex items-center gap-2 cursor-pointer text-sm">
                                    <input type="checkbox" name="type" value="{{ $type }}" {{ request('type') === $type ? 'checked' : '' }} class="rounded border-brand-border text-brand-primary focus:ring-brand-primary">
                                    {{ ucfirst($type) }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <button type="submit" class="btn-primary w-full">Apply Filters</button>
                    <a href="{{ route('hotels.search', ['destination' => request('destination')]) }}" class="btn-ghost w-full text-center text-xs">Clear All</a>
                </form>
            </aside>

            {{-- Results --}}
            <div class="flex-1">
                {{-- Sort Bar --}}
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h1 class="font-heading font-bold text-brand-black text-xl">
                            @if(request('destination'))
                                Hotels in "{{ request('destination') }}"
                            @else
                                All Hotels
                            @endif
                        </h1>
                        <p class="text-sm text-brand-muted">{{ $properties->total() }} properties found</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-brand-muted">Sort by:</span>
                        <select onchange="window.location.href = this.value" class="form-input-styled text-xs py-1.5 w-auto">
                            @foreach(['recommended' => 'Recommended', 'price_low' => 'Price: Low to High', 'price_high' => 'Price: High to Low', 'stars' => 'Star Rating', 'rating' => 'Guest Rating'] as $key => $label)
                                <option value="{{ route('hotels.search', array_merge(request()->all(), ['sort' => $key])) }}" {{ request('sort') === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Property Cards --}}
                <div class="space-y-4">
                    @forelse($properties as $property)
                        <a href="{{ route('hotels.show', ['property' => $property, 'check_in' => request('check_in'), 'check_out' => request('check_out')]) }}" class="property-card block animate-slide-up" style="animation-delay: {{ $loop->index * 0.05 }}s">
                            {{-- Image --}}
                            <div class="property-card-image">
                                @php
                                    $coverUrl = $property->cover_photo_url;
                                    $isPlaceholder = str_contains($coverUrl, 'placeholder-hotel.jpg');
                                @endphp
                                @if(!$isPlaceholder)
                                    <img src="{{ $coverUrl }}" alt="{{ $property->name }}">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-brand-light to-white flex items-center justify-center">
                                        <i class="fas fa-hotel text-5xl text-brand-border"></i>
                                    </div>
                                @endif

                                {{-- Badges --}}
                                @if($property->lowest_price && $property->lowest_price < 100)
                                    <div class="absolute top-3 left-3">
                                        <span class="badge-brand"><i class="fas fa-bolt"></i> Great Deal</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Content --}}
                            <div class="property-card-content">
                                <div>
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <h3 class="font-heading font-bold text-brand-black text-base mb-1">{{ $property->name }}</h3>
                                            <div class="flex items-center gap-1 mb-1">
                                                @for($i = 1; $i <= $property->stars; $i++)
                                                    <i class="fas fa-star text-yellow-400 text-xs"></i>
                                                @endfor
                                                <span class="text-xs text-brand-muted ml-1">{{ ucfirst($property->type) }}</span>
                                            </div>
                                            <p class="text-xs text-brand-muted">
                                                <i class="fas fa-map-marker-alt text-brand-primary"></i>
                                                {{ $property->city }}{{ $property->country ? ', ' . $property->country : '' }}
                                                @if($property->city_center_distance)
                                                    · {{ $property->city_center_distance }} from center
                                                @endif
                                            </p>
                                        </div>

                                        {{-- Rating Badge --}}
                                        @if($property->average_rating)
                                            <div class="text-right flex-shrink-0 ml-4">
                                                <div class="guest-score {{ $property->average_rating >= 8 ? 'excellent' : ($property->average_rating >= 6 ? 'good' : 'average') }}">
                                                    {{ number_format($property->average_rating, 1) }}
                                                </div>
                                                <p class="text-[10px] text-brand-muted mt-1">{{ $property->review_count }} reviews</p>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Amenity Icons --}}
                                    @if($property->amenities)
                                        <div class="flex items-center gap-3 mt-3">
                                            @php $flatAmenities = collect($property->amenities)->flatten()->take(5); @endphp
                                            @foreach($flatAmenities as $amenity)
                                                <span class="text-xs text-brand-muted"><i class="fas fa-check text-brand-primary text-[10px]"></i> {{ str_replace('_', ' ', ucfirst($amenity)) }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                {{-- Price --}}
                                <div class="flex items-end justify-between mt-4 pt-3 border-t border-brand-border">
                                    <div>
                                        @if($property->cancellation_policy && ($property->cancellation_policy['type'] ?? '') === 'free')
                                            <span class="text-xs text-status-confirmed font-medium"><i class="fas fa-check-circle"></i> Free cancellation</span>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs text-brand-muted">From</p>
                                        <p class="text-xl font-heading font-bold text-brand-black">
                                            ${{ number_format($property->lowest_price ?? 0, 0) }}
                                        </p>
                                        <p class="text-[10px] text-brand-muted">per night</p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="card card-body text-center py-16">
                            <i class="fas fa-search text-5xl text-brand-border mb-4"></i>
                            <h3 class="font-heading text-xl font-bold text-brand-black mb-2">No hotels found</h3>
                            <p class="text-brand-muted text-sm">Try adjusting your search or filters</p>
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $properties->links() }}
                </div>
            </div>
        </div>
    </div>

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
