<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Booking Details
        </h2>
    </x-slot>

    <div class="py-12 bg-[#F5F7FA] min-h-screen">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-4">
                <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800 font-semibold flex items-center gap-1 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back to Dashboard
                </a>
            </div>

            <!-- Main Booking Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="bg-[#1a2b49] px-8 py-6 flex flex-col md:flex-row justify-between items-center text-white">
                    <div>
                        <p class="text-gray-300 text-sm font-medium tracking-widest uppercase mb-1">Booking Reference (PNR)</p>
                        <h1 class="text-3xl font-mono font-bold tracking-widest">{{ $booking->api_reference_id }}</h1>
                    </div>
                    <div class="mt-4 md:mt-0 text-right">
                        <span class="inline-block px-4 py-1.5 rounded-full text-sm font-bold tracking-wide {{ $booking->status == 'confirmed' ? 'bg-green-500 text-white shadow-lg shadow-green-500/30' : 'bg-yellow-500 text-white' }} uppercase">
                            {{ $booking->status }}
                        </span>
                        <p class="text-gray-300 text-xs mt-2">Booked on {{ $booking->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Left Column (Passengers) -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            Passenger Information
                        </h3>
                        
                        <div class="space-y-4">
                            @foreach($booking->passengers as $index => $passenger)
                                <div class="flex items-start gap-4 p-4 rounded-xl {{ $loop->even ? 'bg-gray-50' : 'bg-blue-50/30 border border-blue-100/50' }}">
                                    <div class="w-10 h-10 rounded-full bg-[#1882FF] text-white flex items-center justify-center font-bold shadow-md shadow-blue-500/20 shrink-0">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-y-3 gap-x-4">
                                        <div>
                                            <p class="text-[10px] uppercase font-bold tracking-widest text-gray-400">Full Name</p>
                                            <p class="font-semibold text-gray-800">{{ $passenger->title }} {{ $passenger->first_name }} {{ $passenger->last_name }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[10px] uppercase font-bold tracking-widest text-gray-400">Date of Birth</p>
                                            <p class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($passenger->date_of_birth)->format('M d, Y') }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[10px] uppercase font-bold tracking-widest text-gray-400">Nationality</p>
                                            <p class="font-semibold text-gray-800">{{ $passenger->nationality ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Contact Details (Derived from Bookings logic / first passenger usually) -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-bold text-gray-800 border-b border-gray-100 pb-3 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            Contact Details
                        </h3>
                        @php $contact = $booking->passengers->first() @endphp
                        @if($contact)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                <p class="text-xs font-semibold text-gray-500 mb-1">Email Address</p>
                                <p class="font-bold text-gray-800">{{ $contact->email ?? $booking->user->email }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                                <p class="text-xs font-semibold text-gray-500 mb-1">Phone Number</p>
                                <p class="font-bold text-gray-800">{{ $contact->phone ?? 'N/A' }}</p>
                            </div>
                        </div>
                        @else
                        <p class="text-sm text-gray-500">No contact details found.</p>
                        @endif
                    </div>
                </div>

                <!-- Right Column (Payment & Summary) -->
                <div class="space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden sticky top-24">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Payment Summary
                            </h3>
                            
                            <div class="space-y-3 mb-6">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-500">Base Fare & Taxes</span>
                                    <span class="font-bold text-gray-800">{{ number_format($booking->total_amount, 2) }} BDT</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-500">Service Fee</span>
                                    <span class="font-bold text-green-600">Free</span>
                                </div>
                                <hr class="border-gray-100 my-2">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-gray-800">Total Paid</span>
                                    <span class="font-bold text-xl text-[#1882FF]">{{ number_format($booking->total_amount, 2) }} BDT</span>
                                </div>
                            </div>
                            
                            @if($booking->payments->isNotEmpty())
                                <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3">Transaction Details</h4>
                                    @foreach($booking->payments as $payment)
                                        <div class="flex items-center gap-3 mb-2 last:mb-0">
                                            <div class="w-8 h-8 rounded bg-white border border-gray-200 flex items-center justify-center shrink-0">
                                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-sm font-bold text-gray-800 capitalize">{{ $payment->provider }} Payment</p>
                                                <p class="text-xs text-gray-500">{{ $payment->created_at->format('M d, Y') }} - Confirmed</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                        </div>
                        <div class="bg-yellow-50 p-4 border-t border-yellow-100">
                            <div class="flex items-start gap-2 text-yellow-800 text-xs">
                                <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <p>Please carry a printed copy of your e-ticket and a valid photo ID (Passport for international travel) on the day of departure.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
