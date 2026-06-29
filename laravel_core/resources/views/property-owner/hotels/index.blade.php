<x-pms-layout pageTitle="My Properties" pageSubtitle="Manage your hotel listings">
    <x-slot:headerActions>
        <a href="{{ route('property-owner.hotels.create') }}" class="btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add Property
        </a>
    </x-slot:headerActions>

    @if($properties->isEmpty())
        {{-- Empty State --}}
        <div class="card card-body text-center py-16 animate-fade-in">
            <div class="w-20 h-20 rounded-full bg-brand-light mx-auto mb-5 flex items-center justify-center">
                <i class="fas fa-hotel text-3xl text-brand-primary"></i>
            </div>
            <h3 class="font-heading text-xl font-bold text-brand-black mb-2">No properties yet</h3>
            <p class="text-brand-muted text-sm mb-6 max-w-md mx-auto">
                Start by adding your first property. Our step-by-step wizard will guide you through the setup process.
            </p>
            <a href="{{ route('property-owner.hotels.create') }}" class="btn-primary inline-flex">
                <i class="fas fa-plus"></i> Add Your First Property
            </a>
        </div>
    @else
        {{-- Properties Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach($properties as $property)
                <div class="card card-interactive overflow-hidden animate-slide-up" style="animation-delay: {{ $loop->index * 0.05 }}s">
                    {{-- Cover Image --}}
                    <div class="relative h-44 bg-brand-surface overflow-hidden">
                        @if($property->cover_photo_url)
                            <img src="{{ $property->cover_photo_url }}" alt="{{ $property->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-brand-light to-white">
                                <i class="fas fa-image text-4xl text-brand-border"></i>
                            </div>
                        @endif

                        {{-- Status Badge --}}
                        <div class="absolute top-3 right-3">
                            @switch($property->status)
                                @case('approved')
                                    <span class="badge-confirmed"><i class="fas fa-check-circle"></i> Live</span>
                                    @break
                                @case('pending_approval')
                                    <span class="badge-pending"><i class="fas fa-clock"></i> Pending Review</span>
                                    @break
                                @case('draft')
                                    <span class="badge-info"><i class="fas fa-edit"></i> Draft</span>
                                    @break
                                @case('rejected')
                                    <span class="badge-cancelled"><i class="fas fa-times-circle"></i> Rejected</span>
                                    @break
                            @endswitch
                        </div>

                        {{-- Stars --}}
                        <div class="absolute bottom-3 left-3 flex items-center gap-0.5">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star text-xs {{ $i <= $property->stars ? 'text-yellow-400' : 'text-white/40' }}"></i>
                            @endfor
                        </div>
                    </div>

                    {{-- Card Body --}}
                    <div class="p-4">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <h3 class="font-heading font-bold text-brand-black text-sm">{{ $property->name }}</h3>
                                <p class="text-xs text-brand-muted mt-0.5">
                                    <i class="fas fa-map-marker-alt text-brand-primary"></i>
                                    {{ $property->city ?? 'Location not set' }}{{ $property->country ? ', ' . $property->country : '' }}
                                </p>
                            </div>
                            <span class="badge text-[10px] bg-brand-surface text-brand-text">{{ ucfirst($property->type) }}</span>
                        </div>

                        {{-- Stats Row --}}
                        <div class="flex items-center gap-4 mt-3 pt-3 border-t border-brand-border">
                            <div class="text-center">
                                <p class="text-xs font-bold text-brand-black">{{ $property->room_types_count }}</p>
                                <p class="text-[10px] text-brand-muted">Rooms</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs font-bold text-brand-black">{{ $property->hotel_bookings_count }}</p>
                                <p class="text-[10px] text-brand-muted">Bookings</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs font-bold text-brand-black">{{ $property->reviews_count }}</p>
                                <p class="text-[10px] text-brand-muted">Reviews</p>
                            </div>
                            <div class="ml-auto">
                                <a href="{{ route('property-owner.hotels.show', $property) }}" class="btn-ghost btn-sm text-brand-primary">
                                    Manage <i class="fas fa-chevron-right text-[10px]"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-pms-layout>
