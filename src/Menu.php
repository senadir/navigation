<?php
/**
 * WooCommerce Navigation Menu
 *
 * @package Woocommerce Navigation
 */

namespace Automattic\WooCommerce\Navigation;

use Automattic\WooCommerce\Navigation\Screen;

/**
 * Contains logic for the WooCommerce Navigation menu.
 */
class Menu {
	/**
	 * Class instance.
	 *
	 * @var Menu instance
	 */
	protected static $instance = null;

	/**
	 * Array index of menu capability.
	 *
	 * @var int
	 */
	const CAPABILITY = 1;

	/**
	 * Array index of menu callback.
	 *
	 * @var int
	 */
	const CALLBACK = 2;

	/**
	 * Array index of menu callback.
	 *
	 * @var int
	 */
	const SLUG = 3;

	/**
	 * Array index of menu CSS class string.
	 *
	 * @var int
	 */
	const CSS_CLASSES = 4;

	/**
	 * Store top level categories.
	 *
	 * @var array
	 */
	protected static $categories = array();

	/**
	 * Store related menu items.
	 *
	 * @var array
	 */
	protected static $menu_items = array();

	/**
	 * Registered callbacks or URLs with migration boolean as key value pairs.
	 *
	 * @var array
	 */
	protected static $callbacks = array();

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
		add_filter( 'admin_enqueue_scripts', array( $this, 'enqueue_data' ), 20 );
		add_filter( 'add_menu_classes', array( $this, 'migrate_menu_items' ), 30 );
	}

	/**
	 * Convert a WordPress menu callback to a URL.
	 *
	 * @param string $callback Menu callback.
	 * @return string
	 */
	public static function get_callback_url( $callback ) {
		$pos  = strpos( $callback, '?' );
		$file = $pos > 0 ? substr( $callback, 0, $pos ) : $callback;
		if ( file_exists( ABSPATH . "/wp-admin/$file" ) ) {
			return $callback;
		}
		return 'admin.php?page=' . $callback;
	}

	/**
	 * Get the parent key if one exists.
	 *
	 * @param string $callback Callback or URL.
	 * @return string|null
	 */
	public static function get_parent_key( $callback ) {
		global $submenu;

		// This is already a parent item.
		if ( isset( $submenu[ $callback ] ) ) {
			return null;
		}

		foreach ( $submenu as $key => $menu ) {
			foreach ( $menu as $item ) {
				if ( $item[ self::CALLBACK ] === $callback ) {
					return $key;
				}
			}
		}

		return null;
	}

	/**
	 * Adds a top level menu item to the navigation.
	 *
	 * @param string $title Menu title.
	 * @param string $capability WordPress capability.
	 * @param string $slug Menu slug.
	 * @param string $url URL or menu callback.
	 * @param string $icon Menu icon.
	 * @param int    $order Menu order.
	 * @param bool   $migrate Migrate the menu option and hide the old one.
	 */
	public static function add_category( $title, $capability, $slug, $url = null, $icon = null, $order = null, $migrate = true ) {
		self::$categories[] = array(
			'title'      => $title,
			'capability' => $capability,
			'slug'       => $slug,
			'url'        => self::get_callback_url( $url ),
			'icon'       => $icon,
			'order'      => $order,
			'migrate'    => $migrate,
		);

		self::$callbacks[ $url ] = $migrate;
	}

	/**
	 * Adds a child menu item to the navigation.
	 *
	 * @param string $parent_slug Parent item slug.
	 * @param string $title Menu title.
	 * @param string $capability WordPress capability.
	 * @param string $slug Menu slug.
	 * @param string $url URL or menu callback.
	 * @param string $icon Menu icon.
	 * @param int    $order Menu order.
	 * @param bool   $migrate Migrate the menu option and hide the old one.
	 */
	public static function add_item( $parent_slug, $title, $capability, $slug, $url = null, $icon = null, $order = null, $migrate = true ) {
		self::$menu_items[ $parent_slug ][] = array(
			'title'      => $title,
			'capability' => $capability,
			'slug'       => $slug,
			'url'        => self::get_callback_url( $url ),
			'icon'       => $icon,
			'order'      => $order,
			'migrate'    => $migrate,
		);

		self::$callbacks[ $url ] = $migrate;
	}

	/**
	 * Adds a post type as a menu category.
	 *
	 * @param string $post_type Post type.
	 */
	public static function add_post_type_category( $post_type ) {
		$post_type_object = get_post_type_object( $post_type );

		if ( ! $post_type_object ) {
			return;
		}

		self::add_category(
			esc_attr( $post_type_object->labels->menu_name ),
			$post_type_object->cap->edit_posts,
			$post_type,
			"edit.php?post_type={$post_type}"
		);
		self::add_item(
			$post_type,
			esc_attr( $post_type_object->labels->all_items ),
			$post_type_object->cap->edit_posts,
			"{$post_type}-all-items",
			"edit.php?post_type={$post_type}"
		);
		self::add_item(
			$post_type,
			esc_attr( $post_type_object->labels->add_new ),
			$post_type_object->cap->create_posts,
			"{$post_type}-add-new",
			"post-new.php?post_type={$post_type}"
		);
	}

	/**
	 * Hides all WP admin menus items and adds screen IDs to check for new items.
	 *
	 * @param array $menu Menu items.
	 * @return array
	 */
	public static function migrate_menu_items( $menu ) {
		global $submenu;

		foreach ( $menu as $key => $menu_item ) {
			if (
				isset( self::$callbacks[ $menu_item[ self::CALLBACK ] ] ) &&
				self::$callbacks[ $menu_item[ self::CALLBACK ] ]
			) {
				$menu[ $key ][ self::CSS_CLASSES ] .= ' hide-if-js';
			}
		}

		foreach ( $submenu as $parent_key => $parent ) {
			foreach ( $parent as $key => $menu_item ) {
				if (
					isset( self::$callbacks[ $menu_item[ self::CALLBACK ] ] ) &&
					self::$callbacks[ $menu_item[ self::CALLBACK ] ]
				) {
					// Disable phpcs since we need to override submenu classes.
					// Note that `phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited` does not work to disable this check.
					// phpcs:disable
					if ( ! isset( $menu_item[ self::SLUG ] ) ) {
						$submenu[ $parent_key ][ $key ][] = '';
					}
					if ( ! isset( $menu_item[ self::CSS_CLASSES ] ) ) {
						$submenu[ $parent_key ][ $key ][] .= ' hide-if-js';
					} else {
						$submenu[ $parent_key ][ $key ][ self::CSS_CLASSES ] .= ' hide-if-js';
					}
					// phps:enable
				}
			}
		}

		foreach ( array_keys( self::$callbacks ) as $callback ) {
			Screen::add_screen( $callback );
		}

		return $menu;
	}

	/**
	 * Add the menu to the page output.
	 *
	 * @param array $menu Menu items.
	 * @return array
	 */
	public function enqueue_data( $menu ) {
		global $submenu, $parent_file, $typenow, $self;

		$categories = self::$categories;
		foreach ( $categories as $index => $category ) {
			if ( $category[ 'capability' ] && ! current_user_can( $category[ 'capability' ] ) ) {
				unset( $categories[ $index ] );
				continue;
			}

			$categories[ $index ]['children'] = array();
			if( isset( self::$menu_items[ $category['slug'] ] ) ) {
				foreach ( self::$menu_items[ $category['slug'] ] as $item ) {
					if ( $item[ 'capability' ] && ! current_user_can( $item[ 'capability' ] ) ) {
						continue;
					}

					$categories[ $index ]['children'][] = $item;
				}
			}
		}

		wp_add_inline_script( 'woocommerce-navigation', 'window.wcNavigation = ' . wp_json_encode( $categories ), 'before' );

		return $menu;
	}
}
