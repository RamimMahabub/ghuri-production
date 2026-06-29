<div x-show="tab === 'flights'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="py-12 md:py-16 text-center relative z-10" style="display:none;">
    <div class="max-w-2xl mx-auto px-4">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-red-50 mb-6 text-[#d00e15] relative shadow-inner">
            <svg class="w-10 h-10 transform -rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
            <div class="absolute -top-1 -right-1 w-4 h-4 bg-yellow-400 rounded-full border-2 border-white animate-ping"></div>
            <div class="absolute -top-1 -right-1 w-4 h-4 bg-yellow-400 rounded-full border-2 border-white"></div>
        </div>
        
        <h3 class="text-3xl md:text-4xl font-extrabold text-[#19100F] mb-4 tracking-tight">Flight Booking <span class="text-[#d00e15]">Coming Soon!</span></h3>
        
        <p class="text-gray-500 text-sm md:text-base mb-8 max-w-lg mx-auto font-medium">
            We are working hard to bring you the best flight deals across the globe. Our flight booking system is currently under maintenance and will be taking off shortly. Stay tuned!
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <button @click="tab = 'hotels'; $dispatch('tab-changed', { tab: 'hotels' })" class="bg-[#d00e15] hover:bg-[#A90B16] text-white px-8 py-3 rounded-xl font-bold shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                Book Hotels Instead
            </button>
            <a href="#" class="bg-white border border-gray-200 text-[#19100F] px-8 py-3 rounded-xl font-bold shadow-sm hover:bg-gray-50 transition-all">
                Notify Me
            </a>
        </div>
    </div>
</div>
