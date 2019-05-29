const entryPointNames = [
    'admin',
    'privacy',
    'render',
    //'legacy-bundle'
];

module.exports = entryPointNames.reduce( ( memo, entryPointName ) => {
    memo[ entryPointName ] = './clients/' + entryPointName + '/index.js';
    return memo;
}, {} );
