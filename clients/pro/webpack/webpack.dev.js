'use strict'
process.env.NODE_ENV = 'development'

const webpack = require('webpack')
const base = require('./webpack.base')
const _ = require('./utils')
const FriendlyErrors = require('friendly-errors-webpack-plugin');
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');


base.devtool = 'eval-source-map'
base.plugins.push(
	new webpack.DefinePlugin({
		'process.env.NODE_ENV': JSON.stringify('development')
	}),
	new webpack.HotModuleReplacementPlugin(),
	new webpack.NoEmitOnErrorsPlugin(),
	new FriendlyErrors(),
	new BrowserSyncPlugin({
		files: [],
		host: 'localhost',
		port: 3000,
		proxy: 'wp2.dev'
	})
);

// push loader for css files
_.cssProcessors.forEach(processor => {
  let loaders
  if (processor.loader === '') {
    loaders = ['postcss-loader']
  } else {
    loaders = ['postcss-loader', processor.loader]
  }
  base.module.loaders.push(
    {
      test: processor.test,
      loaders: ['style-loader', _.cssLoader].concat(loaders)
    }
  )
})

module.exports = base
