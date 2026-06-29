<x-guest-layout>
<div class="max-w-4xl mx-auto py-8 px-4">
    <div class="flex items-center justify-center gap-0 mb-10">
        <div class="wizard-step completed"><div class="step-number"><i class="fas fa-check text-[10px]"></i></div><span class="text-xs hidden sm:inline">Review</span></div>
        <div class="wizard-connector completed"></div>
        <div class="wizard-step completed"><div class="step-number"><i class="fas fa-check text-[10px]"></i></div><span class="text-xs hidden sm:inline">Details</span></div>
        <div class="wizard-connector completed"></div>
        <div class="wizard-step active"><div class="step-number">3</div><span class="text-xs hidden sm:inline">Payment</span></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="card card-body">
                <h2 class="section-heading mb-6">Confirm & Pay</h2>

                <form method="POST" action="{{ route('hotels.book.confirm') }}">
                    @csrf
                    <input type="hidden" name="property_id" value="{{ $data['property_id'] }}">
                    <input type="hidden" name="room_type_id" value="{{ $data['room_type_id'] }}">
                    <input type="hidden" name="check_in" value="{{ $data['check_in'] }}">
                    <input type="hidden" name="check_out" value="{{ $data['check_out'] }}">
                    <input type="hidden" name="adults" value="{{ $data['adults'] }}">
                    <input type="hidden" name="children" value="{{ $data['children'] ?? 0 }}">
                    <input type="hidden" name="rate_plan_id" value="{{ $data['rate_plan_id'] ?? '' }}">
                    <input type="hidden" name="special_requests" value="{{ $data['special_requests'] ?? '' }}">
                    <input type="hidden" name="estimated_arrival" value="{{ $data['estimated_arrival'] ?? '' }}">

                    {{-- Promo Code --}}
                    <div class="mb-6">
                        <label class="form-label text-xs">Promo Code</label>
                        <div class="flex gap-2">
                            <input type="text" name="promo_code" class="form-input-styled flex-1" placeholder="Enter promo code">
                            <button type="button" class="btn-secondary btn-sm">Apply</button>
                        </div>
                    </div>

                    {{-- Payment Method (Phase 1: pending only) --}}
                    <div class="bg-brand-surface rounded-xl p-5 mb-6">
                        <h3 class="text-sm font-bold text-brand-black mb-3">Payment Method</h3>
                        <div class="space-y-3">
                            <label class="flex items-center gap-3 p-3 bg-white rounded-lg border border-brand-primary cursor-pointer">
                                <input type="radio" name="payment_method" value="pending" checked class="text-brand-primary focus:ring-brand-primary">
                                <div>
                                    <p class="text-sm font-medium text-brand-black">Pay at Hotel</p>
                                    <p class="text-xs text-brand-muted">Payment will be collected at check-in</p>
                                </div>
                            </label>
                            <label class="flex items-center gap-3 p-3 bg-white rounded-lg border border-brand-border cursor-pointer opacity-50">
                                <input type="radio" name="payment_method" value="card" disabled class="text-brand-primary">
                                <div>
                                    <p class="text-sm font-medium text-brand-black">Credit/Debit Card</p>
                                    <p class="text-xs text-brand-muted">Online payment coming soon</p>
                                </div>
                                <span class="badge-info ml-auto text-[10px]">Coming Soon</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary w-full btn-lg">
                        <i class="fas fa-lock mr-1"></i> Confirm Booking
                    </button>

                    <p class="text-[10px] text-brand-muted text-center mt-3">
                        By confirming, you agree to our Terms & Conditions
                    </p>
                </form>
            </div>
        </div>

        {{-- Price Summary --}}
        <div>
            <div class="card card-body sticky top-24">
                <h3 class="font-heading font-bold text-brand-black text-sm mb-4">Booking Summary</h3>

                <div class="flex items-center gap-3 mb-4 pb-4 border-b border-brand-border">
                    <div class="w-16 h-12 rounded-lg overflow-hidden bg-brand-surface">
                        <img src="{{ $property->cover_photo_url }}" alt="{{ $property->name }}" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <p class="text-sm font-medium text-brand-black">{{ $property->name }}</p>
                        <p class="text-xs text-brand-muted">{{ $roomType->name }}</p>
                    </div>
                </div>

                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-brand-muted">{{ \Carbon\Carbon::parse($data['check_in'])->format('M d') }} – {{ \Carbon\Carbon::parse($data['check_out'])->format('M d') }}</span>
                        <span class="text-brand-black">{{ $pricing['nights'] }} nights</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-brand-muted">Room rate</span>
                        <span class="text-brand-black">${{ number_format($pricing['subtotal'], 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-brand-muted">Taxes & fees</span>
                        <span class="text-brand-black">${{ number_format($pricing['taxes'] + $pricing['fees'], 2) }}</span>
                    </div>
                </div>

                <div class="flex justify-between mt-4 pt-4 border-t-2 border-brand-black">
                    <span class="font-heading font-bold text-lg text-brand-black">Total</span>
                    <span class="font-heading font-bold text-2xl text-brand-black">${{ number_format($pricing['total'], 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
</x-guest-layout>
