<x-admin-layout>
    <x-slot name="pageTitle">Review Property: {{ $property->name }}</x-slot>
    <x-slot name="pageSubtitle">Check details and approve or reject</x-slot>
    
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('admin.properties.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-semibold transition-colors flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Back to Properties
        </a>
        
        <div>
            @switch($property->status)
                @case('approved')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                        <i class="fas fa-check-circle mr-1.5"></i> Live / Approved
                    </span>
                    @break
                @case('pending_approval')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800">
                        <i class="fas fa-clock mr-1.5"></i> Pending Review
                    </span>
                    @break
                @case('draft')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-800">
                        <i class="fas fa-edit mr-1.5"></i> Draft / Needs Changes
                    </span>
                    @break
                @case('rejected')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">
                        <i class="fas fa-times-circle mr-1.5"></i> Rejected
                    </span>
                    @break
            @endswitch
        </div>
    </div>

    {{-- Hero Section --}}
    <div class="relative w-full h-64 rounded-2xl overflow-hidden shadow-lg mb-8">
        @if($property->cover_photo_url && !str_contains($property->cover_photo_url, 'placeholder'))
            <img src="{{ $property->cover_photo_url }}" alt="{{ $property->name }}" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full bg-gradient-to-r from-slate-800 to-slate-900 flex flex-col items-center justify-center text-white/50">
                <i class="fas fa-hotel text-6xl mb-4 opacity-50"></i>
                <span class="text-sm font-medium">No Cover Photo</span>
            </div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>
        <div class="absolute bottom-0 left-0 p-8 w-full flex justify-between items-end">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="px-2.5 py-1 bg-white/20 backdrop-blur-md rounded-md text-xs font-bold uppercase tracking-wider text-white border border-white/20 shadow-sm">
                        {{ ucfirst($property->type) }}
                    </span>
                    <div class="flex items-center text-yellow-400 drop-shadow">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star text-sm {{ $i <= $property->stars ? 'text-yellow-400' : 'text-white/40' }}"></i>
                        @endfor
                    </div>
                </div>
                <h1 class="text-3xl font-bold font-heading text-white mb-1 drop-shadow-lg">{{ $property->name }}</h1>
                <p class="text-white font-medium text-sm flex items-center drop-shadow-md">
                    <i class="fas fa-map-marker-alt text-red-400 mr-2 drop-shadow-sm"></i>
                    {{ $property->full_address }}
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pb-12">
        <div class="lg:col-span-2 space-y-8">
            
            {{-- Overview --}}
            <section class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3 mb-4 flex items-center">
                    <i class="fas fa-info-circle text-blue-500 mr-2"></i> Property Overview
                </h3>
                <div class="prose prose-sm max-w-none text-slate-600 mb-6">
                    <p class="font-medium text-slate-800">{{ $property->short_description }}</p>
                    <p class="whitespace-pre-line mt-2">{{ $property->full_description }}</p>
                </div>
                
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 bg-slate-50 rounded-xl p-4">
                    <div>
                        <span class="block text-xs font-semibold text-slate-400 uppercase">Check-in</span>
                        <span class="font-bold text-slate-800"><i class="far fa-clock text-blue-500 mr-1"></i> {{ $property->check_in_time ?? '14:00' }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-semibold text-slate-400 uppercase">Check-out</span>
                        <span class="font-bold text-slate-800"><i class="far fa-clock text-blue-500 mr-1"></i> {{ $property->check_out_time ?? '12:00' }}</span>
                    </div>
                    @if(!empty($property->languages_spoken))
                    <div>
                        <span class="block text-xs font-semibold text-slate-400 uppercase">Languages</span>
                        <span class="font-medium text-slate-800 text-sm">{{ implode(', ', $property->languages_spoken) }}</span>
                    </div>
                    @endif
                </div>
            </section>

            {{-- Location & Distances --}}
            <section class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3 mb-4 flex items-center">
                    <i class="fas fa-map-marked-alt text-red-500 mr-2"></i> Location Details
                </h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                    @if($property->airport_distance)
                    <div class="border border-slate-100 rounded-lg p-3 flex items-start">
                        <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center mr-3 shrink-0">
                            <i class="fas fa-plane"></i>
                        </div>
                        <div>
                            <div class="text-xs text-slate-500 font-medium">Nearest Airport</div>
                            <div class="text-sm font-bold text-slate-800">{{ $property->airport_distance }}</div>
                        </div>
                    </div>
                    @endif
                    
                    @if($property->city_center_distance)
                    <div class="border border-slate-100 rounded-lg p-3 flex items-start">
                        <div class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mr-3 shrink-0">
                            <i class="fas fa-city"></i>
                        </div>
                        <div>
                            <div class="text-xs text-slate-500 font-medium">City Center</div>
                            <div class="text-sm font-bold text-slate-800">{{ $property->city_center_distance }}</div>
                        </div>
                    </div>
                    @endif
                    
                    @if($property->beach_distance)
                    <div class="border border-slate-100 rounded-lg p-3 flex items-start">
                        <div class="w-8 h-8 rounded-full bg-cyan-50 text-cyan-600 flex items-center justify-center mr-3 shrink-0">
                            <i class="fas fa-umbrella-beach"></i>
                        </div>
                        <div>
                            <div class="text-xs text-slate-500 font-medium">Beach Distance</div>
                            <div class="text-sm font-bold text-slate-800">{{ $property->beach_distance }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </section>

            {{-- Amenities --}}
            @if(!empty($property->amenities))
            <section class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3 mb-4 flex items-center">
                    <i class="fas fa-concierge-bell text-yellow-500 mr-2"></i> Property Amenities
                </h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($property->amenities as $category => $categoryAmenities)
                        @if(is_array($categoryAmenities))
                            @foreach($categoryAmenities as $amenity)
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-slate-50 border border-slate-200 text-slate-700">
                                    <i class="fas fa-check text-green-500 mr-2 text-xs"></i> {{ Str::headline($amenity) }}
                                </span>
                            @endforeach
                        @else
                            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-slate-50 border border-slate-200 text-slate-700">
                                <i class="fas fa-check text-green-500 mr-2 text-xs"></i> {{ Str::headline($categoryAmenities) }}
                            </span>
                        @endif
                    @endforeach
                </div>
            </section>
            @endif

            {{-- Policies --}}
            <section class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3 mb-4 flex items-center">
                    <i class="fas fa-file-contract text-purple-500 mr-2"></i> Property Policies
                </h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    @if($property->cancellation_policy)
                    <div>
                        <h4 class="text-sm font-bold text-slate-800 mb-1 flex items-center"><i class="fas fa-ban text-slate-400 mr-2"></i> Cancellation</h4>
                        <ul class="text-sm text-slate-600 space-y-1 pl-6 list-disc marker:text-slate-300">
                            @if(isset($property->cancellation_policy['type']))
                                <li>Type: <strong>{{ ucfirst(str_replace('_', ' ', $property->cancellation_policy['type'])) }}</strong></li>
                            @endif
                            @if(isset($property->cancellation_policy['free_cancel_days']))
                                <li>Free Cancellation: <strong>Up to {{ $property->cancellation_policy['free_cancel_days'] }} days before arrival</strong></li>
                            @endif
                            @if(!isset($property->cancellation_policy['type']) && !isset($property->cancellation_policy['free_cancel_days']))
                                @foreach((array)$property->cancellation_policy as $key => $val)
                                    <li>{{ is_string($key) ? ucfirst($key) . ': ' : '' }}{{ is_scalar($val) ? $val : json_encode($val) }}</li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                    @endif
                    
                    @if($property->children_policy)
                    <div>
                        <h4 class="text-sm font-bold text-slate-800 mb-1 flex items-center"><i class="fas fa-child text-slate-400 mr-2"></i> Children</h4>
                        <p class="text-sm text-slate-600 pl-6">{{ $property->children_policy }}</p>
                    </div>
                    @endif
                    
                    @if($property->pet_policy)
                    <div>
                        <h4 class="text-sm font-bold text-slate-800 mb-1 flex items-center"><i class="fas fa-paw text-slate-400 mr-2"></i> Pets</h4>
                        <p class="text-sm text-slate-600 pl-6">{{ $property->pet_policy }}</p>
                    </div>
                    @endif
                    
                    @if($property->payment_policy)
                    <div>
                        <h4 class="text-sm font-bold text-slate-800 mb-1 flex items-center"><i class="fas fa-credit-card text-slate-400 mr-2"></i> Payment</h4>
                        <p class="text-sm text-slate-600 pl-6">{{ $property->payment_policy }}</p>
                    </div>
                    @endif
                </div>
            </section>

            {{-- Room Types --}}
            <section class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-6">
                    <h3 class="text-lg font-bold text-slate-900 flex items-center">
                        <i class="fas fa-door-open text-teal-500 mr-2"></i> Room Types ({{ $property->roomTypes->count() }})
                    </h3>
                </div>
                
                <div class="space-y-6">
                    @foreach($property->roomTypes as $room)
                        <div class="border border-slate-200 rounded-xl overflow-hidden hover:border-slate-300 transition-colors">
                            <div class="bg-slate-50 p-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div>
                                    <h4 class="font-bold text-slate-900 text-lg flex items-center">
                                        {{ $room->name }}
                                        @if($room->status === 'active')
                                            <span class="ml-3 px-2 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-700 uppercase">Active</span>
                                        @else
                                            <span class="ml-3 px-2 py-0.5 rounded text-[10px] font-bold bg-gray-200 text-gray-700 uppercase">Inactive</span>
                                        @endif
                                    </h4>
                                    <div class="flex flex-wrap items-center gap-3 mt-2 text-sm text-slate-600">
                                        <span class="flex items-center"><i class="fas fa-expand-arrows-alt w-4 text-slate-400"></i> {{ $room->size_sqm ?? '-' }} m²</span>
                                        <span class="flex items-center"><i class="fas fa-user-friends w-4 text-slate-400"></i> Max {{ $room->max_adults }} Adults{{ $room->max_children ? ' + ' . $room->max_children . ' Children' : '' }}</span>
                                        <span class="flex items-center"><i class="fas fa-bed w-4 text-slate-400"></i> {{ is_array($room->bed_config) ? count($room->bed_config) . ' Beds' : 'Varied' }}</span>
                                    </div>
                                </div>
                                <div class="text-left sm:text-right">
                                    <div class="text-xs text-slate-500 uppercase font-semibold">Base Price</div>
                                    <div class="text-2xl font-bold text-blue-600">{{ \App\Helpers\Currency::format($room->base_price_per_night) }}</div>
                                    <div class="text-xs text-slate-500 mt-1">Inventory: <strong class="text-slate-800">{{ $room->inventory_count }}</strong> Rooms</div>
                                </div>
                            </div>
                            
                            <div class="p-4 bg-white grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @if(!empty($room->amenities))
                                <div>
                                    <h5 class="text-xs font-bold uppercase text-slate-500 mb-2">Room Amenities</h5>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach(array_slice($room->amenities, 0, 8) as $amenity)
                                            <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded text-xs">{{ $amenity }}</span>
                                        @endforeach
                                        @if(count($room->amenities) > 8)
                                            <span class="px-2 py-1 bg-slate-50 text-slate-500 border border-slate-200 rounded text-xs">+{{ count($room->amenities) - 8 }} more</span>
                                        @endif
                                    </div>
                                </div>
                                @endif
                                
                                <div>
                                    <h5 class="text-xs font-bold uppercase text-slate-500 mb-2">Rate Plans</h5>
                                    @if($room->ratePlans->isNotEmpty())
                                        <div class="space-y-1">
                                            @foreach($room->ratePlans as $plan)
                                                <div class="flex justify-between items-center text-sm border-b border-slate-50 last:border-0 pb-1">
                                                    <span class="text-slate-700">{{ $plan->plan_name }}</span>
                                                    <span class="font-medium text-slate-900">+{{ \App\Helpers\Currency::format($plan->price_supplement_per_adult) }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-sm text-slate-500 italic">No additional rate plans configured.</div>
                                    @endif
                                </div>
                            </div>
                            
                            @if($room->photos && $room->photos->isNotEmpty())
                            <div class="p-4 bg-slate-50 border-t border-slate-200">
                                <h5 class="text-xs font-bold uppercase text-slate-500 mb-2">Room Photos ({{ $room->photos->count() }})</h5>
                                <div class="flex gap-2 overflow-x-auto pb-2">
                                    @foreach($room->photos->take(5) as $photo)
                                        <img src="{{ $photo->url }}" class="h-16 w-24 object-cover rounded shadow-sm border border-slate-200" alt="Room Photo">
                                    @endforeach
                                    @if($room->photos->count() > 5)
                                        <div class="h-16 w-24 rounded bg-slate-200 flex items-center justify-center text-xs font-bold text-slate-500 border border-slate-300">
                                            +{{ $room->photos->count() - 5 }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
            
            {{-- Property Gallery --}}
            @if($property->photos->isNotEmpty())
            <section class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3 mb-4 flex items-center">
                    <i class="fas fa-images text-indigo-500 mr-2"></i> Property Gallery ({{ $property->photos->count() }})
                </h3>
                
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    @foreach($property->photos as $photo)
                        <div class="relative group aspect-square rounded-xl overflow-hidden border border-slate-200 bg-slate-50">
                            <img src="{{ $photo->url }}" alt="Property Image" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                            @if($photo->is_cover)
                                <div class="absolute top-2 left-2 bg-yellow-400 text-yellow-900 text-[10px] font-bold px-2 py-0.5 rounded shadow">
                                    COVER
                                </div>
                            @endif
                            @if($photo->category)
                                <div class="absolute bottom-0 left-0 w-full bg-gradient-to-t from-black/70 to-transparent p-2 pt-6">
                                    <span class="text-white text-[10px] font-medium uppercase tracking-wider">{{ $photo->category }}</span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
            @endif

        </div>

        {{-- Right Action Panel (Sticky) --}}
        <div class="lg:col-span-1">
            <div class="sticky top-6 space-y-6">
                
                {{-- Validation Checklist --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="bg-slate-50 p-4 border-b border-slate-200">
                        <h3 class="font-bold text-slate-800 text-sm flex items-center">
                            <i class="fas fa-clipboard-check text-blue-500 mr-2"></i> Submission Checklist
                        </h3>
                    </div>
                    <div class="p-4 space-y-3">
                        @foreach($checklist as $key => $passed)
                            @php
                                $labels = [
                                    'has_photos' => 'Property Photos Uploaded',
                                    'has_room_types' => 'Room Types Defined',
                                    'has_address' => 'Location & Address Complete',
                                    'has_description' => 'Property Description Provided',
                                    'has_pricing' => 'Base Pricing Configured'
                                ];
                            @endphp
                            <div class="flex items-center text-sm">
                                @if($passed)
                                    <div class="w-5 h-5 rounded-full bg-green-100 text-green-600 flex items-center justify-center mr-3 shrink-0">
                                        <i class="fas fa-check text-[10px]"></i>
                                    </div>
                                    <span class="text-slate-700 font-medium">{{ $labels[$key] }}</span>
                                @else
                                    <div class="w-5 h-5 rounded-full bg-red-100 text-red-600 flex items-center justify-center mr-3 shrink-0">
                                        <i class="fas fa-times text-[10px]"></i>
                                    </div>
                                    <span class="text-slate-500 line-through">{{ $labels[$key] }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Action Panel --}}
                <div class="bg-white rounded-2xl shadow-lg border border-slate-200 overflow-hidden relative">
                    <div class="absolute top-0 left-0 w-full h-1 {{ $property->status === 'pending_approval' ? 'bg-blue-500' : 'bg-slate-200' }}"></div>
                    <div class="p-6">
                        <h3 class="text-lg font-bold mb-5 text-slate-800 flex items-center">
                            <i class="fas fa-gavel text-slate-500 mr-2"></i> Review Decision
                        </h3>
                        
                        @if($property->status === 'approved')
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4 text-center">
                                <i class="fas fa-check-circle text-green-500 text-3xl mb-2 block"></i>
                                <p class="text-sm font-bold text-green-800">Property is Approved</p>
                                <p class="text-xs text-green-600 mt-1">Approved on {{ $property->approved_at?->format('M d, Y') }}</p>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.properties.approve', $property) }}" class="mb-5">
                            @csrf
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-3.5 bg-green-600 border border-transparent rounded-xl font-bold text-sm text-white hover:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all shadow-sm hover:shadow-md disabled:opacity-50" {{ $property->status === 'approved' ? 'disabled' : '' }}>
                                <i class="fas fa-check-circle mr-2"></i> Approve Property
                            </button>
                        </form>

                        <div class="relative flex items-center py-2 mb-5">
                            <div class="flex-grow border-t border-slate-200"></div>
                            <span class="flex-shrink-0 mx-4 text-xs font-medium text-slate-400 uppercase">Or</span>
                            <div class="flex-grow border-t border-slate-200"></div>
                        </div>

                        <form method="POST" action="{{ route('admin.properties.reject', $property) }}">
                            @csrf
                            <div class="mb-4">
                                <label for="reason" class="block font-semibold text-xs text-slate-600 uppercase mb-2">Rejection / Revision Notes</label>
                                <textarea id="reason" name="reason" rows="3" class="mt-1 block w-full border border-slate-300 focus:border-red-500 focus:ring-red-500 rounded-xl shadow-sm px-3 py-2 text-sm" placeholder="Please provide a clear reason for rejection..." required>{{ $property->admin_notes }}</textarea>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-3">
                                <button type="submit" formaction="{{ route('admin.properties.request-changes', $property) }}" class="inline-flex justify-center items-center px-4 py-3 bg-white border border-yellow-300 rounded-xl font-bold text-xs text-yellow-700 hover:bg-yellow-50 focus:outline-none focus:ring-2 focus:ring-yellow-500 transition-colors shadow-sm">
                                    <i class="fas fa-undo mr-1.5"></i> Request Fix
                                </button>
                                
                                <button type="submit" class="inline-flex justify-center items-center px-4 py-3 bg-red-50 border border-red-200 rounded-xl font-bold text-xs text-red-600 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors shadow-sm">
                                    <i class="fas fa-times-circle mr-1.5"></i> Reject
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                {{-- Owner Info Card --}}
                <div class="bg-slate-50 rounded-xl border border-slate-200 p-5 mt-6">
                    <h4 class="text-xs font-bold uppercase text-slate-500 mb-3 border-b border-slate-200 pb-2">Property Owner</h4>
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-lg mr-3">
                            {{ substr($property->owner->name ?? '?', 0, 1) }}
                        </div>
                        <div>
                            <div class="text-sm font-bold text-slate-800">{{ $property->owner->name ?? 'Unknown Owner' }}</div>
                            <div class="text-xs text-slate-500">{{ $property->owner->email ?? 'No email' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
