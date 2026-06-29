<x-pms-layout pageTitle="Guest Directory" pageSubtitle="Manage and view your previous and upcoming guests">

    <div class="card">
        <div class="card-header border-b border-brand-border">
            <h3 class="font-heading font-bold text-brand-black text-sm">Guest List</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-brand-surface text-xs text-brand-muted border-b border-brand-border">
                        <th class="py-3 px-5 font-semibold">Guest</th>
                        <th class="py-3 px-5 font-semibold">Contact</th>
                        <th class="py-3 px-5 font-semibold text-center">Total Stays</th>
                        <th class="py-3 px-5 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brand-border">
                    @forelse($guests as $guest)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-3 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-brand-primary/10 flex items-center justify-center">
                                        <span class="text-sm font-bold text-brand-primary">{{ substr($guest->name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <span class="font-heading font-bold text-brand-black block text-sm">{{ $guest->name }}</span>
                                        <span class="text-[10px] text-brand-muted">Joined {{ $guest->created_at->format('M Y') }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-5 text-sm text-brand-text">
                                <div class="space-y-1">
                                    <p><i class="fas fa-envelope text-brand-muted mr-1"></i> {{ $guest->email }}</p>
                                    @if($guest->phone)
                                        <p><i class="fas fa-phone text-brand-muted mr-1"></i> {{ $guest->phone }}</p>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-5 text-center">
                                <span class="badge-info">{{ $guest->hotel_bookings_count }}</span>
                            </td>
                            <td class="py-3 px-5 text-right">
                                <a href="{{ route('property-owner.guests.show', $guest) }}" class="btn-secondary btn-sm">View Profile</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 px-5 text-center text-brand-muted text-sm">
                                <i class="fas fa-users text-3xl mb-3 text-brand-border block"></i>
                                No guests found. Wait for bookings to come in!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($guests->hasPages())
            <div class="px-5 py-4 border-t border-brand-border">
                {{ $guests->links() }}
            </div>
        @endif
    </div>

</x-pms-layout>
