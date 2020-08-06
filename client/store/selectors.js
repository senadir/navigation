/**
 * External dependencies
 */
import { applyFilters } from '@wordpress/hooks';

const MENU_ITEMS_HOOK = 'woocommerce_navigation_menu_items';

export const getMenuItems = ( state, id = null ) => {
	if ( ! id ) {
		return applyFilters( MENU_ITEMS_HOOK, state.menuItems );
	}

	return applyFilters(
		MENU_ITEMS_HOOK,
		state.menuItems.filter( ( item ) => item.menuId === id ),
		id
	);
};

export const getActiveItem = ( state ) => {
	return state.activeItem || null;
};
