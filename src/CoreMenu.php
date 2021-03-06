<?php
/**
 * WooCommerce Navigation Core Menu
 *
 * @package Woocommerce Admin
 */

namespace Automattic\WooCommerce\Navigation;

use Automattic\WooCommerce\Navigation\Menu;
use Automattic\WooCommerce\Navigation\Screen;


/**
 * CoreMenu class. Handles registering Core menu items.
 */
class CoreMenu {
	/**
	 * Class instance.
	 *
	 * @var Menu instance
	 */
	protected static $instance = null;

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
		add_action( 'admin_menu', array( $this, 'add_core_items' ) );
		add_action( 'admin_menu', array( $this, 'add_core_setting_items' ) );
		add_filter( 'add_menu_classes', array( $this, 'migrate_child_items' ) );
	}

	/**
	 * Add registered admin settings as menu items.
	 */
	public function add_core_setting_items() {
		$setting_pages = \WC_Admin_Settings::get_settings_pages();
		$settings      = array();
		foreach ( $setting_pages as $setting_page ) {
			$settings = $setting_page->add_settings_page( $settings );
		}
		foreach ( $settings as $key => $setting ) {
			Menu::add_item(
				array(
					'parent'     => 'settings',
					'title'      => $setting,
					'capability' => 'manage_woocommerce',
					'id'         => $key,
					'url'        => 'admin.php?page=wc-settings&tab=' . $key,
				)
			);
		}
	}

	/**
	 * Add the core menu items to the new navigation
	 */
	public function add_core_items() {
		// Orders category.
		Screen::register_post_type( 'shop_order', null );
		Menu::add_post_type_category( 'shop_order' );

		// Products category.
		Screen::register_post_type( 'product', null );
		Menu::add_post_type_category( 'product' );

		// Marketing category.
		Menu::add_category(
			array(
				'title'      => __( 'Marketing', 'woocommerce-navigation' ),
				'capability' => 'manage_woocommerce',
				'id'         => 'woocommerce-marketing',
			)
		);
		Screen::register_post_type( 'shop_coupon', 'woocommerce-marketing' );

		// Extensions category.
		Menu::add_category(
			array(
				'title'      => __( 'Extensions', 'woocommerce-navigation' ),
				'capability' => 'activate_plugins',
				'id'         => 'extensions',
				'url'        => 'plugins.php',
				'migrate'    => false,
				'menuId'     => 'secondary',
			)
		);
		Menu::add_item(
			array(
				'parent'     => 'extensions',
				'title'      => __( 'My extensions', 'woocommerce-navigation' ),
				'capability' => 'manage_woocommerce',
				'id'         => 'my-extensions',
				'url'        => 'plugins.php',
				'migrate'    => false,
				'menuId'     => 'secondary',
			)
		);
		Menu::add_item(
			array(
				'parent'     => 'extensions',
				'title'      => __( 'Marketplace', 'woocommerce-navigation' ),
				'capability' => 'manage_woocommerce',
				'id'         => 'marketplace',
				'url'        => 'wc-addons',
				'menuId'     => 'secondary',
			)
		);

		// Settings category.
		Menu::add_category(
			array(
				'title'      => __( 'Settings', 'woocommerce-navigation' ),
				'capability' => 'manage_woocommerce',
				'id'         => 'settings',
				'url'        => 'wc-settings',
				'menuId'     => 'secondary',
			)
		);

		// Tools category.
		Menu::add_category(
			array(
				'title'      => __( 'Tools', 'woocommerce-navigation' ),
				'capability' => 'manage_woocommerce',
				'id'         => 'tools',
				'url'        => 'wc-status',
				'menuId'     => 'secondary',
			)
		);
		Menu::add_item(
			array(
				'parent'     => 'tools',
				'title'      => __( 'System status', 'woocommerce-navigation' ),
				'capability' => 'manage_woocommerce',
				'id'         => 'system-status',
				'url'        => 'wc-status',
				'menuId'     => 'secondary',
			)
		);
		Menu::add_item(
			array(
				'parent'     => 'tools',
				'title'      => __( 'Import / Export', 'woocommerce-navigation' ),
				'capability' => 'import',
				'id'         => 'import-export',
				'url'        => 'import.php',
				'migrate'    => false,
				'menuId'     => 'secondary',
			)
		);
		Menu::add_item(
			array(
				'parent'     => 'tools',
				'title'      => __( 'Utilities', 'woocommerce-navigation' ),
				'capability' => 'manage_woocommerce',
				'id'         => 'utilities',
				'url'        => 'admin.php?page=wc-status&tab=tools',
				'menuId'     => 'secondary',
			)
		);

		// User profile.
		// @todo This may fall under a tertiary menu.
		Menu::add_category(
			array(
				'title'      => wp_get_current_user()->user_login,
				'capability' => 'read',
				'id'         => 'profile',
				'url'        => 'profile.php',
				'migrate'    => false,
				'menuId'     => 'secondary',
			)
		);
	}

	/**
	 * Migrate any remaining WooCommerce child items.
	 *
	 * @param array $menu Menu items.
	 * @return array
	 */
	public function migrate_child_items( $menu ) {
		global $submenu;

		if ( ! isset( $submenu['woocommerce'] ) ) {
			return;
		}

		foreach ( $submenu['woocommerce'] as $menu_item ) {
			if ( 'woocommerce' === $menu_item[2] ) {
				continue;
			}

			Menu::add_item(
				array(
					'parent'     => 'settings',
					'title'      => $menu_item[0],
					'capability' => $menu_item[1],
					'id'         => sanitize_title( $menu_item[0] ),
					'url'        => $menu_item[2],
					'menuId'     => 'secondary',
				)
			);
		}

		return $menu;
	}
}
