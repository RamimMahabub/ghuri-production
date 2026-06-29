<x-pms-layout pageTitle="New Promotion" pageSubtitle="Create a discount code or special offer">

    <div class="max-w-3xl">
        <form action="{{ route('property-owner.promotions.store') }}" method="POST" class="card card-body space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Property --}}
                <div class="md:col-span-2">
                    <label class="form-label">Select Property *</label>
                    <select name="property_id" class="form-input-styled w-full" required>
                        <option value="">Choose a property...</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}" {{ old('property_id') == $property->id ? 'selected' : '' }}>
                                {{ $property->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('property_id') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                {{-- Promo Code --}}
                <div>
                    <label class="form-label">Promo Code</label>
                    <input type="text" name="code" value="{{ old('code') }}" class="form-input-styled w-full uppercase" placeholder="e.g. SUMMER2026">
                    <p class="text-xs text-brand-muted mt-1">Leave blank to auto-generate</p>
                    @error('code') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                {{-- Type --}}
                <div>
                    <label class="form-label">Promotion Type *</label>
                    <select name="type" class="form-input-styled w-full" required>
                        <option value="promo_code" {{ old('type') == 'promo_code' ? 'selected' : '' }}>Promo Code</option>
                        <option value="flash_deal" {{ old('type') == 'flash_deal' ? 'selected' : '' }}>Flash Deal</option>
                        <option value="package" {{ old('type') == 'package' ? 'selected' : '' }}>Package</option>
                    </select>
                    @error('type') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                {{-- Discount --}}
                <div>
                    <label class="form-label">Discount Value *</label>
                    <div class="flex">
                        <select name="discount_type" class="form-input-styled rounded-r-none border-r-0 w-1/3 bg-gray-50" required>
                            <option value="percent" {{ old('discount_type') == 'percent' ? 'selected' : '' }}>%</option>
                            <option value="flat" {{ old('discount_type') == 'flat' ? 'selected' : '' }}>$</option>
                        </select>
                        <input type="number" name="discount_value" value="{{ old('discount_value') }}" class="form-input-styled w-2/3 rounded-l-none" min="1" step="0.01" required placeholder="e.g. 15">
                    </div>
                    @error('discount_value') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                
                {{-- Min Nights --}}
                <div>
                    <label class="form-label">Minimum Nights</label>
                    <input type="number" name="min_nights" value="{{ old('min_nights', 1) }}" class="form-input-styled w-full" min="1">
                    @error('min_nights') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                {{-- Valid Dates --}}
                <div>
                    <label class="form-label">Valid From</label>
                    <input type="date" name="valid_from" value="{{ old('valid_from') }}" class="form-input-styled w-full">
                    @error('valid_from') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label class="form-label">Valid To</label>
                    <input type="date" name="valid_to" value="{{ old('valid_to') }}" class="form-input-styled w-full">
                    @error('valid_to') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                {{-- Max Usage --}}
                <div class="md:col-span-2">
                    <label class="form-label">Maximum Total Uses</label>
                    <input type="number" name="max_usage_total" value="{{ old('max_usage_total') }}" class="form-input-styled w-full md:w-1/2" min="1" placeholder="Leave blank for unlimited">
                    @error('max_usage_total') <p class="form-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <hr class="border-brand-border">

            <div class="flex items-center gap-4">
                <button type="submit" class="btn-primary">Create Promotion</button>
                <a href="{{ route('property-owner.promotions.index') }}" class="btn-ghost">Cancel</a>
            </div>
        </form>
    </div>

</x-pms-layout>
