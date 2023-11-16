/* global process __dirname */

const path = require('path');
const webpack = require('webpack');
const CleanWebpackPlugin = require('clean-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const UglifyJsPlugin = require('uglifyjs-webpack-plugin');
const ManifestPlugin = require('webpack-manifest-plugin');

const appPath = `${path.resolve(__dirname)}`;

// Plugin
const pluginPath = '/assets';
const pluginFullPath = `${appPath}${pluginPath}`;
const pluginPublicPath = `${pluginPath}/public/`;
const pluginEntry = `${pluginFullPath}/dev/application.js`;
const pluginAdminEntry = `${pluginFullPath}/dev/application-admin.js`;
const pluginOutput = `${pluginFullPath}/public`;

// Outputs
const outputJs = 'scripts/[name]-[hash].js';
const outputCss = 'styles/[name]-[hash].css';
const outputFile = '[name]-[hash].[ext]';

const allModules = {
  rules: [
    {
      test: /\.(js|jsx)$/,
      use: 'babel-loader',
      exclude: /node_modules/,
    },
    {
      test: /\.scss$/,
      exclude: /node_modules/,
      use: [
        MiniCssExtractPlugin.loader,
        'css-loader', 'postcss-loader', 'sass-loader',
      ],
    },
  ],
};

const allPlugins = [
  new MiniCssExtractPlugin({
    filename: outputCss,
  }),

  new webpack.ProvidePlugin({
    $: 'jquery',
    jQuery: 'jquery',
  }),

  new webpack.DefinePlugin({
    'process.env': {
      NODE_ENV: JSON.stringify(process.env.NODE_ENV || 'development'),
    },
  }),

  new ManifestPlugin(),
];

const allOptimizations = {
  runtimeChunk: false,
  splitChunks: {
    cacheGroups: {
      commons: {
        test: /[\\/]node_modules[\\/]/,
        name: 'vendors',
        chunks: 'all',
      },
    },
  },
};

allOptimizations.minimizer = [
  new UglifyJsPlugin({
    cache: true,
    parallel: true,
    sourceMap: true,
    uglifyOptions: {
      output: {
        comments: false,
      },
      compress: {
        warnings: false,
        drop_console: true, // eslint-disable-line camelcase
      },
    },
  }),
];

allPlugins.push(new CleanWebpackPlugin([pluginOutput]));

module.exports = [

  // Skin
  {
    context: path.join(__dirname),

    entry: {
      application: [pluginEntry],
      applicationAdmin: [pluginAdminEntry],
    },

    output: {
      path: pluginOutput,
      publicPath: pluginPublicPath,
      filename: outputJs,
    },

    externals: {
      jquery: 'jQuery',
    },

    optimization: allOptimizations,

    module: allModules,

    plugins: allPlugins,
  },
];
