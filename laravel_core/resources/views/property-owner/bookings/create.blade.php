<x-pms-layout pageTitle="Create Walk-in Booking">
<div class="max-w-2xl">
    <form method="POST" action="{{ route('property-owner.bookings.store') }}" class="space-y-6">
        @csrf
        <div class="card card-body space-y-5">
            <h2 class="section-heading text-base"><i class="fas fa-calendar-plus text-brand-primary mr-2"></i>New Walk-in Booking</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="form-group md:col-span-2">
                    <label class="form-label">Property *</label>
                    <select name="property_id" class="form-input-styled" x-data x-on:change="$dispatch('property-changed')">
                        @foreach($properties as $prop)
                            <option value="{{ $prop->id }}">{{ $prop->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group md:col-span-2">
                    <label class="form-label">Room Type *</label>
                    <select name="room_type_id" class="form-input-styled">
                        @foreach($properties as $prop)
                            @foreach($prop->activeRoomTypes as $rt)
                                <option value="{{ $rt->id }}">{{ $prop->name }} — {{ $rt->name }} (${{ number_format($rt->base_price_per_night, 0) }})</option>
                            @endforeach
                        @endforeach
                    </select>
                </div>
                <div class="form-group"><label class="form-label">Guest Name *</label><input type="text" name="guest_name" class="form-input-styled" required></div>
                <div class="form-group"><label class="form-label">Guest Email *</label><input type="email" name="guest_email" class="form-input-styled" required></div>
                <div class="form-group"><label class="form-label">Guest Phone</label><input type="text" name="guest_phone" class="form-input-styled"></div>
                <div class="form-group"><label class="form-label">Adults *</label><input type="number" name="adults" class="form-input-styled" value="1" min="1" required></div>
                <div class="form-group"><label class="form-label">Children</label><input type="number" name="children" class="form-input-styled" value="0" min="0"></div>
                <div class="form-group"><label class="form-label">Check-in *</label><input type="date" name="check_in" class="form-input-styled" value="{{ now()->format('Y-m-d') }}" required></div>
                <div class="form-group"><label class="form-label">Check-out *</label><input type="date" name="check_out" class="form-input-styled" value="{{ now()->addDay()->format('Y-m-d') }}" required></div>
                <div class="form-group md:col-span-2"><label class="form-label">Special Requests</label><textarea name="special_requests" class="form-input-styled" rows="2"></textarea></div>
            </div>
        </div>
        <div class="flex justify-end gap-3">
            <a href="{{ route('property-owner.bookings.index') }}" class="btn-ghost">Cancel</a>
            <button type="submit" class="btn-primary btn-lg"><i class="fas fa-calendar-plus"></i> Create Booking</button>
        </div>
    </form>
</div>
</x-pms-layout>
