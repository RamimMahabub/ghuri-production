<x-admin-layout>
    <x-slot name="pageTitle">Partner Payouts</x-slot>
    <x-slot name="pageSubtitle">Process and track earnings owed to property owners</x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pending Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Payout</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($payoutData as $data)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $data->property->name }}</div>
                                            <div class="text-xs text-gray-500">Owner: {{ $data->property->owner->name ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-gray-900">${{ number_format($data->pending_amount, 2) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($data->last_payout)
                                                <div class="text-sm text-gray-900">${{ number_format($data->last_payout->amount, 2) }}</div>
                                                <div class="text-xs text-gray-500">{{ $data->last_payout->processed_at->format('M d, Y') }}</div>
                                            @else
                                                <span class="text-sm text-gray-500">No previous payouts</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            @if($data->pending_amount > 0)
                                                <form action="{{ route('admin.payouts.process', $data->property) }}" method="POST" onsubmit="return confirm('Process payout of ${{ number_format($data->pending_amount, 2) }}?');">
                                                    @csrf
                                                    <input type="hidden" name="amount" value="{{ $data->pending_amount }}">
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                        Process Payout
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-gray-400">Up to date</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-gray-500 text-sm">
                                            No active properties found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
