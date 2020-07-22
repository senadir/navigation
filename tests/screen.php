<?php
/**
 * Screen tests for adding registered screen IDs for the navigation.
 *
 * @package Woocommerce Navigation
 */

use Automattic\WooCommerce\Navigation\Menu;
use Automattic\WooCommerce\Navigation\Screen;

/**
 * WC_Tests_Navigation_Screen
 */
class WC_Tests_Navigation_Screen extends WC_REST_Unit_Test_Case {
	/**
	 * Test that screens can be added.
	 */
	public function test_add_screen() {
		Screen::add_screen( 'wc-test' );
		Screen::add_screen( 'wc-page' );

		$screen_ids = Screen::instance()::get_screen_ids();
		$this->assertEquals( 2, count( $screen_ids ) );
		$this->assertEquals( 'admin_page_wc-test', $screen_ids[0] );
		$this->assertEquals( 'admin_page_wc-page', $screen_ids[1] );
	}

	/**
	 * Test that adding an already registered page does not get added again.
	 */
	public function test_add_duplicate_screen() {
		Screen::add_screen( 'wc-page' );
		$screen_ids = Screen::instance()::get_screen_ids();
		$this->assertEquals( 2, count( $screen_ids ) );
	}

	/**
	 * Test that post types can be registered.
	 */
	public function test_register_post_types() {
		register_post_type(
			'custom-post-type',
			array(
				'show_in_menu' => true,
				'show_ui'      => true,
			)
		);
		Screen::register_post_type( 'custom-post-type', 'test-category' );
		$this->assertContains( 'custom-post-type', Screen::get_post_types() );
		$this->assertArrayHasKey( 'custom-post-type', Menu::instance()::get_items() );
	}

	/**
	 * Test the check for detecting if we're on a WooCommerce page.
	 */
	public function test_is_woocommerce_page() {
		Screen::add_screen( 'wc-page' );
		Screen::register_post_type( 'custom-post-type', 'test-category' );

		// Mimic the global variables to detect pages.
		// phpcs:disable WordPress.WP.GlobalVariablesOverride.OverrideProhibited
		global $pagenow, $current_screen;
		$_pagenow        = $pagenow;
		$_current_screen = $current_screen;
		$_post_type      = isset( $_GET['post_type'] ) ? sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) : null; // phpcs:ignore csrf ok.
		$_post_id        = isset( $_GET['post'] ) ? sanitize_text_field( wp_unslash( $_GET['post'] ) ) : null; // phpcs:ignore csrf ok.

		// Create test posts.
		$post_id             = wp_insert_post(
			array(
				'post_title' => 'Test post',
				'post_type'  => 'post',
			)
		);
		$custom_post_type_id = wp_insert_post(
			array(
				'post_title' => 'Test post',
				'post_type'  => 'custom-post-type',
			)
		);

		// phpcs:disable WordPress.WP.GlobalVariablesOverride.Prohibited
		// Registered post types.
		$pagenow           = 'edit.php';
		$_GET['post_type'] = null;
		$this->assertEquals( false, Screen::is_woocommerce_page() );
		$pagenow      = 'post.php';
		$_GET['post'] = $post_id;
		$this->assertEquals( false, Screen::is_woocommerce_page() );
		$pagenow           = 'edit.php';
		$_GET['post_type'] = 'custom-post-type';
		$_GET['post']      = null;
		$this->assertEquals( true, Screen::is_woocommerce_page() );
		$pagenow      = 'post.php';
		$_GET['post'] = $custom_post_type_id;
		$this->assertEquals( true, Screen::is_woocommerce_page() );

		// Reset globals to original values.
		$pagenow           = $_pagenow;
		$_GET['post_type'] = $_post_type;
		$_GET['post_id']   = $_post_id;

		// Registered pages.
		$current_screen = (object) array(
			'id'       => 'admin_page_non-wc-page',
			'in_admin' => true,
		);
		$this->assertEquals( false, Screen::is_woocommerce_page() );
		$current_screen = (object) array(
			'id'       => 'admin_page_wc-page',
			'in_admin' => true,
		);
		$this->assertEquals( true, Screen::is_woocommerce_page() );

		// Reset globals to original values.
		$current_screen = $_current_screen;
		// phpcs:enable WordPress.WP.GlobalVariablesOverride.Prohibited
	}
}
