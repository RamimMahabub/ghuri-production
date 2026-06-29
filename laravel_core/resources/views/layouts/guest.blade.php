<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:ital,wght@0,400;0,500;0,600;0,700;1,400&family=JetBrains+Mono:wght@400;500;600&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-brand-text antialiased bg-brand-background selection:bg-brand-primary selection:text-white">
        <!-- Dynamic Gradient Background -->
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative overflow-hidden bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-brand-primary/5 via-brand-background to-brand-primary/10">
            
            <!-- Decorative Elements -->
            <div class="absolute top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
                <div class="absolute -top-40 -right-40 w-96 h-96 bg-brand-primary/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-pulse-soft"></div>
                <div class="absolute top-40 -left-20 w-72 h-72 bg-brand-accent/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-pulse-soft" style="animation-delay: 2s;"></div>
            </div>

            <div class="z-10 mb-8 animate-slide-up">
                <a href="/">
                    <x-application-logo class="w-24 h-24 text-brand-primary drop-shadow-md" />
                </a>
            </div>

            <div class="w-full sm:max-w-md px-8 py-8 bg-white/80 backdrop-blur-xl shadow-card-lg border border-white/50 overflow-hidden sm:rounded-2xl z-10 animate-fade-in transition-all duration-300">
                {{ $slot }}
            </div>
            
            <div class="mt-8 text-center text-sm text-gray-500 z-10 animate-fade-in" style="animation-delay: 0.2s;">
                &copy; {{ date('Y') }} {{ config('app.name', 'Travel Agent') }}. All rights reserved.
            </div>
        </div>
    </body>
</html>
