/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    DEFAULT: '#2E7D32',
                    light: '#4CAF50',
                    dark: '#1B5E20',
                },
            },
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
            transitionTimingFunction: {
                DEFAULT: 'cubic-bezier(0.32,0.72,0,1)',
            },
        },
    },
    daisyui: {
        themes: [
            {
                archery: {
                    ...require('daisyui/src/theming/themes')['light'],
                    primary: '#2E7D32',
                    'primary-focus': '#1B5E20',
                    'primary-content': '#ffffff',
                },
            },
        ],
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
        require('daisyui'),
    ],
};
