/**
 * Internal dependencies
 */
import { getMenuItems } from '../utils';
import MenuItem from "../menu-item";

export default function Menu() {
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