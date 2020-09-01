/**
 * External dependencies
 */
const path = require( 'path' );
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

module.exports = {
	...defaultConfig,
	entry: {
		index: __dirname + '/client/index.js',
	},
	resolve: {
		// @todo This is used to resolve multiple versions of React
		// when using `npm link` and can be removed after feature/navigation
		// has been merged and `@wordpress/components` has been updated.
		alias: {
			react: path.resolve( './node_modules/react' ),
		},
	},
};
