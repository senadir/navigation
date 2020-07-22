export const getMenuItems = ( state, id ) => {
	return state.menus[ id ] || [];
};

export const getActiveItem = ( state ) => {
	return state.activeItem || null;
};
