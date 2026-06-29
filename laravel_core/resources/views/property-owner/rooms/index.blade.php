<x-pms-layout pageTitle="Room Types" :pageSubtitle="$hotel->name">
    <x-slot:headerActions>
        <a href="{{ route('property-owner.hotels.rooms.create', $hotel) }}" class="btn-primary btn-sm"><i class="fas fa-plus"></i> Add Room Type</a>
    </x-slot:headerActions>

    @if($roomTypes->isEmpty())
        <div class="card card-body text-center py-16">
            <i class="fas fa-bed text-5xl text-brand-border mb-4"></i>
            <h3 class="font-heading text-xl font-bold text-brand-black mb-2">No room types yet</h3>
            <p class="text-brand-muted text-sm mb-6">Add at least one room type before submitting your property.</p>
            <a href="{{ route('property-owner.hotels.rooms.create', $hotel) }}" class="btn-primary inline-flex"><i class="fas fa-plus"></i> Add Room Type</a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach($roomTypes as $room)
                <div class="card overflow-hidden animate-slide-up" style="animation-delay: {{ $loop->index * 0.05 }}s">
                    <div class="h-40 bg-brand-surface relative overflow-hidden">
                        @if($room->photos_count > 0 && $room->photos->isNotEmpty())
                            <img src="{{ $room->photos->first()->url }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center"><i class="fas fa-bed text-4xl text-brand-border"></i></div>
                        @endif
                        <div class="absolute top-3 right-3">
                            <span class="badge-{{ $room->status === 'active' ? 'confirmed' : 'cancelled' }}">{{ ucfirst($room->status) }}</span>
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="font-heading font-bold text-brand-black mb-1">{{ $room->name }}</h3>
                        <div class="flex items-center gap-3 text-xs text-brand-muted mb-3">
                            @if($room->size_sqm)<span><i class="fas fa-expand"></i> {{ $room->size_sqm }}m²</span>@endif
                            <span><i class="fas fa-user"></i> {{ $room->max_adults }}+{{ $room->max_children }}</span>
                            <span><i class="fas fa-bed"></i> {{ $room->bed_config_display }}</span>
                        </div>
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <p class="text-xl font-heading font-bold text-brand-primary">${{ number_format($room->base_price_per_night, 0) }}</p>
                                <p class="text-[10px] text-brand-muted">per night</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-brand-black">{{ $room->inventory_count }}</p>
                                <p class="text-[10px] text-brand-muted">rooms</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 pt-3 border-t border-brand-border">
                            <a href="{{ route('property-owner.hotels.rooms.edit', [$hotel, $room]) }}" class="btn-ghost btn-sm flex-1 text-center"><i class="fas fa-edit"></i> Edit</a>
                            <form method="POST" action="{{ route('property-owner.hotels.rooms.toggle-status', [$hotel, $room]) }}">
                                @csrf
                                <button class="btn-ghost btn-sm"><i class="fas {{ $room->status === 'active' ? 'fa-eye-slash' : 'fa-eye' }}"></i></button>
                            </form>
                            <form method="POST" action="{{ route('property-owner.hotels.rooms.duplicate', [$hotel, $room]) }}">
                                @csrf
                                <button class="btn-ghost btn-sm"><i class="fas fa-clone"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-pms-layout>
