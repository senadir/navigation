/**
 * Internal dependencies
 */
import MenuItem from "./MenuItem";

export default function Menu() {
	const getMenuItems = () => {
		// @todo This should be updated to use a wp data store.
		return window.wcNavigation || [];
	}

	const getCategories = () => {
		return getMenuItems().filter( ( item ) => ! item.parent );
	}

	return (
		<ul className="woocommerce-navigation__menu">
			{ getCategories().map( ( item ) => {
				return (
					<MenuItem
						key={ item.slug }
						slug={ item.slug }
						title={ item.title }
						url={ item.url }
					/>
				)
			} ) }
		</ul>
	);
}