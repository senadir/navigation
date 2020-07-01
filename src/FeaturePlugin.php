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
            // todo: properly deactivate
			// add_action( 'admin_init', array( $this, 'deactivate_self' ) );
			// add_action( 'admin_notices', array( $this, 'render_dependencies_notice' ) );
			return;
		}

		if ( ! $this->check_build() ) {
            // todo: properly render notice
			// add_action( 'admin_notices', array( $this, 'render_build_notice' ) );
		}

		new Loader();
    }

    /**
	 * Returns true if build file exists.
	 *
	 * @return bool
	 */
	protected function check_build() {
		return file_exists( plugin_dir_path( __DIR__ ) . '/build/index.js' );
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