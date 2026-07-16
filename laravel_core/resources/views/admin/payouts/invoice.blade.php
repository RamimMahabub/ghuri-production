<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $payout->reference }}</title>
    <meta name="robots" content="noindex, nofollow">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f9fafb; color: #111827; }
        @media print {
            body { background-color: white; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="antialiased py-8">
    
    <div class="max-w-4xl mx-auto bg-white p-8 md:p-12 shadow-sm border border-gray-100 rounded-2xl">
        
        <!-- Print Button -->
        <div class="flex justify-end mb-8 no-print">
            <button onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-6 rounded-lg transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Print Invoice
            </button>
        </div>

        <!-- Header -->
        <div class="flex justify-between items-start mb-12 border-b border-gray-100 pb-8">
            <div>
                <!-- Brand -->
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-red-600 rounded-lg flex items-center justify-center text-white font-bold text-xl">
                        G
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 leading-tight">Bookdei</h1>
                        <p class="text-xs text-gray-500 font-medium">PARTNER PAYOUTS</p>
                    </div>
                </div>
                
                <h2 class="text-3xl font-bold text-gray-900 mb-1">INVOICE</h2>
                <p class="text-gray-500 font-medium">{{ $payout->reference }}</p>
            </div>
            
            <div class="text-right text-sm">
                <p class="font-bold text-gray-900 mb-1">Bookdei Inc.</p>
                <p class="text-gray-500">123 Travel Avenue</p>
                <p class="text-gray-500">Dhaka, Bangladesh</p>
                <p class="text-gray-500">billing@bookdei.com</p>
            </div>
        </div>

        <!-- Details -->
        <div class="grid grid-cols-2 gap-8 mb-12">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Billed To</p>
                <p class="font-bold text-gray-900 text-lg mb-1">{{ $payout->property->name }}</p>
                <p class="text-gray-500">{{ $payout->property->owner->name ?? 'N/A' }}</p>
                <p class="text-gray-500">{{ $payout->property->owner->email ?? 'N/A' }}</p>
                <p class="text-gray-500">{{ $payout->property->address_line_1 }}</p>
                <p class="text-gray-500">{{ $payout->property->city }}, {{ $payout->property->country }}</p>
            </div>
            <div class="text-right">
                <div class="mb-4">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Date Issued</p>
                    <p class="font-medium text-gray-900">{{ $payout->created_at->format('F d, Y') }}</p>
                </div>
                <div class="mb-4">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Period</p>
                    <p class="font-medium text-gray-900">{{ $payout->period_start ? $payout->period_start->format('M d') : 'N/A' }} - {{ $payout->period_end ? $payout->period_end->format('M d, Y') : 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Status</p>
                    @if($payout->status === 'processed')
                        <span class="inline-flex px-3 py-1 bg-green-100 text-green-800 text-xs font-bold rounded-full">PAID</span>
                    @else
                        <span class="inline-flex px-3 py-1 bg-orange-100 text-orange-800 text-xs font-bold rounded-full uppercase">{{ $payout->status }}</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="mb-12 rounded-xl overflow-hidden border border-gray-200">
            <table class="w-full text-left">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Total</th>
                        <th class="py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Commission</th>
                        <th class="py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Payout</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($bookings as $booking)
                    <tr>
                        <td class="py-4 px-6">
                            <p class="font-bold text-gray-900 text-sm">{{ $booking->booking_ref }}</p>
                            <p class="text-xs text-gray-500">Guest: {{ $booking->guest->name ?? 'N/A' }} ({{ \Carbon\Carbon::parse($booking->check_in)->format('M d') }} - {{ \Carbon\Carbon::parse($booking->check_out)->format('M d') }})</p>
                        </td>
                        <td class="py-4 px-6 text-right text-sm text-gray-600 font-medium">
                            {{ \App\Helpers\Currency::format($booking->total) }}
                        </td>
                        <td class="py-4 px-6 text-right text-sm text-red-500 font-medium">
                            -{{ \App\Helpers\Currency::format($booking->commission_amount) }}
                        </td>
                        <td class="py-4 px-6 text-right text-sm font-bold text-gray-900">
                            {{ \App\Helpers\Currency::format($booking->total - $booking->commission_amount) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td class="py-4 px-6">
                            <p class="font-bold text-gray-900 text-sm">Payout Request</p>
                            <p class="text-xs text-gray-500">Manual withdrawal</p>
                        </td>
                        <td class="py-4 px-6 text-right text-sm text-gray-600 font-medium">
                            -
                        </td>
                        <td class="py-4 px-6 text-right text-sm text-red-500 font-medium">
                            -
                        </td>
                        <td class="py-4 px-6 text-right text-sm font-bold text-gray-900">
                            {{ \App\Helpers\Currency::format($payout->amount) }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Totals -->
        <div class="flex justify-end mb-12">
            <div class="w-72">
                @if($bookings->isNotEmpty())
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-500 font-medium">Subtotal</span>
                    <span class="font-bold text-gray-900">{{ \App\Helpers\Currency::format($bookings->sum('total')) }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-500 font-medium">Total Commission</span>
                    <span class="font-bold text-red-500">-{{ \App\Helpers\Currency::format($bookings->sum('commission_amount')) }}</span>
                </div>
                @endif
                <div class="flex justify-between py-4 text-xl border-b-2 border-gray-900">
                    <span class="font-bold text-gray-900">Total Payout</span>
                    <span class="font-bold text-green-600">{{ \App\Helpers\Currency::format($payout->amount) }}</span>
                </div>
            </div>
        </div>

        <!-- Footer Notes -->
        <div class="border-t border-gray-100 pt-8 text-center">
            <p class="text-gray-500 text-sm mb-2">Thank you for partnering with Bookdei.</p>
            <p class="text-xs text-gray-400">If you have any questions about this invoice, please contact billing@bookdei.com</p>
        </div>

    </div>

</body>
</html>
