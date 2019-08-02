/**
 * This file defines the configuration for development and dev-server builds for the render client
 */
const config = require( './webpack.clients');
const DependencyExtractionWebpackPlugin = require( '@wordpress/dependency-extraction-webpack-plugin' );



/**
 *
 * @see https://www.npmjs.com/package/@wordpress/dependency-extraction-webpack-plugin
 */
const plugins = config.hasOwnProperty('plugins' ) ? config.plugins : [];
plugins.filter(
	plugin => plugin.constructor.name !== 'DependencyExtractionWebpackPlugin',
);
plugins.push(
	new DependencyExtractionWebpackPlugin( {
		injectPolyfill: true,
		requestToExternal(request) {
			/* My externals */
		},
	} ),
);

/**
 * webpack config used for compiling render client
 *
 * @since 1.8.7
 */
module.exports = {
	...config,
	plugins,
};
