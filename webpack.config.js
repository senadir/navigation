const defaultConfig = require( "@wordpress/scripts/config/webpack.config" );

module.exports = {
    ...defaultConfig,
    entry: {
        index: './client/index.js',
    }
};