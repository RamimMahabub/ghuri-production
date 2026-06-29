<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Checkout Process</h2>
            <a href="{{ url()->previous() }}" class="inline-flex items-center gap-2 rounded-md border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100">
                Return
            </a>
        </div>
    </x-slot>

    <div class="py-10 bg-[#f5f7fb] min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 rounded-lg border border-blue-200 bg-white px-4 py-3">
                <div class="flex flex-wrap items-center justify-center gap-3 text-xs font-semibold text-gray-600">
                    <span class="inline-flex items-center gap-2 text-blue-700">
                        <span class="inline-flex h-5 w-5 items-center justify-center rounded bg-blue-600 text-white">1</span>
                        Your selection
                    </span>
                    <span class="text-gray-300">----------------</span>
                    <span class="inline-flex items-center gap-2 text-gray-700">
                        <span class="inline-flex h-5 w-5 items-center justify-center rounded bg-gray-700 text-white">2</span>
                        Your details
                    </span>
                    <span class="text-gray-300">----------------</span>
                    <span class="inline-flex items-center gap-2 text-gray-500">
                        <span class="inline-flex h-5 w-5 items-center justify-center rounded bg-gray-300 text-white">3</span>
                        Payment
                    </span>
                </div>
            </div>

            <form action="{{ route('booking.store') }}" method="POST">
                @csrf
                <input type="hidden" name="flight_id" value="{{ $flightId }}">

                @if ($errors->any())
                    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <div class="space-y-6 lg:col-span-2">
                        <div class="rounded-lg border border-blue-200 bg-white">
                            <div class="border-b border-blue-100 px-4 py-3">
                                <h3 class="text-lg font-bold text-gray-800">Review Your Booking</h3>
                            </div>
                            <div class="p-4">
                                <div class="mb-4 rounded bg-gray-50 p-3 text-sm text-gray-600">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <p><span class="font-semibold text-gray-800">Booking Ref:</span> <code class="bg-blue-50 text-blue-700 px-1.5 py-0.5 rounded text-xs font-mono">{{ $flightId }}</code></p>
                                        <p><span class="font-semibold text-gray-800">Passengers:</span> {{ $passengers }} Adult{{ $passengers > 1 ? 's' : '' }}</p>
                                    </div>
                                </div>
                                <div class="rounded-lg border border-green-100 bg-green-50 p-4 flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <svg class="w-8 h-8 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <div>
                                            <p class="font-bold text-green-700 text-sm">Fare Validated via GHURI</p>
                                            <p class="text-green-600 text-xs mt-0.5">Your selected fare has been confirmed and is ready to book. Price includes all taxes.</p>
                                        </div>
                                    </div>
                                    <div class="shrink-0 text-right">
                                        <p class="text-[10px] text-gray-500">Total Fare</p>
                                        <p class="font-extrabold text-gray-800 text-lg">{{ $priceInfo['currency'] }} {{ number_format($priceInfo['total_price'] ?? 0, 2) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-lg border border-blue-200 bg-white">
                            <div class="border-b border-blue-100 px-4 py-3">
                                <h3 class="text-lg font-bold text-gray-800">Enter Traveller Details</h3>
                            </div>
                            <div class="p-4 space-y-4">
                                @for($i = 0; $i < $passengers; $i++)
                                    <div class="rounded-md border border-gray-200 bg-gray-50 p-4">
                                        <h4 class="mb-3 text-sm font-bold text-gray-800">Adult {{ $i + 1 }}</h4>
                                        <div class="grid grid-cols-1 gap-3 md:grid-cols-6">
                                            <div class="md:col-span-1">
                                                <label class="mb-1 block text-xs font-semibold text-gray-600">Title*</label>
                                                <select name="title[]" class="w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500" required>
                                                    <option value="Mr" {{ old('title.' . $i, 'Mr') === 'Mr' ? 'selected' : '' }}>Mr</option>
                                                    <option value="Mrs" {{ old('title.' . $i) === 'Mrs' ? 'selected' : '' }}>Mrs</option>
                                                    <option value="Ms" {{ old('title.' . $i) === 'Ms' ? 'selected' : '' }}>Ms</option>
                                                </select>
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="mb-1 block text-xs font-semibold text-gray-600">First Name*</label>
                                                <input type="text" name="first_name[]" value="{{ old('first_name.' . $i) }}" class="w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500" required>
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="mb-1 block text-xs font-semibold text-gray-600">Last Name*</label>
                                                <input type="text" name="last_name[]" value="{{ old('last_name.' . $i) }}" class="w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500" required>
                                            </div>
                                            <div class="md:col-span-1">
                                                <label class="mb-1 block text-xs font-semibold text-gray-600">Gender</label>
                                                <select class="w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                                    <option>Male</option>
                                                    <option>Female</option>
                                                </select>
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="mb-1 block text-xs font-semibold text-gray-600">Date of Birth*</label>
                                                <input type="date" name="dob[]" value="{{ old('dob.' . $i) }}" class="w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500" required>
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="mb-1 block text-xs font-semibold text-gray-600">Nationality*</label>
                                                <select name="nationality[]" class="w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500" required>
                                                    <option value="AE" {{ old('nationality.' . $i, 'AE') === 'AE' ? 'selected' : '' }}>United Arab Emirates</option>
                                                    <option value="US" {{ old('nationality.' . $i) === 'US' ? 'selected' : '' }}>United States</option>
                                                    <option value="UK" {{ old('nationality.' . $i) === 'UK' ? 'selected' : '' }}>United Kingdom</option>
                                                    <option value="IN" {{ old('nationality.' . $i) === 'IN' ? 'selected' : '' }}>India</option>
                                                    <option value="BD" {{ old('nationality.' . $i) === 'BD' ? 'selected' : '' }}>Bangladesh</option>
                                                </select>
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="mb-1 block text-xs font-semibold text-gray-600">Frequent Flyer Number</label>
                                                <input type="text" class="w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="FF Number">
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                                <p class="rounded-md bg-red-50 px-3 py-2 text-xs text-red-700">Please ensure names on tickets match passenger passports.</p>
                            </div>
                        </div>

                        <div class="rounded-lg border border-blue-200 bg-white">
                            <div class="border-b border-blue-100 px-4 py-3">
                                <h3 class="text-lg font-bold text-gray-800">Your Details/Billing Info</h3>
                            </div>
                            <div class="p-4 space-y-4">
                                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold text-gray-600">Email*</label>
                                        <input type="email" name="email" value="{{ old('email', auth()->check() ? auth()->user()->email : '') }}" class="w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500" required>
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold text-gray-600">Mobile/Telephone*</label>
                                        <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="Country code | Mobile Number" class="w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500" required>
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold text-gray-600">Address</label>
                                        <input type="text" class="w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Address">
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs font-semibold text-gray-600">City</label>
                                        <input type="text" class="w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="City">
                                    </div>
                                </div>

                                <div class="rounded-lg border border-gray-200 p-3">
                                    <p class="mb-2 text-xs text-gray-600">Payment Method*</p>
                                    <div class="grid grid-cols-1 gap-2 sm:grid-cols-3">
                                        <label class="cursor-pointer rounded border border-gray-300 px-3 py-2 text-center text-sm hover:border-blue-500">
                                            <input type="radio" name="payment_method" value="card" class="sr-only peer" {{ old('payment_method', 'card') === 'card' ? 'checked' : '' }}>
                                            <span class="font-semibold text-gray-700 peer-checked:text-blue-700">Credit/Debit Card</span>
                                        </label>
                                        <label class="cursor-pointer rounded border border-gray-300 px-3 py-2 text-center text-sm hover:border-blue-500">
                                            <input type="radio" name="payment_method" value="bkash" class="sr-only peer" {{ old('payment_method') === 'bkash' ? 'checked' : '' }}>
                                            <span class="font-semibold text-gray-700 peer-checked:text-blue-700">bKash</span>
                                        </label>
                                        <label class="cursor-pointer rounded border border-gray-300 px-3 py-2 text-center text-sm hover:border-blue-500">
                                            <input type="radio" name="payment_method" value="nagad" class="sr-only peer" {{ old('payment_method') === 'nagad' ? 'checked' : '' }}>
                                            <span class="font-semibold text-gray-700 peer-checked:text-blue-700">Nagad</span>
                                        </label>
                                    </div>
                                </div>

                                <label class="flex items-start gap-2 text-xs text-gray-600">
                                    <input type="checkbox" class="mt-0.5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span>I confirm that I have read and understood and agree with the Rate Details and Terms & Conditions.</span>
                                </label>

                                <div class="pt-2">
                                    <button type="submit" class="inline-flex items-center rounded bg-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow hover:bg-blue-700">
                                        Proceed To Book
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 lg:col-span-1">
                        <div class="rounded-lg border border-blue-200 bg-white">
                            <div class="border-b border-blue-100 px-4 py-3">
                                <h3 class="text-lg font-bold text-gray-800">Fare Details</h3>
                            </div>
                            <div class="space-y-3 p-4 text-sm">
                                @php
                                    $total = (float) ($priceInfo['total_price'] ?? 0);
                                    $base = $total * 0.42;
                                    $taxes = $total - $base;
                                @endphp
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600">Base Fare ({{ $passengers }} Traveller)</span>
                                    <span class="font-semibold">{{ $priceInfo['currency'] }} {{ number_format($base, 2) }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600">Taxes & Fee</span>
                                    <span class="font-semibold">{{ $priceInfo['currency'] }} {{ number_format($taxes, 2) }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600">Extra Baggage Fee</span>
                                    <span class="font-semibold">{{ $priceInfo['currency'] }} 0.00</span>
                                </div>
                                <div class="border-t border-blue-100 pt-3 flex items-center justify-between">
                                    <span class="font-bold text-gray-800">Total Fare</span>
                                    <span class="font-extrabold text-blue-700">{{ $priceInfo['currency'] }} {{ number_format($total, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-lg border border-blue-200 bg-white">
                            <div class="border-b border-blue-100 px-4 py-3">
                                <h3 class="text-lg font-bold text-gray-800">Promo Code</h3>
                            </div>
                            <div class="p-4">
                                <label class="mb-2 block text-xs font-semibold text-gray-600">Have a Promo Code?</label>
                                <input type="text" class="mb-3 w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Enter Promo Code">
                                <button type="button" class="w-full rounded bg-blue-600 py-2 text-sm font-semibold text-white hover:bg-blue-700">Apply</button>
                            </div>
                        </div>

                        <div class="rounded-lg border border-blue-200 bg-white">
                            <div class="border-b border-blue-100 px-4 py-3">
                                <h3 class="text-lg font-bold text-gray-800">Deal Code</h3>
                            </div>
                            <div class="p-4">
                                <label class="mb-2 block text-xs font-semibold text-gray-600">Have a Deal Code?</label>
                                <input type="text" class="mb-3 w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Enter Deal Code">
                                <button type="button" class="w-full rounded bg-blue-600 py-2 text-sm font-semibold text-white hover:bg-blue-700">Apply</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
