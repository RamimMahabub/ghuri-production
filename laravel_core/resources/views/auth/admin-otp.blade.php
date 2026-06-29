<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-4 text-sm text-gray-600">
        Enter the 6-digit OTP sent to your email to complete internal sign-in.
    </div>

    @if(app()->environment(['local', 'testing']) && session('dev_admin_otp_preview'))
        <div class="mb-4 rounded-md border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800">
            <strong>Local OTP Preview:</strong> {{ session('dev_admin_otp_preview') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.otp.verify') }}">
        @csrf

        <div>
            <x-input-label for="otp" :value="__('One-Time Password')" />
            <x-text-input id="otp" class="block mt-1 w-full" type="text" name="otp" :value="old('otp')" required autofocus maxlength="6" autocomplete="one-time-code" />
            <x-input-error :messages="$errors->get('otp')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('admin.login') }}">
                Back to admin login
            </a>

            <x-primary-button>
                Verify OTP
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
