<x-admin-layout>
    <x-slot name="pageTitle">Global Bookings</x-slot>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex gap-2">
                <a href="{{ route('admin.bookings.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ !request('status') ? 'bg-blue-50 text-blue-700' : 'text-gray-500 hover:bg-gray-50' }}">All</a>
                <a href="{{ route('admin.bookings.index', ['status' => 'confirmed']) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') === 'confirmed' ? 'bg-green-50 text-green-700' : 'text-gray-500 hover:bg-gray-50' }}">Confirmed</a>
                <a href="{{ route('admin.bookings.index', ['status' => 'cancelled']) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') === 'cancelled' ? 'bg-red-50 text-red-700' : 'text-gray-500 hover:bg-gray-50' }}">Cancelled</a>
            </div>
            
            <form method="GET" action="{{ route('admin.bookings.index') }}" class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" placeholder="Search booking ID..." class="pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 w-full sm:w-64">
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                        <th class="px-6 py-4 font-medium">Booking ID</th>
                        <th class="px-6 py-4 font-medium">Property</th>
                        <th class="px-6 py-4 font-medium">Guest</th>
                        <th class="px-6 py-4 font-medium">Dates</th>
                        <th class="px-6 py-4 font-medium">Amount</th>
                        <th class="px-6 py-4 font-medium">Status</th>
                        <th class="px-6 py-4 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($bookings as $booking)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-mono text-sm font-medium text-slate-700">{{ $booking->booking_ref }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-slate-800">{{ $booking->property->name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500">{{ $booking->roomType->name ?? 'N/A' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-slate-800">{{ $booking->guest_name ?? ($booking->guest->name ?? 'N/A') }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-slate-800">{{ \Carbon\Carbon::parse($booking->check_in)->format('M d, Y') }}</p>
                                <p class="text-xs text-gray-500">to {{ \Carbon\Carbon::parse($booking->check_out)->format('M d, Y') }}</p>
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-800">
                                ${{ number_format($booking->total, 2) }}
                            </td>
                            <td class="px-6 py-4">
                                @if($booking->status === 'confirmed')
                                    <span class="px-2.5 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">Confirmed</span>
                                @elseif($booking->status === 'cancelled')
                                    <span class="px-2.5 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full">Cancelled</span>
                                @elseif($booking->status === 'completed')
                                    <span class="px-2.5 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-full">Completed</span>
                                @else
                                    <span class="px-2.5 py-1 bg-gray-100 text-gray-700 text-xs font-bold rounded-full">{{ ucfirst($booking->status) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.bookings.show', $booking) }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                No bookings found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($bookings->hasPages())
            <div class="p-6 border-t border-gray-100">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
