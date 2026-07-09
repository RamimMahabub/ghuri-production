<x-app-layout>
    <div class="max-w-4xl mx-auto py-16 px-4 text-center">
        <div class="relative inline-block mb-8">
            <div class="w-32 h-32 bg-red-50 rounded-full flex items-center justify-center animate-pulse-slow">
                <i class="fas fa-plane-departure text-5xl text-[#d00e15] ml-2"></i>
            </div>
            
            {{-- Decorative clouds/dots --}}
            <div class="absolute top-0 right-[-20px] w-8 h-8 bg-blue-100 rounded-full opacity-60"></div>
            <div class="absolute bottom-4 left-[-10px] w-4 h-4 bg-yellow-100 rounded-full opacity-80"></div>
        </div>

        <h1 class="text-4xl md:text-5xl font-heading font-bold text-gray-900 mb-4 tracking-tight">
            Flights Are Taking Off Soon!
        </h1>
        
        <p class="text-lg md:text-xl text-gray-500 mb-10 max-w-2xl mx-auto leading-relaxed">
            We are working hard behind the scenes to bring you the best flight deals and a seamless booking experience. Get ready to explore the skies with GhuriTravel.
        </p>

        <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 max-w-xl mx-auto">
            <h3 class="font-heading font-semibold text-gray-800 mb-2">Be the first to know</h3>
            <p class="text-sm text-gray-500 mb-6">Join our waitlist to get notified as soon as flights are available, plus receive an exclusive launch discount.</p>
            
            <form class="flex gap-2 max-w-md mx-auto" @submit.prevent="alert('Thank you! We will notify you when flights are launched.')">
                <input type="email" placeholder="Enter your email address" required 
                       class="flex-1 rounded-xl border-gray-200 shadow-sm focus:border-[#d00e15] focus:ring focus:ring-[#d00e15]/20 text-sm px-4">
                <button type="submit" class="bg-[#d00e15] hover:bg-[#b00c12] text-white px-6 py-2.5 rounded-xl font-medium transition-colors whitespace-nowrap shadow-sm">
                    Notify Me
                </button>
            </form>
        </div>

        <div class="mt-12">
            <a href="{{ route('hotels.search') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-[#d00e15] transition-colors font-medium">
                <i class="fas fa-bed"></i> In the meantime, browse our amazing hotels
            </a>
        </div>
    </div>

    <style>
        .animate-pulse-slow {
            animation: pulse-slow 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse-slow {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: .8; transform: scale(1.05); }
        }
    </style>
</x-app-layout>
