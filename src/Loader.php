<?php
/**
 * Register the scripts, styles, and includes needed for pieces of the WooCommerce Navigation.
 * NOTE: DO NOT edit this file in WooCommerce core, this is generated from woocommerce-admin.
 *
 * @package Woocommerce Admin
 */

namespace Automattic\WooCommerce\Navigation;

/**
 * Loader Class.
 */
class Loader {

	/**
	 * Class instance.
	 *
	 * @var Loader instance
	 */
	protected static $instance = null;

	/**
	 * Get class instance.
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 * Hooks added here should be removed in `wc_admin_initialize` via the feature plugin.
	 */
	public function __construct() {
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'register_navigation_script' ) );
    }

    /**
	 * Gets the file modified time as a cache buster if we're in dev mode, or the plugin version otherwise.
	 *
	 * @param string $ext File extension.
	 * @return string The cache buster value to use for the given file.
	 */
	public static function get_file_version( $path ) {
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
            return filemtime( WC_NAVIGATION_ABSPATH . $path );
		}
		return WC_NAVIGATION_VERSION_NUMBER;
	}
    
    /**
     * Register the JS.
     */
    public static function register_navigation_script() {

        if ( ! is_admin() ) {
            return;
        }

        $script_path       = '/build/index.js';
        $script_asset_path = WC_NAVIGATION_ABSPATH . '/build/index.asset.php';
        $script_asset      = file_exists( $script_asset_path )
            ? require( $script_asset_path )
            : array( 'dependencies' => array(), 'version' => self::get_file_version( $script_path ) );
        $script_url = plugins_url( $script_path, WC_NAVIGATION_PLUGIN_FILE );

        wp_register_script(
            'woocommerce-navigation',
            $script_url,
            $script_asset['dependencies'],
            $script_asset['version'],
            true
        );
        
        wp_register_style(
            'woocommerce-navigation',
            plugins_url( '/build/index.css', WC_NAVIGATION_PLUGIN_FILE ),
            array(),
            self::get_file_version( '/build/index.css' )
        );

        wp_enqueue_script( 'woocommerce-navigation' );
        wp_enqueue_style( 'woocommerce-navigation' );
    }
}
