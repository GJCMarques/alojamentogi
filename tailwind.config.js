/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './*.php',
    './admin/**/*.php',
    './alojamento/**/*.php',
    './atividades/**/*.php',
    './contactos/**/*.php',
    './en/**/*.php',
    './includes/**/*.php',
    './loja/**/*.php',
    './manutencao/**/*.php',
    './politica-privacidade/**/*.php',
    './sobre-nos/**/*.php',
    './templates/**/*.php',
    './termos-condicoes/**/*.php',
  ],
  safelist: [
    // Classes de estado adicionadas via JavaScript (podem não ser detetadas no scan)
    'scrolled', 'menu-open', 'open', 'visible', 'active',
    'opacity-0', 'opacity-100', 'invisible', 'scale-90', 'scale-100', 'scale-105', 'scale-110',
    'translate-y-0', 'translate-y-4', 'translate-y-8', 'translate-x-0', 'scale-x-0',
    // Cores de flash messages construídas dinamicamente
    'bg-secondary', 'bg-red-500', 'bg-accent', 'bg-primary',
    'text-cream', 'text-white', 'text-primary',
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          50: '#e8edef', 100: '#d1dbdf', 200: '#a3b7bf', 300: '#75939f', 400: '#476f7f',
          500: '#264653', 600: '#1e3842', 700: '#172a32', 800: '#0f1c21', 900: '#080e11',
          DEFAULT: '#264653',
        },
        secondary: {
          50: '#f2f4f0', 100: '#e5e9e1', 200: '#cbd3c3', 300: '#b1bda5', 400: '#97a787',
          500: '#768A68', 600: '#5e6e53', 700: '#47533e', 800: '#2f372a', 900: '#181c15',
          DEFAULT: '#768A68',
        },
        accent: {
          50: '#faf6ed', 100: '#f5eddb', 200: '#ebdbb7', 300: '#e1c993', 400: '#d7b76f',
          500: '#C5A059', 600: '#9e8047', 700: '#766035', 800: '#4f4024', 900: '#272012',
          DEFAULT: '#C5A059',
        },
        cream: {
          50: '#FDFBF7', 100: '#faf5eb', 200: '#f5ebd7', 300: '#f0e1c3', 400: '#ebd7af',
          DEFAULT: '#FDFBF7',
        },
        charcoal: {
          50: '#f7f8f8', 100: '#ebedef', 200: '#d4d8dc', 300: '#b8bfc5', 400: '#9aa3ab',
          500: '#7b8792', 600: '#5f6a74', 700: '#4a5259', 800: '#2D3748', 900: '#1a2028',
          DEFAULT: '#2D3748',
        },
        terracotta: {
          50: '#fff8f6', 100: '#ffedeb', 200: '#fcd7d4', 300: '#fac1bd', 400: '#f29891',
          500: '#E07A5F', 600: '#cc6147', 700: '#a64d38', 800: '#8c402f', 900: '#733629',
          DEFAULT: '#E07A5F',
        },
      },
      fontFamily: {
        serif: ['Poppins', 'system-ui', 'sans-serif'],
        sans: ['Poppins', 'system-ui', 'sans-serif'],
        display: ['Great Vibes', 'cursive'],
        cursive: ['Great Vibes', 'cursive'],
      },
      animation: {
        'fade-in': 'fadeIn 1s ease-in-out',
        'slide-up': 'slideUp 0.8s ease-out',
        'float': 'float 3s ease-in-out infinite',
      },
      keyframes: {
        fadeIn: { '0%': { opacity: '0' }, '100%': { opacity: '1' } },
        slideUp: {
          '0%': { transform: 'translateY(40px)', opacity: '0' },
          '100%': { transform: 'translateY(0)', opacity: '1' },
        },
        float: {
          '0%, 100%': { transform: 'translateY(0)' },
          '50%': { transform: 'translateY(-10px)' },
        },
      },
    },
  },
  plugins: [],
};
