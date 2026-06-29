<x-app-layout>
<div class="max-w-5xl mx-auto py-8 px-4">
    <h1 class="page-title mb-6">My Bookings</h1>

    {{-- Tabs --}}
    <div class="flex items-center gap-1 mb-6 border-b border-brand-border">
        @foreach(['upcoming' => 'Upcoming', 'past' => 'Past', 'cancelled' => 'Cancelled'] as $key => $label)
            <a href="{{ route('my-bookings.index', ['tab' => $key]) }}"
               class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors {{ $tab === $key ? 'border-brand-primary text-brand-primary' : 'border-transparent text-brand-muted hover:text-brand-text' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="space-y-4">
        @forelse($bookings as $booking)
            <div class="card overflow-hidden animate-slide-up">
                <div class="flex">
                    <div class="w-40 flex-shrink-0 bg-brand-surface">
                        <img src="{{ $booking->property->cover_photo_url }}" alt="{{ $booking->property->name }}" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1 p-5">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="font-heading font-bold text-brand-black">{{ $booking->property->name }}</h3>
                                <p class="text-xs text-brand-muted">{{ $booking->roomType->name ?? 'Room' }}</p>
                            </div>
                            <span class="badge-{{ $booking->status_color }}">{{ $booking->status_label }}</span>
                        </div>
                        <div class="grid grid-cols-3 gap-4 mt-3 text-sm">
                            <div><span class="text-brand-muted">Dates</span><p class="font-medium text-brand-black">{{ $booking->check_in->format('M d') }} – {{ $booking->check_out->format('M d, Y') }}</p></div>
                            <div><span class="text-brand-muted">Reference</span><p class="font-mono font-semibold text-brand-primary">{{ $booking->booking_ref }}</p></div>
                            <div><span class="text-brand-muted">Total</span><p class="font-heading font-bold text-brand-black">${{ number_format($booking->total, 2) }}</p></div>
                        </div>
                        <div class="flex items-center gap-2 mt-3 pt-3 border-t border-brand-border">
                            <a href="{{ route('my-bookings.show', $booking) }}" class="btn-ghost btn-sm">View Details</a>
                            @if(in_array($booking->status, ['pending', 'confirmed']))
                                <form method="POST" action="{{ route('my-bookings.cancel', $booking) }}" x-data @submit.prevent="if(confirm('Cancel this booking?')) $el.submit()">
                                    @csrf
                                    <button type="submit" class="btn-ghost btn-sm text-red-500">Cancel</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="card card-body text-center py-12">
                <i class="fas fa-suitcase text-4xl text-brand-border mb-3"></i>
                <h3 class="font-heading font-bold text-brand-black mb-1">No {{ $tab }} bookings</h3>
                <p class="text-sm text-brand-muted mb-4">{{ $tab === 'upcoming' ? 'Start planning your next trip!' : '' }}</p>
                @if($tab === 'upcoming')
                    <a href="{{ route('hotels.search') }}" class="btn-primary inline-flex"><i class="fas fa-search"></i> Search Hotels</a>
                @endif
            </div>
        @endforelse
    </div>
</div>
</x-app-layout>
