<x-customer-layout>
    <x-slot name="pageTitle">Dashboard</x-slot>
    <x-slot name="pageSubtitle">Overview of your bookings and activities</x-slot>

    @php
        $totalBookings = auth()->user()->bookings()->count() + auth()->user()->hotelBookings()->count();
        $upcomingTrips = auth()->user()->bookings()->where('status', 'confirmed')->count() + auth()->user()->hotelBookings()->where('status', 'confirmed')->count();
        $rewardPoints = auth()->user()->reward_points ?? 0;
        $walletBalance = auth()->user()->wallet_balance ?? 0;
    @endphp

    {{-- KPI Cards (Premium Glassmorphic Design) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {{-- Total Bookings --}}
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl shadow-inner">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                    <span class="bg-green-100 text-green-700 text-xs font-bold px-2.5 py-1 rounded-full flex items-center gap-1">
                        <i class="fas fa-arrow-up text-[10px]"></i> New
                    </span>
                </div>
                <h3 class="text-3xl font-bold text-brand-black tracking-tight mb-1">{{ $totalBookings }}</h3>
                <p class="text-sm font-medium text-gray-500">Total Bookings</p>
            </div>
        </div>

        {{-- Upcoming Trips --}}
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-green-100 text-green-600 flex items-center justify-center text-xl shadow-inner">
                        <i class="fas fa-plane-arrival"></i>
                    </div>
                </div>
                <h3 class="text-3xl font-bold text-brand-black tracking-tight mb-1">{{ $upcomingTrips }}</h3>
                <p class="text-sm font-medium text-gray-500">Upcoming Trips</p>
            </div>
        </div>

        {{-- Reward Points --}}
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-purple-100 text-purple-600 flex items-center justify-center text-xl shadow-inner">
                        <i class="fas fa-coins"></i>
                    </div>
                </div>
                <h3 class="text-3xl font-bold text-brand-black tracking-tight mb-1">{{ number_format($rewardPoints) }}</h3>
                <p class="text-sm font-medium text-gray-500">Reward Points</p>
            </div>
        </div>

        {{-- Wallet Balance --}}
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center text-xl shadow-inner">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
                <h3 class="text-3xl font-bold text-brand-black tracking-tight mb-1">{{ number_format($walletBalance) }} <span class="text-lg text-gray-500 font-normal">BDT</span></h3>
                <p class="text-sm font-medium text-gray-500">Wallet Balance</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Left Column: Operations & Charts --}}
        <div class="lg:col-span-2 space-y-8">
            
            {{-- Quick Search Widget --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 animate-slide-up" style="animation-delay: 0.1s;">
                <h3 class="font-heading font-bold text-lg text-brand-black mb-6">Start Planning</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Flights Card --}}
                    <a href="{{ route('flights.search') }}" class="flex items-center gap-5 p-6 bg-gray-50 hover:bg-brand-primary/5 hover:text-brand-primary rounded-2xl border border-gray-100 hover:border-brand-primary/30 transition-all text-gray-700 group">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-100 text-blue-600 transition-transform group-hover:scale-110 shadow-inner text-xl">
                            <i class="fas fa-plane-departure"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 text-base">Search Flights</h3>
                            <p class="text-xs text-gray-500">Find the best airlines</p>
                        </div>
                        <i class="fas fa-arrow-right absolute right-6 text-gray-300 opacity-0 transition-all group-hover:opacity-100 group-hover:translate-x-2 group-hover:text-brand-primary"></i>
                    </a>

                    {{-- Hotels Card --}}
                    <a href="{{ route('hotels.search') }}" class="flex items-center gap-5 p-6 bg-gray-50 hover:bg-brand-primary/5 hover:text-brand-primary rounded-2xl border border-gray-100 hover:border-brand-primary/30 transition-all text-gray-700 group">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-teal-100 text-teal-600 transition-transform group-hover:scale-110 shadow-inner text-xl">
                            <i class="fas fa-bed"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 text-base">Browse Hotels</h3>
                            <p class="text-xs text-gray-500">Book perfect stays</p>
                        </div>
                        <i class="fas fa-arrow-right absolute right-6 text-gray-300 opacity-0 transition-all group-hover:opacity-100 group-hover:translate-x-2 group-hover:text-brand-primary"></i>
                    </a>
                </div>
            </div>

            {{-- Customer Analytics Chart (Spending) --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 animate-slide-up" style="animation-delay: 0.15s;">
                <h3 class="font-heading font-bold text-lg text-brand-black mb-2">Spending Trends</h3>
                <p class="text-xs text-gray-500 mb-6">Your travel spend over the last 6 months</p>
                <div class="relative w-full">
                    <div id="customerSpendChart" style="min-height: 250px;"></div>
                </div>
            </div>

            {{-- Booking History Section --}}
            <div x-data="{ tab: 'flights' }" class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 animate-slide-up" style="animation-delay: 0.2s;">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-heading font-bold text-lg text-brand-black">Booking History</h3>
                    
                    {{-- Tabs --}}
                    <div class="flex bg-gray-100 p-1 rounded-xl">
                        @php
                            $allFlightsCount = auth()->user()->bookings()->count();
                            $allHotelsCount = auth()->user()->hotelBookings()->count();
                        @endphp
                        <button @click="tab = 'flights'" :class="tab === 'flights' ? 'bg-white shadow-sm font-semibold text-gray-900' : 'text-gray-500 hover:text-gray-700'" class="px-4 py-1.5 text-sm rounded-lg transition-all">Flights <span class="bg-blue-100 text-blue-700 ml-1 px-1.5 rounded-full text-[10px]">{{ $allFlightsCount }}</span></button>
                        <button @click="tab = 'hotels'" :class="tab === 'hotels' ? 'bg-white shadow-sm font-semibold text-gray-900' : 'text-gray-500 hover:text-gray-700'" class="px-4 py-1.5 text-sm rounded-lg transition-all">Hotels <span class="bg-gray-200 text-gray-700 ml-1 px-1.5 rounded-full text-[10px]">{{ $allHotelsCount }}</span></button>
                    </div>
                </div>

                <div class="max-h-[500px] overflow-y-auto custom-scrollbar pr-2">
                    {{-- Flights Tab --}}
                    <div x-show="tab === 'flights'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-3">
                        @php
                            $flightBookings = auth()->user()->bookings()->latest()->get();
                        @endphp

                        @if($flightBookings->isEmpty())
                            <div class="py-12 text-center flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-ticket-alt text-gray-300 text-2xl"></i>
                                </div>
                                <p class="text-gray-500 mb-4 text-sm font-medium">You have no flight bookings.</p>
                            </div>
                        @else
                            @foreach($flightBookings as $booking)
                                <a href="{{ route('booking.show', $booking->id) }}" class="flex items-center justify-between p-4 rounded-2xl border border-gray-100 hover:border-brand-primary/30 transition-colors group bg-gray-50 hover:bg-white hover:shadow-sm">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center shadow-inner">
                                            <i class="fas fa-plane"></i>
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $booking->status == 'confirmed' ? 'bg-green-100 text-green-700 border border-green-100' : 'bg-yellow-100 text-yellow-700 border border-yellow-100' }}">
                                                    {{ strtoupper($booking->status) }}
                                                </span>
                                                <span class="text-xs text-gray-500 font-mono">{{ $booking->api_reference_id }}</span>
                                            </div>
                                            <p class="font-bold text-gray-900 group-hover:text-brand-primary text-sm transition-colors">Flight Booking</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-gray-900">{{ number_format($booking->total_amount, 0) }} <span class="text-xs text-gray-500">BDT</span></p>
                                        <p class="text-[10px] text-gray-500 mt-0.5">{{ $booking->created_at->format('M d, Y') }}</p>
                                    </div>
                                </a>
                            @endforeach
                        @endif
                    </div>

                    {{-- Hotels Tab --}}
                    <div x-show="tab === 'hotels'" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-3">
                        @php
                            $hotelBookings = auth()->user()->hotelBookings()->with('property')->latest()->get();
                        @endphp

                        @if($hotelBookings->isEmpty())
                            <div class="py-12 text-center flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-hotel text-gray-300 text-2xl"></i>
                                </div>
                                <p class="text-gray-500 mb-4 text-sm font-medium">You have no hotel reservations.</p>
                            </div>
                        @else
                            @foreach($hotelBookings as $booking)
                                <a href="{{ route('my-bookings.show', $booking->id) }}" class="flex items-center justify-between p-4 rounded-2xl border border-gray-100 hover:border-brand-primary/30 transition-colors group bg-gray-50 hover:bg-white hover:shadow-sm">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-xl bg-gray-200 overflow-hidden shrink-0 shadow-inner">
                                            @if($booking->property && $booking->property->photos && count($booking->property->photos) > 0)
                                                <img src="{{ Storage::url($booking->property->photos[0]) }}" class="w-full h-full object-cover" alt="Hotel">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-100"><i class="fas fa-building"></i></div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $booking->status == 'confirmed' ? 'bg-green-100 text-green-700 border border-green-100' : ($booking->status == 'pending' ? 'bg-yellow-100 text-yellow-700 border border-yellow-100' : 'bg-gray-100 text-gray-700 border border-gray-200') }}">
                                                    {{ strtoupper($booking->status) }}
                                                </span>
                                            </div>
                                            <p class="font-bold text-gray-900 text-sm group-hover:text-brand-primary transition-colors line-clamp-1">{{ $booking->property->name ?? 'Hotel Stay' }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-gray-900">{{ number_format($booking->total_amount ?? 0, 0) }} <span class="text-xs text-gray-500">BDT</span></p>
                                        <p class="text-[10px] text-gray-500 mt-0.5">{{ \Carbon\Carbon::parse($booking->check_in)->format('M d') }} - {{ \Carbon\Carbon::parse($booking->check_out)->format('M d') }}</p>
                                    </div>
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Wallet & Offers --}}
        <div class="space-y-8 animate-slide-up" style="animation-delay: 0.3s;">
            
            {{-- Wallet Card --}}
            <div class="bg-gradient-to-br from-[#1C212B] to-[#12151C] rounded-3xl p-6 relative overflow-hidden shadow-xl border border-gray-800 h-[220px] flex flex-col justify-between">
                {{-- Decorative element --}}
                <div class="absolute -right-16 -top-16 w-48 h-48 bg-brand-primary rounded-full opacity-40 mix-blend-screen blur-3xl"></div>
                
                <div class="relative z-10 flex justify-between items-start">
                    <div class="w-12 h-8 bg-gradient-to-br from-[#E2B75A] to-[#C99C3D] rounded-lg shadow-sm flex items-center justify-center">
                        <div class="w-8 h-4 border border-white/20 rounded"></div>
                    </div>
                    <div class="text-white/80 font-bold tracking-tight text-sm">Bookdei Wallet</div>
                </div>

                <div class="relative z-10">
                    <p class="text-[#8993A4] text-xs font-medium mb-1 uppercase tracking-wider">Available balance</p>
                    <div class="flex items-end gap-1 text-white">
                        <span class="text-4xl font-bold tracking-tight">{{ number_format($walletBalance) }}</span>
                        <span class="text-xl font-bold text-gray-500 mb-1">.00 BDT</span>
                    </div>
                </div>

                <div class="relative z-10 flex gap-3 mt-2">
                    <button class="flex-1 bg-white/10 hover:bg-white/20 text-white text-[10px] font-bold uppercase tracking-wider py-2.5 rounded-xl transition-colors backdrop-blur-sm border border-white/10 flex items-center justify-center gap-2">
                        <i class="fas fa-plus"></i> Add Funds
                    </button>
                    <button class="flex-1 bg-white hover:bg-gray-100 text-brand-black text-[10px] font-bold uppercase tracking-wider py-2.5 rounded-xl transition-colors flex items-center justify-center gap-2">
                        <i class="fas fa-history"></i> History
                    </button>
                </div>
            </div>

            {{-- Special Offers --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-heading font-bold text-lg text-brand-black">Special Offers</h3>
                    <span class="text-[10px] font-bold text-white bg-brand-primary px-2 py-0.5 rounded-md">NEW</span>
                </div>
                
                <div class="space-y-4">
                    <!-- Offer 1 -->
                    <div class="relative rounded-2xl overflow-hidden group cursor-pointer h-32 shadow-sm border border-gray-100">
                        <img src="https://images.unsplash.com/photo-1540541338287-41700207dee6?q=80&w=2070&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" alt="Resort">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 p-4 w-full">
                            <span class="bg-white text-brand-primary text-[10px] font-bold px-2 py-0.5 rounded-md uppercase tracking-wider mb-1 inline-block shadow-sm">20% OFF</span>
                            <p class="text-white font-bold text-sm leading-tight drop-shadow-md">Summer getaway to Maldives</p>
                        </div>
                    </div>

                    <!-- Offer 2 -->
                    <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-2xl hover:bg-brand-primary/5 hover:border-brand-primary/30 border border-gray-100 transition-colors cursor-pointer group">
                        <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center shrink-0 shadow-inner group-hover:bg-brand-primary/10 group-hover:text-brand-primary">
                            <i class="fas fa-plane-arrival"></i>
                        </div>
                        <div>
                            <p class="font-bold text-sm text-gray-900 group-hover:text-brand-primary transition-colors">Fly to Dubai</p>
                            <p class="text-[11px] text-gray-500">Earn double points on Emirates</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chartLabels = @json($chartLabels ?? []);
            const spendingData = @json($spendingData ?? []);
            
            if(document.querySelector("#customerSpendChart")) {
                const options = {
                    series: [{
                        name: 'Total Spend',
                        data: spendingData
                    }],
                    chart: {
                        type: 'line',
                        height: 280,
                        toolbar: { show: false },
                        fontFamily: 'Inter, sans-serif',
                        dropShadow: {
                            enabled: true,
                            top: 4,
                            left: 0,
                            blur: 8,
                            color: '#10b981',
                            opacity: 0.2
                        }
                    },
                    colors: ['#10b981'],
                    stroke: { curve: 'smooth', width: 4 },
                    markers: {
                        size: 6,
                        colors: ['#fff'],
                        strokeColors: '#10b981',
                        strokeWidth: 3,
                        hover: { size: 8 }
                    },
                    dataLabels: { enabled: false },
                    xaxis: {
                        categories: chartLabels,
                        axisBorder: { show: false },
                        axisTicks: { show: false },
                        labels: { style: { colors: '#9CA3AF', fontSize: '11px' } }
                    },
                    yaxis: {
                        labels: { 
                            style: { colors: '#9CA3AF', fontSize: '11px' },
                            formatter: function(val) { return val > 0 ? val / 1000 + 'k BDT' : '0'; }
                        }
                    },
                    grid: {
                        borderColor: '#F3F4F6',
                        strokeDashArray: 4,
                        xaxis: { lines: { show: true } },
                        yaxis: { lines: { show: true } }
                    },
                    theme: { mode: 'light' },
                    tooltip: { theme: 'dark' },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shade: 'dark',
                            gradientToColors: ['#34d399'],
                            shadeIntensity: 1,
                            type: 'horizontal',
                            opacityFrom: 1,
                            opacityTo: 1,
                            stops: [0, 100]
                        }
                    }
                };
                new ApexCharts(document.querySelector("#customerSpendChart"), options).render();
            }
        });
    </script>
    @endpush
</x-customer-layout>
