<?php
/**
 * WooCommerce Navigation Core Menu
 *
 * @package Woocommerce Admin
 */

namespace Automattic\WooCommerce\Navigation;

use Automattic\WooCommerce\Navigation\Menu;


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
				'settings',
				$setting,
				'manage_woocommerce',
				$key,
				'admin.php?page=wc-status&tab=' . $key
			);
		}
	}

	/**
	 * Add the core menu items to the new navigation
	 */
	public function add_core_items() {
		// Orders category.
		Menu::add_category(
			__( 'Orders', 'woocommerce-navigation' ),
			'edit_shop_orders',
			'orders',
			'edit.php?post_type=shop_order'
		);

		// Products category.
		Menu::add_category(
			__( 'Products', 'woocommerce-navigation' ),
			'edit_products',
			'products',
			'edit.php?post_type=product'
		);

		// Extensions category.
		Menu::add_category(
			__( 'Extensions', 'woocommerce-navigation' ),
			'activate_plugins',
			'extensions',
			'plugins.php',
			null,
			null,
			false
		);
		Menu::add_item(
			'extensions',
			__( 'My extensions', 'woocommerce-navigation' ),
			'manage_woocommerce',
			'my-extensions',
			'plugins.php',
			null,
			null,
			false
		);
		Menu::add_item(
			'extensions',
			__( 'Marketplace', 'woocommerce-navigation' ),
			'manage_woocommerce',
			'marketplace',
			'wc-addons'
		);

		// Settings category.
		Menu::add_category(
			__( 'Settings', 'woocommerce-navigation' ),
			'manage_woocommerce',
			'settings',
			'wc-settings'
		);

		// Tools category.
		Menu::add_category(
			__( 'Tools', 'woocommerce-navigation' ),
			'manage_woocommerce',
			'tools',
			'wc-status'
		);
		Menu::add_item(
			'tools',
			__( 'System status', 'woocommerce-navigation' ),
			'manage_woocommerce',
			'system-status',
			'wc-status'
		);
		Menu::add_item(
			'tools',
			__( 'Import / Export', 'woocommerce-navigation' ),
			'import',
			'import-export',
			'import.php',
			null,
			null,
			false
		);
		Menu::add_item(
			'tools',
			__( 'Utilities', 'woocommerce-navigation' ),
			'manage_woocommerce',
			'utilities',
			'admin.php?page=wc-status&tab=tools'
		);

		// User profile.
		Menu::add_category(
			wp_get_current_user()->user_login,
			'read',
			'profile',
			'profile.php',
			null,
			null,
			false
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
				'settings',
				$menu_item[0],
				$menu_item[1],
				sanitize_title( $menu_item[0] ),
				$menu_item[2]
			);
		}

		return $menu;
	}
}
