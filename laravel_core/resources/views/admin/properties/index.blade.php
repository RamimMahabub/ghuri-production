<x-admin-layout>
    <x-slot name="pageTitle">Property Inventory</x-slot>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex gap-2 flex-wrap">
                <a href="{{ route('admin.properties.index', ['status' => 'pending_approval']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium {{ $status === 'pending_approval' ? 'bg-amber-50 text-amber-700' : 'text-gray-500 hover:bg-gray-50' }}">
                   Pending ({{ $counts['pending'] }})
                </a>
                <a href="{{ route('admin.properties.index', ['status' => 'approved']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium {{ $status === 'approved' ? 'bg-green-50 text-green-700' : 'text-gray-500 hover:bg-gray-50' }}">
                   Active ({{ $counts['approved'] }})
                </a>
                <a href="{{ route('admin.properties.index', ['status' => 'rejected']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium {{ $status === 'rejected' ? 'bg-red-50 text-red-700' : 'text-gray-500 hover:bg-gray-50' }}">
                   Rejected ({{ $counts['rejected'] }})
                </a>
                <a href="{{ route('admin.properties.index', ['status' => 'draft']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium {{ $status === 'draft' ? 'bg-gray-100 text-gray-700' : 'text-gray-500 hover:bg-gray-50' }}">
                   Drafts
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                        <th class="px-6 py-4 font-medium">Property Details</th>
                        <th class="px-6 py-4 font-medium">Owner</th>
                        <th class="px-6 py-4 font-medium">Location</th>
                        <th class="px-6 py-4 font-medium">Stats</th>
                        <th class="px-6 py-4 font-medium text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($properties as $property)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-slate-800">{{ $property->name }}</p>
                                <p class="text-xs text-gray-500">{{ ucfirst($property->type) }} &bull; {{ $property->stars }} Stars</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-slate-800">{{ $property->owner->name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500">{{ $property->owner->email ?? 'N/A' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-slate-800">{{ $property->city }}</p>
                                <p class="text-xs text-gray-500">{{ $property->country }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-3 text-xs text-gray-500">
                                    <span title="Room Types"><i class="fas fa-bed mr-1"></i> {{ $property->room_types_count }}</span>
                                    <span title="Photos"><i class="fas fa-image mr-1"></i> {{ $property->photos_count }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.properties.review', $property) }}" class="px-4 py-2 bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white rounded-lg text-sm font-medium transition-colors">
                                    Review
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                No properties found in this status.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($properties->hasPages())
            <div class="p-6 border-t border-gray-100">
                {{ $properties->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
