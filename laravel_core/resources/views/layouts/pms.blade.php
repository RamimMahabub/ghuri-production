<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Property Management' }} — {{ config('app.name', 'GhuriTravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-body antialiased bg-[#F8F9FA] text-[#19100F] overflow-hidden">
    <div class="flex h-screen w-full" x-data="{ sidebarOpen: true }">
        
        {{-- ── Sidebar ─────────────────────────────── --}}
        <aside 
            :class="sidebarOpen ? 'w-64 translate-x-0' : '-translate-x-full w-0 lg:w-20 lg:translate-x-0'"
            class="fixed inset-y-0 left-0 z-50 bg-white text-gray-700 transition-all duration-300 ease-in-out flex flex-col shadow-xl border-r border-gray-100 lg:static lg:flex-shrink-0"
        >
            {{-- Logo (red header strip) --}}
            <div class="flex items-center gap-3 px-6 h-20 bg-[#d00e15] shrink-0">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                    <i class="fas fa-building text-white text-lg"></i>
                </div>
                <div x-show="sidebarOpen" x-transition.opacity.duration.300ms class="truncate">
                    <span class="font-heading font-bold text-lg tracking-tight text-white block leading-tight">GhuriTravel</span>
                    <span class="text-[10px] text-white/80 uppercase tracking-widest font-bold">Partner Hub</span>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 py-6 px-3 space-y-1 overflow-y-auto scrollbar-hide">
                <div x-show="sidebarOpen" class="px-3 text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-2 mt-4 first:mt-0">Overview</div>

                <a href="{{ route('property-owner.dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('property-owner.dashboard') ? 'bg-red-50 text-[#d00e15] font-semibold border-l-3 border-[#d00e15]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-chart-line w-5 text-center {{ request()->routeIs('property-owner.dashboard') ? 'text-[#d00e15]' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                    <span x-show="sidebarOpen" class="font-medium text-sm">Dashboard</span>
                </a>

                <a href="{{ route('property-owner.hotels.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('property-owner.hotels.*') ? 'bg-red-50 text-[#d00e15] font-semibold border-l-3 border-[#d00e15]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-hotel w-5 text-center {{ request()->routeIs('property-owner.hotels.*') ? 'text-[#d00e15]' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                    <span x-show="sidebarOpen" class="font-medium text-sm">Properties</span>
                </a>

                <a href="{{ route('property-owner.payouts.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('property-owner.payouts.*') ? 'bg-red-50 text-[#d00e15] font-semibold border-l-3 border-[#d00e15]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-wallet w-5 text-center {{ request()->routeIs('property-owner.payouts.*') ? 'text-[#d00e15]' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                    <span x-show="sidebarOpen" class="font-medium text-sm">Payouts</span>
                </a>

                <div x-show="sidebarOpen" class="px-3 text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-2 mt-6">Operations</div>

                <a href="{{ route('property-owner.bookings.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('property-owner.bookings.*') ? 'bg-red-50 text-[#d00e15] font-semibold border-l-3 border-[#d00e15]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-calendar-check w-5 text-center {{ request()->routeIs('property-owner.bookings.*') ? 'text-[#d00e15]' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                    <span x-show="sidebarOpen" class="font-medium text-sm flex-1">Bookings</span>
                    @if(isset($pendingBookingsCount) && $pendingBookingsCount > 0)
                        <span x-show="sidebarOpen" class="bg-[#d00e15] text-white text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $pendingBookingsCount }}</span>
                    @endif
                </a>

                @php
                    $availHotelId = $currentPropertyId
                        ?? optional(\App\Models\Property::where('owner_id', Auth::id())->first())->id;
                    $availUrl = $availHotelId
                        ? route('property-owner.availability.index', ['hotel' => $availHotelId])
                        : route('property-owner.hotels.index');
                @endphp
                <a href="{{ $availUrl }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('property-owner.availability.*') ? 'bg-red-50 text-[#d00e15] font-semibold border-l-3 border-[#d00e15]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-calendar-days w-5 text-center {{ request()->routeIs('property-owner.availability.*') ? 'text-[#d00e15]' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                    <span x-show="sidebarOpen" class="font-medium text-sm">Availability</span>
                </a>

                <div x-show="sidebarOpen" class="px-3 text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-2 mt-6">Growth</div>

                <a href="{{ route('property-owner.promotions.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('property-owner.promotions.*') ? 'bg-red-50 text-[#d00e15] font-semibold border-l-3 border-[#d00e15]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-tags w-5 text-center {{ request()->routeIs('property-owner.promotions.*') ? 'text-[#d00e15]' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                    <span x-show="sidebarOpen" class="font-medium text-sm">Promotions</span>
                </a>

                <a href="{{ route('property-owner.reviews.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('property-owner.reviews.*') ? 'bg-red-50 text-[#d00e15] font-semibold border-l-3 border-[#d00e15]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-star w-5 text-center {{ request()->routeIs('property-owner.reviews.*') ? 'text-[#d00e15]' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                    <span x-show="sidebarOpen" class="font-medium text-sm">Reviews</span>
                </a>

                <div x-show="sidebarOpen" class="px-3 text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-2 mt-6">Management</div>

                <a href="{{ route('property-owner.guests.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('property-owner.guests.*') ? 'bg-red-50 text-[#d00e15] font-semibold border-l-3 border-[#d00e15]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-users w-5 text-center {{ request()->routeIs('property-owner.guests.*') ? 'text-[#d00e15]' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                    <span x-show="sidebarOpen" class="font-medium text-sm">Guests</span>
                </a>

                <a href="{{ route('property-owner.settings') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('property-owner.settings*') ? 'bg-red-50 text-[#d00e15] font-semibold border-l-3 border-[#d00e15]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-cog w-5 text-center {{ request()->routeIs('property-owner.settings*') ? 'text-[#d00e15]' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                    <span x-show="sidebarOpen" class="font-medium text-sm">Settings</span>
                </a>
            </nav>

            {{-- User Profile --}}
            <div class="p-4 border-t border-gray-100 shrink-0">
                <div class="flex items-center gap-3" :class="sidebarOpen ? 'px-2' : 'justify-center'">
                    <div class="w-10 h-10 rounded-full bg-[#d00e15] flex items-center justify-center shrink-0">
                        <span class="text-sm font-bold text-white">{{ substr(Auth::user()->name ?? 'O', 0, 1) }}</span>
                    </div>
                    <div x-show="sidebarOpen" class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-800 truncate">{{ Auth::user()->name ?? 'Property Owner' }}</p>
                        <p class="text-[11px] text-gray-400 truncate">ID: {{ Auth::id() }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" x-show="sidebarOpen">
                        @csrf
                        <button type="submit" class="text-gray-400 hover:text-[#d00e15] transition-colors p-2 rounded-lg hover:bg-gray-50" title="Logout">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- ── Main Content Area ────────────────────────── --}}
        <div class="flex-1 flex flex-col h-full min-w-0 bg-[#F8F9FA] overflow-hidden relative">
            
            {{-- Header --}}
            <header class="h-20 bg-white/80 backdrop-blur-md border-b border-gray-100 flex items-center justify-between px-6 shrink-0 z-40">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-brand-primary transition-colors p-2 rounded-lg hover:bg-gray-100 focus:outline-none">
                        <i class="fas fa-bars text-lg"></i>
                    </button>
                    <div>
                        <h1 class="text-xl font-heading font-bold text-brand-black">{{ $pageTitle ?? 'Dashboard' }}</h1>
                        @isset($pageSubtitle)
                            <p class="text-xs text-gray-500">{{ $pageSubtitle }}</p>
                        @endisset
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    @isset($headerActions)
                        <div class="hidden sm:flex items-center gap-3">
                            {{ $headerActions }}
                        </div>
                    @endisset

                    <button class="relative p-2 text-gray-400 hover:text-brand-primary transition-colors rounded-full hover:bg-gray-50">
                        <i class="fas fa-bell text-lg"></i>
                        <span class="absolute top-2 right-2 w-2 h-2 bg-brand-primary rounded-full ring-2 ring-white"></span>
                    </button>
                </div>
            </header>

            {{-- Main Scrollable Content --}}
            <main class="flex-1 overflow-y-auto p-6 md:p-8 custom-scrollbar">
                
                {{-- Flash Messages --}}
                @if(session('success'))
                    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm animate-fade-in">
                        <i class="fas fa-check-circle text-green-600 text-lg"></i>
                        <p class="text-sm font-medium">{{ session('success') }}</p>
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm animate-fade-in">
                        <i class="fas fa-exclamation-circle text-red-600 text-lg"></i>
                        <p class="text-sm font-medium">{{ session('error') }}</p>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    @stack('scripts')
    
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #E5E7EB;
            border-radius: 20px;
        }
        .custom-scrollbar:hover::-webkit-scrollbar-thumb {
            background-color: #D1D5DB;
        }
    </style>
</body>
</html>
