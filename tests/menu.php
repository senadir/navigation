<?php
/**
 * Menu tests for registering categories and items.
 *
 * @package Woocommerce Navigation
 */

use Automattic\WooCommerce\Navigation\Menu;

/**
 * WC_Tests_Navigation_Menu
 */
class WC_Tests_Navigation_Menu extends WC_REST_Unit_Test_Case {
	/**
	 * Test that menu items can be added.
	 */
	public function test_add_menu_items() {
		Menu::add_category(
			array(
				'title'      => 'Test Category',
				'capability' => 'manage_woocommerce',
				'id'         => 'test-category',
			)
		);

		Menu::add_item(
			array(
				'parent'     => 'test-category',
				'title'      => 'Test Item',
				'capability' => 'manage_woocommerce',
				'id'         => 'test-item',
			)
		);

		$menu_items = Menu::instance()::get_items();
		$this->assertEquals( 2, count( $menu_items ) );
		$this->assertCount( 7, $menu_items['test-item'] );
		$this->assertArrayNotHasKey( 'parent', $menu_items['test-category'] );
		$this->assertArrayHasKey( 'parent', $menu_items['test-item'] );
		$this->assertArrayHasKey( 'title', $menu_items['test-item'] );
		$this->assertArrayHasKey( 'capability', $menu_items['test-item'] );
		$this->assertArrayHasKey( 'id', $menu_items['test-item'] );
		$this->assertArrayHasKey( 'url', $menu_items['test-item'] );
		$this->assertArrayHasKey( 'order', $menu_items['test-item'] );
		$this->assertArrayHasKey( 'migrate', $menu_items['test-item'] );
	}

	/**
	 * Test adding a menu item that has already been added.
	 */
	public function test_duplicate_menu_id() {
		Menu::add_item(
			array(
				'parent'     => 'test-category',
				'title'      => 'Test Item',
				'capability' => 'manage_woocommerce',
				'id'         => 'test-item',
			)
		);

		$this->assertEquals( 2, count( Menu::instance()::get_items() ) );
	}

	/**
	 * Test that the callback is converted to a URL.
	 */
	public function test_callback_url() {
		Menu::add_category(
			array(
				'title'      => 'Test Page',
				'capability' => 'manage_woocommerce',
				'id'         => 'test-plugins',
				'url'        => 'wc-test',
			)
		);

		$url = Menu::instance()::get_items()['test-plugins']['url'];
		$this->assertEquals( 'admin.php?page=wc-test', $url );
	}
}
