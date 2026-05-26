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
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                cafe: {
                    50:  '#fdf8f2',
                    100: '#f5e6d3',
                    200: '#e8c9a0',
                    300: '#d4a574',
                    400: '#c08040',
                    500: '#a0522d', // sienna — main brand
                    600: '#8b4513', // saddlebrown
                    700: '#6b3410',
                    800: '#4a2008',
                    900: '#2d1205',
                    950: '#1a0a02',
                },
            },
        },
    },

    plugins: [forms],
};
