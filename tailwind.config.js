import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/Livewire/**/*.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#EEF0FF',
                    100: '#D9DCFF',
                    200: '#B3BAFF',
                    300: '#8590FF',
                    400: '#4D5DFF',
                    500: '#0700FF', // Primary Dominante SurHospital
                    600: '#0500C9', // Dark Hover
                    700: '#04009B',
                    800: '#03006D',
                    900: '#020042',
                    primary: '#0700FF',
                    'primary-dark': '#0500C9',
                    soft: '#EEF0FF',
                },
                surface: '#FFFFFF',
                appbg: '#F7F8FC',
            },
        },
    },

    plugins: [forms, typography],
};
