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
        // Shared semantic components (see resources/css/app.css)
        'fc-btn-primary',
        'fc-btn-cta',
        'fc-btn-ghost',
        'fc-section-kicker',
        'fc-surface',
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
                // Secondary = teal, pairs with primary green (matches landing config)
                secondary: {
                    50: '#f0fdfa',
                    100: '#ccfbf1',
                    200: '#99f6e4',
                    300: '#5eead4',
                    400: '#2dd4bf',
                    500: '#14b8a6',
                    600: '#0d9488',
                    700: '#0f766e',
                    800: '#115e59',
                    900: '#134e4a',
                },
                // Accent = warm gold CTA. Previously blue, which clashed with the
                // amber buttons already used on the landing pages.
                accent: {
                    50: '#fffbeb',
                    100: '#fef3c7',
                    200: '#fde68a',
                    300: '#fcd34d',
                    400: '#fbbf24',
                    500: '#f59e0b',
                    600: '#d97706',
                    700: '#b45309',
                    800: '#92400e',
                    900: '#78350f',
                },
                // Semantic state colors, kept in sync with the CSS tokens
                success: '#059669',
                warning: '#d97706',
                danger: '#dc2626',
                info: '#0d9488',
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
                // Previously declared only in the inline `tailwind.config`
                // blocks that the Play CDN needed. Those blocks are gone, so
                // the animations the public pages call have to live here.
                'slide-in': 'slideIn 0.5s ease-out forwards',
                'fade-in': 'fadeIn 0.8s ease-out forwards',
                'bounce-slow': 'bounceSlow 5s ease-in-out infinite',
            },
            keyframes: {
                float: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-10px)' },
                },
                slideIn: {
                    '0%': { transform: 'translateY(20px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                bounceSlow: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-15px)' },
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
