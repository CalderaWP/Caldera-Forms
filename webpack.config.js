/**
 * External dependencies
 */
//utils need for building pro
const _ = require('./clients/pro/webpack/utils');
// Load webpack for use of certain webpack tools and methods
const webpack = require( 'webpack' );
// For extracting CSS (and SASS) into separate files
const ExtractTextPlugin = require( 'extract-text-webpack-plugin' );

//Creates HTML for mounting using a PHP file
const HtmlWebpackPlugin = require('html-webpack-plugin');


// Main CSS loader for everything but blocks..
const cssExtractTextPlugin = new ExtractTextPlugin({
    // Extracts CSS into a build folder inside the directory current directory
    filename: './clients/[name]/build/style.min.css'
});

// Configuration for the ExtractTextPlugin.
// Handles CSS
const extractConfig = {
    use: [
        { loader: 'raw-loader' },
        {
            loader: 'postcss-loader',
            options: {
                plugins: [ require( 'autoprefixer' ) ]
            }
        },
        {
            loader: 'sass-loader',
            query: {
                outputStyle:
                // Compresses CSS when in production
                    'production' === process.env.NODE_ENV ? 'compressed' : 'nested'
            }
        }
    ]
};

// Define JavaScript entry points
const entryPointNames = [
    'privacy',
    'blocks',
    'pro',
    'render',
    'legacy-bundle'
];

// Setup externals
const externals = {
    jquery: 'jQuery'
};
// Setup external for each entry point
entryPointNames.forEach( entryPointName => {
    externals[ '@/calderaForms' + entryPointName ] = {
        this: [ 'calderaForms', entryPointName ]
    }
} );

// Define WordPress dependencies
const wpDependencies = [ 'components', 'element', 'blocks', 'utils', 'date', 'data', 'editor' ];
// Setup externals for all WordPress dependencies
wpDependencies.forEach( wpDependency => {
    externals[ '@wordpress/' + wpDependency ] = {
        this: [ 'wp', wpDependency ]
    };
});


// Start of main webpack config
const config = {
    // Go through each entry point and prepare for use with externals
    entry: entryPointNames.reduce( ( memo, entryPointName ) => {
        memo[ entryPointName ] = './clients/' + entryPointName + '/index.js';
        return memo;
    }, {} ),
    // Include externals
    externals,
    // Set output
    output: {
        // Place all bundles JS into build folder in current directory
        filename: 'clients/[name]/build/index.min.js',
        path: __dirname,
        library: [ 'calderaForms', '[name]' ],
        libraryTarget: 'this'
    },
    // Fall back to node_modules for file resolution
    resolve: {
        extensions: ['.js', '.vue', '.css', '.json'],
        modules: [ __dirname, 'node_modules' ]
    },
    module: {
        rules: [
            //Compile Vue single file templates
            {
                test: /\.vue$/,
                loader: 'vue-loader',
                exclude: /(node_modules|bower_components)/,
                query: {
                    presets: ['es2015']
                }
            },
            {
                // Run JavaScript files through Babel
                test: /\.js$/,
                exclude: /(node_modules|clients\/editor)/,
                use: 'babel-loader'
            },
            {
                // Setup SASS (and CSS) to be extracted
                test: /\.s?css$/,
                use: cssExtractTextPlugin.extract( extractConfig )
            }
        ]
    },
    plugins: [
        // Setup environment conditions
        new webpack.DefinePlugin( {
            'process.env.NODE_ENV': JSON.stringify(
                process.env.NODE_ENV || 'development'
            )
        } ),
        // Pull in cssExtractTextPlugin settings
        cssExtractTextPlugin,
        // For migrations from webpack 1 to webpack 2+
        new webpack.LoaderOptionsPlugin( {
            minimize: process.env.NODE_ENV === 'production',
            debug: process.env.NODE_ENV !== 'production'
        } ),
        new webpack.ProvidePlugin({
            jQuery: 'jquery',
        }),
        new webpack.LoaderOptionsPlugin(
            _.loadersOptions()
        )

    ],
    // Do not include information about children in stats
    stats: {
        children: false
    }
};

switch ( process.env.NODE_ENV ) {
    case 'production':
        // Minify JavaScript when in production
        config.plugins.push( new webpack.optimize.UglifyJsPlugin() );
        break;

    default:
        // Apply source mapping when not in production
        config.devtool = 'source-map';
}

module.exports = config;