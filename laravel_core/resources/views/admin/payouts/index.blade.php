<x-admin-layout>
    <x-slot name="pageTitle">Partner Payouts</x-slot>
    <x-slot name="pageSubtitle">Track and process payout requests</x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Top Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <p class="text-sm font-medium text-gray-500 mb-1">Total Payouts Amount (All Time)</p>
                    <h3 class="text-2xl font-bold text-gray-900">{{ \App\Helpers\Currency::format($totalPayoutsAmount) }}</h3>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-orange-100 p-6 relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-2 h-full bg-orange-400"></div>
                    <p class="text-sm font-medium text-orange-600 mb-1">Total Requested Payouts</p>
                    <h3 class="text-2xl font-bold text-gray-900">{{ \App\Helpers\Currency::format($totalRequestedAmount) }}</h3>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-green-100 p-6 relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-2 h-full bg-green-500"></div>
                    <p class="text-sm font-medium text-green-600 mb-1">Total Given Payouts</p>
                    <h3 class="text-2xl font-bold text-gray-900">{{ \App\Helpers\Currency::format($totalGivenAmount) }}</h3>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ref & Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property & Owner</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($payouts as $payout)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-gray-900">{{ $payout->reference }}</div>
                                            <div class="text-xs text-gray-500">{{ $payout->created_at->format('M d, Y H:i') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $payout->property->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $payout->property->owner->name ?? 'N/A' }}</div>
                                            
                                            <!-- Show Payment Method if requested -->
                                            @if($payout->status === 'requested' || $payout->status === 'processing')
                                                <div class="mt-2 text-xs text-gray-600 bg-gray-50 p-2 rounded border border-gray-100">
                                                    @if(!empty($payout->property->owner->bank_details))
                                                        <strong>Bank:</strong> {{ $payout->property->owner->bank_details['bank_name'] }}<br>
                                                        <strong>Acct Name:</strong> {{ $payout->property->owner->bank_details['account_name'] }}<br>
                                                        <strong>Acct No:</strong> {{ $payout->property->owner->bank_details['account_number'] }}<br>
                                                        <strong>Routing:</strong> {{ $payout->property->owner->bank_details['routing_number'] }}
                                                    @elseif(!empty($payout->property->owner->card_details))
                                                        <strong>Card:</strong> {{ $payout->property->owner->card_details['card_number'] }}
                                                    @else
                                                        <span class="text-red-500">No payment method found on user profile.</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-gray-900">{{ \App\Helpers\Currency::format($payout->amount) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($payout->status === 'requested')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">Requested</span>
                                            @elseif($payout->status === 'processing')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Processing</span>
                                            @elseif($payout->status === 'processed')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Processed</span>
                                            @elseif($payout->status === 'rejected')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex flex-col items-end gap-1.5">
                                                <div>
                                                    <button onclick="openModal('{{ $payout->id }}', '{{ $payout->status }}')" class="text-indigo-600 hover:text-indigo-900 mr-3 text-xs font-bold uppercase"><i class="fas fa-edit"></i> Update</button>
                                                    <a href="{{ route('admin.payouts.invoice', $payout->id) }}" target="_blank" class="text-gray-600 hover:text-gray-900 text-xs font-bold uppercase"><i class="fas fa-file-invoice"></i> Invoice</a>
                                                </div>
                                                @if($payout->payment_proof)
                                                    <a href="{{ \Illuminate\Support\Facades\Storage::url($payout->payment_proof) }}" target="_blank" class="text-[11px] text-blue-500 hover:text-blue-700 mt-1 font-bold"><i class="fas fa-receipt"></i> Payment Proof</a>
                                                @endif
                                                @if($payout->bank_invoice)
                                                    <a href="{{ \Illuminate\Support\Facades\Storage::url($payout->bank_invoice) }}" target="_blank" class="text-[11px] text-blue-500 hover:text-blue-700 font-bold"><i class="fas fa-file-alt"></i> Bank Invoice</a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-500 text-sm">
                                            No payout records found.
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

    <!-- Update Status Modal -->
    <div id="statusModal" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center">
        <div class="bg-white rounded-xl p-8 max-w-md w-full shadow-2xl relative">
            <button onclick="document.getElementById('statusModal').classList.add('hidden')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
            <h2 class="text-xl font-bold text-gray-900 mb-6">Update Payout Status</h2>
            
            <form id="statusForm" action="" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="statusSelect" class="w-full border-gray-300 rounded-lg focus:ring-brand-primary focus:border-brand-primary text-sm" onchange="toggleFileFields()">
                        <option value="requested">Requested</option>
                        <option value="processing">Processing</option>
                        <option value="processed">Processed</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>

                <div id="fileFields" class="hidden space-y-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Proof (Optional)</label>
                        <input type="file" name="payment_proof" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bank Invoice (Optional)</label>
                        <input type="file" name="bank_invoice" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="bg-brand-primary hover:bg-brand-primary/90 text-white font-bold py-2 px-6 rounded-lg transition-colors">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id, currentStatus) {
            document.getElementById('statusModal').classList.remove('hidden');
            document.getElementById('statusForm').action = '/admin/payouts/' + id + '/status';
            document.getElementById('statusSelect').value = currentStatus;
            toggleFileFields();
        }

        function toggleFileFields() {
            if (document.getElementById('statusSelect').value === 'processed') {
                document.getElementById('fileFields').classList.remove('hidden');
            } else {
                document.getElementById('fileFields').classList.add('hidden');
            }
        }
    </script>
</x-admin-layout>
