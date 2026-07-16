<x-pms-layout pageTitle="Payouts" pageSubtitle="Withdrawals, commissions & invoices">
    
    @php
        $pageTitle = 'Payouts';
        $pageSubtitle = 'Withdrawals, commissions & invoices';
    @endphp

    <div class="font-inter">
        
        <!-- Payment Method Section -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 mb-8 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-500">
                    <i class="fas fa-university text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Payment Method</h3>
                    <div class="flex flex-col gap-1 mt-1">
                        @if(!empty($user->bank_details))
                            <p class="text-sm text-gray-500"><i class="fas fa-university w-4 text-center"></i> Bank Transfer • {{ $user->bank_details['bank_name'] }} (Ending in {{ substr($user->bank_details['account_number'], -4) }})</p>
                        @endif
                        @if(!empty($user->card_details))
                            <p class="text-sm text-gray-500"><i class="fas fa-credit-card w-4 text-center"></i> Card • {{ $user->card_details['card_number'] }}</p>
                        @endif
                        @if(!empty($user->mfs_details))
                            <p class="text-sm text-gray-500"><i class="fas fa-mobile-alt w-4 text-center"></i> Mobile Banking • {{ ucfirst($user->mfs_details['mfs_provider']) }} ({{ $user->mfs_details['mfs_number'] }})</p>
                        @endif
                        @if(empty($user->bank_details) && empty($user->card_details) && empty($user->mfs_details))
                            <p class="text-sm text-gray-500">No payment method added. Please add one to receive payouts.</p>
                        @endif
                    </div>
                </div>
            </div>
            <button onclick="document.getElementById('paymentMethodModal').classList.remove('hidden')" class="bg-indigo-50 text-indigo-600 hover:bg-indigo-100 font-medium py-2 px-6 rounded-lg transition-colors text-sm whitespace-nowrap">
                {{ (!empty($user->bank_details) || !empty($user->card_details) || !empty($user->mfs_details)) ? 'Update Method' : 'Add Method' }}
            </button>
        </div>

        <!-- Top Stats Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Left large card: Available for Withdrawal -->
            <div class="lg:col-span-2 bg-white rounded-3xl p-8 shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-shadow">
                <div class="absolute -right-6 -top-6 w-32 h-32 bg-red-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Available for withdrawal</p>
                    <h2 class="text-5xl font-bold text-gray-900 mb-2">{{ \App\Helpers\Currency::format($withdrawable) }}</h2>
                    <p class="text-sm text-gray-500 mb-6">From checked-out bookings · After commission</p>
                    
                    <div class="flex gap-4">
                        <button type="button" onclick="document.getElementById('withdrawModal').classList.remove('hidden')" @if($withdrawable <= 0) disabled @endif class="bg-[#D00E15] hover:bg-red-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-medium py-2 px-6 rounded-lg transition-colors">
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
                        <h3 class="text-xl font-bold text-gray-900">{{ \App\Helpers\Currency::format($pending) }}</h3>
                        <p class="text-xs text-gray-500 font-medium">Pending (ongoing stays)</p>
                    </div>
                </div>

                <!-- Upcoming -->
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow group">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 group-hover:bg-blue-100 transition-colors flex items-center justify-center text-blue-500 shrink-0">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ \App\Helpers\Currency::format($upcoming) }}</h3>
                        <p class="text-xs text-gray-500 font-medium">Upcoming (future)</p>
                    </div>
                </div>

                <!-- Total Paid Out -->
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow group">
                    <div class="w-12 h-12 rounded-2xl bg-green-50 group-hover:bg-green-100 transition-colors flex items-center justify-center text-green-500 shrink-0">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ \App\Helpers\Currency::format($totalPaidOut) }}</h3>
                        <p class="text-xs text-gray-500 font-medium">Total Paid Out</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Balances by Property -->
        <div class="bg-white border border-gray-100 shadow-sm rounded-3xl p-6 mb-8">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Balances by Property</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="py-3 px-4 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Property</th>
                            <th class="py-3 px-4 text-[11px] font-bold text-gray-500 uppercase tracking-wider text-right">Withdrawable</th>
                            <th class="py-3 px-4 text-[11px] font-bold text-gray-500 uppercase tracking-wider text-right">Pending</th>
                            <th class="py-3 px-4 text-[11px] font-bold text-gray-500 uppercase tracking-wider text-right">Total Paid Out</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @foreach($propertyBalances as $balance)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-4 px-4 font-bold text-gray-900">{{ $balance['name'] }}</td>
                                <td class="py-4 px-4 text-right font-bold text-green-600">{{ \App\Helpers\Currency::format($balance['withdrawable']) }}</td>
                                <td class="py-4 px-4 text-right font-medium text-orange-500">{{ \App\Helpers\Currency::format($balance['pending']) }}</td>
                                <td class="py-4 px-4 text-right font-medium text-gray-900">{{ \App\Helpers\Currency::format($balance['paid_out']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
                                <td class="py-4 px-6 font-medium text-gray-600">{{ \App\Helpers\Currency::format($booking->total) }}</td>
                                <td class="py-4 px-6 font-medium text-gray-500">{{ \App\Helpers\Currency::format($booking->commission_amount) }}</td>
                                <td class="py-4 px-6 font-bold text-gray-900">{{ \App\Helpers\Currency::format($booking->total - $booking->commission_amount) }}</td>
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
                                    @if($booking->payout_id)
                                        <div class="flex flex-col items-end gap-1.5">
                                            <a href="{{ route('property-owner.payouts.invoice', $booking->payout_id) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 transition-colors text-[11px] font-bold flex items-center justify-end gap-1.5">
                                                <i class="fas fa-file-invoice"></i> Invoice
                                            </a>
                                            @if($booking->payout && $booking->payout->payment_proof)
                                                <a href="{{ \Illuminate\Support\Facades\Storage::url($booking->payout->payment_proof) }}" target="_blank" class="text-gray-500 hover:text-gray-800 transition-colors text-[11px] font-bold flex items-center justify-end gap-1.5">
                                                    <i class="fas fa-receipt"></i> Proof
                                                </a>
                                            @endif
                                            @if($booking->payout && $booking->payout->bank_invoice)
                                                <a href="{{ \Illuminate\Support\Facades\Storage::url($booking->payout->bank_invoice) }}" target="_blank" class="text-gray-500 hover:text-gray-800 transition-colors text-[11px] font-bold flex items-center justify-end gap-1.5">
                                                    <i class="fas fa-file-alt"></i> Bank Inv.
                                                </a>
                                            @endif
                                        </div>
                                    @endif
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

    <!-- Payment Method Modal -->
    <div id="paymentMethodModal" x-data="{ methodType: 'bank' }" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center font-inter">
        <div class="bg-white rounded-3xl p-8 max-w-md w-full shadow-2xl relative max-h-[90vh] overflow-y-auto">
            <button type="button" onclick="document.getElementById('paymentMethodModal').classList.add('hidden')" class="absolute top-6 right-6 text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Payment Method</h2>
            
            <form action="{{ route('property-owner.payouts.payment-method') }}" method="POST">
                @csrf
                
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Method Type</label>
                    <div class="flex gap-4">
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="payment_type" value="bank" class="sr-only" x-model="methodType">
                            <div :class="methodType === 'bank' ? 'border-brand-primary bg-blue-50 text-brand-primary' : 'border-gray-200 text-gray-500'" class="p-4 border rounded-xl text-center font-medium transition-colors">
                                <i class="fas fa-university block text-2xl mb-2"></i>
                                Bank Transfer
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="payment_type" value="card" class="sr-only" x-model="methodType">
                            <div :class="methodType === 'card' ? 'border-brand-primary bg-blue-50 text-brand-primary' : 'border-gray-200 text-gray-500'" class="p-4 border rounded-xl text-center font-medium transition-colors">
                                <i class="fas fa-credit-card block text-2xl mb-2"></i>
                                Card
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="payment_type" value="mfs" class="sr-only" x-model="methodType">
                            <div :class="methodType === 'mfs' ? 'border-brand-primary bg-blue-50 text-brand-primary' : 'border-gray-200 text-gray-500'" class="p-4 border rounded-xl text-center font-medium transition-colors">
                                <i class="fas fa-mobile-alt block text-2xl mb-2"></i>
                                Mobile Banking
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Bank Fields -->
                <div id="bankFields" x-show="methodType === 'bank'" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bank Name</label>
                        <input type="text" name="bank_name" value="{{ $user->bank_details['bank_name'] ?? '' }}" class="w-full border-gray-300 rounded-lg focus:ring-brand-primary focus:border-brand-primary text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Account Name</label>
                        <input type="text" name="account_name" value="{{ $user->bank_details['account_name'] ?? '' }}" class="w-full border-gray-300 rounded-lg focus:ring-brand-primary focus:border-brand-primary text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Account Number</label>
                        <input type="text" name="account_number" value="{{ $user->bank_details['account_number'] ?? '' }}" class="w-full border-gray-300 rounded-lg focus:ring-brand-primary focus:border-brand-primary text-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Branch Name</label>
                            <input type="text" name="branch" value="{{ $user->bank_details['branch'] ?? '' }}" class="w-full border-gray-300 rounded-lg focus:ring-brand-primary focus:border-brand-primary text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Routing Number</label>
                            <input type="text" name="routing_number" value="{{ $user->bank_details['routing_number'] ?? '' }}" class="w-full border-gray-300 rounded-lg focus:ring-brand-primary focus:border-brand-primary text-sm">
                        </div>
                    </div>
                </div>

                <!-- Card Fields -->
                <div id="cardFields" x-show="methodType === 'card'" style="display: none;" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cardholder Name</label>
                        <input type="text" name="cardholder_name" value="{{ $user->card_details['cardholder_name'] ?? '' }}" class="w-full border-gray-300 rounded-lg focus:ring-brand-primary focus:border-brand-primary text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Card Number</label>
                        <input type="text" name="card_number" value="{{ $user->card_details['card_number'] ?? '' }}" class="w-full border-gray-300 rounded-lg focus:ring-brand-primary focus:border-brand-primary text-sm" placeholder="XXXX XXXX XXXX XXXX">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Month</label>
                            <input type="text" name="expiry_month" value="{{ $user->card_details['expiry_month'] ?? '' }}" class="w-full border-gray-300 rounded-lg focus:ring-brand-primary focus:border-brand-primary text-sm" placeholder="MM">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Year</label>
                            <input type="text" name="expiry_year" value="{{ $user->card_details['expiry_year'] ?? '' }}" class="w-full border-gray-300 rounded-lg focus:ring-brand-primary focus:border-brand-primary text-sm" placeholder="YY">
                        </div>
                    </div>
                </div>

                <!-- MFS Fields -->
                <div id="mfsFields" x-show="methodType === 'mfs'" style="display: none;" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Provider</label>
                        <select name="mfs_provider" class="w-full border-gray-300 rounded-lg focus:ring-brand-primary focus:border-brand-primary text-sm">
                            <option value="bkash" {{ ($user->mfs_details['mfs_provider'] ?? '') === 'bkash' ? 'selected' : '' }}>bKash</option>
                            <option value="nagad" {{ ($user->mfs_details['mfs_provider'] ?? '') === 'nagad' ? 'selected' : '' }}>Nagad</option>
                            <option value="rocket" {{ ($user->mfs_details['mfs_provider'] ?? '') === 'rocket' ? 'selected' : '' }}>Rocket</option>
                            <option value="upay" {{ ($user->mfs_details['mfs_provider'] ?? '') === 'upay' ? 'selected' : '' }}>Upay</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Account Number</label>
                        <input type="text" name="mfs_number" value="{{ $user->mfs_details['mfs_number'] ?? '' }}" class="w-full border-gray-300 rounded-lg focus:ring-brand-primary focus:border-brand-primary text-sm" placeholder="e.g., 01XXXXXXXXX">
                    </div>
                </div>

                <div class="mt-8">
                    <button type="submit" class="w-full bg-brand-primary hover:bg-brand-primary/90 text-white font-bold py-3 px-4 rounded-xl transition-colors">
                        Save Payment Method
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Withdraw Modal -->
    <div id="withdrawModal" class="fixed inset-0 hidden flex items-center justify-center font-inter" style="z-index: 50; background-color: rgba(0,0,0,0.5); backdrop-filter: blur(4px);">
        <div class="bg-white rounded-3xl p-8 w-full shadow-2xl relative" style="max-width: 450px; max-height: 90vh; overflow-y: auto;">
            <button onclick="document.getElementById('withdrawModal').classList.add('hidden')" class="absolute top-6 right-6 text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Request Withdrawal</h2>
            <p class="text-sm text-gray-500 mb-6">Enter the amount you wish to withdraw and select your payment method.</p>
            
            <form id="withdrawForm" action="{{ route('property-owner.payouts.request') }}" method="POST" class="flex flex-col">
                @csrf
                
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Amount (Available: {{ \App\Helpers\Currency::format($withdrawable) }})</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 font-bold text-gray-500">৳</span>
                        <input type="number" name="amount" id="withdrawAmount" max="{{ $withdrawableInUserCurrency }}" min="1" step="0.01" required class="w-full pl-8 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-brand-primary focus:border-brand-primary font-bold text-lg text-gray-900 outline-none transition-all">
                    </div>
                </div>

                <div class="mb-8">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Payment Method</label>
                    <div class="space-y-3">
                        @if(!empty($user->bank_details))
                        <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:border-brand-primary transition-colors bg-white">
                            <input type="radio" name="payment_method" value="bank" class="text-brand-primary focus:ring-brand-primary accent-brand-primary" style="width: 16px; height: 16px;" checked>
                            <div class="flex-1">
                                <p class="font-bold text-gray-900">Bank Transfer</p>
                                <p class="text-xs text-gray-500">{{ $user->bank_details['bank_name'] }} (Ending in {{ substr($user->bank_details['account_number'], -4) }})</p>
                            </div>
                        </label>
                        @endif

                        @if(!empty($user->card_details))
                        <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:border-brand-primary transition-colors bg-white">
                            <input type="radio" name="payment_method" value="card" class="text-brand-primary focus:ring-brand-primary accent-brand-primary" style="width: 16px; height: 16px;" {{ empty($user->bank_details) ? 'checked' : '' }}>
                            <div class="flex-1">
                                <p class="font-bold text-gray-900">Card Payment</p>
                                <p class="text-xs text-gray-500">{{ $user->card_details['card_number'] }}</p>
                            </div>
                        </label>
                        @endif

                        @if(!empty($user->mfs_details))
                        <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:border-brand-primary transition-colors bg-white">
                            <input type="radio" name="payment_method" value="mfs" class="text-brand-primary focus:ring-brand-primary accent-brand-primary" style="width: 16px; height: 16px;" {{ empty($user->bank_details) && empty($user->card_details) ? 'checked' : '' }}>
                            <div class="flex-1">
                                <p class="font-bold text-gray-900">Mobile Banking ({{ ucfirst($user->mfs_details['mfs_provider']) }})</p>
                                <p class="text-xs text-gray-500">{{ $user->mfs_details['mfs_number'] }}</p>
                            </div>
                        </label>
                        @endif

                        @if(empty($user->bank_details) && empty($user->card_details) && empty($user->mfs_details))
                        <div class="p-4 bg-orange-50 border border-orange-100 rounded-xl text-sm text-orange-800 flex items-center gap-2">
                            <i class="fas fa-exclamation-triangle"></i>
                            Please add a payment method first.
                        </div>
                        @endif
                    </div>
                </div>

                <button type="button" onclick="showConfirmDialog()" class="w-full bg-[#D00E15] hover:bg-red-700 text-white font-bold py-3 px-4 rounded-xl transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed">
                    Proceed
                </button>
            </form>
        </div>
    </div>

    <!-- Confirm Card -->
    <div id="confirmDialog" class="fixed inset-0 hidden flex items-center justify-center font-inter transition-opacity duration-200" style="z-index: 60; background-color: rgba(0,0,0,0.6); backdrop-filter: blur(4px);">
        <div class="bg-white rounded-3xl p-8 w-full shadow-2xl text-center transform transition-all duration-200" style="max-width: 400px; transform: scale(0.95); opacity: 0;" id="confirmDialogCard">
            <div class="text-[#D00E15] rounded-full flex items-center justify-center mx-auto mb-4 shadow-inner" style="width: 64px; height: 64px; background-color: #FEF2F2; font-size: 24px;">
                <i class="fas fa-question-circle"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Confirm Withdrawal</h3>
            <p class="text-gray-500 text-sm mb-8">Are you sure you want to withdraw <span class="font-bold text-gray-900 text-base" id="confirmAmountDisplay"></span>?</p>
            
            <div class="flex gap-3">
                <button type="button" onclick="hideConfirmDialog()" class="flex-1 py-3 px-4 rounded-xl font-bold text-gray-700 bg-gray-100 hover:bg-gray-200 transition-colors">Cancel</button>
                <button type="button" onclick="document.getElementById('withdrawForm').submit()" class="flex-1 py-3 px-4 rounded-xl font-bold text-white bg-[#D00E15] hover:bg-red-700 transition-colors shadow-md">Confirm</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function showConfirmDialog() {
            const amountInput = document.getElementById('withdrawAmount');
            if (!amountInput.value || parseFloat(amountInput.value) <= 0) {
                amountInput.reportValidity();
                return;
            }
            
            // Format number to local currency style (BDT assumed from backend format)
            document.getElementById('confirmAmountDisplay').textContent = '৳ ' + new Intl.NumberFormat('en-US').format(amountInput.value);
            
            const dialog = document.getElementById('confirmDialog');
            const card = document.getElementById('confirmDialogCard');
            
            dialog.classList.remove('hidden');
            // Small delay to allow display block to apply before animating opacity/transform
            setTimeout(() => {
                card.style.transform = 'scale(1)';
                card.style.opacity = '1';
            }, 10);
        }

        function hideConfirmDialog() {
            const dialog = document.getElementById('confirmDialog');
            const card = document.getElementById('confirmDialogCard');
            
            card.style.transform = 'scale(0.95)';
            card.style.opacity = '0';
            
            setTimeout(() => {
                dialog.classList.add('hidden');
            }, 200); // match duration-200 class
        }
    </script>
    @endpush
</x-pms-layout>
