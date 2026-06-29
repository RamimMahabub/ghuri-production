<x-pms-layout pageTitle="Edit Property" :pageSubtitle="$hotel->name">
<div class="max-w-3xl">
    <form method="POST" action="{{ route('property-owner.hotels.update', $hotel) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf @method('PUT')
        <div class="card card-body space-y-5">
            <h2 class="section-heading text-base">Basic Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="form-group md:col-span-2">
                    <label class="form-label">Property Name *</label>
                    <input type="text" name="name" class="form-input-styled" value="{{ old('name', $hotel->name) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Type *</label>
                    <select name="type" class="form-input-styled">
                        @foreach(\App\Models\Property::getTypes() as $type)
                            <option value="{{ $type }}" {{ $hotel->type === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Stars *</label>
                    <select name="stars" class="form-input-styled">
                        @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ $hotel->stars == $i ? 'selected' : '' }}>{{ $i }} Star</option>
                        @endfor
                    </select>
                </div>
                <div class="form-group md:col-span-2">
                    <label class="form-label">Short Description</label>
                    <input type="text" name="short_description" class="form-input-styled" value="{{ old('short_description', $hotel->short_description) }}">
                </div>
                <div class="form-group md:col-span-2">
                    <label class="form-label">Full Description</label>
                    <textarea name="full_description" rows="4" class="form-input-styled">{{ old('full_description', $hotel->full_description) }}</textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Check-in Time</label>
                    <input type="text" name="check_in_time" class="form-input-styled" value="{{ old('check_in_time', $hotel->check_in_time) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Check-out Time</label>
                    <input type="text" name="check_out_time" class="form-input-styled" value="{{ old('check_out_time', $hotel->check_out_time) }}">
                </div>
            </div>
        </div>

        {{-- Location Section --}}
        <div class="card card-body space-y-6" x-data="mapPicker({{ old('latitude', $hotel->latitude ?? 23.8103) }}, {{ old('longitude', $hotel->longitude ?? 90.4125) }})">
            <div>
                <h2 class="section-heading text-base mb-1">Property Location</h2>
                <p class="text-sm text-brand-muted">Search for your property location</p>
            </div>

            {{-- Search Bar --}}
            <div class="relative">
                <div class="flex items-center w-full bg-gray-50 border border-gray-200 focus-within:border-brand-primary focus-within:bg-white focus-within:ring-1 focus-within:ring-brand-primary/20 rounded-lg px-4 py-3 transition-all">
                    <i class="fas fa-search text-gray-500 mr-3 text-lg"></i>
                    <input type="text" x-ref="searchInput" class="flex-grow bg-transparent border-none outline-none focus:ring-0 text-brand-black placeholder-gray-500 text-sm p-0" placeholder="Search for your property location">
                </div>
            </div>

            <div class="mt-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-7 gap-x-5">
                    <div class="relative md:col-span-2">
                        <label class="absolute -top-2.5 left-3 bg-white px-1.5 text-[11px] font-medium text-gray-500 z-10">Country/Region</label>
                        <input type="text" name="country" class="block w-full px-4 py-3.5 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:outline-none focus:ring-1 focus:ring-brand-primary focus:border-brand-primary transition-colors" required value="{{ old('country', $hotel->country) }}">
                    </div>
                    
                    <div class="relative">
                        <label class="absolute -top-2.5 left-3 bg-white px-1.5 text-[11px] font-medium text-gray-500 z-10">State/Province</label>
                        <input type="text" name="state" class="block w-full px-4 py-3.5 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:outline-none focus:ring-1 focus:ring-brand-primary focus:border-brand-primary transition-colors" value="{{ old('state') }}">
                    </div>
                    
                    <div class="relative">
                        <label class="absolute -top-2.5 left-3 bg-white px-1.5 text-[11px] font-medium text-gray-500 z-10">City</label>
                        <input type="text" name="city" class="block w-full px-4 py-3.5 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:outline-none focus:ring-1 focus:ring-brand-primary focus:border-brand-primary transition-colors" required value="{{ old('city', $hotel->city) }}">
                    </div>
                    
                    <div class="relative md:col-span-2">
                        <label class="absolute -top-2.5 left-3 bg-white px-1.5 text-[11px] font-medium text-gray-500 z-10">Street address in English</label>
                        <input type="text" name="address_line_1" class="block w-full px-4 py-3.5 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:outline-none focus:ring-1 focus:ring-brand-primary focus:border-brand-primary transition-colors" value="{{ old('address_line_1', $hotel->address_line_1) }}">
                    </div>
                    
                    <div class="relative md:col-span-2">
                        <label class="absolute -top-2.5 left-3 bg-white px-1.5 text-[11px] font-medium text-gray-500 z-10">Building, floor or unit number (optional)</label>
                        <input type="text" name="address_line_2" class="block w-full px-4 py-3.5 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:outline-none focus:ring-1 focus:ring-brand-primary focus:border-brand-primary transition-colors" value="{{ old('address_line_2') }}">
                    </div>
                    
                    <div class="relative md:col-span-2">
                        <label class="absolute -top-2.5 left-3 bg-white px-1.5 text-[11px] font-medium text-gray-500 z-10">ZIP/Postal code (optional)</label>
                        <input type="text" name="postal_code" class="block w-full px-4 py-3.5 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:outline-none focus:ring-1 focus:ring-brand-primary focus:border-brand-primary transition-colors" value="{{ old('postal_code') }}">
                    </div>
                </div>

                {{-- Hidden lat/lng fields --}}
                <input type="hidden" name="latitude" x-model="lat">
                <input type="hidden" name="longitude" x-model="lng">
            </div>

            {{-- Map Display --}}
            <div class="mt-8">
                <div class="rounded-xl overflow-hidden border border-gray-200 shadow-sm relative">
                    <div id="map" class="w-full z-10" style="height: 350px;" x-ref="mapContainer"></div>
                </div>
                <p class="text-[13px] text-gray-500 mt-3">Is this the correct location of your property? If not, drag the pin to the correct location.</p>
            </div>
        </div>
        <div class="card card-body space-y-4" x-data="photoManager({{ $hotel->id }}, {{ $hotel->photos->map(fn($p) => ['id' => $p->id, 'url' => $p->url, 'is_cover' => $p->is_cover])->toJson() }})">
            <div class="flex items-center justify-between mb-3">
                <h2 class="section-heading text-base mb-0">Manage Photos</h2>
                <div class="flex items-center gap-2">
                    <template x-if="orderChanged">
                        <button type="button" @click.prevent="saveOrder()" :disabled="saving" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#d00e15] text-white text-xs font-semibold rounded-lg hover:bg-[#b00c12] transition-all shadow-sm disabled:opacity-50">
                            <i class="fas" :class="saving ? 'fa-spinner fa-spin' : 'fa-save'"></i>
                            <span x-text="saving ? 'Saving...' : 'Save Order'"></span>
                        </button>
                    </template>
                </div>
            </div>
            
            @if($hotel->photos->isNotEmpty())
                <div class="grid grid-cols-4 gap-3 mb-4">
                    <template x-for="(photo, index) in photos" :key="photo.id">
                        <div 
                            class="aspect-[4/3] rounded-lg overflow-hidden bg-brand-surface relative group cursor-grab active:cursor-grabbing border-2 transition-all duration-200"
                            :class="dragOverId === photo.id ? 'border-[#d00e15] scale-[1.02] shadow-lg' : (photo.is_cover ? 'border-yellow-400' : 'border-gray-200 hover:border-gray-300')"
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
                                <button type="button" x-show="!photo.is_cover" @click.stop="setCover(photo.id)" class="bg-yellow-400 hover:bg-yellow-500 text-yellow-900 text-xs font-bold px-2.5 py-1.5 rounded-lg transition-colors shadow-md flex items-center gap-1" title="Set as cover photo">
                                    <i class="fas fa-star text-[10px]"></i> Cover
                                </button>
                                <button type="button" 
                                    @click.stop="if(confirm('Are you sure you want to remove this photo?')) { let f = document.getElementById('delete-photo-form'); f.action = '{{ route('property-owner.hotels.photos.destroy', [$hotel, '__ID__']) }}'.replace('__ID__', photo.id); f.submit(); }" 
                                    class="bg-red-500 hover:bg-red-600 text-white w-8 h-8 rounded-lg flex items-center justify-center shadow-md transition-colors" title="Delete photo">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
                <p class="text-xs text-brand-muted mt-1 mb-3 flex items-center gap-1.5"><i class="fas fa-arrows-alt text-brand-muted"></i> Drag photos to reorder. Click ⭐ Cover to set the main photo.</p>
            @else
                <p class="text-sm text-brand-muted mb-4">No photos uploaded yet.</p>
            @endif

            {{-- Toast notification --}}
            <div x-show="toast" x-transition.opacity class="fixed bottom-6 right-6 bg-gray-900 text-white text-sm font-medium px-4 py-3 rounded-xl shadow-2xl z-50 flex items-center gap-2" style="display:none;">
                <i class="fas fa-check-circle text-green-400"></i>
                <span x-text="toast"></span>
            </div>

            <div>
                <label class="form-label text-sm font-semibold mb-2 block">Upload New Photos</label>
                <input type="file" name="photos[]" multiple accept="image/*" class="form-input-styled">
            </div>
        </div>
        <div class="flex justify-end gap-3">
            <a href="{{ route('property-owner.hotels.show', $hotel) }}" class="btn-ghost">Cancel</a>
            <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Save Changes</button>
        </div>
    </form>
</div>

{{-- Hidden form for photo deletion --}}
<form id="delete-photo-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places"></script>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('mapPicker', (initialLat, initialLng) => ({
        lat: initialLat,
        lng: initialLng,
        map: null,
        marker: null,
        geocoder: null,
        autocomplete: null,
        
        async reverseGeocode(lat, lng) {
            if (!this.geocoder) this.geocoder = new google.maps.Geocoder();
            
            try {
                const response = await this.geocoder.geocode({ location: { lat: parseFloat(lat), lng: parseFloat(lng) } });
                if (response.results && response.results.length > 0) {
                    this.fillAddressFields(response.results[0]);
                }
            } catch (e) {
                console.error('Reverse geocoding failed', e);
            }
        },

        fillAddressFields(place) {
            let address1 = "";
            let postcode = "";
            let city = "";
            let state = "";
            let country = "";
            let neighborhood = "";
            let streetNumber = "";
            let route = "";

            for (const component of place.address_components) {
                const componentType = component.types[0];
                switch (componentType) {
                    case "street_number":
                        streetNumber = component.long_name;
                        break;
                    case "route":
                        route = component.short_name;
                        break;
                    case "postal_code":
                        postcode = component.long_name;
                        break;
                    case "locality":
                        city = component.long_name;
                        break;
                    case "administrative_area_level_1":
                        state = component.long_name;
                        break;
                    case "country":
                        country = component.long_name;
                        break;
                    case "neighborhood":
                    case "sublocality_level_1":
                        neighborhood = component.long_name;
                        break;
                }
            }
            
            address1 = `${streetNumber} ${route}`.trim();

            const elCity = document.querySelector('input[name="city"]');
            const elState = document.querySelector('input[name="state"]');
            const elCountry = document.querySelector('input[name="country"]');
            const elPostal = document.querySelector('input[name="postal_code"]');
            const elAddress1 = document.querySelector('input[name="address_line_1"]');
            const elNeighborhood = document.querySelector('input[name="neighborhood"]');
            
            if (elCity && city) elCity.value = city;
            if (elState && state) elState.value = state;
            if (elCountry && country) elCountry.value = country;
            if (elPostal && postcode) elPostal.value = postcode;
            if (elAddress1 && address1) elAddress1.value = address1;
            if (elNeighborhood && neighborhood) elNeighborhood.value = neighborhood;
        },

        initMap() {
            if (typeof google === 'undefined') {
                console.error('Google Maps API not loaded.');
                return;
            }
            
            const position = { lat: parseFloat(this.lat) || 23.8103, lng: parseFloat(this.lng) || 90.4125 };
            
            this.map = new google.maps.Map(this.$refs.mapContainer, {
                center: position,
                zoom: 13,
                mapTypeControl: false,
                streetViewControl: false,
            });

            this.marker = new google.maps.Marker({
                position: position,
                map: this.map,
                draggable: true
            });

            this.marker.addListener("dragend", async (e) => {
                const pos = e.latLng;
                this.lat = pos.lat().toFixed(7);
                this.lng = pos.lng().toFixed(7);
                await this.reverseGeocode(this.lat, this.lng);
            });

            this.map.addListener("click", async (e) => {
                this.marker.setPosition(e.latLng);
                this.lat = e.latLng.lat().toFixed(7);
                this.lng = e.latLng.lng().toFixed(7);
                await this.reverseGeocode(this.lat, this.lng);
            });
            
            if (this.$refs.searchInput) {
                this.autocomplete = new google.maps.places.Autocomplete(this.$refs.searchInput, {
                    fields: ["address_components", "geometry", "name"],
                });
                
                this.autocomplete.addListener("place_changed", () => {
                    const place = this.autocomplete.getPlace();
                    if (!place.geometry || !place.geometry.location) {
                        return;
                    }
                    
                    this.lat = place.geometry.location.lat().toFixed(7);
                    this.lng = place.geometry.location.lng().toFixed(7);
                    
                    this.map.setCenter(place.geometry.location);
                    this.map.setZoom(17);
                    this.marker.setPosition(place.geometry.location);
                    
                    this.fillAddressFields(place);
                });
            }
        },

        init() {
            const visibilityCheck = setInterval(() => {
                const el = this.$refs.mapContainer;
                if (el && el.offsetHeight > 0) {
                    if (!this.map) {
                        this.initMap();
                    }
                }
            }, 250);

            this.$watch('lat', value => {
                if (this.marker && !isNaN(value) && value !== '') {
                    const pos = { lat: parseFloat(value), lng: parseFloat(this.lng) };
                    this.marker.setPosition(pos);
                    this.map.panTo(pos);
                }
            });

            this.$watch('lng', value => {
                if (this.marker && !isNaN(value) && value !== '') {
                    const pos = { lat: parseFloat(this.lat), lng: parseFloat(value) };
                    this.marker.setPosition(pos);
                    this.map.panTo(pos);
                }
            });
        }
    }));

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

</x-pms-layout>
