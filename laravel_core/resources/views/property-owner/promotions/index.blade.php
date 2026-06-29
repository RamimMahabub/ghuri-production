<x-pms-layout pageTitle="Promotions" pageSubtitle="Manage discount codes and special deals">

    <x-slot name="headerActions">
        <a href="{{ route('property-owner.promotions.create') }}" class="btn-primary">
            <i class="fas fa-plus"></i> New Promotion
        </a>
    </x-slot>

    <div class="card">
        <div class="card-header">
            <h3 class="font-heading font-bold text-brand-black text-sm">Active Promotions</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-brand-surface text-xs text-brand-muted border-b border-brand-border">
                        <th class="py-3 px-5 font-semibold">Code / Name</th>
                        <th class="py-3 px-5 font-semibold">Property</th>
                        <th class="py-3 px-5 font-semibold">Discount</th>
                        <th class="py-3 px-5 font-semibold">Valid Period</th>
                        <th class="py-3 px-5 font-semibold text-center">Status</th>
                        <th class="py-3 px-5 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-border">
                    @forelse($promotions as $promo)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-3 px-5">
                                <span class="font-heading font-bold text-brand-black block">{{ $promo->code }}</span>
                                <span class="text-xs text-brand-muted">{{ ucfirst(str_replace('_', ' ', $promo->type)) }}</span>
                            </td>
                            <td class="py-3 px-5 text-sm text-brand-text">
                                {{ $promo->property->name ?? 'Unknown' }}
                            </td>
                            <td class="py-3 px-5">
                                <span class="text-sm font-semibold text-status-confirmed">
                                    {{ $promo->discount_type === 'percent' ? $promo->discount_value.'%' : '$'.$promo->discount_value }} OFF
                                </span>
                            </td>
                            <td class="py-3 px-5 text-xs text-brand-text">
                                @if($promo->valid_from || $promo->valid_to)
                                    {{ $promo->valid_from ? \Carbon\Carbon::parse($promo->valid_from)->format('M d, Y') : 'Anytime' }} - 
                                    {{ $promo->valid_to ? \Carbon\Carbon::parse($promo->valid_to)->format('M d, Y') : 'Never Expires' }}
                                @else
                                    <span class="text-brand-muted">No expiry</span>
                                @endif
                            </td>
                            <td class="py-3 px-5 text-center">
                                @if($promo->is_active)
                                    <span class="badge-confirmed">Active</span>
                                @else
                                    <span class="badge-pending">Inactive</span>
                                @endif
                            </td>
                            <td class="py-3 px-5 text-right">
                                <form action="{{ route('property-owner.promotions.destroy', $promo) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this promotion?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-status-danger hover:text-red-700 p-2">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 px-5 text-center text-brand-muted text-sm">
                                <i class="fas fa-tags text-3xl mb-3 text-brand-border block"></i>
                                No promotions found. Create one to attract more guests!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($promotions->hasPages())
            <div class="px-5 py-4 border-t border-brand-border">
                {{ $promotions->links() }}
            </div>
        @endif
    </div>

</x-pms-layout>
