/**
 * External dependencies
 */
import { useEffect } from '@wordpress/element';

/**
 * Internal dependencies
 */
import Menu from '../menu';

export default function Navigation() {
	useEffect(() => {
		// Collapse the original WP Menu.
		const adminMenu = document.getElementById( 'adminmenumain' );
		adminMenu.classList.add( 'folded' );
	});

	return (
		<div className="woocommerce-navigation">
			<Menu />
		</div>
	);
}
