<?php
/**
 * Plugin Name: WooCommerce Navigation
 *
 * @package WC_Admin
 */

/**
 * Register the JS.
 */
function add_extension_register_script() {

	if ( ! is_admin() ) {
		return;
	}

	$script_path       = '/build/index.js';
	$script_asset_path = dirname( __FILE__ ) . '/build/index.asset.php';
	$script_asset      = file_exists( $script_asset_path )
		? require( $script_asset_path )
		: array( 'dependencies' => array(), 'version' => filemtime( $script_path ) );
	$script_url = plugins_url( $script_path, __FILE__ );

	wp_register_script(
		'woocommerce-navigation',
		$script_url,
		$script_asset['dependencies'],
		$script_asset['version'],
		true
    );
    
	wp_register_style(
		'woocommerce-navigation',
		plugins_url( '/build/index.css', __FILE__ ),
		array(),
		filemtime( dirname( __FILE__ ) . '/build/index.css' )
	);

	wp_enqueue_script( 'woocommerce-navigation' );
	wp_enqueue_style( 'woocommerce-navigation' );
}

add_action( 'admin_enqueue_scripts', 'add_extension_register_script' );
