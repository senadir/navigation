/**
 * External dependencies
 */
import { Component } from '@wordpress/element';

/**
 * Internal dependencies
 */
import Menu from './Menu';

export default class Navigation extends Component {
	componentDidMount() {
		// Collapse the original WP Menu.
		const adminMenu = document.getElementById( 'adminmenumain' );
		adminMenu.classList.add( 'folded' );
	}

	render() {
		return (
			<div className="woocommerce-navigation">
				<Menu />
			</div>
		);
	}
}
