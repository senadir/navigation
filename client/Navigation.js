/**
 * External dependencies
 */
import { Component } from '@wordpress/element';
import { compose } from '@wordpress/compose';
import { withSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { NAVIGATION_STORE_NAME } from './store';
class Navigation extends Component {
	componentDidMount() {
		// Collapse the original WP Menu.
		const adminMenu = document.getElementById( 'adminmenumain' );
		adminMenu.classList.add( 'folded' );
	}

	getMenuItems() {
		// @todo This should be updated to use a wp data store.
		return window.wcNavigation || [];
	}

	getCategories() {
		return this.getMenuItems().filter( ( item ) => ! item.parent );
	}

	getChildren( id ) {
		if ( ! id ) {
			return [];
		}

		return this.getMenuItems().filter( ( item ) => item.parent === id );
	}

	renderMenuItem( item, depth = 0 ) {
		const { id, title, url } = item;
		const children = this.getChildren( id );

		return (
			<li
				key={ id }
				className={ `woocommerce-navigation__menu-item woocommerce-navigation__menu-item-depth-${ depth }` }
			>
				<a href={ url }>{ title }</a>
				{ children.length && (
					<ul className="woocommerce-navigation__submenu">
						{ children.map( ( childItem ) => {
							return this.renderMenuItem( childItem, depth + 1 );
						} ) }
					</ul>
				) }
			</li>
		);
	}

	render() {
		return (
			<div className="woocommerce-navigation">
				<ul className="woocommerce-navigation__menu">
					{ this.getCategories().map( ( item ) => {
						return this.renderMenuItem( item );
					} ) }
				</ul>
			</div>
		);
	}
}

export default compose(
	withSelect( ( select ) => {
		const { getActiveItem, getMenuItems } = select( NAVIGATION_STORE_NAME );

		return {
			activeItem: getActiveItem(),
			primaryMenuItems: getMenuItems( 'primary' ),
			secondaryMenuItems: getMenuItems( 'secondary' ),
		};
	} )
)( Navigation );
