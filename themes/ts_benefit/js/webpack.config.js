var webpack = require('webpack'),
    path = require('path');

module.exports = {
    debug: true,
    entry: {
        main: './main.js'
    },
    output: {
        path: __dirname,
        filename: '[name].js'
    },
    module: {
      loaders: [{
        test: /\.js$/,
        exclude: /(node_modules|bower_components)/,
        loader: 'babel', // 'babel-loader' is also a valid name to reference
        query: {
          presets: ['es2015']
        }
      }]
    },
    resolve: {
      extensions: ['', '.js', '.json', '.coffee'],
      alias: {
          $: "/core/assets/vendor/jquery/jquery" // Drupal core jquery
      }

    },
};
