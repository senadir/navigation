/**
 * External dependencies
 */
import { Component } from '@wordpress/element';

export default class Navigation extends Component {
	componentDidMount() {
		// Collapse the original WP Menu.
		const adminMenu = document.getElementById( 'adminmenumain' );
		adminMenu.classList.add( 'folded' );
	}

	renderMenuItem( item, depth = 0 ) {
		const { slug, title, url } = item;

		return (
			<li
				key={ slug }
				className={ `woocommerce-navigation__menu-item woocommerce-navigation__menu-item-depth-${ depth }` }
			>
				<a href={ url }>{ title }</a>
				{ item.children && item.children.length && (
					<ul className="woocommerce-navigation__submenu">
						{ item.children.map( ( childItem ) => {
							return this.renderMenuItem( childItem, depth + 1 );
						} ) }
					</ul>
				) }
			</li>
		);
	}

	render() {
		// @todo This should be updated to use a wp data store.
		const items = window.wcSettings && window.wcSettings.wcNavigation ? window.wcSettings.wcNavigation : [];

		return (
			<div className="woocommerce-navigation">
				<ul className="woocommerce-navigation__menu">
					{ items.map( ( item ) => {
						return this.renderMenuItem( item );
					} ) }
				</ul>
			</div>
		);
	}
}
