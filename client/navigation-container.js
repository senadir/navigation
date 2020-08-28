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
	__experimentalText as Text,
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

	const renderMenu = ( items ) => {
		if ( ! items.length ) {
			return null;
		}

		return (
			<NavigationMenu>
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
							{ parentLevel && (
								<NavigationBackButton className="woocommerce-navigation__back-button">
									<Icon icon={ arrowLeft } />
									{ parentLevel.title }
								</NavigationBackButton>
							) }
							<Text
								variant="title.small"
								as="h1"
								className="woocommerce-navigation__title"
							>
								{ level.title }
							</Text>
							{ renderMenu( primaryMenuItems ) }
							{ renderMenu( secondaryMenuItems ) }
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
