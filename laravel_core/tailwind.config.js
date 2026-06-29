import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                heading: ['Outfit', 'Space Grotesk', ...defaultTheme.fontFamily.sans],
                body: ['Inter', ...defaultTheme.fontFamily.sans],
                mono: ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    primary: '#d00e15',
                    secondary: '#A90B16',
                    accent: '#E76553',
                    black: '#19100F',
                    text: '#19100F',
                    muted: '#6B7280',
                    surface: '#FFFFFF',
                    background: '#F8F7F7',
                    border: '#E5E7EB',
                    white: '#FFFFFF',
                },
                status: {
                    confirmed: '#2E7D32',
                    'confirmed-light': '#E8F5E9',
                    pending: '#F57F17',
                    'pending-light': '#FFF8E1',
                    cancelled: '#C62828',
                    'cancelled-light': '#FFEBEE',
                    info: '#1565C0',
                    'info-light': '#E3F2FD',
                    'checked-in': '#1565C0',
                    'checked-out': '#424242',
                    'no-show': '#212121',
                },
                // Keep legacy compat
                primary: '#d00e15',
                dark: '#19100F',
                light: '#F8F7F7',
                white: '#FFFFFF',
            },
            borderRadius: {
                sm: '6px',
                md: '8px',
                lg: '12px',
                xl: '16px',
                pill: '999px',
            },
            boxShadow: {
                card: '0 2px 8px rgba(0, 0, 0, 0.06)',
                'card-hover': '0 4px 16px rgba(0, 0, 0, 0.10)',
                'card-lg': '0 8px 30px rgba(0, 0, 0, 0.08)',
                sidebar: '4px 0 16px rgba(0, 0, 0, 0.08)',
            },
            animation: {
                'fade-in': 'fadeIn 0.3s ease-out',
                'slide-up': 'slideUp 0.3s ease-out',
                'slide-down': 'slideDown 0.3s ease-out',
                'slide-right': 'slideRight 0.3s ease-out',
                'scale-in': 'scaleIn 0.2s ease-out',
                'pulse-soft': 'pulseSoft 2s ease-in-out infinite',
                'check-bounce': 'checkBounce 0.6s ease-out',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideUp: {
                    '0%': { opacity: '0', transform: 'translateY(12px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                slideDown: {
                    '0%': { opacity: '0', transform: 'translateY(-12px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                slideRight: {
                    '0%': { opacity: '0', transform: 'translateX(-12px)' },
                    '100%': { opacity: '1', transform: 'translateX(0)' },
                },
                scaleIn: {
                    '0%': { opacity: '0', transform: 'scale(0.95)' },
                    '100%': { opacity: '1', transform: 'scale(1)' },
                },
                pulseSoft: {
                    '0%, 100%': { opacity: '1' },
                    '50%': { opacity: '0.7' },
                },
                checkBounce: {
                    '0%': { transform: 'scale(0)' },
                    '50%': { transform: 'scale(1.2)' },
                    '100%': { transform: 'scale(1)' },
                },
            },
        },
    },

    plugins: [forms],
};
