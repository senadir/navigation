/**
 * Get all the menu items.
 *
 * @todo This should be updated to use a wp data store.
 */
export const getMenuItems = () => {
	return window.wcNavigation || [];
}