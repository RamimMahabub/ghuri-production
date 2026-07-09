<x-app-layout>
<div class="max-w-5xl mx-auto py-8 px-4">
    <h1 class="page-title mb-6">My Bookings</h1>

    {{-- Main Type Tabs --}}
    <div class="flex items-center gap-4 mb-6">
        <a href="#" class="flex items-center gap-2 px-6 py-2.5 rounded-full text-sm font-semibold transition-all bg-[#d00e15] text-white shadow-md">
            <i class="fas fa-bed"></i> Hotels
        </a>
        <a href="{{ route('flights.search') }}" class="flex items-center gap-2 px-6 py-2.5 rounded-full text-sm font-semibold transition-all bg-white text-gray-600 border border-gray-200 hover:border-gray-300 hover:bg-gray-50">
            <i class="fas fa-plane"></i> Flights
        </a>
    </div>

    {{-- Status Tabs --}}
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
                            <div><span class="text-brand-muted">Total</span><p class="font-heading font-bold text-brand-black">{{ \App\Helpers\Currency::format($booking->total) }}</p></div>
                        </div>
                        <div class="flex items-center gap-2 mt-3 pt-3 border-t border-brand-border">
                            <a href="{{ route('my-bookings.show', $booking) }}" class="btn-ghost btn-sm">View Details</a>
                            @if(in_array($booking->status, ['pending', 'confirmed']))
                                <form method="POST" action="{{ route('my-bookings.cancel', $booking) }}" x-data @submit.prevent="if(confirm('Cancel this booking?')) $el.submit()">
                                    @csrf
                                    <button type="submit" class="btn-ghost btn-sm text-red-500">Cancel</button>
                                </form>
                            @endif
                            @if($booking->status === 'checked_out')
                                @if(\App\Models\Review::where('hotel_booking_id', $booking->id)->exists())
                                    <span class="text-sm text-brand-muted flex items-center gap-1"><svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Reviewed</span>
                                @else
                                    <div x-data="{ open: false }">
                                        <button type="button" @click="open = true" class="btn-ghost btn-sm text-brand-primary">Write a Review</button>
                                        
                                        <!-- Review Modal -->
                                        <div x-show="open" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                                <div x-show="open" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="open = false" aria-hidden="true"></div>
                                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                                <div x-show="open" x-transition class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                                                    <form method="POST" action="{{ route('my-bookings.review', $booking) }}">
                                                        @csrf
                                                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                            <h3 class="text-lg leading-6 font-bold text-gray-900 mb-4" id="modal-title">Review {{ $booking->property->name }}</h3>
                                                            
                                                            <div class="mb-4">
                                                                <label class="block text-sm font-medium text-gray-700 mb-1">Rating (1-10)</label>
                                                                <input type="number" name="overall_score" min="1" max="10" step="0.1" required class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-brand-primary focus:border-brand-primary sm:text-sm">
                                                            </div>
                                                            
                                                            <div class="mb-4">
                                                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                                                <textarea name="public_review" rows="4" required class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-brand-primary focus:border-brand-primary sm:text-sm" placeholder="Tell us about your stay..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                                                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#006ce4] text-base font-medium text-white hover:bg-[#0057b8] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#006ce4] sm:ml-3 sm:w-auto sm:text-sm">
                                                                Submit Review
                                                            </button>
                                                            <button type="button" @click="open = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-primary sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                                                Cancel
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
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
