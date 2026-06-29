<x-pms-layout pageTitle="Guest Reviews" pageSubtitle="See what guests are saying about your properties">

    <div class="card">
        <div class="card-header">
            <h3 class="font-heading font-bold text-brand-black text-sm">All Reviews</h3>
        </div>
        
        <div class="divide-y divide-brand-border">
            @forelse($reviews as $review)
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex gap-4">
                            <div class="guest-score {{ $review->overall_score >= 8 ? 'excellent' : ($review->overall_score >= 6 ? 'good' : 'average') }}">
                                {{ number_format($review->overall_score, 1) }}
                            </div>
                            <div>
                                <h4 class="font-heading font-bold text-brand-black text-sm">{{ $review->guest->name ?? 'Guest' }}</h4>
                                <p class="text-xs text-brand-muted">
                                    {{ $review->property->name ?? '' }} · {{ $review->hotelBooking->roomType->name ?? '' }}
                                    · {{ $review->created_at->format('M d, Y') }}
                                </p>
                            </div>
                        </div>
                        <span class="badge-{{ $review->status === 'published' ? 'confirmed' : 'pending' }} text-[10px]">
                            {{ ucfirst($review->status) }}
                        </span>
                    </div>

                    <p class="text-sm text-brand-text mb-4">{{ $review->comment }}</p>

                    @if($review->hotel_response)
                        <div class="bg-gray-50 border-l-4 border-brand-primary p-4 rounded-r-lg">
                            <p class="text-xs font-bold text-brand-black mb-1">Your Response:</p>
                            <p class="text-sm text-brand-text">{{ $review->hotel_response }}</p>
                            <p class="text-[10px] text-brand-muted mt-1">Responded on {{ $review->responded_at->format('M d, Y') }}</p>
                        </div>
                    @else
                        <div x-data="{ open: false }">
                            <button @click="open = !open" class="text-xs text-brand-primary font-semibold hover:underline">
                                <i class="fas fa-reply mr-1"></i> Respond to review
                            </button>
                            
                            <form x-show="open" style="display: none;" action="{{ route('property-owner.reviews.respond', $review) }}" method="POST" class="mt-3 bg-brand-surface p-4 rounded-lg">
                                @csrf
                                <textarea name="response" class="form-input-styled w-full text-sm mb-3" rows="3" placeholder="Write a polite public response to the guest..." required></textarea>
                                <div class="flex gap-2">
                                    <button type="submit" class="btn-primary btn-sm">Post Response</button>
                                    <button type="button" @click="open = false" class="btn-ghost btn-sm">Cancel</button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            @empty
                <div class="p-8 text-center text-brand-muted text-sm">
                    <i class="fas fa-star text-4xl mb-3 text-brand-border block"></i>
                    No reviews received yet.
                </div>
            @endforelse
        </div>
        
        @if($reviews->hasPages())
            <div class="px-5 py-4 border-t border-brand-border">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>

</x-pms-layout>
