<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Voucher #{{ $booking->booking_ref }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body class="bg-gray-100 font-sans text-gray-800 p-8">

    <div class="max-w-3xl mx-auto">
        <div class="mb-6 flex justify-between items-center no-print">
            <a href="{{ route('my-bookings.show', $booking) }}" class="text-blue-600 hover:underline">&larr; Back to Booking</a>
            <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700">Print Voucher</button>
        </div>

        <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-200">
            {{-- Header --}}
            <div class="bg-gray-900 text-white p-8 flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight mb-1">Booking Voucher</h1>
                    <p class="text-gray-400">Ref: {{ $booking->booking_ref }}</p>
                </div>
                <div class="text-right">
                    <p class="font-bold text-xl">{{ config('app.name', 'GhuriTravel') }}</p>
                    <p class="text-sm text-gray-400">Your trusted travel partner</p>
                </div>
            </div>

            <div class="p-8">
                {{-- Hotel Info --}}
                <div class="flex gap-6 mb-8 pb-8 border-b border-gray-100">
                    <div class="w-1/2">
                        <h2 class="text-xl font-bold text-gray-900 mb-2">{{ $booking->property->name }}</h2>
                        <p class="text-sm text-gray-600 mb-1">{{ $booking->property->address_line_1 }}</p>
                        <p class="text-sm text-gray-600 mb-1">{{ $booking->property->city }}, {{ $booking->property->country }}</p>
                        @if($booking->property->phone)
                            <p class="text-sm text-gray-600 mt-2">Phone: {{ $booking->property->phone }}</p>
                        @endif
                    </div>
                    <div class="w-1/2 text-right">
                        <p class="text-sm text-gray-500 uppercase tracking-wider font-semibold mb-1">Guest Details</p>
                        <p class="font-bold text-gray-900">{{ $booking->guest->name }}</p>
                        <p class="text-sm text-gray-600">{{ $booking->guest->email }}</p>
                    </div>
                </div>

                {{-- Stay Details --}}
                <div class="grid grid-cols-2 gap-8 mb-8 pb-8 border-b border-gray-100">
                    <div>
                        <p class="text-sm text-gray-500 uppercase tracking-wider font-semibold mb-1">Check-in</p>
                        <p class="text-xl font-bold text-gray-900">{{ $booking->check_in->format('D, d M Y') }}</p>
                        <p class="text-sm text-gray-600">From {{ $booking->property->check_in_time ?? '14:00' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 uppercase tracking-wider font-semibold mb-1">Check-out</p>
                        <p class="text-xl font-bold text-gray-900">{{ $booking->check_out->format('D, d M Y') }}</p>
                        <p class="text-sm text-gray-600">Until {{ $booking->property->check_out_time ?? '12:00' }}</p>
                    </div>
                    <div class="col-span-2 bg-gray-50 p-4 rounded-lg">
                        <p class="font-bold text-gray-900">{{ $booking->roomType->name }}</p>
                        <p class="text-sm text-gray-600">{{ $booking->nights }} nights · {{ $booking->adults }} Adults, {{ $booking->children }} Children</p>
                    </div>
                </div>

                {{-- Payment --}}
                <div>
                    <p class="text-sm text-gray-500 uppercase tracking-wider font-semibold mb-3">Payment Summary</p>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-600">Status</span>
                        <span class="font-bold {{ $booking->payment_status === 'paid' ? 'text-green-600' : 'text-yellow-600' }}">{{ strtoupper($booking->payment_status) }}</span>
                    </div>
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-600">Total Amount</span>
                        <span class="font-bold text-gray-900">${{ number_format($booking->total, 2) }}</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-4 text-center">Please present this voucher upon arrival. Valid ID is required for check-in.</p>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
