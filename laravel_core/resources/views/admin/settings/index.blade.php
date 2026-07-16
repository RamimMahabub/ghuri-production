<x-admin-layout>
    <x-slot name="pageTitle">{{ __('Global Settings') }}</x-slot>
    <x-slot name="pageSubtitle">Manage platform configuration</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        @if(session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg shadow-sm flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form action="{{ route('admin.settings.store') }}" method="POST" class="max-w-md">
                    @csrf
                    
                    <div class="mb-6">
                        <label for="exchange_rate_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Exchange Rate Source
                        </label>
                        <select name="exchange_rate_type" id="exchange_rate_type" class="focus:ring-[#1a2b49] focus:border-[#1a2b49] block w-full sm:text-sm border-gray-300 rounded-md @error('exchange_rate_type') border-red-500 @enderror" onchange="document.getElementById('manual_rate_container').style.display = this.value === 'manual' ? 'block' : 'none'">
                            <option value="live" {{ old('exchange_rate_type', $exchangeRateType ?? 'live') === 'live' ? 'selected' : '' }}>Live (Automatic from API)</option>
                            <option value="manual" {{ old('exchange_rate_type', $exchangeRateType ?? 'live') === 'manual' ? 'selected' : '' }}>Manual (Custom Rate)</option>
                        </select>
                        <p class="mt-2 text-sm text-gray-500">
                            Choose whether to use real-time market rates or a custom fixed rate for USD to BDT conversions.
                        </p>
                    </div>

                    <div id="manual_rate_container" class="mb-6" style="display: {{ old('exchange_rate_type', $exchangeRateType ?? 'live') === 'manual' ? 'block' : 'none' }};">
                        <label for="exchange_rate_usd_bdt" class="block text-sm font-medium text-gray-700 mb-2">
                            Manual USD to BDT Exchange Rate
                        </label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">
                                    ৳
                                </span>
                            </div>
                            <input type="number" 
                                   step="0.01" 
                                   name="exchange_rate_usd_bdt" 
                                   id="exchange_rate_usd_bdt" 
                                   class="focus:ring-[#1a2b49] focus:border-[#1a2b49] block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md @error('exchange_rate_usd_bdt') border-red-500 @enderror" 
                                   placeholder="120.00"
                                   value="{{ old('exchange_rate_usd_bdt', $exchangeRate) }}">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">
                                    BDT
                                </span>
                            </div>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">
                            1 USD = X BDT. This rate will be used for displaying dual currency prices across the platform.
                        </p>
                        @error('exchange_rate_usd_bdt')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <hr class="my-8 border-gray-200">

                    <h3 class="text-lg font-bold text-gray-900 mb-4">Promotional Banner</h3>
                    
                    <div class="mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="promo_banner_enabled" class="rounded border-gray-300 text-[#1a2b49] shadow-sm focus:border-[#1a2b49] focus:ring focus:ring-[#1a2b49] focus:ring-opacity-50" value="1" {{ old('promo_banner_enabled', $promoBannerEnabled) == '1' ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-600">Enable Promotional Banner on Homepage</span>
                        </label>
                    </div>

                    <div class="mb-4">
                        <label for="promo_banner_title" class="block text-sm font-medium text-gray-700 mb-1">Banner Title</label>
                        <input type="text" name="promo_banner_title" id="promo_banner_title" class="focus:ring-[#1a2b49] focus:border-[#1a2b49] block w-full sm:text-sm border-gray-300 rounded-md" value="{{ old('promo_banner_title', $promoBannerTitle) }}">
                    </div>

                    <div class="mb-4">
                        <label for="promo_banner_subtitle" class="block text-sm font-medium text-gray-700 mb-1">Banner Subtitle</label>
                        <textarea name="promo_banner_subtitle" id="promo_banner_subtitle" rows="2" class="focus:ring-[#1a2b49] focus:border-[#1a2b49] block w-full sm:text-sm border-gray-300 rounded-md">{{ old('promo_banner_subtitle', $promoBannerSubtitle) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="promo_banner_button_text" class="block text-sm font-medium text-gray-700 mb-1">Button Text</label>
                            <input type="text" name="promo_banner_button_text" id="promo_banner_button_text" class="focus:ring-[#1a2b49] focus:border-[#1a2b49] block w-full sm:text-sm border-gray-300 rounded-md" value="{{ old('promo_banner_button_text', $promoBannerButtonText) }}">
                        </div>
                        <div>
                            <label for="promo_banner_button_url" class="block text-sm font-medium text-gray-700 mb-1">Button URL</label>
                            <input type="text" name="promo_banner_button_url" id="promo_banner_button_url" class="focus:ring-[#1a2b49] focus:border-[#1a2b49] block w-full sm:text-sm border-gray-300 rounded-md" value="{{ old('promo_banner_button_url', $promoBannerButtonUrl) }}" placeholder="https://... or #">
                        </div>
                    </div>

                    <div class="flex items-center justify-end">
                        <button type="submit" class="bg-[#1a2b49] hover:bg-[#24385d] text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline transition">
                            Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</x-admin-layout>
