<x-pms-layout :pageTitle="$hotel->name" :pageSubtitle="ucfirst($hotel->type) . ' · ' . ($hotel->city ?? 'Location not set')">
    <x-slot:headerActions>
        <a href="{{ route('property-owner.hotels.edit', $hotel) }}" class="btn-secondary btn-sm"><i class="fas fa-edit"></i> Edit</a>
        @if($hotel->isDraft())
            <form method="POST" action="{{ route('property-owner.hotels.submit-approval', $hotel) }}" class="inline">
                @csrf
                <button type="submit" class="btn-primary btn-sm"><i class="fas fa-paper-plane"></i> Submit for Approval</button>
            </form>
        @endif
    </x-slot:headerActions>

    {{-- Status Banner --}}
    @if($hotel->admin_notes && $hotel->status === 'draft')
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-5 flex items-start gap-3">
            <i class="fas fa-exclamation-triangle text-status-pending mt-0.5"></i>
            <div>
                <p class="text-sm font-medium text-brand-black">Changes Requested by Admin</p>
                <p class="text-xs text-brand-text mt-1">{{ $hotel->admin_notes }}</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-5">
            {{-- Property Details Card --}}
            <div class="card card-body">
                <div class="flex items-center gap-3 mb-4">
                    <span class="badge-{{ $hotel->status === 'approved' ? 'confirmed' : ($hotel->status === 'pending_approval' ? 'pending' : 'info') }}">
                        {{ ucfirst(str_replace('_', ' ', $hotel->status)) }}
                    </span>
                    <div class="flex items-center gap-0.5">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star text-sm {{ $i <= $hotel->stars ? 'text-yellow-400' : 'text-brand-border' }}"></i>
                        @endfor
                    </div>
                </div>
                @if($hotel->short_description)
                    <p class="text-sm text-brand-text mb-3">{{ $hotel->short_description }}</p>
                @endif
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div><span class="text-brand-muted">Location:</span> {{ $hotel->city }}, {{ $hotel->country }}</div>
                    <div><span class="text-brand-muted">Check-in:</span> {{ $hotel->check_in_time }}</div>
                    <div><span class="text-brand-muted">Check-out:</span> {{ $hotel->check_out_time }}</div>
                    <div><span class="text-brand-muted">Address:</span> {{ $hotel->address_line_1 }}</div>
                </div>
            </div>

            {{-- Photos --}}
            <div class="card card-body" x-data="photoManager({{ $hotel->id }}, {{ $hotel->photos->map(fn($p) => ['id' => $p->id, 'url' => $p->url, 'is_cover' => $p->is_cover])->toJson() }})">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-heading font-bold text-brand-black text-sm">Photos ({{ $hotel->photos->count() }})</h3>
                    <div class="flex items-center gap-2">
                        <template x-if="orderChanged">
                            <button @click="saveOrder()" :disabled="saving" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#d00e15] text-white text-xs font-semibold rounded-lg hover:bg-[#b00c12] transition-all shadow-sm disabled:opacity-50">
                                <i class="fas" :class="saving ? 'fa-spinner fa-spin' : 'fa-save'"></i>
                                <span x-text="saving ? 'Saving...' : 'Save Order'"></span>
                            </button>
                        </template>
                    </div>
                </div>
                @if($hotel->photos->isNotEmpty())
                    <div class="grid grid-cols-4 gap-2">
                        <template x-for="(photo, index) in photos" :key="photo.id">
                            <div 
                                class="aspect-[4/3] rounded-lg overflow-hidden bg-brand-surface relative group cursor-grab active:cursor-grabbing border-2 transition-all duration-200"
                                :class="dragOverId === photo.id ? 'border-[#d00e15] scale-[1.02] shadow-lg' : (photo.is_cover ? 'border-yellow-400' : 'border-transparent hover:border-gray-300')"
                                draggable="true"
                                @dragstart="dragStart($event, index)"
                                @dragend="dragEnd()"
                                @dragover.prevent="dragOverId = photo.id"
                                @dragleave="dragOverId = null"
                                @drop.prevent="drop(index)"
                            >
                                <img :src="photo.url" class="w-full h-full object-cover pointer-events-none">
                                
                                {{-- Cover badge --}}
                                <div x-show="photo.is_cover" class="absolute top-2 left-2 bg-yellow-400 text-yellow-900 text-[10px] font-bold px-2 py-0.5 rounded-full flex items-center gap-1 shadow-sm">
                                    <i class="fas fa-star text-[9px]"></i> Cover
                                </div>

                                {{-- Order number --}}
                                <div class="absolute bottom-2 left-2 bg-black/60 text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center" x-text="index + 1"></div>
                                
                                {{-- Hover actions --}}
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-all flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100">
                                    <button x-show="!photo.is_cover" @click.stop="setCover(photo.id)" class="bg-yellow-400 hover:bg-yellow-500 text-yellow-900 text-xs font-bold px-2.5 py-1.5 rounded-lg transition-colors shadow-md flex items-center gap-1" title="Set as cover photo">
                                        <i class="fas fa-star text-[10px]"></i> Cover
                                    </button>
                                    <form method="POST" :action="'{{ route('property-owner.hotels.photos.destroy', [$hotel, '__ID__']) }}'.replace('__ID__', photo.id)" @submit.prevent="if(confirm('Delete this photo?')) $el.submit()">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white w-8 h-8 rounded-lg flex items-center justify-center shadow-md transition-colors" title="Delete photo">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </template>
                    </div>
                    <p class="text-xs text-brand-muted mt-3 flex items-center gap-1.5"><i class="fas fa-arrows-alt text-brand-muted"></i> Drag photos to reorder. Click ⭐ Cover to set the main photo.</p>
                @else
                    <p class="text-sm text-brand-muted">No photos uploaded yet.</p>
                @endif

                {{-- Toast notification --}}
                <div x-show="toast" x-transition.opacity class="fixed bottom-6 right-6 bg-gray-900 text-white text-sm font-medium px-4 py-3 rounded-xl shadow-2xl z-50 flex items-center gap-2" style="display:none;">
                    <i class="fas fa-check-circle text-green-400"></i>
                    <span x-text="toast"></span>
                </div>
            </div>

            {{-- Room Types --}}
            <div class="card card-body">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-heading font-bold text-brand-black text-sm">Room Types ({{ $hotel->roomTypes->count() }})</h3>
                    <a href="{{ route('property-owner.hotels.rooms.index', $hotel) }}" class="text-xs text-brand-primary hover:underline">Manage →</a>
                </div>
                @foreach($hotel->roomTypes as $room)
                    <div class="flex items-center justify-between py-2 {{ !$loop->last ? 'border-b border-brand-border' : '' }}">
                        <div>
                            <p class="text-sm font-medium text-brand-black">{{ $room->name }}</p>
                            <p class="text-xs text-brand-muted">{{ $room->inventory_count }} rooms · {{ $room->max_adults }} adults</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-brand-primary">{{ \App\Helpers\Currency::format($room->base_price_per_night) }}</p>
                            <span class="badge-{{ $room->status === 'active' ? 'confirmed' : 'cancelled' }} text-[10px]">{{ ucfirst($room->status) }}</span>
                        </div>
                    </div>
                @endforeach
                @if($hotel->roomTypes->isEmpty())
                    <a href="{{ route('property-owner.hotels.rooms.create', $hotel) }}" class="btn-secondary w-full text-center"><i class="fas fa-plus"></i> Add Room Type</a>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-5">
            <div class="card card-body">
                <h3 class="font-heading font-bold text-brand-black text-sm mb-3">Quick Links</h3>
                <div class="space-y-2">
                    <a href="{{ route('property-owner.hotels.rooms.index', $hotel) }}" class="flex items-center gap-2 text-sm text-brand-text hover:text-brand-primary p-2 rounded-lg hover:bg-brand-surface transition-colors">
                        <i class="fas fa-bed w-5 text-center text-brand-muted"></i> Room Types
                    </a>
                    <a href="{{ route('property-owner.hotels.rate-rules.index', $hotel) }}" class="flex items-center gap-2 text-sm text-brand-text hover:text-brand-primary p-2 rounded-lg hover:bg-brand-surface transition-colors">
                        <i class="fas fa-tags w-5 text-center text-brand-muted"></i> Rate Rules
                    </a>
                    <a href="{{ route('property-owner.availability.index', $hotel) }}" class="flex items-center gap-2 text-sm text-brand-text hover:text-brand-primary p-2 rounded-lg hover:bg-brand-surface transition-colors">
                        <i class="fas fa-calendar w-5 text-center text-brand-muted"></i> Availability
                    </a>
                    <a href="{{ route('property-owner.bookings.index', ['property_id' => $hotel->id]) }}" class="flex items-center gap-2 text-sm text-brand-text hover:text-brand-primary p-2 rounded-lg hover:bg-brand-surface transition-colors">
                        <i class="fas fa-calendar-check w-5 text-center text-brand-muted"></i> Bookings
                    </a>
                </div>
            </div>
            <div class="card card-body">
                <h3 class="font-heading font-bold text-brand-black text-sm mb-2">Danger Zone</h3>
                <form method="POST" action="{{ route('property-owner.hotels.destroy', $hotel) }}" x-data @submit.prevent="if(confirm('Delete this property? This action cannot be undone.')) $el.submit()">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-danger btn-sm w-full"><i class="fas fa-trash"></i> Delete Property</button>
                </form>
            </div>
        </div>
    </div>
</x-pms-layout>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('photoManager', (hotelId, initialPhotos) => ({
        hotelId,
        photos: initialPhotos,
        draggedIndex: null,
        dragOverId: null,
        orderChanged: false,
        saving: false,
        toast: '',
        
        dragStart(event, index) {
            this.draggedIndex = index;
            event.dataTransfer.effectAllowed = 'move';
            // Slight delay so dragging visual looks correct
            setTimeout(() => {
                event.target.style.opacity = '0.5';
            }, 0);
        },
        
        dragEnd(event) {
            this.dragOverId = null;
            if(event && event.target) {
                event.target.style.opacity = '1';
            }
        },
        
        drop(index) {
            this.dragOverId = null;
            if (this.draggedIndex === null || this.draggedIndex === index) return;
            
            // Move item in array
            const item = this.photos.splice(this.draggedIndex, 1)[0];
            this.photos.splice(index, 0, item);
            this.orderChanged = true;
        },
        
        async saveOrder() {
            this.saving = true;
            try {
                const response = await fetch(`/property-owner/hotels/${this.hotelId}/photos/reorder`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        order: this.photos.map(p => p.id)
                    })
                });
                if (response.ok) {
                    this.orderChanged = false;
                    this.showToast('Photo order saved successfully!');
                }
            } catch (error) {
                console.error('Error saving order', error);
                alert('Failed to save photo order. Please try again.');
            } finally {
                this.saving = false;
            }
        },
        
        async setCover(photoId) {
            try {
                const response = await fetch(`/property-owner/hotels/${this.hotelId}/photos/${photoId}/set-cover`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });
                
                if (response.ok) {
                    this.photos = this.photos.map(p => ({ ...p, is_cover: p.id === photoId }));
                    this.showToast('Cover photo updated!');
                }
            } catch (error) {
                console.error('Error setting cover', error);
                alert('Failed to set cover photo. Please try again.');
            }
        },
        
        showToast(message) {
            this.toast = message;
            setTimeout(() => this.toast = '', 3000);
        }
    }));
});
</script>
@endpush
