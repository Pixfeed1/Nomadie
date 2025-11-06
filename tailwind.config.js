/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  theme: {
    extend: {
      colors: {
        'primary': '#38B2AC',
        'primary-dark': '#2C7A7B',
        'accent': '#F6AD55',
        'accent-dark': '#DD6B20',
        'bg-main': '#F7FAFC',
        'bg-alt': '#EDF2F7',
        'text-primary': '#2D3748',
        'text-secondary': '#718096',
        'border': '#E2E8F0',
        'success': '#68D391',
        'error': '#FC8181',
      }
    },
  },
  plugins: [],
};