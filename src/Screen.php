<?php
/**
 * WooCommerce Navigation Screen
 *
 * @package Woocommerce Navigation
 */

namespace Automattic\WooCommerce\Navigation;

use Automattic\WooCommerce\Navigation\Menu;

/**
 * Contains logic for the WooCommerce Navigation menu.
 */
class Screen {
	/**
	 * Class instance.
	 *
	 * @var Menu instance
	 */
	protected static $instance = null;

	/**
	 * Screen IDs of registered pages.
	 *
	 * @var array
	 */
	protected static $screen_ids = array();

	/**
	 * Registered post types.
	 *
	 * @var array
	 */
	protected static $post_types = array();

	/**
	 * Get class instance.
	 */
	final public static function instance() {
		if ( ! static::$instance ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Init.
	 */
	public function init() {
		add_filter( 'admin_body_class', array( $this, 'add_body_class' ) );
	}

	/**
	 * Check if we're on a WooCommerce page
	 *
	 * @return bool
	 */
	public static function is_woocommerce_page() {
		global $pagenow, $plugin_page;

		// Get post type if on a post screen.
		$post_type = '';
		if ( in_array( $pagenow, array( 'edit.php', 'post.php', 'post-new.php' ), true ) ) {
			if ( isset( $_GET['post'] ) ) { // phpcs:ignore CSRF ok.
				$post_type = get_post_type( (int) $_GET['post'] ); // phpcs:ignore CSRF ok.
			} elseif ( isset( $_GET['post_type'] ) ) { // phpcs:ignore CSRF ok.
				$post_type = sanitize_text_field( wp_unslash( $_GET['post_type'] ) ); // phpcs:ignore CSRF ok.
			}
		}
		$post_types = apply_filters( 'woocommerce_navigation_post_types', self::$post_types );

		// Get current screen ID.
		$current_screen = get_current_screen();
		$screen_ids     = apply_filters( 'woocommerce_navigation_screen_ids', self::$screen_ids );

		if (
			in_array( $post_type, $post_types, true ) ||
			in_array( $current_screen->id, self::$screen_ids, true )
		) {
			return true;
		}

		return false;
	}

	/**
	 * Add navigation classes to body.
	 *
	 * @param string $classes Classes.
	 * @return string
	 */
	public function add_body_class( $classes ) {
		if ( self::is_woocommerce_page() ) {
			$classes .= ' has-woocommerce-navigation';
		}

		return $classes;
	}

	/**
	 * Adds a screen ID to the list of screens that use the navigtion.
	 * Finds the parent if none is given to grab the correct screen ID.
	 *
	 * @param string      $callback Callback or URL for page.
	 * @param string|null $parent   Parent slug.
	 */
	public static function add_screen( $callback, $parent = null ) {
		global $submenu;

		if ( ! $parent ) {
			$parent = Menu::get_parent_key( $callback );
		}
		self::$screen_ids[] = get_plugin_page_hookname( $callback, $parent );
	}
}
