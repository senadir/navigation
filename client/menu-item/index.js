/**
 * Internal dependencies
 */
import { getMenuItems } from '../utils';

export default function MenuItem( { slug, url, title } ) {
	const getChildren = ( slug ) => {
		if ( ! slug ) {
			return [];
		}

		return getMenuItems().filter( ( item ) => item.parent === slug );
	}

	const renderItem = ( { slug, url, title }, depth = 0 ) => {
		const children = getChildren( slug );

		return (
			<li
				key={ slug }
				className={ `woocommerce-navigation__menu-item woocommerce-navigation__menu-item-depth-${ depth }` }
			>
				<a href={ url }>{ title }</a>
				{ children.length && (
					<ul className="woocommerce-navigation__submenu">
						{ children.map( ( childItem ) => {
							return renderItem( childItem, depth + 1 );
						} ) }
					</ul>
				) }
			</li>
		);
	}

	return renderItem( { slug, url, title }, 0 );
}