/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./app/Models/**/*.php",
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          50: '#f0f9ff',
          100: '#e0f2fe',
          200: '#bae6fd',
          300: '#7dd3fc',
          400: '#38bdf8',
          500: '#0284c7', // Aquatica blue
          600: '#0369a1',
          700: '#075985',
          800: '#0c4a6e',
          900: '#082f49',
        },
        navy: {
          800: '#0f172a',
          900: '#020617',
        }
      }
    },
  },
  plugins: [],
}
