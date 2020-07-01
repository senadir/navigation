<?php
/**
 * WooCommerce Navigation: Feature plugin main class.
 *
 * @package WooCommerce Navigation
 */

namespace Automattic\WooCommerce\Navigation;

defined( 'ABSPATH' ) || exit;

use \Automattic\WooCommerce\Admin\FeaturePlugin as WCAdminFeaturePlugin;

/**
 * Feature plugin main class.
 *
 * @internal This file will not be bundled with woo core, only the feature plugin.
 */
class FeaturePlugin {
    /**
	 * The single instance of the class.
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Constructor
	 *
	 * @return void
	 */
	protected function __construct() {}

	/**
	 * Get class instance.
	 *
	 * @return object Instance.
	 */
	final public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}
		return static::$instance;
    }
    
    /**
	 * Init the feature plugin, only if we can detect both Gutenberg and WooCommerce.
	 */
	public function init() {
        $this->define( 'WC_NAVIGATION_ABSPATH', dirname( __DIR__ ) . '/' );
        $this->define( 'WC_NAVIGATION_PLUGIN_FILE', WC_NAVIGATION_ABSPATH . 'woocommerce-admin.php' );
        // WARNING: Do not directly edit this version number constant.
		// It is updated as part of the prebuild process from the package.json value.
		$this->define( 'WC_NAVIGATION_VERSION_NUMBER', '0.1.0' );

		if ( did_action( 'plugins_loaded' ) ) {
			self::on_plugins_loaded();
		} else {
			// Make sure we hook into `plugins_loaded` before core's Automattic\WooCommerce\Package::init().
			// If core is network activated but we aren't, the packaged version of WooCommerce Admin will
			// attempt to use a data store that hasn't been loaded yet - because we've defined our constants here.
			// See: https://github.com/woocommerce/woocommerce-admin/issues/3869.
			add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ), 9 );
		}
    }
        
    /**
	 * Setup plugin once all other plugins are loaded.
	 *
	 * @return void
	 */
	public function on_plugins_loaded() {
		if ( ! WCAdminFeaturePlugin::instance()->has_satisfied_dependencies() ) {
			add_action( 'admin_init', array( $this, 'deactivate_self' ) );
			add_action( 'admin_notices', array( $this, 'render_dependencies_notice' ) );
            
            return;
		}

		new Loader();
    }

    /**
	 * Notify users of the plugin requirements.
	 */
	public function render_dependencies_notice() {
        WCAdminFeaturePlugin::instance()->render_dependencies_notice();
	}

    /**
	 * Deactivates this plugin.
	 */
	public function deactivate_self() {
		deactivate_plugins( plugin_basename( WC_NAVIGATION_PLUGIN_FILE ) );
		unset( $_GET['activate'] ); // phpcs:ignore CSRF ok.
	}
    
    /**
	 * Define constant if not already set.
	 *
	 * @param string      $name  Constant name.
	 * @param string|bool $value Constant value.
	 */
	protected function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}
}