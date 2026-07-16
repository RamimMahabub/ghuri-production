<x-pms-layout pageTitle="Dashboard" pageSubtitle="Overview of your property performance">

    {{-- KPI Cards (Premium Glassmorphic Design) --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Bookings -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl shadow-inner">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <span class="bg-green-100 text-green-700 text-xs font-bold px-2.5 py-1 rounded-full flex items-center gap-1">
                        <i class="fas fa-arrow-up text-[10px]"></i> 12%
                    </span>
                </div>
                <h3 class="text-3xl font-bold text-brand-black tracking-tight mb-1">{{ $totalBookings ?? 0 }}</h3>
                <p class="text-sm font-medium text-gray-500">Total Bookings</p>
                <p class="text-xs text-gray-400 mt-2">{{ $monthBookings ?? 0 }} this month</p>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-green-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-green-100 text-green-600 flex items-center justify-center text-xl shadow-inner">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <span class="bg-green-100 text-green-700 text-xs font-bold px-2.5 py-1 rounded-full flex items-center gap-1">
                        <i class="fas fa-arrow-up text-[10px]"></i> 8%
                    </span>
                </div>
                <h3 class="text-3xl font-bold text-brand-black tracking-tight mb-1">{{ \App\Helpers\Currency::format($totalRevenue ?? 0) }}</h3>
                <p class="text-sm font-medium text-gray-500">Total Revenue</p>
                <p class="text-xs text-gray-400 mt-2">{{ \App\Helpers\Currency::format($monthRevenue ?? 0) }} MTD</p>
            </div>
        </div>

        <!-- Active Properties -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-brand-primary/5 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-brand-primary/10 text-brand-primary flex items-center justify-center text-xl shadow-inner">
                        <i class="fas fa-hotel"></i>
                    </div>
                </div>
                <h3 class="text-3xl font-bold text-brand-black tracking-tight mb-1">{{ $properties->count() ?? 0 }}</h3>
                <p class="text-sm font-medium text-gray-500">Active Properties</p>
            </div>
        </div>

        <!-- Pending Action -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-yellow-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-yellow-100 text-yellow-600 flex items-center justify-center text-xl shadow-inner">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <h3 class="text-3xl font-bold text-brand-black tracking-tight mb-1">{{ $pendingCount ?? 0 }}</h3>
                <p class="text-sm font-medium text-gray-500">Pending Confirmation</p>
                @if(($pendingCount ?? 0) > 0)
                    <a href="{{ route('property-owner.bookings.index', ['status' => 'pending']) }}" class="text-xs font-bold text-brand-primary hover:underline mt-2 inline-block">Review now →</a>
                @endif
            </div>
        </div>
    </div>

    {{-- Main Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Left Column: Operations & Charts --}}
        <div class="lg:col-span-2 space-y-8">
            
            {{-- Revenue Chart --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="font-heading font-bold text-lg text-brand-black">Revenue Analytics</h3>
                        <p class="text-xs text-gray-500">Performance over the last 7 days</p>
                    </div>
                    <select class="text-sm border-gray-200 rounded-lg text-gray-600 shadow-sm focus:ring-brand-primary focus:border-brand-primary">
                        <option>Last 7 Days</option>
                        <option>This Month</option>
                        <option>Last Month</option>
                    </select>
                </div>
                <div class="relative w-full">
                    <div id="revenueChart" style="min-height: 280px;"></div>
                </div>
            </div>

            {{-- Bookings Chart --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="font-heading font-bold text-lg text-brand-black">Booking Volume</h3>
                        <p class="text-xs text-gray-500">Total confirmed bookings</p>
                    </div>
                </div>
                <div class="relative w-full">
                    <div id="bookingsChart" style="min-height: 280px;"></div>
                </div>
            </div>

            {{-- Action Center (Upcoming Check-ins / Check-outs) --}}
            <div x-data="{ tab: 'checkins' }" class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-heading font-bold text-lg text-brand-black">Front Desk</h3>
                    <div class="flex bg-gray-100 p-1 rounded-xl">
                        <button @click="tab = 'checkins'" :class="tab === 'checkins' ? 'bg-white shadow-sm font-semibold text-gray-900' : 'text-gray-500 hover:text-gray-700'" class="px-4 py-1.5 text-sm rounded-lg transition-all">Arrivals <span class="bg-blue-100 text-blue-700 ml-1 px-1.5 rounded-full text-[10px]">{{ $upcomingCheckins->count() ?? 0 }}</span></button>
                        <button @click="tab = 'checkouts'" :class="tab === 'checkouts' ? 'bg-white shadow-sm font-semibold text-gray-900' : 'text-gray-500 hover:text-gray-700'" class="px-4 py-1.5 text-sm rounded-lg transition-all">Departures <span class="bg-gray-200 text-gray-700 ml-1 px-1.5 rounded-full text-[10px]">{{ $upcomingCheckouts->count() ?? 0 }}</span></button>
                    </div>
                </div>

                {{-- Arrivals Tab --}}
                <div x-show="tab === 'checkins'" class="space-y-3">
                    @forelse($upcomingCheckins ?? [] as $booking)
                        <div class="flex items-center justify-between p-4 rounded-2xl border border-gray-100 hover:border-brand-primary/30 transition-colors group">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold shadow-inner">
                                    {{ substr($booking->guest->name ?? 'G', 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900">{{ $booking->guest->name ?? 'Guest Name' }}</p>
                                    <p class="text-xs text-gray-500">{{ $booking->roomType->name ?? 'Standard Room' }} • {{ $booking->nights }} nights</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-gray-900">{{ $booking->check_in->format('M d') }}</p>
                                <span class="text-[10px] font-bold text-yellow-600 bg-yellow-50 px-2 py-0.5 rounded-full border border-yellow-100">Expected</span>
                            </div>
                        </div>
                    @empty
                        <div class="py-10 text-center">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-calendar-check text-gray-300 text-2xl"></i>
                            </div>
                            <p class="text-gray-500 text-sm font-medium">No arrivals scheduled for the next 7 days.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Departures Tab --}}
                <div x-show="tab === 'checkouts'" style="display: none;" class="space-y-3">
                    @forelse($upcomingCheckouts ?? [] as $booking)
                        <div class="flex items-center justify-between p-4 rounded-2xl border border-gray-100 hover:border-brand-primary/30 transition-colors group">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center font-bold shadow-inner">
                                    {{ substr($booking->guest->name ?? 'G', 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900">{{ $booking->guest->name ?? 'Guest Name' }}</p>
                                    <p class="text-xs text-gray-500">{{ $booking->roomType->name ?? 'Standard Room' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-gray-900">{{ $booking->check_out->format('M d') }}</p>
                                <span class="text-[10px] font-bold text-green-600 bg-green-50 px-2 py-0.5 rounded-full border border-green-100">Checked In</span>
                            </div>
                        </div>
                    @empty
                        <div class="py-10 text-center">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fas fa-door-open text-gray-300 text-2xl"></i>
                            </div>
                            <p class="text-gray-500 text-sm font-medium">No departures scheduled for the next 7 days.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- Right Column: Wallet, Actions, Reviews --}}
        <div class="space-y-8">
            
            {{-- Wallet Card --}}
            <div class="bg-gradient-to-br from-[#1C212B] to-[#12151C] rounded-3xl p-6 relative overflow-hidden shadow-xl border border-gray-800 h-[220px] flex flex-col justify-between">
                {{-- Decorative element --}}
                <div class="absolute -right-16 -top-16 w-48 h-48 bg-brand-primary rounded-full opacity-40 mix-blend-screen blur-3xl"></div>
                
                <div class="relative z-10 flex justify-between items-start">
                    <div class="w-12 h-8 bg-gradient-to-br from-[#E2B75A] to-[#C99C3D] rounded-lg shadow-sm flex items-center justify-center">
                        <div class="w-8 h-4 border border-white/20 rounded"></div>
                    </div>
                    <div class="text-white/80 font-bold tracking-tight text-sm">Payout Account</div>
                </div>

                <div class="relative z-10">
                    <p class="text-[#8993A4] text-xs font-medium mb-1 uppercase tracking-wider">Available balance</p>
                    <div class="flex items-end gap-1 text-white">
                        <span class="text-4xl font-bold tracking-tight">{{ \App\Helpers\Currency::format($totalRevenue ?? 0) }}</span>
                        <span class="text-xl font-bold text-gray-500 mb-1">.00</span>
                    </div>
                </div>

                <div class="relative z-10 flex justify-between items-center mt-2">
                    <div class="text-[#8993A4] font-mono text-sm tracking-widest flex items-center gap-2">
                        <span class="text-lg leading-none mt-1">••••</span> 4012
                    </div>
                    <button class="bg-white/10 hover:bg-white/20 text-white text-[10px] font-bold uppercase tracking-wider px-3 py-1.5 rounded-lg transition-colors backdrop-blur-sm border border-white/10">
                        Withdraw
                    </button>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                <h3 class="font-heading font-bold text-lg text-brand-black mb-4">Quick Actions</h3>
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('property-owner.hotels.create') }}" class="flex flex-col items-center justify-center p-4 bg-gray-50 hover:bg-brand-primary/5 hover:text-brand-primary rounded-2xl border border-gray-100 hover:border-brand-primary/30 transition-all text-gray-700 group">
                        <i class="fas fa-plus text-xl mb-2 text-gray-400 group-hover:text-brand-primary"></i>
                        <span class="text-xs font-bold text-center">Add Property</span>
                    </a>
                    <a href="{{ route('property-owner.bookings.create') }}" class="flex flex-col items-center justify-center p-4 bg-gray-50 hover:bg-brand-primary/5 hover:text-brand-primary rounded-2xl border border-gray-100 hover:border-brand-primary/30 transition-all text-gray-700 group">
                        <i class="fas fa-calendar-plus text-xl mb-2 text-gray-400 group-hover:text-brand-primary"></i>
                        <span class="text-xs font-bold text-center">Manual Booking</span>
                    </a>
                </div>
            </div>

            {{-- Recent Reviews --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-heading font-bold text-lg text-brand-black">Recent Reviews</h3>
                    <a href="{{ route('property-owner.reviews.index') }}" class="text-xs font-bold text-brand-primary hover:underline">View all</a>
                </div>
                <div class="space-y-4">
                    @forelse($recentReviews ?? [] as $review)
                        <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-brand-primary/10 text-brand-primary flex items-center justify-center text-xs font-bold">
                                        {{ substr($review->guest->name ?? 'G', 0, 1) }}
                                    </div>
                                    <p class="text-xs font-bold text-gray-900">{{ $review->guest->name ?? 'Guest' }}</p>
                                </div>
                                <div class="bg-brand-primary text-white text-[10px] font-bold px-2 py-0.5 rounded-md flex items-center gap-1">
                                    {{ number_format($review->overall_score, 1) }} <i class="fas fa-star text-[8px]"></i>
                                </div>
                            </div>
                            @if($review->comment)
                                <p class="text-xs text-gray-600 italic line-clamp-2">"{{ $review->comment }}"</p>
                            @endif
                            <p class="text-[10px] text-gray-400 mt-2">{{ $review->created_at->diffForHumans() }}</p>
                        </div>
                    @empty
                        <div class="py-6 text-center">
                            <i class="fas fa-star text-gray-200 text-3xl mb-2"></i>
                            <p class="text-gray-500 text-xs font-medium">No recent reviews.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    {{-- Property Performance --}}
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 mt-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="font-heading font-bold text-lg text-brand-black">Property Performance</h3>
                <p class="text-xs text-gray-500">Revenue and booking breakdown by property</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-[11px] uppercase tracking-wider text-gray-400 border-b border-gray-100">
                        <th class="pb-3 font-semibold pl-2">Property</th>
                        <th class="pb-3 font-semibold text-right">Total Revenue</th>
                        <th class="pb-3 font-semibold text-center">Active Bookings</th>
                        <th class="pb-3 font-semibold text-right pr-2">Action</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($properties as $property)
                        <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                            <td class="py-4 pl-2">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-brand-primary/5 text-brand-primary flex items-center justify-center font-bold text-lg shadow-inner border border-brand-primary/10">
                                        <i class="fas fa-hotel"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900">{{ $property->name }}</p>
                                        <p class="text-[11px] text-gray-500"><i class="fas fa-map-marker-alt text-brand-primary/60"></i> {{ $property->city }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 text-right">
                                <span class="font-bold text-gray-900 text-sm">{{ \App\Helpers\Currency::format($property->total_revenue ?? 0) }}</span>
                            </td>
                            <td class="py-4 text-center">
                                <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-lg text-xs font-bold border border-gray-200 shadow-sm">
                                    {{ $property->active_bookings_count ?? 0 }}
                                </span>
                            </td>
                            <td class="py-4 text-right pr-2">
                                <a href="{{ route('property-owner.hotels.edit', $property->id) }}" class="text-brand-primary hover:text-brand-primary/80 text-[11px] font-bold uppercase tracking-wider bg-brand-primary/5 px-3 py-1.5 rounded-lg border border-brand-primary/20 transition-all hover:bg-brand-primary hover:text-white">
                                    Manage
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-gray-500 text-sm">No properties found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const chartLabels = @json($chartLabels ?? []);
            const chartData = @json($chartData ?? []);
            const bookingsData = @json($bookingsData ?? []);

            // Futuristic Revenue Line/Area Chart
            const revOptions = {
                series: [{
                    name: 'Revenue',
                    data: chartData
                }],
                chart: {
                    type: 'area',
                    height: 300,
                    toolbar: { show: false },
                    fontFamily: 'Inter, sans-serif',
                    dropShadow: {
                        enabled: true,
                        top: 4,
                        left: 0,
                        blur: 8,
                        color: '#d00e15',
                        opacity: 0.15
                    }
                },
                colors: ['#d00e15'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.4,
                        opacityTo: 0.05,
                        stops: [0, 100]
                    }
                },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 3 },
                xaxis: {
                    categories: chartLabels,
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: { style: { colors: '#9CA3AF', fontSize: '11px' } }
                },
                yaxis: {
                    labels: { 
                        style: { colors: '#9CA3AF', fontSize: '11px' },
                        formatter: function(val) { return '৳ ' + val; }
                    }
                },
                grid: {
                    borderColor: '#F3F4F6',
                    strokeDashArray: 4,
                    yaxis: { lines: { show: true } }
                },
                theme: { mode: 'light' },
                tooltip: { theme: 'dark' }
            };
            new ApexCharts(document.querySelector("#revenueChart"), revOptions).render();

            // Futuristic Bookings Bar Chart
            const bookOptions = {
                series: [{
                    name: 'Bookings',
                    data: bookingsData
                }],
                chart: {
                    type: 'bar',
                    height: 300,
                    toolbar: { show: false },
                    fontFamily: 'Inter, sans-serif'
                },
                colors: ['#4f46e5'],
                plotOptions: {
                    bar: {
                        borderRadius: 6,
                        columnWidth: '30%',
                    }
                },
                dataLabels: { enabled: false },
                xaxis: {
                    categories: chartLabels,
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: { style: { colors: '#9CA3AF', fontSize: '11px' } }
                },
                yaxis: {
                    labels: { style: { colors: '#9CA3AF', fontSize: '11px' } }
                },
                grid: {
                    borderColor: '#F3F4F6',
                    strokeDashArray: 4,
                    yaxis: { lines: { show: true } }
                },
                theme: { mode: 'light' },
                tooltip: { theme: 'dark' }
            };
            new ApexCharts(document.querySelector("#bookingsChart"), bookOptions).render();
        });
    </script>
    @endpush

</x-pms-layout>
