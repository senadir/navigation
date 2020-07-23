/**
 * Internal dependencies
 */
import TYPES from './action-types';

const reducer = (
	state = {
		activeItem: null,
		menus: {
			primary: window.wcNavigation || [],
			secondary: [],
		},
	},
	{ type, activeItem, menuId, menuItems }
) => {
	switch ( type ) {
		case TYPES.SET_ACTIVE_ITEM:
			state = {
				...state,
				activeItem,
			};
			break;
		case TYPES.SET_MENU_ITEMS:
			state = {
				...state,
				menus: {
					...state.menus,
					[ menuId ]: menuItems,
				},
			};
			break;
	}
	return state;
};

export default reducer;
