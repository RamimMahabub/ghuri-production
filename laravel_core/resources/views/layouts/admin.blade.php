<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'OTA Admin' }} — {{ config('app.name', 'Bookdei') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-body antialiased bg-[#F8F9FA] text-[#19100F] overflow-hidden">
    <div class="flex h-screen w-full" x-data="{ sidebarOpen: window.innerWidth >= 1024 }">
        
        {{-- Mobile Overlay --}}
        <div x-show="sidebarOpen" 
             style="display: none;"
             class="fixed inset-0 bg-gray-900/50 z-40 lg:hidden backdrop-blur-sm"
             x-transition.opacity
             @click="sidebarOpen = false"></div>

        {{-- ── Sidebar ─────────────────────────────── --}}
        <aside 
            :class="sidebarOpen ? 'translate-x-0 w-64' : '-translate-x-full w-64 lg:w-20 lg:translate-x-0'"
            class="fixed inset-y-0 left-0 z-50 bg-white text-gray-700 transition-all duration-300 ease-in-out flex flex-col shadow-xl border-r border-gray-100 lg:static lg:flex-shrink-0"
        >
            {{-- Logo (red header strip) --}}
            <div class="flex items-center gap-3 px-6 h-20 bg-[#d00e15] shrink-0">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                    <i class="fas fa-globe text-white text-lg"></i>
                </div>
                <div x-show="sidebarOpen" x-transition.opacity.duration.300ms class="truncate">
                    <span class="font-heading font-bold text-lg tracking-tight text-white block leading-tight">Bookdei</span>
                    <span class="text-[10px] text-white/80 uppercase tracking-widest font-bold">OTA Admin</span>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 py-6 px-3 space-y-1 overflow-y-auto scrollbar-hide">
                <div x-show="sidebarOpen" class="px-3 text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-2 mt-4 first:mt-0">Overview</div>

                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('admin.dashboard') ? 'bg-red-50 text-[#d00e15] font-semibold border-l-3 border-[#d00e15]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-chart-pie w-5 text-center {{ request()->routeIs('admin.dashboard') ? 'text-[#d00e15]' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                    <span x-show="sidebarOpen" class="font-medium text-sm">Dashboard</span>
                </a>

                <div x-show="sidebarOpen" class="px-3 text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-2 mt-6">Inventory</div>

                <a href="{{ route('admin.properties.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('admin.properties.*') ? 'bg-red-50 text-[#d00e15] font-semibold border-l-3 border-[#d00e15]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-building w-5 text-center {{ request()->routeIs('admin.properties.*') ? 'text-[#d00e15]' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                    <span x-show="sidebarOpen" class="font-medium text-sm flex-1">Properties</span>
                </a>

                <a href="{{ route('admin.bookings.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('admin.bookings.*') ? 'bg-red-50 text-[#d00e15] font-semibold border-l-3 border-[#d00e15]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-calendar-alt w-5 text-center {{ request()->routeIs('admin.bookings.*') ? 'text-[#d00e15]' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                    <span x-show="sidebarOpen" class="font-medium text-sm flex-1">Global Bookings</span>
                </a>

                <div x-show="sidebarOpen" class="px-3 text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-2 mt-6">Customer Care</div>
                <a href="{{ route('admin.support.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('admin.support.*') ? 'bg-red-50 text-[#d00e15] font-semibold border-l-3 border-[#d00e15]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-headset w-5 text-center {{ request()->routeIs('admin.support.*') ? 'text-[#d00e15]' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                    <span x-show="sidebarOpen" class="font-medium text-sm flex-1">Support Center</span>
                    @php($staffSupportUnread = \App\Models\SupportTicket::where(fn($q) => $q->whereNull('staff_last_read_at')->orWhereColumn('last_message_at', '>', 'staff_last_read_at'))->count())
                    @if($staffSupportUnread)<span x-show="sidebarOpen" class="bg-[#d00e15] text-white text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $staffSupportUnread }}</span>@endif
                </a>

                <div x-show="sidebarOpen" class="px-3 text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-2 mt-6">Financials</div>

                <a href="{{ route('admin.commissions.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('admin.commissions.*') ? 'bg-red-50 text-[#d00e15] font-semibold border-l-3 border-[#d00e15]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-percentage w-5 text-center {{ request()->routeIs('admin.commissions.*') ? 'text-[#d00e15]' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                    <span x-show="sidebarOpen" class="font-medium text-sm">Commissions</span>
                </a>

                <a href="{{ route('admin.payouts.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('admin.payouts.*') ? 'bg-red-50 text-[#d00e15] font-semibold border-l-3 border-[#d00e15]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-money-bill-transfer w-5 text-center {{ request()->routeIs('admin.payouts.*') ? 'text-[#d00e15]' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                    <span x-show="sidebarOpen" class="font-medium text-sm">Partner Payouts</span>
                </a>

                <div x-show="sidebarOpen" class="px-3 text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-2 mt-6">Administration</div>

                <a href="{{ route('admin.users.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('admin.users.*') ? 'bg-red-50 text-[#d00e15] font-semibold border-l-3 border-[#d00e15]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-users-cog w-5 text-center {{ request()->routeIs('admin.users.*') ? 'text-[#d00e15]' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                    <span x-show="sidebarOpen" class="font-medium text-sm">User Management</span>
                </a>

                <a href="{{ route('admin.settings.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group {{ request()->routeIs('admin.settings.*') ? 'bg-red-50 text-[#d00e15] font-semibold border-l-3 border-[#d00e15]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fas fa-cog w-5 text-center {{ request()->routeIs('admin.settings.*') ? 'text-[#d00e15]' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                    <span x-show="sidebarOpen" class="font-medium text-sm">Global Settings</span>
                </a>

                <div x-show="sidebarOpen" class="px-3 text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-2 mt-6">Quick Links</div>

                <a href="{{ url('/') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all group text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                    <i class="fas fa-home w-5 text-center text-gray-400 group-hover:text-gray-600"></i>
                    <span x-show="sidebarOpen" class="font-medium text-sm">Back to Home</span>
                </a>
            </nav>

            {{-- User Profile --}}
            <div class="p-4 border-t border-gray-100 shrink-0">
                <div class="flex items-center gap-3" :class="sidebarOpen ? 'px-2' : 'justify-center'">
                    <div class="w-10 h-10 rounded-full bg-[#d00e15] flex items-center justify-center shrink-0">
                        <span class="text-sm font-bold text-white">{{ substr(Auth::user()->name ?? 'A', 0, 1) }}</span>
                    </div>
                    <div x-show="sidebarOpen" class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-800 truncate">{{ Auth::user()->name ?? 'Administrator' }}</p>
                        <p class="text-[11px] text-gray-400 truncate">System Admin</p>
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
                    <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-[#d00e15] transition-colors p-2 rounded-lg hover:bg-gray-100 focus:outline-none">
                        <i class="fas fa-bars text-lg"></i>
                    </button>
                    <div>
                        <h1 class="text-xl font-heading font-bold text-[#19100F]">{{ $pageTitle ?? 'Admin Dashboard' }}</h1>
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

                    <a href="{{ url('/') }}" class="text-sm font-semibold text-gray-600 hover:text-[#d00e15] transition-colors px-3">
                        Back to Home
                    </a>

                    <div x-data="{ currencyOpen: false }" class="relative">
                        <button type="button" @click="currencyOpen = !currencyOpen" class="flex items-center gap-1 text-sm font-semibold text-gray-700 hover:text-[#d00e15] transition-colors p-2 rounded-lg hover:bg-gray-50" aria-label="Select currency">
                            {{ session('currency', 'BDT') }}
                            <i class="fas fa-chevron-down text-[10px] ml-1 transition-transform" :class="currencyOpen ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="currencyOpen" @click.away="currencyOpen = false" x-transition style="display:none;" class="absolute right-0 mt-2 w-32 bg-white border border-gray-100 rounded-xl shadow-lg overflow-hidden z-50">
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

                    <button class="relative p-2 text-gray-400 hover:text-[#d00e15] transition-colors rounded-full hover:bg-gray-50">
                        <i class="fas fa-bell text-lg"></i>
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
