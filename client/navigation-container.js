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
import { Icon, arrowLeft } from '@wordpress/icons';
import { withSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { NAVIGATION_STORE_NAME } from './store';

const NavigationContainer = ( { menuItems } ) => {
	useEffect( () => {
		// Collapse the original WP Menu.
		const adminMenu = document.getElementById( 'adminmenumain' );
		adminMenu.classList.add( 'folded' );
	}, [] );

	const dashboardUrl =
		window.wcNavigation && window.wcNavigation.dashboardUrl;

	return (
		<div className="woocommerce-navigation">
			<Navigation
				activeItemId={ 'active' }
				data={ menuItems }
				rootTitle="Home"
			>
				{ ( { level, parentLevel, NavigationBackButton } ) => {
					return (
						<>
							{ parentLevel && (
								<NavigationBackButton>
									<Icon icon={ arrowLeft } />
									{ parentLevel.title }
								</NavigationBackButton>
							) }
							{ ! parentLevel && dashboardUrl && (
								<Button
									isPrimary
									href={ dashboardUrl }
									className="woocommerce-navigation__back-button"
								>
									<Icon icon={ arrowLeft } />
									{ __(
										'WordPress Dashboard',
										'woocommerce-navigation'
									) }
								</Button>
							) }
							<h1>{ level.title }</h1>
							<NavigationMenu>
								{ level.children
									.filter(
										( item ) => item.menuId !== 'secondary'
									)
									.map( ( item ) => {
										return (
											<NavigationMenuItem
												{ ...item }
												key={ item.id }
											/>
										);
									} ) }
							</NavigationMenu>
							<NavigationMenu>
								{ level.children
									.filter(
										( item ) => item.menuId === 'secondary'
									)
									.map( ( item ) => {
										return (
											<NavigationMenuItem
												{ ...item }
												key={ item.id }
											/>
										);
									} ) }
							</NavigationMenu>
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
