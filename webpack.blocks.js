const defaultConfig = require("./node_modules/@wordpress/scripts/config/webpack.config");
const DependencyExtractionWebpackPlugin = require( '@wordpress/dependency-extraction-webpack-plugin' );
const path = require('path');


//Remove default dependency extractor
const plugins = defaultConfig.plugins.filter(
    plugin => plugin.constructor.name !== 'DependencyExtractionWebpackPlugin',
);

//Add dependency extractor back, with different options.
plugins.push(
    new DependencyExtractionWebpackPlugin( {
        injectPolyfill: true,
        //By default php file is generated, we want JSON
        outputFormat: 'json',
    } ),
);
/**
 * webpack config used for compiling blocks
 *
 * @since 1.8.6
 */
module.exports = {
    ...defaultConfig,
    entry: {
        index: path.resolve( process.cwd(), 'clients/blocks', 'index.js' ),
    },
    output: {
        filename: 'index.min.js',
        path: path.resolve( process.cwd(), 'clients/blocks/build' ),
    },
    plugins
};
