<?php
/**
 * WooCommerce Navigation Unit Tests Bootstrap
 *
 * @package WooCommerce Navigation Tests
 */

/**
 * Class Navigation_Unit_Tests_Bootstrap
 */
class Navigation_Unit_Tests_Bootstrap {

	/** @var Navigation_Unit_Tests_Bootstrap instance */
	protected static $instance = null;

	/** @var string directory where wordpress-tests-lib is installed */
	public $wp_tests_dir;

	/** @var string testing directory */
	public $tests_dir;

	/** @var string plugin directory */
	public $plugin_dir;

	/** @var string WC core directory */
	public $wc_core_dir;

	/**
	 * Setup the unit testing environment.
	 */
	public function __construct() {
		ini_set( 'display_errors', 'on' ); // phpcs:ignore WordPress.PHP.IniSet.display_errors_Blacklisted
		error_reporting( E_ALL ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.prevent_path_disclosure_error_reporting, WordPress.PHP.DiscouragedPHPFunctions.runtime_configuration_error_reporting

		// Ensure server variable is set for WP email functions.
		// phpcs:disable WordPress.VIP.SuperGlobalInputUsage.AccessDetected
		if ( ! isset( $_SERVER['SERVER_NAME'] ) ) {
			$_SERVER['SERVER_NAME'] = 'localhost';
		}
		// phpcs:enable WordPress.VIP.SuperGlobalInputUsage.AccessDetected

		$this->tests_dir    = dirname( __FILE__ );
		$this->plugin_dir   = dirname( $this->tests_dir );
		$this->wc_core_dir  = dirname( $this->plugin_dir );
		$this->wp_tests_dir = getenv( 'WP_TESTS_DIR' ) ? getenv( 'WP_TESTS_DIR' ) : rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';

		$wc_tests_framework_base_dir = $this->wc_core_dir . '/tests';
		if ( ! is_dir( $wc_tests_framework_base_dir . '/framework' ) ) {
			$wc_tests_framework_base_dir .= '/legacy';
		}
		$this->wc_core_tests_dir = $wc_tests_framework_base_dir;

		// load test function so tests_add_filter() is available.
		require_once $this->wp_tests_dir . '/includes/functions.php';

		// load WC.
		tests_add_filter( 'muplugins_loaded', array( $this, 'load_wc' ) );

		// install WC.
		tests_add_filter( 'setup_theme', array( $this, 'install_wc' ) );

		// load the WP testing environment.
		require_once $this->wp_tests_dir . '/includes/bootstrap.php';

		// load WC testing framework.
		$this->includes();
	}

	/**
	 * Load WooCommerce Navigation.
	 */
	public function load_wc() {
		echo $this->wc_core_dir;
		require_once $this->wc_core_dir . '/woocommerce.php';
		require $this->plugin_dir . '/vendor/autoload.php';
		require $this->plugin_dir . '/navigation.php';
	}

	/**
	 * Install WooCommerce after the test environment and WC have been loaded.
	 */
	public function install_wc() {
		// Clean existing install first.
		define( 'WP_UNINSTALL_PLUGIN', true );
		define( 'WC_REMOVE_ALL_DATA', true );

		WC_Install::install();

		// Reload capabilities after install, see https://core.trac.wordpress.org/ticket/28374.
		if ( version_compare( $GLOBALS['wp_version'], '4.7', '<' ) ) {
			$GLOBALS['wp_roles']->reinit();
		} else {
			$GLOBALS['wp_roles'] = null; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			wp_roles();
		}

		echo esc_html( 'Installing WooCommerce and Navigation...' . PHP_EOL );
	}

	/**
	 * Load WC-specific test cases and factories.
	 */
	public function includes() {
		// WooCommerce test classes.
		$wc_tests_framework_base_dir = $this->wc_core_tests_dir;

		// Framework.
		require_once $wc_tests_framework_base_dir . '/framework/class-wc-unit-test-factory.php';
		require_once $wc_tests_framework_base_dir . '/framework/class-wc-mock-session-handler.php';
		require_once $wc_tests_framework_base_dir . '/framework/class-wc-mock-wc-data.php';
		require_once $wc_tests_framework_base_dir . '/framework/class-wc-mock-wc-object-query.php';
		require_once $wc_tests_framework_base_dir . '/framework/vendor/class-wp-test-spy-rest-server.php';

		// Test cases.
		require_once $wc_tests_framework_base_dir . '/includes/wp-http-testcase.php';
		require_once $wc_tests_framework_base_dir . '/framework/class-wc-unit-test-case.php';
		require_once $wc_tests_framework_base_dir . '/framework/class-wc-api-unit-test-case.php';
		require_once $wc_tests_framework_base_dir . '/framework/class-wc-rest-unit-test-case.php';

		// Helpers.
		require_once $wc_tests_framework_base_dir . '/framework/helpers/class-wc-helper-settings.php';
	}

	/**
	 * Get the single class instance.
	 * @return Navigation_Unit_Tests_Bootstrap
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

Navigation_Unit_Tests_Bootstrap::instance();
