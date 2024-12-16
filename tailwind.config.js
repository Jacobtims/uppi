import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './resources/js/**/*.vue',
        './app/Filament/**/*.php',
    ],

    safelist: [
        // Colors for status indicators
        'text-green-500',
        'text-red-500',
        'text-yellow-500',
        'text-green-700',
        'text-red-700',
        'text-yellow-700',
        'bg-green-500',
        'bg-red-500',
        'bg-yellow-500',
        'bg-gray-200',
        'bg-gray-600',
        'dark:bg-gray-600',
        'dark:bg-gray-800',
        'dark:border-gray-700',
        'dark:text-white',
        'dark:text-neutral-300',
        'dark:text-neutral-400',

        // Utility classes that might be dynamically applied
        'rotate-180',
        'cursor-help',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [
        forms,
    ],
};
