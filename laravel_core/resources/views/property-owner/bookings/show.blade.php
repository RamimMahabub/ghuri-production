<x-pms-layout pageTitle="Booking {{ $booking->booking_ref }}" pageSubtitle="{{ $booking->guest->name ?? 'Guest' }} · {{ $booking->property->name ?? '' }}">

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Main Content --}}
    <div class="lg:col-span-2 space-y-5">
        {{-- Status & Actions --}}
        <div class="card card-body">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <span class="badge-{{ $booking->status_color }} text-sm px-3 py-1">{{ $booking->status_label }}</span>
                    <span class="font-mono text-sm text-brand-primary font-semibold">{{ $booking->booking_ref }}</span>
                </div>
                <div class="flex items-center gap-2">
                    @if($booking->status === 'pending')
                        <form method="POST" action="{{ route('property-owner.bookings.confirm', $booking) }}">
                            @csrf
                            <button type="submit" class="btn-success btn-sm"><i class="fas fa-check"></i> Confirm</button>
                        </form>
                    @endif
                    @if($booking->status === 'confirmed')
                        <form method="POST" action="{{ route('property-owner.bookings.check-in', $booking) }}">
                            @csrf
                            <button type="submit" class="btn-primary btn-sm"><i class="fas fa-sign-in-alt"></i> Check In</button>
                        </form>
                    @endif
                    @if($booking->status === 'checked_in')
                        <form method="POST" action="{{ route('property-owner.bookings.check-out', $booking) }}">
                            @csrf
                            <button type="submit" class="btn-secondary btn-sm"><i class="fas fa-sign-out-alt"></i> Check Out</button>
                        </form>
                    @endif
                    @if(in_array($booking->status, ['pending', 'confirmed']))
                        <form method="POST" action="{{ route('property-owner.bookings.cancel', $booking) }}" x-data @submit.prevent="if(confirm('Cancel this booking?')) $el.submit()">
                            @csrf
                            <input type="hidden" name="cancel_reason" value="Cancelled by hotel">
                            <button type="submit" class="btn-danger btn-sm"><i class="fas fa-times"></i> Cancel</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- Guest Information --}}
        <div class="card card-body">
            <h3 class="font-heading font-bold text-brand-black text-sm mb-4"><i class="fas fa-user text-brand-primary mr-2"></i>Guest Information</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><span class="text-brand-muted">Name</span><p class="font-medium text-brand-black">{{ $booking->guest->name ?? 'N/A' }}</p></div>
                <div><span class="text-brand-muted">Email</span><p class="font-medium text-brand-black">{{ $booking->guest->email ?? 'N/A' }}</p></div>
                <div><span class="text-brand-muted">Phone</span><p class="font-medium text-brand-black">{{ $booking->guest->phone ?? 'N/A' }}</p></div>
                <div><span class="text-brand-muted">Source</span><p class="font-medium text-brand-black">{{ ucfirst(str_replace('_', ' ', $booking->source)) }}</p></div>
            </div>
            @if($booking->special_requests)
                <div class="mt-4 pt-4 border-t border-brand-border">
                    <p class="text-xs text-brand-muted mb-1">Special Requests</p>
                    <p class="text-sm text-brand-text">{{ $booking->special_requests }}</p>
                </div>
            @endif
        </div>

        {{-- Booking Summary --}}
        <div class="card card-body">
            <h3 class="font-heading font-bold text-brand-black text-sm mb-4"><i class="fas fa-calendar text-brand-primary mr-2"></i>Booking Summary</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><span class="text-brand-muted">Property</span><p class="font-medium text-brand-black">{{ $booking->property->name }}</p></div>
                <div><span class="text-brand-muted">Room Type</span><p class="font-medium text-brand-black">{{ $booking->roomType->name }}</p></div>
                <div><span class="text-brand-muted">Check-in</span><p class="font-medium text-brand-black">{{ $booking->check_in->format('D, M d, Y') }}</p></div>
                <div><span class="text-brand-muted">Check-out</span><p class="font-medium text-brand-black">{{ $booking->check_out->format('D, M d, Y') }}</p></div>
                <div><span class="text-brand-muted">Guests</span><p class="font-medium text-brand-black">{{ $booking->adults }} adults, {{ $booking->children }} children</p></div>
                <div><span class="text-brand-muted">Duration</span><p class="font-medium text-brand-black">{{ $booking->nights }} nights</p></div>
            </div>
        </div>

        {{-- Price Breakdown --}}
        <div class="card card-body">
            <h3 class="font-heading font-bold text-brand-black text-sm mb-4"><i class="fas fa-receipt text-brand-primary mr-2"></i>Price Breakdown</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-brand-muted">Nightly rate × {{ $booking->nights }} nights</span><span>${{ number_format($booking->subtotal, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-brand-muted">Taxes</span><span>${{ number_format($booking->taxes, 2) }}</span></div>
                <div class="flex justify-between"><span class="text-brand-muted">Fees</span><span>${{ number_format($booking->fees, 2) }}</span></div>
                @if($booking->discount_amount > 0)
                    <div class="flex justify-between text-status-confirmed"><span>Discount</span><span>-${{ number_format($booking->discount_amount, 2) }}</span></div>
                @endif
                <div class="flex justify-between pt-3 border-t-2 border-brand-black font-heading font-bold text-lg">
                    <span>Total</span><span>${{ number_format($booking->total, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Timeline --}}
        <div class="card card-body">
            <h3 class="font-heading font-bold text-brand-black text-sm mb-4"><i class="fas fa-clock text-brand-primary mr-2"></i>Activity Timeline</h3>
            <div class="space-y-3">
                @foreach($booking->activityLogs as $log)
                    <div class="flex items-start gap-3 text-sm">
                        <span class="text-lg">{{ $log->action_icon }}</span>
                        <div class="flex-1">
                            <p class="text-brand-black">{{ $log->description ?? ucfirst(str_replace('_', ' ', $log->action)) }}</p>
                            <p class="text-xs text-brand-muted">{{ $log->created_at->format('M d, Y H:i') }} {{ $log->performer ? '· ' . $log->performer->name : '' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-5">
        {{-- Payment Status --}}
        <div class="card card-body">
            <h3 class="font-heading font-bold text-brand-black text-sm mb-3">Payment</h3>
            <span class="badge-{{ $booking->payment_status === 'paid' ? 'confirmed' : ($booking->payment_status === 'refunded' ? 'cancelled' : 'pending') }}">
                {{ ucfirst(str_replace('_', ' ', $booking->payment_status)) }}
            </span>
            @if($booking->payment_method)
                <p class="text-xs text-brand-muted mt-2">Method: {{ ucfirst($booking->payment_method) }}</p>
            @endif
        </div>

        {{-- Internal Notes --}}
        <div class="card card-body">
            <h3 class="font-heading font-bold text-brand-black text-sm mb-3">Internal Notes</h3>
            <div class="space-y-2 mb-3">
                @forelse($booking->internalNotes as $note)
                    <div class="bg-brand-surface rounded-lg p-2.5">
                        <p class="text-xs text-brand-text">{{ $note->note }}</p>
                        <p class="text-[10px] text-brand-muted mt-1">{{ $note->user->name ?? '' }} · {{ $note->created_at->diffForHumans() }}</p>
                    </div>
                @empty
                    <p class="text-xs text-brand-muted">No notes yet</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
</x-pms-layout>
