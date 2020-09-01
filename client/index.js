/**
 * External dependencies
 */
import { render } from '@wordpress/element';

/**
 * Internal dependencies
 */
import './stylesheets/index.scss';
import NavigationContainer from './navigation-container';

const navigationRoot = document.getElementById(
	'woocommerce-embedded-navigation'
);

if ( navigationRoot ) {
	render( <NavigationContainer />, navigationRoot );
}
