<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Travel Agent') }} - Authentication</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:ital,wght@0,400;0,500;0,600;0,700;1,400&family=JetBrains+Mono:wght@400;500;600&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-brand-background text-brand-text">
    <div class="flex min-h-screen w-full">
        <!-- Left Side: Dynamic Image/Branding -->
        <div class="hidden md:flex md:w-1/2 relative bg-brand-black items-center justify-center overflow-hidden">
            <!-- Background Image -->
            <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1542314831-c6a4d27ce66b?q=80&w=2070&auto=format&fit=crop')] bg-cover bg-center opacity-60 mix-blend-overlay"></div>
            
            <!-- Overlay Gradient -->
            <div class="absolute inset-0 bg-gradient-to-t from-[#19100F] via-[#19100F]/60 to-transparent"></div>

            <div class="relative z-10 w-full max-w-lg px-12 text-center">
                <a href="/" class="inline-block mb-8">
                    <x-application-logo class="w-24 h-24 text-brand-primary drop-shadow-md mx-auto" />
                </a>
                <h1 class="text-4xl font-heading font-bold text-white mb-4 leading-tight">
                    Your Journey Begins Here
                </h1>
                <p class="text-lg text-gray-300">
                    Join our platform as a traveler to explore the world, or as a property owner to host amazing guests.
                </p>
                
                <div class="mt-12 flex justify-center gap-6">
                    <div class="text-center">
                        <div class="w-16 h-16 rounded-full bg-white/10 backdrop-blur-md flex items-center justify-center mx-auto mb-3 border border-white/20">
                            <i class="fas fa-plane-departure text-2xl text-white"></i>
                        </div>
                        <span class="text-sm font-medium text-white/80">Flight Booking</span>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 rounded-full bg-white/10 backdrop-blur-md flex items-center justify-center mx-auto mb-3 border border-white/20">
                            <i class="fas fa-hotel text-2xl text-white"></i>
                        </div>
                        <span class="text-sm font-medium text-white/80">Hotel Stays</span>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 rounded-full bg-white/10 backdrop-blur-md flex items-center justify-center mx-auto mb-3 border border-white/20">
                            <i class="fas fa-key text-2xl text-white"></i>
                        </div>
                        <span class="text-sm font-medium text-white/80">List Property</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Form -->
        <div class="w-full md:w-1/2 flex items-center justify-center bg-brand-background overflow-y-auto p-4 sm:p-8">
            <div class="w-full max-w-md bg-white px-8 py-10 rounded-3xl shadow-sm border border-gray-100">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>
