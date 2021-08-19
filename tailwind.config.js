module.exports = {
  purge: [
    './resources/views/**/*.blade.php',
    './resources/css/**/*.css',
  ],
  theme: {
    extend: {

    }
  },
  variants: {
      extend: {
          display: ['group-hover'],
      }
  },
  plugins: [
    require('@tailwindcss/ui'),
  ]
}
