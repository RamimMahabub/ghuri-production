<x-guest-layout>
<div class="max-w-4xl mx-auto py-8 px-4">
    <div class="flex items-center justify-center gap-0 mb-10">
        <div class="wizard-step completed"><div class="step-number"><i class="fas fa-check text-[10px]"></i></div><span class="text-xs hidden sm:inline">Review</span></div>
        <div class="wizard-connector completed"></div>
        <div class="wizard-step active"><div class="step-number">2</div><span class="text-xs hidden sm:inline">Details</span></div>
        <div class="wizard-connector"></div>
        <div class="wizard-step"><div class="step-number">3</div><span class="text-xs hidden sm:inline">Payment</span></div>
    </div>

    <div class="card card-body max-w-2xl mx-auto">
        <h2 class="section-heading mb-6">Guest Details</h2>

        <form method="POST" action="{{ route('hotels.book.step3') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="property_id" value="{{ $data['property_id'] }}">
            <input type="hidden" name="room_type_id" value="{{ $data['room_type_id'] }}">
            <input type="hidden" name="check_in" value="{{ $data['check_in'] }}">
            <input type="hidden" name="check_out" value="{{ $data['check_out'] }}">
            <input type="hidden" name="adults" value="{{ $data['adults'] }}">
            <input type="hidden" name="children" value="{{ $data['children'] ?? 0 }}">
            <input type="hidden" name="rate_plan_id" value="{{ $data['rate_plan_id'] ?? '' }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="form-group">
                    <label class="form-label">First Name *</label>
                    <input type="text" name="first_name" class="form-input-styled" required value="{{ old('first_name', Auth::user()->name ? explode(' ', Auth::user()->name)[0] : '') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Last Name *</label>
                    <input type="text" name="last_name" class="form-input-styled" required value="{{ old('last_name', Auth::user()->name && count(explode(' ', Auth::user()->name)) > 1 ? explode(' ', Auth::user()->name, 2)[1] : '') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" class="form-input-styled" required value="{{ old('email', Auth::user()->email) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Phone *</label>
                    <input type="tel" name="phone" class="form-input-styled" required value="{{ old('phone', Auth::user()->phone) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Country</label>
                    <input type="text" name="country" class="form-input-styled" value="{{ old('country') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Estimated Arrival</label>
                    <select name="estimated_arrival" class="form-input-styled">
                        <option value="">Select time...</option>
                        @for($h = 0; $h < 24; $h++)
                            <option value="{{ sprintf('%02d:00', $h) }}">{{ sprintf('%02d:00', $h) }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Special Requests</label>
                <textarea name="special_requests" rows="3" class="form-input-styled" placeholder="e.g., late check-in, extra pillows, room on a high floor...">{{ old('special_requests') }}</textarea>
            </div>

            <div class="flex items-start gap-2">
                <input type="checkbox" id="terms" required class="mt-0.5 rounded border-brand-border text-brand-primary focus:ring-brand-primary">
                <label for="terms" class="text-xs text-brand-text">I accept the booking terms and conditions and the property's cancellation policy</label>
            </div>

            <button type="submit" class="btn-primary w-full btn-lg">
                Continue to Payment <i class="fas fa-arrow-right"></i>
            </button>
        </form>
    </div>
</div>
</x-guest-layout>
