<x-admin-layout>
    <x-slot name="pageTitle">OTA Dashboard</x-slot>
    <x-slot name="pageSubtitle">Platform Overview & Operations</x-slot>

    <div class="space-y-6">
        <!-- Key Metrics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl p-5 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Total Sales</p>
                    <p class="text-2xl font-black text-[#19100F] mt-1">${{ number_format($stats['total_sales'], 2) }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center text-[#d00e15]">
                    <i class="fas fa-dollar-sign text-xl"></i>
                </div>
            </div>

            <div class="bg-white rounded-xl p-5 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Est. Commission</p>
                    <p class="text-2xl font-black text-[#19100F] mt-1">${{ number_format($stats['total_commission'], 2) }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center text-green-600">
                    <i class="fas fa-hand-holding-dollar text-xl"></i>
                </div>
            </div>

            <div class="bg-white rounded-xl p-5 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Total Bookings</p>
                    <p class="text-2xl font-black text-[#19100F] mt-1">{{ number_format($stats['bookings']) }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600">
                    <i class="fas fa-calendar-check text-xl"></i>
                </div>
            </div>

            <div class="bg-white rounded-xl p-5 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Pending Properties</p>
                    <p class="text-2xl font-black text-[#19100F] mt-1">{{ number_format($stats['pending_properties']) }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-amber-50 flex items-center justify-center text-amber-600">
                    <i class="fas fa-building-circle-exclamation text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Pending Approvals -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                        <h3 class="font-bold text-[#19100F]">Action Required: Pending Properties</h3>
                        <a href="{{ route('admin.properties.index') }}" class="text-sm font-medium text-[#d00e15] hover:text-[#A90B16]">View All</a>
                    </div>
                    <div class="p-0">
                        @if(count($pendingPropertiesList) > 0)
                            <div class="divide-y divide-gray-100">
                                @foreach($pendingPropertiesList as $prop)
                                    <div class="p-6 flex items-center justify-between hover:bg-gray-50 transition-colors">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-lg bg-gray-200 flex items-center justify-center shrink-0 overflow-hidden">
                                                @if($prop->cover_photo_url && !str_contains($prop->cover_photo_url, 'placeholder'))
                                                    <img src="{{ $prop->cover_photo_url }}" class="w-full h-full object-cover" alt="">
                                                @else
                                                    <i class="fas fa-hotel text-gray-400"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-[#19100F] text-sm">{{ $prop->name }}</h4>
                                                <p class="text-xs text-gray-500 mt-0.5"><i class="fas fa-map-marker-alt mr-1"></i> {{ $prop->city }}, {{ $prop->country }}</p>
                                            </div>
                                        </div>
                                        <a href="{{ route('admin.properties.review', $prop->id) }}" class="px-4 py-2 bg-red-50 text-[#d00e15] hover:bg-[#d00e15] hover:text-white rounded-lg text-sm font-medium transition-colors">
                                            Review & Approve
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-12 text-center">
                                <div class="w-16 h-16 bg-green-50 text-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-check-double text-2xl"></i>
                                </div>
                                <h4 class="font-bold text-[#19100F]">All caught up!</h4>
                                <p class="text-sm text-gray-500 mt-1">There are no properties pending approval at the moment.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column: User Stats -->
            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="font-bold text-[#19100F]">Platform Users</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                                    <i class="fas fa-users text-sm"></i>
                                </div>
                                <span class="text-sm font-medium text-slate-700">Customers</span>
                            </div>
                            <span class="font-bold text-[#19100F]">{{ number_format($stats['active_customers']) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center">
                                    <i class="fas fa-user-tie text-sm"></i>
                                </div>
                                <span class="text-sm font-medium text-slate-700">Property Owners</span>
                            </div>
                            <span class="font-bold text-[#19100F]">{{ number_format($stats['active_partners']) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
