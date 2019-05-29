const path = require( 'path');
const {join,cwd} = path;
const isProduction =  'production' === process.env.NODE_ENV;

module.exports = {
    filename: '../clients/[name]/build/index.min.js',
    //filename: isProduction ? '../clients/[name]/build/index.min.js' : '../clients/[name]/build/index.[hash].js',
    library: [ 'calderaForms', '[name]' ],
    libraryTarget: 'this'
};
