// For extracting CSS (and SASS) into separate files
const MiniCssExtractPlugin  = require( 'mini-css-extract-plugin' );

// Main CSS loader for everything but blocks..
const cssExtractTextPlugin = new MiniCssExtractPlugin({
    filename:  'production' === process.env.NODE_ENV ? './clients/[[name].css' : '[name].[hash].css',
    chunkFilename: 'production' === process.env.NODE_ENV ? './clients/[[id].css' : './clients/[[id].[hash].css'
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

module.exports = {extractConfig,cssExtractTextPlugin}
