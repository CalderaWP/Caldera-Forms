/**
 * This file defines the configuration for development and dev-server builds.
 */
const fs = require('fs');
const {unlinkSync} = fs;
const path = require('path');
const {join} = path;
const onExit = require('signal-exit');
const webpack = require('webpack');
const ManifestPlugin = require('webpack-manifest-plugin');
const TerserJSPlugin = require('terser-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const OptimizeCSSAssetsPlugin = require('optimize-css-assets-webpack-plugin');

/**
 * Is build production or dev?
 *
 * @since 1.8.6
 *
 * @type {boolean}
 */
const isProduction = 'production' === process.env.NODE_ENV;
console.log( `Building for ${isProduction ? 'Production' : "Development"}`);

/**
 * The names of each entry point
 *
 * @since 1.8.6
 *
 * @type {string[]}
 */
const entryPointNames = [
	'admin',
	'privacy',
	'render',
];

/**
 * The webpack configuration for "entry"
 *
 * @since 1.8.6
 *
 * @see https://webpack.js.org/configuration/entry-context#entry
 */
const entry = entryPointNames.reduce((memo, entryPointName) => {
	memo[entryPointName] = './clients/' + entryPointName + '/index.js';
	return memo;
}, {});

/**
 * Compiler plugin for (S)CSS
 *
 * @since 1.8.6
 *
 * @see https://github.com/webpack-contrib/mini-css-extract-plugin
 *
 * @type {MiniCssExtractPlugin}
 */
const cssPlugin = new MiniCssExtractPlugin({
	filename: isProduction
		? '../clients/[name]/build/style.min.css'
		: '../clients/[name]/build/style.[hash].css',
	chunkFilename: isProduction
		? '../clients/[name]/build/[id].css'
		: '../clients/[name]/build/[id].[hash].css'
});

/**
 * Babel loader rule for (S)CSS
 *
 * @since 1.8.6
 *
 * @see https://github.com/webpack-contrib/mini-css-extract-plugin
 *
 * @type {{test: RegExp, use: *[]}}
 */
const cssRule = {
	test: /\.(sa|sc|c)ss$/,
	use: [
		{
			loader: MiniCssExtractPlugin.loader,
			options: {
				hmr: process.env.NODE_ENV === 'development',
			},
		},
		'css-loader',
		'sass-loader',
	],
};

/**
 * The port that webpack dev server uses
 *
 * @since 1.8.6
 *
 * @type {number}
 */
const port = parseInt(process.env.PORT, 10) || 3030;

/**
 * The URL that webpack dev server uses
 *
 * @since 1.8.6
 *
 * @type {string}
 */
const publicPath = `https://localhost:${port}/`;

/**
 * The dev server
 *
 * @see https://webpack.js.org/configuration/dev-server/
 *
 * @since 1.8.6
 *
 */
const devServer = {
	https: true,
	headers: {
		'Access-Control-Allow-Origin': '*',
	},
	hotOnly: true,
	watchOptions: {
		aggregateTimeout: 300,
	},
	writeToDisk: true,//False by default, so 404 on intial load
	disableHostCheck: true,
	stats: {
		all: false,
		assets: true,
		colors: true,
		errors: true,
		performance: true,
		timings: true,
		warnings: true,
	},
	port,
};

/**
 * The webpack configuration for "output"
 *
 * @since 1.8.6
 *
 * @see https://webpack.js.org/configuration/output/
 *
 * @type {{libraryTarget: string, filename: string, library: string[]}}
 */
const output = {
	//filename: '../clients/[name]/build/index.min.js',
	filename: isProduction ? '../clients/[name]/build/index.min.js' : '../clients/[name]/build/index.[hash].js',
	library: ['calderaForms', '[name]'],
	libraryTarget: 'this'
};

/**
 * The webpack configuration for "optimization"
 *
 * @since 1.8.6
 *
 * @see https://webpack.js.org/configuration/optimization/
 *
 * @type {{}}
 */
const optimization = isProduction ? {
	minimizer: [new TerserJSPlugin({}), new OptimizeCSSAssetsPlugin({})]
} : {
	minimize: false
};

/**
 * The webpack configuration for "externals"
 *
 * @since 1.8.6
 *
 * @see https://webpack.js.org/configuration/externals/
 *
 * @type {{}}
 */
const externals = {
	jquery: 'jQuery',
};

// Setup external for each entry point
entryPointNames.forEach( entryPointName => {
	externals[ '@/calderaForms' + entryPointName ] = {
		this: [ 'calderaForms', entryPointName ]
	}
} );




// Clean up manifest on exit.
onExit(() => {
	try {
		unlinkSync('./build/asset-manifest.json');
	} catch (e) {
		// Silently ignore unlinking errors: so long as the file is gone, that is good.
	}
});
/**
 * webpack config used for compiling clients that are not the blocks
 *
 * @since 1.8.6
 */
module.exports = {
	mode: isProduction ? 'production' : 'development',
	entry,
	output,
	optimization,
	externals,
	devtool: 'cheap-module-source-map',
	context: process.cwd(),
	devServer,
	module: {
		strictExportPresence: true,
		rules: [
			{
				// Process JS with Babel.
				test: /\.js$/,
				exclude: /(node_modules|clients\/editor)/,
				loader: require.resolve('babel-loader'),
				options: {
					// Cache compilation results in ./node_modules/.cache/babel-loader/
					cacheDirectory: true,
					presets: [require('@calderajs/babel-preset-calderajs')]

				},
			},
			cssRule
		],
	},

	plugins: [
		// Generate a manifest file which contains a mapping of all asset filenames
		// to their corresponding output file so that PHP can pick up their paths.
		new ManifestPlugin({
			fileName: 'asset-manifest.json',
			writeToFileEmit: true,
			publicPath,
			generate: (seed, files) => files.reduce((manifest, {name, path}) => {
				return ({...manifest, [name]: path.replace('/../clients/', '/clients/')})
			}, seed)
		}),
		// Enable HMR.
		new webpack.HotModuleReplacementPlugin(),
		//CSS/SASS
		cssPlugin
	],
};
