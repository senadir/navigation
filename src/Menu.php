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
	 * Store menu items.
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

		if ( ! $submenu ) {
			return null;
		}

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
	 * @param array $args Array containing the necessary arguments.
	 *    $args = array(
	 *      'id'         => (string) The unique ID of the menu item. Required.
	 *      'title'      => (string) Title of the menu item. Required.
	 *      'capability' => (string) Capability to view this menu item.
	 *      'url'        => (string) URL or callback to be used. Required.
	 *      'order'      => (int) Menu item order.
	 *      'migrate'    => (bool) Whether or not to hide the item in the wp admin menu.
	 *      'menuId'     => (string) The ID of the menu to add the category to.
	 *    ).
	 */
	public static function add_category( $args ) {
		if ( ! isset( $args['id'] ) || isset( self::$menu_items[ $args['id'] ] ) ) {
			return;
		}

		$defaults         = array(
			'id'         => '',
			'title'      => '',
			'capability' => 'manage_woocommerce',
			'url'        => '',
			'order'      => 10,
			'migrate'    => true,
			'menuId'     => 'primary',
		);
		$menu_item        = wp_parse_args( $args, $defaults );
		$menu_item['url'] = self::get_callback_url( $menu_item['url'] );

		self::$menu_items[ $menu_item['id'] ] = $menu_item;

		if ( isset( $args['url'] ) ) {
			self::$callbacks[ $args['url'] ] = $menu_item['migrate'];
		}
	}

	/**
	 * Adds a child menu item to the navigation.
	 *
	 * @param array $args Array containing the necessary arguments.
	 *    $args = array(
	 *      'id'         => (string) The unique ID of the menu item. Required.
	 *      'title'      => (string) Title of the menu item. Required.
	 *      'parent'     => (string) Parent menu item ID.
	 *      'capability' => (string) Capability to view this menu item.
	 *      'url'        => (string) URL or callback to be used. Required.
	 *      'order'      => (int) Menu item order.
	 *      'migrate'    => (bool) Whether or not to hide the item in the wp admin menu.
	 *      'menuId'     => (string) The ID of the menu to add the item to.
	 *    ).
	 */
	public static function add_item( $args ) {
		if ( ! isset( $args['id'] ) || isset( self::$menu_items[ $args['id'] ] ) ) {
			return;
		}

		$defaults         = array(
			'id'         => '',
			'title'      => '',
			'parent'     => 'settings',
			'capability' => 'manage_woocommerce',
			'url'        => '',
			'order'      => 10,
			'migrate'    => true,
			'menuId'     => 'primary',
		);
		$menu_item        = wp_parse_args( $args, $defaults );
		$menu_item['url'] = self::get_callback_url( $menu_item['url'] );

		self::$menu_items[ $menu_item['id'] ] = $menu_item;

		if ( isset( $args['url'] ) ) {
			self::$callbacks[ $args['url'] ] = $menu_item['migrate'];
		}
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
			array(
				'title'      => esc_attr( $post_type_object->labels->menu_name ),
				'capability' => $post_type_object->cap->edit_posts,
				'id'         => $post_type,
				'url'        => "edit.php?post_type={$post_type}",
			)
		);
		self::add_item(
			array(
				'parent'     => $post_type,
				'title'      => esc_attr( $post_type_object->labels->all_items ),
				'capability' => $post_type_object->cap->edit_posts,
				'id'         => "{$post_type}-all-items",
				'url'        => "edit.php?post_type={$post_type}",
			)
		);
		self::add_item(
			array(
				'parent'     => $post_type,
				'title'      => esc_attr( $post_type_object->labels->add_new ),
				'capability' => $post_type_object->cap->create_posts,
				'id'         => "{$post_type}-add-new",
				'url'        => "post-new.php?post_type={$post_type}",
			)
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
	 * Get registered menu items.
	 *
	 * @return array
	 */
	public static function get_items() {
		return apply_filters( 'woocommerce_navigation_menu_items', self::$menu_items );
	}

	/**
	 * Add the menu to the page output.
	 *
	 * @param array $menu Menu items.
	 * @return array
	 */
	public function enqueue_data( $menu ) {
		global $submenu, $parent_file, $typenow, $self;

		$menu_items = self::get_items();
		foreach ( $menu_items as $index => $menu_item ) {
			if ( $menu_item[ 'capability' ] && ! current_user_can( $menu_item[ 'capability' ] ) ) {
				unset( $menu_items[ $index ] );
			}
		}

		$data = array(
			'menuItems'    => array_values( $menu_items ),
			'dashboardUrl' => get_dashboard_url(),
		);

		wp_add_inline_script( 'woocommerce-navigation', 'window.wcNavigation = ' . wp_json_encode( $data ), 'before' );

		return $menu;
	}
}
