<x-pms-layout pageTitle="Edit Room Type" :pageSubtitle="$room->name . ' — ' . $hotel->name">

<div class="max-w-3xl">
    <form method="POST" action="{{ route('property-owner.hotels.rooms.update', [$hotel, $room]) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf @method('PUT')

        <div class="card card-body space-y-5">
            <h2 class="section-heading text-base"><i class="fas fa-bed text-brand-primary mr-2"></i>Room Details</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="form-group md:col-span-2">
                    <label class="form-label">Room Name *</label>
                    <input type="text" name="name" class="form-input-styled" value="{{ old('name', $room->name) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Size (sqm)</label>
                    <input type="number" name="size_sqm" class="form-input-styled" min="1" value="{{ old('size_sqm', $room->size_sqm) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Floor Level</label>
                    <input type="text" name="floor_level" class="form-input-styled" value="{{ old('floor_level', $room->floor_level) }}">
                </div>
            </div>
        </div>

        <div class="card card-body space-y-5">
            <h2 class="section-heading text-base"><i class="fas fa-users text-brand-primary mr-2"></i>Occupancy & Beds</h2>
            <div class="grid grid-cols-3 gap-5">
                <div class="form-group">
                    <label class="form-label">Max Adults *</label>
                    <input type="number" name="max_adults" class="form-input-styled" value="{{ old('max_adults', $room->max_adults) }}" min="1" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Max Children</label>
                    <input type="number" name="max_children" class="form-input-styled" value="{{ old('max_children', $room->max_children) }}" min="0">
                </div>
                <div class="form-group">
                    <label class="form-label">Max Infants</label>
                    <input type="number" name="max_infants" class="form-input-styled" value="{{ old('max_infants', $room->max_infants) }}" min="0">
                </div>
            </div>

            <div x-data="{ beds: {{ json_encode($room->bed_config ?: [['type' => 'king', 'count' => 1]]) }} }">
                <label class="form-label">Bed Configuration</label>
                <template x-for="(bed, index) in beds" :key="index">
                    <div class="flex items-center gap-2 mb-2">
                        <select :name="'bed_config[' + index + '][type]'" x-model="bed.type" class="form-input-styled text-sm flex-1">
                            @foreach(\App\Models\RoomType::getBedTypes() as $bedType)
                                <option value="{{ $bedType }}">{{ ucfirst($bedType) }}</option>
                            @endforeach
                        </select>
                        <input type="number" :name="'bed_config[' + index + '][count]'" x-model="bed.count" min="1" class="form-input-styled text-sm w-20">
                        <button type="button" @click="beds.splice(index, 1)" x-show="beds.length > 1" class="btn-ghost btn-sm text-red-500"><i class="fas fa-trash"></i></button>
                    </div>
                </template>
                <button type="button" @click="beds.push({type: 'twin', count: 1})" class="text-xs text-brand-primary hover:underline"><i class="fas fa-plus"></i> Add bed</button>
            </div>
        </div>

        <div class="card card-body space-y-5">
            <h2 class="section-heading text-base"><i class="fas fa-dollar-sign text-brand-primary mr-2"></i>Pricing & Inventory</h2>
            <div class="grid grid-cols-2 gap-5">
                <div class="form-group">
                    <label class="form-label">Base Price per Night ($) *</label>
                    <input type="number" name="base_price_per_night" class="form-input-styled" step="0.01" min="0" value="{{ old('base_price_per_night', $room->base_price_per_night) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Total Rooms of This Type *</label>
                    <input type="number" name="inventory_count" class="form-input-styled" min="1" value="{{ old('inventory_count', $room->inventory_count) }}" required>
                </div>
            </div>

            <div>
                <label class="form-label">Rate Plans</label>
                <p class="text-xs text-brand-muted mb-3">Room Only is included by default. Enable additional meal plans:</p>
                @foreach(['BB' => 'Bed & Breakfast', 'HB' => 'Half Board', 'FB' => 'Full Board', 'AI' => 'All Inclusive'] as $code => $name)
                    @php $existingPlan = $room->ratePlans->where('plan_code', $code)->first(); @endphp
                    <div x-data="{ enabled: {{ $existingPlan && $existingPlan->is_active ? 'true' : 'false' }} }" class="flex items-center gap-3 mb-2 p-2 rounded-lg" :class="enabled ? 'bg-brand-light' : 'bg-brand-surface'">
                        <input type="checkbox" name="rate_plans[{{ $code }}][enabled]" value="1" x-model="enabled" class="rounded border-brand-border text-brand-primary">
                        <span class="text-sm font-medium text-brand-black flex-1">{{ $name }}</span>
                        <div x-show="enabled" class="flex items-center gap-1">
                            <span class="text-xs text-brand-muted">+ $</span>
                            <input type="number" name="rate_plans[{{ $code }}][supplement]" class="form-input-styled text-sm w-20 py-1" step="0.01" value="{{ $existingPlan->price_supplement_per_adult ?? 0 }}" placeholder="0">
                            <span class="text-xs text-brand-muted">/adult</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card card-body">
            <h2 class="section-heading text-base"><i class="fas fa-concierge-bell text-brand-primary mr-2"></i>Room Amenities</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @php $existingAmenities = $room->amenities ?? []; @endphp
                @foreach(\App\Models\RoomType::getAmenityOptions() as $amenity)
                    <label class="cursor-pointer border rounded-2xl p-5 flex flex-col justify-between min-h-[120px] transition-all duration-200" 
                           x-data="{ on: {{ in_array($amenity, $existingAmenities) ? 'true' : 'false' }} }" 
                           :class="on ? 'border-brand-black bg-gray-50 border-2 shadow-sm' : 'border-gray-200 bg-white hover:border-gray-900'">
                        <input type="checkbox" name="amenities[]" value="{{ $amenity }}" class="hidden" x-model="on">
                        <div class="mb-4">
                            <i class="fas fa-check-circle text-3xl" :class="on ? 'text-brand-primary' : 'text-gray-300'"></i>
                        </div>
                        <span class="font-semibold text-sm leading-tight text-brand-black">{{ $amenity }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="card card-body space-y-4">
            <h2 class="section-heading text-base"><i class="fas fa-camera text-brand-primary mr-2"></i>Manage Photos</h2>
            
            @if($room->photos->isNotEmpty())
                <div class="grid grid-cols-4 gap-3 mb-4">
                    @foreach($room->photos as $photo)
                        <div class="aspect-[4/3] rounded-lg overflow-hidden bg-brand-surface relative group border border-gray-200">
                            <img src="{{ $photo->url }}" class="w-full h-full object-cover">
                            <button type="button" 
                                onclick="if(confirm('Are you sure you want to remove this photo?')) { let f = document.getElementById('delete-photo-form'); f.action = '{{ route('property-owner.hotels.rooms.photos.destroy', [$hotel, $room, $photo]) }}'; f.submit(); }" 
                                class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-7 h-7 flex items-center justify-center hover:bg-red-600 shadow-md">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-brand-muted">No photos uploaded yet.</p>
            @endif

            <div>
                <label class="form-label text-sm font-semibold mb-2 block">Upload New Photos</label>
                <input type="file" name="photos[]" multiple accept="image/*" class="form-input-styled">
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('property-owner.hotels.rooms.index', $hotel) }}" class="btn-ghost">Cancel</a>
            <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Save Changes</button>
        </div>
    </form>
</div>

{{-- Hidden form for photo deletion --}}
<form id="delete-photo-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

</x-pms-layout>
