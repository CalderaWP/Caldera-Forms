const defaultConfig = require("./node_modules/@wordpress/scripts/config/webpack.config");
const path = require('path');

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
        filename: '[name].js',
        path: path.resolve( process.cwd(), 'clients/blocks/build' ),
    }
};
