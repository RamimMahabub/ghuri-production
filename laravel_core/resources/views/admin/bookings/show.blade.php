<x-admin-layout>
    <x-slot name="pageTitle">Booking Details</x-slot>
    
    <div class="mb-4">
        <a href="{{ route('admin.bookings.index') }}" class="text-blue-600 hover:underline text-sm font-medium">
            <i class="fas fa-arrow-left mr-1"></i> Back to Bookings
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-start">
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">Booking #{{ $booking->booking_reference }}</h2>
                        <p class="text-sm text-gray-500 mt-1">Placed on {{ $booking->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                    <div>
                        @if($booking->status === 'confirmed')
                            <span class="px-3 py-1.5 bg-green-100 text-green-700 text-sm font-bold rounded-full">Confirmed</span>
                        @elseif($booking->status === 'cancelled')
                            <span class="px-3 py-1.5 bg-red-100 text-red-700 text-sm font-bold rounded-full">Cancelled</span>
                        @elseif($booking->status === 'completed')
                            <span class="px-3 py-1.5 bg-blue-100 text-blue-700 text-sm font-bold rounded-full">Completed</span>
                        @else
                            <span class="px-3 py-1.5 bg-gray-100 text-gray-700 text-sm font-bold rounded-full">{{ ucfirst($booking->status) }}</span>
                        @endif
                    </div>
                </div>
                
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Reservation Details</h3>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Property</p>
                            <p class="text-sm font-bold text-slate-800">{{ $booking->property->name ?? 'N/A' }}</p>
                            <a href="{{ route('admin.properties.review', $booking->property_id) }}" class="text-xs text-blue-600 hover:underline">View Property</a>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Room Type</p>
                            <p class="text-sm font-bold text-slate-800">{{ $booking->roomType->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Check-in</p>
                            <p class="text-sm font-bold text-slate-800">{{ \Carbon\Carbon::parse($booking->check_in_date)->format('D, M d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Check-out</p>
                            <p class="text-sm font-bold text-slate-800">{{ \Carbon\Carbon::parse($booking->check_out_date)->format('D, M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Guest Information</h3>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Name</p>
                            <p class="text-sm font-medium text-slate-800">{{ $booking->guest_name ?? ($booking->guest->name ?? 'N/A') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Email</p>
                            <p class="text-sm font-medium text-slate-800">{{ $booking->guest_email ?? ($booking->guest->email ?? 'N/A') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Phone</p>
                            <p class="text-sm font-medium text-slate-800">{{ $booking->guest_phone ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-slate-800">Financial Summary</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Base Price</span>
                        <span class="font-medium text-slate-800">${{ number_format($booking->total_price, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Platform Commission (Est. 15%)</span>
                        <span class="font-medium text-green-600">${{ number_format($booking->total_price * 0.15, 2) }}</span>
                    </div>
                    <div class="pt-4 border-t border-gray-100 flex justify-between">
                        <span class="font-bold text-slate-800">Total Paid</span>
                        <span class="font-bold text-blue-600 text-lg">${{ number_format($booking->total_price, 2) }}</span>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-slate-800">Activity Log</h3>
                </div>
                <div class="p-6">
                    @if($booking->activityLogs && $booking->activityLogs->count() > 0)
                        <ul class="space-y-4">
                            @foreach($booking->activityLogs as $log)
                                <li class="text-sm">
                                    <span class="text-gray-500 text-xs block mb-0.5">{{ $log->created_at->format('M d, g:i A') }}</span>
                                    <span class="text-slate-800">{{ $log->description }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-gray-500">No activity logs recorded.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
