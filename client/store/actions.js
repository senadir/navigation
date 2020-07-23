/**
 * Internal Dependencies
 */
import TYPES from './action-types';

export function setActiveItem( activeItem ) {
	return {
		type: TYPES.SET_ACTIVE_ITEM,
		activeItem,
	};
}

export function setMenuItems( menuId, menuItems ) {
	return {
		type: TYPES.SET_ACTIVE_ITEM,
		menuId,
		menuItems,
	};
}
