<x-app-layout>
<div class="max-w-4xl mx-auto py-8 px-4">
    
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('dashboard') }}" class="text-brand-text hover:text-brand-primary text-sm font-semibold flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Back to My Bookings
        </a>
        
        @if(in_array($booking->status, ['confirmed', 'checked_in', 'checked_out']))
            <a href="{{ route('my-bookings.voucher', $booking) }}" target="_blank" class="btn-secondary btn-sm ml-auto mr-2">
                <i class="fas fa-file-pdf"></i> Download Voucher
            </a>
        @endif
        
        @if(in_array($booking->status, ['pending', 'confirmed']))
            <form action="{{ route('my-bookings.cancel', $booking) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this booking? Refund policy will apply.');">
                @csrf
                <input type="hidden" name="reason" value="Guest requested cancellation">
                <button type="submit" class="btn-danger btn-sm">Cancel Booking</button>
            </form>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            {{-- Status Card --}}
            <div class="card card-body bg-brand-surface border-none">
                <div class="flex items-start justify-between">
                    <div>
                        <h1 class="font-heading font-bold text-2xl text-brand-black mb-1">Booking #{{ $booking->booking_ref }}</h1>
                        <p class="text-sm text-brand-muted">Placed on {{ $booking->created_at->format('M d, Y') }}</p>
                    </div>
                    <span class="badge-{{ $booking->status === 'confirmed' ? 'confirmed' : ($booking->status === 'cancelled' ? 'cancelled' : 'info') }} text-sm px-3 py-1">
                        {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                    </span>
                </div>
            </div>

            {{-- Hotel Info --}}
            <div class="card">
                <div class="card-header border-b border-brand-border">
                    <h3 class="font-heading font-bold text-brand-black text-sm">Property Details</h3>
                </div>
                <div class="p-5 flex gap-5">
                    <div class="w-32 h-32 rounded-lg bg-gray-200 overflow-hidden flex-shrink-0">
                        <img src="{{ $booking->property->cover_photo_url }}" alt="{{ $booking->property->name }}" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <h4 class="font-heading font-bold text-lg text-brand-black mb-1">{{ $booking->property->name }}</h4>
                        <p class="text-sm text-brand-muted mb-2"><i class="fas fa-map-marker-alt text-brand-primary mr-1"></i> {{ $booking->property->address_line_1 }}, {{ $booking->property->city }}</p>
                        <a href="{{ route('hotels.show', $booking->property) }}" class="text-brand-primary text-sm font-semibold hover:underline">View Property</a>
                    </div>
                </div>
            </div>

            {{-- Stay Details --}}
            <div class="card card-body">
                <h3 class="font-heading font-bold text-brand-black text-sm mb-4">Stay Information</h3>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs text-brand-muted uppercase tracking-wider mb-1">Check-in</p>
                        <p class="font-bold text-brand-black">{{ $booking->check_in->format('D, M d, Y') }}</p>
                        <p class="text-xs text-brand-text mt-1">From {{ $booking->property->check_in_time ?? '14:00' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-brand-muted uppercase tracking-wider mb-1">Check-out</p>
                        <p class="font-bold text-brand-black">{{ $booking->check_out->format('D, M d, Y') }}</p>
                        <p class="text-xs text-brand-text mt-1">Until {{ $booking->property->check_out_time ?? '12:00' }}</p>
                    </div>
                    <div class="col-span-2 pt-4 border-t border-brand-border">
                        <p class="text-sm text-brand-black"><span class="font-semibold">{{ $booking->roomType->name }}</span> ({{ $booking->nights }} nights)</p>
                        <p class="text-sm text-brand-muted mt-1">{{ $booking->adults }} Adults, {{ $booking->children }} Children</p>
                    </div>
                </div>
            </div>
            
            {{-- Guest Activity Logs --}}
            <div class="card card-body">
                <h3 class="font-heading font-bold text-brand-black text-sm mb-4">Booking Timeline</h3>
                <div class="space-y-4 relative before:absolute before:inset-0 before:ml-2.5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-brand-border before:to-transparent">
                    @foreach($booking->activityLogs as $log)
                        <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                            <div class="flex items-center justify-center w-5 h-5 rounded-full border border-white bg-brand-surface text-brand-primary shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2">
                                <i class="fas fa-circle text-[8px]"></i>
                            </div>
                            <div class="w-[calc(100%-2rem)] md:w-[calc(50%-1.5rem)] p-3 rounded border border-brand-border bg-white">
                                <div class="flex items-center justify-between mb-1">
                                    <h4 class="font-bold text-brand-black text-xs">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</h4>
                                    <time class="text-[10px] text-brand-muted">{{ $log->created_at->format('M d, H:i') }}</time>
                                </div>
                                <p class="text-xs text-brand-text">{{ $log->description }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Payment Summary --}}
        <div>
            <div class="card card-body sticky top-24">
                <h3 class="font-heading font-bold text-brand-black text-sm mb-4">Payment Summary</h3>
                
                <div class="space-y-3 text-sm pb-4 border-b border-brand-border">
                    <div class="flex justify-between">
                        <span class="text-brand-text">Room rate</span>
                        <span class="text-brand-black">{{ \App\Helpers\Currency::format($booking->subtotal) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-brand-text">Taxes & fees</span>
                        <span class="text-brand-black">{{ \App\Helpers\Currency::format($booking->taxes + $booking->fees) }}</span>
                    </div>
                    @if($booking->discount_amount > 0)
                        <div class="flex justify-between text-status-confirmed font-medium">
                            <span>Discount</span>
                            <span>-{{ \App\Helpers\Currency::format($booking->discount_amount) }}</span>
                        </div>
                    @endif
                </div>

                <div class="flex justify-between mt-4">
                    <span class="font-heading font-bold text-lg text-brand-black">Total</span>
                    <span class="font-heading font-bold text-xl text-brand-black">{{ \App\Helpers\Currency::format($booking->total) }}</span>
                </div>
                
                <div class="mt-4 pt-4 border-t border-brand-border">
                    <p class="text-xs text-brand-muted uppercase tracking-wider mb-1">Payment Status</p>
                    <span class="badge-{{ $booking->payment_status === 'paid' ? 'confirmed' : ($booking->payment_status === 'refunded' ? 'info' : 'pending') }} text-xs">
                        {{ ucfirst($booking->payment_status) }}
                    </span>
                    @if($booking->refund_amount > 0)
                        <p class="text-xs text-status-confirmed mt-2">Refunded: ${{ number_format($booking->refund_amount, 2) }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
