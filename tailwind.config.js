module.exports = {
  purge: [
    './resources/views/**/*.blade.php',
    './resources/css/**/*.css',
  ],
  theme: {
    extend: {
        colors: {
            google: {
                'white': '#fff',
                'red': '#f28b82',
                'orange': '#fbbc04',
                'yellow': '#fff475',
                'green': '#ccff90',
                'teal': '#a7ffeb',
                'blue': '#cbf0f8',
                'dark-blue': '#aecbfa',
                'purple': '#d7aefb',
                'pink': '#fdcfe8',
                'brown': '#e6c9a8',
                'grey': '#e8eaed'
            }
        }
    }
  },
  variants: {},
  plugins: [
    require('@tailwindcss/ui'),
  ]
}
