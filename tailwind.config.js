import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    safelist: [
        'stat-card',
        'stat-card-accent',
        'stat-card-warning',
        'stat-card-info',
        'card-3d',
        'sidebar-link',
        'header-blur',
        'bell-blink',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    50: '#ecfdf5',
                    100: '#d1fae5',
                    200: '#a7f3d0',
                    300: '#6ee7b7',
                    400: '#34d399',
                    500: '#10b981',
                    600: '#059669',
                    700: '#047857',
                    800: '#065f46',
                    900: '#064e3b',
                },
                accent: {
                    50: '#f0f9ff',
                    100: '#e0f2fe',
                    200: '#bae6fd',
                  300: '#7dd3fc',
                  400: '#38bdf8',
                  500: '#0ea5e9',
                  600: '#0284c7',
                  700: '#0369a1',
                  800: '#075985',
                  900: '#0c4a6e',
                },
            },
            boxShadow: {
                '3d': '0 10px 40px -10px rgba(0,0,0,0.15)',
                '3d-sm': '0 4px 15px -3px rgba(0,0,0,0.1)',
                '3d-lg': '0 20px 60px -15px rgba(0,0,0,0.2)',
                '3d-card': '0 2px 8px -2px rgba(0,0,0,0.08), 0 8px 24px -8px rgba(0,0,0,0.12)',
                '3d-hover': '0 10px 30px -5px rgba(0,0,0,0.12)',
                'glow-primary': '0 0 20px rgba(16, 185, 129, 0.3)',
                'glow-accent': '0 0 20px rgba(14, 165, 233, 0.3)',
                'inner-3d': 'inset 0 2px 4px 0 rgba(0,0,0,0.06)',
            },
            borderRadius: {
                'xl': '0.75rem',
                '2xl': '1rem',
                '3xl': '1.5rem',
            },
            animation: {
                'float': 'float 6s ease-in-out infinite',
                'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                'slide-up': 'slideUp 0.5s ease-out',
                'slide-down': 'slideDown 0.5s ease-out',
            },
            keyframes: {
                float: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-10px)' },
                },
                slideUp: {
                    '0%': { transform: 'translateY(20px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
                slideDown: {
                    '0%': { transform: 'translateY(-20px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
            },
        },
    },

    plugins: [forms],
};
