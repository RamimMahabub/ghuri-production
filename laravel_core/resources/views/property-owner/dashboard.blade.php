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
                <h3 class="text-3xl font-bold text-brand-black tracking-tight mb-1">${{ number_format($totalRevenue ?? 0, 0) }}</h3>
                <p class="text-sm font-medium text-gray-500">Total Revenue</p>
                <p class="text-xs text-gray-400 mt-2">${{ number_format($monthRevenue ?? 0, 0) }} MTD</p>
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
                <div class="relative h-72 w-full">
                    <canvas id="revenueChart"></canvas>
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
                        <span class="text-4xl font-bold tracking-tight">${{ number_format($totalRevenue ?? 0, 0) }}</span>
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

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('revenueChart');
            if(ctx) {
                new Chart(ctx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: @json($chartLabels ?? []),
                        datasets: [{
                            label: 'Revenue ($)',
                            data: @json($chartData ?? []),
                            borderColor: '#d00e15',
                            backgroundColor: 'rgba(208, 14, 21, 0.05)',
                            borderWidth: 3,
                            pointBackgroundColor: '#FFFFFF',
                            pointBorderColor: '#d00e15',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#19100F',
                                padding: 12,
                                titleFont: { family: 'Inter', size: 13 },
                                bodyFont: { family: 'Inter', size: 14, weight: 'bold' },
                                displayColors: false,
                                cornerRadius: 8,
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false, drawBorder: false },
                                ticks: { font: { family: 'Inter', size: 11 }, color: '#9CA3AF' }
                            },
                            y: {
                                border: { display: false },
                                grid: { color: '#F3F4F6', drawBorder: false },
                                ticks: { 
                                    font: { family: 'Inter', size: 11 }, 
                                    color: '#9CA3AF',
                                    callback: function(value) { return '$' + value; },
                                    stepSize: 50 
                                }
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index',
                        },
                    }
                });
            }
        });
    </script>
    @endpush

</x-pms-layout>
