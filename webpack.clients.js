/**
 * This file defines the configuration for development and dev-server builds.
 */
const fs = require( 'fs' );
const { unlinkSync } = fs;
const path = require( 'path' );
const {join} = path;
const onExit = require( 'signal-exit' );
const webpack = require( 'webpack' );
const ManifestPlugin = require( 'webpack-manifest-plugin' );

const entry = [
	'admin',
	'privacy',
	'render',
	//'legacy-bundle'
].reduce( ( memo, entryPointName ) => {
	memo[ entryPointName ] = './clients/' + entryPointName + '/index.js';
	return memo;
}, {} );


// For extracting CSS (and SASS) into separate files
const MiniCssExtractPlugin  = require( 'mini-css-extract-plugin' );

const isProduction =  'production' === process.env.NODE_ENV;

// Clean up manifest on exit.
onExit( () => {
	try {
		unlinkSync( 'build/asset-manifest.json' );
	} catch ( e ) {
		// Silently ignore unlinking errors: so long as the file is gone, that is good.
	}
} );

const port = parseInt( process.env.PORT, 10 ) || 3030;
const publicPath = `https://localhost:${ port }/`;

const output = {
	filename: '../clients/[name]/build/index.min.js',
	//filename: isProduction ? '../clients/[name]/build/index.min.js' : '../clients/[name]/build/index.[hash].js',
	library: [ 'calderaForms', '[name]' ],
	libraryTarget: 'this'
};


/**
 * webpack config used for compiling clients that are not the blocks
 *
 * @since 1.8.6
 */
module.exports = {
	mode: isProduction ? 'production' : 'development',
	devtool: 'cheap-module-source-map',
	context: process.cwd(),
	devServer: {
		https: true,
		headers: {
			'Access-Control-Allow-Origin': '*',
		},
		hotOnly: true,
		watchOptions: {
			aggregateTimeout: 300,
		},
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
	},
	entry,
	output,
	module: {
		strictExportPresence: true,
		rules: [
			{
				// Process JS with Babel.
				test: /\.js$/,
				exclude: /(node_modules|clients\/editor)/,
				loader: require.resolve( 'babel-loader' ),
				options: {
					// Cache compilation results in ./node_modules/.cache/babel-loader/
					cacheDirectory: true,
					presets: [require('@calderajs/babel-preset-calderajs')]

				},
			},
			{
				test: /\.s?css$/,
				use: [
					MiniCssExtractPlugin.loader,
					{
						loader: "css-loader"
					},
					"sass-loader"
				]
			},
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
				return ({...manifest, [name]: path.replace( '/../clients/', '/clients/' )})
			}, seed)
		}),
		// Enable HMR.
		new webpack.HotModuleReplacementPlugin(),
		//Provide lodash global
		new webpack.ProvidePlugin({
			'lodash': '_',
		}),
		//Provide jQuery global
		new webpack.ProvidePlugin({
			jQuery: 'jquery',
		}),
		new MiniCssExtractPlugin({
			filename:  isProduction
				? '../clients/[name]/build/style.min.css'
				: '../clients/[name]/build/style.[hash].css',
			chunkFilename: isProduction
				? '../clients/[name]/build/[id].css'
				: '../[name]/build/[id].[hash].css'
		})
	],
};
