/**
 * External dependencies
 */
import { compose } from '@wordpress/compose';
import { Icon, wordpress } from '@wordpress/icons';
import { withSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import { NAVIGATION_STORE_NAME } from '../../store';

const NavigationContainer = ( { title, url } ) => {
	return (
		<a href={ url } className="woocommerce-navigation-site-title">
			<Icon icon={ wordpress } size="36" />
			<span className="woocommerce-navigation-site-title__text" as="span">
				{ title }
			</span>
		</a>
	);
};

export default compose(
	withSelect( ( select ) => {
		const { getSiteTitle, getSiteUrl } = select( NAVIGATION_STORE_NAME );

		return {
			title: getSiteTitle(),
			url: getSiteUrl(),
		};
	} )
)( NavigationContainer );
