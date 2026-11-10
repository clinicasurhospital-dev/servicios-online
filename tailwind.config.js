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
                    50: '#EFF5FC',
                    100: '#DBE8F8',
                    200: '#BCD6F2',
                    300: '#8CBDE9',
                    400: '#549DDE',
                    500: '#024185', // Primary Dominante SurHospital (#024185)
                    600: '#02336A', // Dark Hover
                    700: '#012650',
                    800: '#011A37',
                    900: '#010E1F',
                    primary: '#024185',
                    'primary-dark': '#02336A',
                    soft: '#EFF5FC',
                },
                surface: '#FFFFFF',
                appbg: '#F7F8FC',
            },
        },
    },

    plugins: [forms, typography],
};
