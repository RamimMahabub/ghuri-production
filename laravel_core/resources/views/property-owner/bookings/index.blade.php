<x-pms-layout pageTitle="Bookings" pageSubtitle="Manage all reservations">
    <x-slot:headerActions>
        <a href="{{ route('property-owner.bookings.create') }}" class="btn-primary btn-sm"><i class="fas fa-plus"></i> Walk-in Booking</a>
    </x-slot:headerActions>

    {{-- Filters --}}
    <div class="card card-body mb-5">
        <form method="GET" action="{{ route('property-owner.bookings.index') }}" class="flex flex-wrap items-end gap-3">
            <div class="form-group flex-1 min-w-[180px]">
                <label class="form-label text-xs">Search</label>
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-brand-muted text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ref, guest name, email..." class="form-input-styled pl-9 text-sm">
                </div>
            </div>
            <div class="form-group w-36">
                <label class="form-label text-xs">Status</label>
                <select name="status" class="form-input-styled text-sm">
                    <option value="">All</option>
                    @foreach(\App\Models\HotelBooking::getStatuses() as $status)
                        <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group w-40">
                <label class="form-label text-xs">Property</label>
                <select name="property_id" class="form-input-styled text-sm">
                    <option value="">All Properties</option>
                    @foreach($properties as $prop)
                        <option value="{{ $prop->id }}" {{ request('property_id') == $prop->id ? 'selected' : '' }}>{{ $prop->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group w-36">
                <label class="form-label text-xs">From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-input-styled text-sm">
            </div>
            <div class="form-group w-36">
                <label class="form-label text-xs">To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-input-styled text-sm">
            </div>
            <button type="submit" class="btn-primary btn-sm"><i class="fas fa-filter"></i> Filter</button>
            <a href="{{ route('property-owner.bookings.index') }}" class="btn-ghost btn-sm">Clear</a>
        </form>
    </div>

    {{-- Bookings Table --}}
    <div class="card overflow-hidden">
        <table class="table-styled">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Guest</th>
                    <th>Property / Room</th>
                    <th>Dates</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Source</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                    <tr class="animate-fade-in">
                        <td>
                            <span class="font-mono text-xs font-semibold text-brand-primary">{{ $booking->booking_ref }}</span>
                        </td>
                        <td>
                            <div>
                                <p class="text-sm font-medium text-brand-black">{{ $booking->guest->name ?? 'N/A' }}</p>
                                <p class="text-xs text-brand-muted">{{ $booking->guest->email ?? '' }}</p>
                            </div>
                        </td>
                        <td>
                            <p class="text-sm text-brand-black">{{ $booking->property->name ?? '' }}</p>
                            <p class="text-xs text-brand-muted">{{ $booking->roomType->name ?? '' }}</p>
                        </td>
                        <td>
                            <p class="text-sm text-brand-black">{{ $booking->check_in->format('M d') }} – {{ $booking->check_out->format('M d') }}</p>
                            <p class="text-xs text-brand-muted">{{ $booking->nights }} nights</p>
                        </td>
                        <td>
                            <span class="text-sm font-semibold text-brand-black">${{ number_format($booking->total, 2) }}</span>
                        </td>
                        <td>
                            <span class="badge-{{ $booking->status_color }}">{{ $booking->status_label }}</span>
                        </td>
                        <td>
                            <span class="badge bg-brand-surface text-brand-text text-[10px]">{{ ucfirst(str_replace('_', ' ', $booking->source)) }}</span>
                        </td>
                        <td>
                            <a href="{{ route('property-owner.bookings.show', $booking) }}" class="btn-ghost btn-sm text-brand-primary">
                                View <i class="fas fa-chevron-right text-[10px]"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-12">
                            <i class="fas fa-calendar-xmark text-3xl text-brand-border mb-3 block"></i>
                            <p class="text-brand-muted text-sm">No bookings found</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">{{ $bookings->links() }}</div>
</x-pms-layout>
