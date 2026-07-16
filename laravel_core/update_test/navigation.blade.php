@php
    $authUser = Auth::user();
    $isInternalUser = $authUser && $authUser->isInternalUser();
    $dashboardRoute = $isInternalUser ? 'admin.dashboard' : 'dashboard';
@endphp

<nav x-data="{ open: false }" class="sticky top-0 z-50 border-b border-white/70 bg-white/92 backdrop-blur-xl shadow-[0_10px_30px_rgba(15,23,42,0.08)]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex h-14 items-center justify-between">
            <div class="flex items-center gap-10">
                <a href="{{ url('/') }}" class="flex items-center gap-2">
                    <img src="{{ asset('images/logo-dark.png') }}" alt="bookdei" class="w-auto object-contain drop-shadow-sm transition-transform duration-300 hover:scale-105" style="height: 48px; margin-left: -10px;">
                </a>

                <div class="hidden sm:flex items-center gap-8">
                    <a href="{{ route($dashboardRoute) }}" class="inline-flex items-center border-b-2 pt-1 text-sm font-semibold transition {{ request()->routeIs($dashboardRoute) ? 'border-[#1882FF] text-[#1882FF]' : 'border-transparent text-slate-500 hover:border-[#1882FF]/40 hover:text-[#1a2b49]' }}">
                        {{ __('Dashboard') }}
                    </a>
                    <a href="{{ route('hotels.search') }}" class="inline-flex items-center border-b-2 pt-1 text-sm font-semibold transition {{ request()->routeIs('hotels.*') ? 'border-[#1882FF] text-[#1882FF]' : 'border-transparent text-slate-500 hover:border-[#1882FF]/40 hover:text-[#1a2b49]' }}">
                        {{ __('Hotels') }}
                    </a>
                    @auth
                        @if (! $isInternalUser && !Auth::user()->isPropertyOwner())
                        <a href="{{ route('my-bookings.index') }}" class="inline-flex items-center border-b-2 pt-1 text-sm font-semibold transition {{ request()->routeIs('my-bookings.*') ? 'border-[#1882FF] text-[#1882FF]' : 'border-transparent text-slate-500 hover:border-[#1882FF]/40 hover:text-[#1a2b49]' }}">
                            {{ __('Booking History') }}
                        </a>
                        @endif
                    @endauth
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center gap-4">
                <div x-data="{ currencyOpen: false }" class="relative">
                    <button type="button" @click="currencyOpen = !currencyOpen" class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-[#1a2b49] shadow-sm transition hover:border-[#1882FF]/30 hover:text-[#1882FF]" aria-label="Select currency">
                        {{ session('currency', 'BDT') }}
                        <svg class="w-3 h-3 transition-transform" :class="currencyOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    <div x-show="currencyOpen" @click.away="currencyOpen = false" x-transition style="display:none;" class="absolute right-0 mt-2 w-32 bg-white border border-gray-100 rounded-xl shadow-lg overflow-hidden z-50">
                        <form action="{{ route('currency.set') }}" method="POST">
                            @csrf
                            <input type="hidden" name="currency" value="BDT">
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm {{ session('currency', 'BDT') === 'BDT' ? 'font-bold text-[#1882FF] bg-blue-50' : 'text-gray-700 hover:bg-gray-50' }}">
                                BDT (৳)
                            </button>
                        </form>
                        <form action="{{ route('currency.set') }}" method="POST" class="border-t border-gray-50">
                            @csrf
                            <input type="hidden" name="currency" value="USD">
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm {{ session('currency') === 'USD' ? 'font-bold text-[#1882FF] bg-blue-50' : 'text-gray-700 hover:bg-gray-50' }}">
                                USD ($)
                            </button>
                        </form>
                    </div>
                </div>

                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-[#1a2b49] shadow-sm transition hover:border-[#1882FF]/30 hover:text-[#1882FF]">
                                <span>{{ Auth::user()->name }}</span>
                                <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            @if (! $isInternalUser)
                                <x-dropdown-link :href="route('profile.edit')">
                                    {{ __('Profile') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('my-bookings.index')">
                                    {{ __('Booking History') }}
                                </x-dropdown-link>
                            @endif

                            @if (Auth::user() && Auth::user()->isPropertyOwner())
                                <x-dropdown-link :href="route('property-owner.dashboard')">
                                    {{ __('PMS Dashboard') }}
                                </x-dropdown-link>
                            @endif

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}" class="rounded-full px-4 py-2 text-sm font-semibold text-[#1a2b49] transition hover:text-[#1882FF]">Log in</a>
                    <a href="{{ route('register') }}" class="rounded-full bg-[#1a2b49] px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-[#24385d]">Register</a>
                @endauth
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center rounded-md p-2 text-slate-600 transition hover:bg-slate-100 hover:text-[#1882FF] focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-slate-100 bg-white/98">
        <div class="space-y-1 px-4 py-3">
            <x-responsive-nav-link :href="route($dashboardRoute)" :active="request()->routeIs($dashboardRoute)" class="text-slate-700">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <div class="border-t border-slate-100 px-4 py-4">
            @auth
                <div class="mb-3">
                    <div class="font-medium text-base text-[#1a2b49]">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-slate-500">{{ Auth::user()->email }}</div>
                </div>

                <div class="space-y-1">
                    @if (! $isInternalUser)
                        <x-responsive-nav-link :href="route('profile.edit')" class="text-slate-700 hover:text-[#1882FF]">
                            {{ __('Profile') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('my-bookings.index')" class="text-slate-700 hover:text-[#1882FF]">
                            {{ __('Booking History') }}
                        </x-responsive-nav-link>
                    @endif

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <x-responsive-nav-link :href="route('logout')" class="text-slate-700 hover:text-[#1882FF]" onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            @else
                <div class="space-y-1">
                    <x-responsive-nav-link :href="route('login')" class="text-slate-700 hover:text-[#1882FF]">
                        {{ __('Log in') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('register')" class="text-slate-700 hover:text-[#1882FF]">
                        {{ __('Register') }}
                    </x-responsive-nav-link>
                </div>
            @endauth
        </div>
    </div>
</nav>
