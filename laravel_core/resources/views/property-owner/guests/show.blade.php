<x-pms-layout :pageTitle="'Guest Profile: ' . $guest->name" pageSubtitle="View booking history and details">

    <x-slot name="headerActions">
        <a href="{{ route('property-owner.guests.index') }}" class="btn-ghost">
            <i class="fas fa-arrow-left"></i> Back to Guests
        </a>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Sidebar Profile --}}
        <div>
            <div class="card card-body sticky top-24">
                <div class="flex flex-col items-center mb-6">
                    <div class="w-24 h-24 rounded-full bg-brand-primary/10 flex items-center justify-center mb-3">
                        <span class="text-3xl font-bold text-brand-primary">{{ substr($guest->name, 0, 1) }}</span>
                    </div>
                    <h2 class="font-heading font-bold text-xl text-brand-black">{{ $guest->name }}</h2>
                    <p class="text-sm text-brand-muted">Guest since {{ $guest->created_at->format('M Y') }}</p>
                </div>
                
                <hr class="border-brand-border mb-4">
                
                <div class="space-y-4 text-sm text-brand-black">
                    <div>
                        <p class="text-xs text-brand-muted uppercase tracking-wider mb-1">Email</p>
                        <p><i class="fas fa-envelope text-brand-primary w-5 text-center"></i> <a href="mailto:{{ $guest->email }}" class="hover:underline">{{ $guest->email }}</a></p>
                    </div>
                    @if($guest->phone)
                        <div>
                            <p class="text-xs text-brand-muted uppercase tracking-wider mb-1">Phone</p>
                            <p><i class="fas fa-phone text-brand-primary w-5 text-center"></i> <a href="tel:{{ $guest->phone }}" class="hover:underline">{{ $guest->phone }}</a></p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Main Booking History --}}
        <div class="lg:col-span-2 space-y-6">
            <h3 class="font-heading font-bold text-brand-black text-sm">Booking History ({{ $bookings->count() }})</h3>
            
            <div class="space-y-4">
                @foreach($bookings as $booking)
                    <div class="card card-body flex flex-col md:flex-row gap-5 hover:border-brand-primary transition-colors">
                        <div class="w-full md:w-32 h-24 rounded-lg bg-gray-200 overflow-hidden flex-shrink-0">
                            <img src="{{ $booking->property->cover_photo_url }}" class="w-full h-full object-cover">
                        </div>
                        
                        <div class="flex-1">
                            <div class="flex justify-between items-start mb-1">
                                <div>
                                    <h4 class="font-bold text-brand-black text-sm">{{ $booking->property->name }}</h4>
                                    <p class="text-xs text-brand-muted">{{ $booking->roomType->name }}</p>
                                </div>
                                <span class="badge-{{ $booking->status === 'confirmed' ? 'confirmed' : ($booking->status === 'cancelled' ? 'cancelled' : 'info') }} text-[10px]">
                                    {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-2 mt-3 text-xs text-brand-black">
                                <div><span class="text-brand-muted block text-[10px] uppercase">Check-in</span> {{ $booking->check_in->format('M d, Y') }}</div>
                                <div><span class="text-brand-muted block text-[10px] uppercase">Check-out</span> {{ $booking->check_out->format('M d, Y') }}</div>
                            </div>
                        </div>
                        
                        <div class="flex flex-row md:flex-col justify-between items-end border-t md:border-t-0 md:border-l border-brand-border pt-3 md:pt-0 md:pl-5 text-right w-full md:w-auto mt-3 md:mt-0">
                            <div class="text-left md:text-right">
                                <p class="text-xs text-brand-muted">Total</p>
                                <p class="font-bold text-brand-black text-lg">${{ number_format($booking->total, 2) }}</p>
                            </div>
                            <a href="{{ route('property-owner.bookings.show', $booking) }}" class="btn-secondary btn-sm whitespace-nowrap">View Details</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</x-pms-layout>
