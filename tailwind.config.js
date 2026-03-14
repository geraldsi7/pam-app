import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        "./resources/**/*.vue",
        "./resources/**/*.js",
        "./resources/**/*.blade.php",
        "./resources/**/*.antlers.html",
        "./content/**/*.md",
        "./content/**/*.yaml",
    ],
    safelist: [
        'col-span-1',
        'md:col-span-1',
        'md:col-span-2',
        'md:col-span-3',
        'md:col-span-4',
        'md:col-span-5',
        'md:col-span-6',
        'md:col-span-7',
        'md:col-span-8',
        'md:col-span-9',
        'md:col-span-10',
        'md:col-span-11',
        'md:col-span-12',
        'bg-red-600',
        'border-red-500',
    ],
    darkMode: 'class',
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                admin: {
                    ink: '#0F172A',
                    muted: '#64748B',
                    line: '#E2E8F0',
                    surface: '#FFFFFF',
                    accent: '#4F46E5',
                },
            },
            boxShadow: {
                panel: '0 12px 40px -24px rgba(15, 23, 42, 0.45)',
            },
        },
    },

    plugins: [
        forms,
        require('@tailwindcss/typography')
    ],
};
