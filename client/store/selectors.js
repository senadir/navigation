export const getMenuItems = ( state, id = null ) => {
	if ( ! id ) {
		return state.menuItems;
	}

	return state.menuItems.filter( ( item ) => item.menuId === id );
};

export const getActiveItem = ( state ) => {
	return state.activeItem || null;
};
