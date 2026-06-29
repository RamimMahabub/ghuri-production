<x-pms-layout pageTitle="Payouts" pageSubtitle="Withdrawals, commissions & invoices">
    
    @php
        $pageTitle = 'Payouts';
        $pageSubtitle = 'Withdrawals, commissions & invoices';
    @endphp

    <div class="font-inter">
        
        <!-- Top Stats Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Left large card: Available for Withdrawal -->
            <div class="lg:col-span-2 bg-white rounded-3xl p-8 shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-shadow">
                <div class="absolute -right-6 -top-6 w-32 h-32 bg-red-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Available for withdrawal</p>
                    <h2 class="text-5xl font-bold text-gray-900 mb-2">৳{{ number_format($withdrawable) }}</h2>
                    <p class="text-sm text-gray-500 mb-6">From checked-out bookings · After commission</p>
                    
                    <div class="flex gap-4">
                        <button class="bg-[#D00E15] hover:bg-red-700 text-white font-medium py-2 px-6 rounded-lg transition-colors">
                            Request Withdrawal
                        </button>
                        <button class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-medium py-2 px-6 rounded-lg transition-colors flex items-center gap-2">
                            <i class="fas fa-file-export"></i> Export CSV
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right side column: Other stats -->
            <div class="flex flex-col gap-4 justify-between">
                <!-- Pending -->
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow group">
                    <div class="w-12 h-12 rounded-2xl bg-orange-50 group-hover:bg-orange-100 transition-colors flex items-center justify-center text-orange-500 shrink-0">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">৳{{ number_format($pending) }}</h3>
                        <p class="text-xs text-gray-500 font-medium">Pending (ongoing stays)</p>
                    </div>
                </div>

                <!-- Upcoming -->
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow group">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 group-hover:bg-blue-100 transition-colors flex items-center justify-center text-blue-500 shrink-0">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">৳{{ number_format($upcoming) }}</h3>
                        <p class="text-xs text-gray-500 font-medium">Upcoming (future)</p>
                    </div>
                </div>

                <!-- Total Paid Out -->
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow group">
                    <div class="w-12 h-12 rounded-2xl bg-green-50 group-hover:bg-green-100 transition-colors flex items-center justify-center text-green-500 shrink-0">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">৳{{ number_format($totalPaidOut) }}</h3>
                        <p class="text-xs text-gray-500 font-medium">Total Paid Out</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Banner -->
        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-8 flex items-center gap-3">
            <i class="fas fa-info-circle text-blue-500"></i>
            <p class="text-sm text-blue-800">
                Only payouts from <span class="font-bold">checked-out guests</span> are withdrawable. Ongoing and upcoming stays will become available after check-out.
            </p>
        </div>

        <!-- Filters / Table header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div class="relative w-full md:w-64">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" placeholder="Search payouts..." class="w-full bg-white border border-gray-200 text-sm rounded-full pl-9 pr-4 py-2 focus:outline-none focus:border-brand-primary focus:ring-1 focus:ring-brand-primary text-gray-700">
            </div>
            <div class="flex bg-gray-100 rounded-full p-1 border border-gray-200">
                <button class="px-4 py-1.5 text-xs font-semibold rounded-full bg-white text-gray-900 shadow-sm">All</button>
                <button class="px-4 py-1.5 text-xs font-semibold rounded-full text-gray-500 hover:text-gray-900 transition-colors">Withdrawable</button>
                <button class="px-4 py-1.5 text-xs font-semibold rounded-full text-gray-500 hover:text-gray-900 transition-colors">Ongoing</button>
                <button class="px-4 py-1.5 text-xs font-semibold rounded-full text-gray-500 hover:text-gray-900 transition-colors">Upcoming</button>
                <button class="px-4 py-1.5 text-xs font-semibold rounded-full text-gray-500 hover:text-gray-900 transition-colors">Paid Out</button>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white border border-gray-100 shadow-sm rounded-3xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="py-4 px-6 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Guest</th>
                            <th class="py-4 px-6 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Property</th>
                            <th class="py-4 px-6 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="py-4 px-6 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Commission</th>
                            <th class="py-4 px-6 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Payout</th>
                            <th class="py-4 px-6 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="py-4 px-6 text-[11px] font-bold text-gray-500 uppercase tracking-wider text-right">Invoice</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($bookings as $booking)
                            <tr class="hover:bg-gray-50 transition-colors group">
                                <td class="py-4 px-6">
                                    <div class="font-bold text-gray-900">{{ $booking->guest->name ?? 'Guest' }}</div>
                                    <div class="text-xs text-gray-500 font-medium">{{ $booking->booking_ref }}</div>
                                </td>
                                <td class="py-4 px-6 font-medium text-gray-600">{{ $booking->property->name }}</td>
                                <td class="py-4 px-6 font-medium text-gray-600">৳{{ number_format($booking->total) }}</td>
                                <td class="py-4 px-6 font-medium text-gray-500">৳{{ number_format($booking->commission_amount) }}</td>
                                <td class="py-4 px-6 font-bold text-gray-900">৳{{ number_format($booking->total - $booking->commission_amount) }}</td>
                                <td class="py-4 px-6">
                                    @if($booking->payout_status === 'Paid Out')
                                        <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                            <div class="w-1.5 h-1.5 rounded-full bg-gray-400"></div>
                                            Paid Out
                                        </span>
                                    @elseif($booking->payout_status === 'Withdrawable')
                                        <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-bold bg-green-50 text-green-600 border border-green-200">
                                            <div class="w-1.5 h-1.5 rounded-full bg-green-500"></div>
                                            Withdrawable
                                        </span>
                                    @elseif($booking->payout_status === 'Ongoing')
                                        <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-bold bg-orange-50 text-orange-600 border border-orange-200">
                                            <div class="w-1.5 h-1.5 rounded-full bg-orange-500"></div>
                                            Ongoing
                                        </span>
                                    @elseif($booking->payout_status === 'Upcoming')
                                        <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-bold bg-blue-50 text-blue-600 border border-blue-200">
                                            <div class="w-1.5 h-1.5 rounded-full bg-blue-500"></div>
                                            Upcoming
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                            <div class="w-1.5 h-1.5 rounded-full bg-gray-400"></div>
                                            {{ $booking->payout_status }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <button class="text-gray-400 hover:text-brand-primary transition-colors text-xs font-medium flex items-center justify-end gap-1.5 ml-auto">
                                        <i class="fas fa-file-invoice"></i> Download
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-wallet text-gray-300 text-2xl"></i>
                                    </div>
                                    <p class="text-gray-500 font-medium text-sm">No payouts found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-pms-layout>
