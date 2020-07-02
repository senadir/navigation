/**
 * External dependencies
 */
import { render } from '@wordpress/element';

/**
 * Internal dependencies
 */
import './stylesheets/index.scss';
import Navigation from './navigation';

const navigationRoot = document.getElementById( 'woocommerce-embedded-navigation' );
render( <Navigation />, navigationRoot );

