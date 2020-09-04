/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { useEffect } from '@wordpress/element';
import { compose } from '@wordpress/compose';
import {
	__experimentalNavigation as Navigation,
	__experimentalNavigationMenu as NavigationMenu,
	__experimentalNavigationMenuItem as NavigationMenuItem,
	Button,
} from '@wordpress/components';
import { Icon, chevronLeft } from '@wordpress/icons';
import { withSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { NAVIGATION_STORE_NAME } from '../../store';

const NavigationContainer = ( { menuItems } ) => {
	useEffect( () => {
		// Collapse the original WP Menu.
		const adminMenu = document.getElementById( 'adminmenumain' );
		adminMenu.classList.add( 'folded' );
	}, [] );

	const dashboardUrl =
		window.wcNavigation && window.wcNavigation.dashboardUrl;

	const renderMenu = ( items, level, rootTitle = null ) => {
		if ( ! items.length ) {
			return null;
		}

		const title = level.id === 'root' ? rootTitle : level.title;
		return (
			<NavigationMenu title={ title }>
				{ items.map( ( item ) => {
					return (
						<NavigationMenuItem
							{ ...item }
							href={ item.url }
							key={ item.id }
						/>
					);
				} ) }
			</NavigationMenu>
		);
	};

	return (
		<div className="woocommerce-navigation">
			<Navigation
				activeItemId={ 'active' }
				data={ menuItems }
				rootTitle="Home"
			>
				{ ( { level, parentLevel, NavigationBackButton } ) => {
					const primaryMenuItems = level.children.filter(
						( item ) => item.menuId !== 'secondary'
					);
					const secondaryMenuItems = level.children.filter(
						( item ) => item.menuId === 'secondary'
					);
					return (
						<>
							{ ! parentLevel && dashboardUrl && (
								<Button
									isTertiary
									href={ dashboardUrl }
									className="woocommerce-navigation__back-button"
								>
									<Icon icon={ chevronLeft } />
									{ __(
										'WordPress Dashboard',
										'woocommerce-navigation'
									) }
								</Button>
							) }
							{ parentLevel && (
								<NavigationBackButton>
									{ parentLevel.title }
								</NavigationBackButton>
							) }
							{ renderMenu(
								primaryMenuItems,
								level,
								__( 'WooCommerce', 'woocommerce-navigation' )
							) }
							{ renderMenu( secondaryMenuItems, level ) }
						</>
					);
				} }
			</Navigation>
		</div>
	);
};

export default compose(
	withSelect( ( select ) => {
		const { getActiveItem, getMenuItems } = select( NAVIGATION_STORE_NAME );

		return {
			activeItem: getActiveItem(),
			menuItems: getMenuItems(),
		};
	} )
)( NavigationContainer );
