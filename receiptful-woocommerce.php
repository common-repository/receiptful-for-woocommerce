<?php
/**
 * Plugin Name: 	CM Commerce for WooCommerce
 * Plugin URI: 		https://campaignmonitor.com/products/cm-commerce/
 * Description: 	CM Commerce is the all-in-one marketing dashboard for your WooCommerce store.
 * Author: 			Campaign Monitor
 * Author URI: 		https://campaignmonitor.com
 * Version: 		1.6.7
 * Text Domain: 	conversio-for-woocommerce
 * Domain Path: 	/languages/
 * WC requires at least: 3.4.0
 * WC tested up to: 5.1.0
 *
 * @package		Receiptful-WooCommerce
 * @author		Conversio
 * @copyright	Copyright (c) 2014-2019, CM Commerce
 * @license		http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class CM_Commerce_WooCommerce.
 *
 * Main class initializes the plugin.
 *
 * @class		CM_Commerce_WooCommerce
 * @version		1.0.0
 * @author		Conversio
 */
class CM_Commerce_WooCommerce {


	/**
	 * Plugin version.
	 *
	 * @since 1.0.1
	 * @var string $version Plugin version number.
	 */
	public $version = '1.6.7';


	/**
	 * Plugin file.
	 *
	 * @since 1.0.0
	 * @var string $file Plugin file path.
	 */
	public $file = __FILE__;


	/**
	 * Instance of CM_Commerce_WooCommerce.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var object $instance The instance of CM_Commerce_WooCommerce.
	 */
	protected static $instance;

	/** @var CM_Commerce_Admin */
	public $admin;

	/** @var CM_Commerce_Email */
	public $email;

	/** @var CM_Commerce_Front_End */
	public $front_end;

	/** @var CM_Commerce_Api */
	public $api;

	/** @var CM_Commerce_Products */
	public $products;

	/** @var CM_Commerce_Order */
	public $order;

	/** @var CM_Commerce_Recommendations */
	public $recommendations;

	/** @var CM_Commerce_Feedback */
	public $feedback;

	/** @var CM_Commerce_Widget */
	public $widget;

	/** @var CM_Commerce_Reviews */
	public $reviews;

	/** @var CM_Commerce_Abandoned_Cart */
	public $abandoned_cart;

	/** @var string */
	public $script_url;

	/**
	 * Constructor.
	 *
	 * Initialize the class and plugin.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Check if WooCommerce is active
		require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) && ! function_exists( 'WC' ) ) {
			return false;
		}

		if ( version_compare( get_option( 'woocommerce_version' ), '3', '<' ) ) {
			return add_action( 'admin_notices', array( $this, 'wc_version_required_notice' ) );
		}

		$this->init();

	}


	/**
	 * Instance.
	 *
	 * An global instance of the class. Used to retrieve the instance
	 * to use on other files/plugins/themes.
	 *
	 * @since 1.0.0
	 *
	 * @return CM_Commerce_WooCommerce Instance of the class.
	 */
	public static function instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;

	}


	/**
	 * init.
	 *
	 * Initialize plugin parts.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->includes(); // Load files

		if ( is_admin() ) {
			require_once plugin_dir_path( __FILE__ ) . '/includes/admin/class-cm-commerce-admin.php';
			$this->admin = new CM_Commerce_Admin();
		}

		$this->script_url = getenv( 'CM_COMMERCE_SCRIPT_URL' ) ?: 'https://media.receiptful.com/scripts/cmcommerce.js';

		$this->email           = new CM_Commerce_Email();
		$this->front_end       = new CM_Commerce_Front_End();
		$this->api             = new CM_Commerce_Api();
		$this->products        = new CM_Commerce_Products();
		$this->order           = new CM_Commerce_Order();
		$this->recommendations = new CM_Commerce_Recommendations();
		$this->feedback        = new CM_Commerce_Feedback();
		$this->widget          = new CM_Commerce_Widget();
		$this->reviews         = new CM_Commerce_Reviews();

		if ( 'yes' == get_option( 'receiptful_enable_abandoned_cart' ) ) {
			require_once plugin_dir_path( __FILE__ ) . '/includes/class-cm-commerce-abandoned-cart.php';
			$this->abandoned_cart = new CM_Commerce_Abandoned_Cart();
		}

		// 3rd party compatibility
		require_once plugin_dir_path( __FILE__ ) . '/includes/integrations/woocommerce-subscriptions.php';
		require_once plugin_dir_path( __FILE__ ) . '/includes/integrations/wpml.php';
		require_once plugin_dir_path( __FILE__ ) . '/includes/integrations/sensei.php';


		$this->hooks(); // Plugin hooks
		$this->load_textdomain(); // Textdomain

		do_action( 'receiptful_loaded' );

	}


	/**
	 * Include files.
	 */
	private function includes() {
		require_once plugin_dir_path( __FILE__ ) . '/includes/class-cm-commerce-email.php';
		require_once plugin_dir_path( __FILE__ ) . '/includes/class-cm-commerce-front-end.php';
		require_once plugin_dir_path( __FILE__ ) . '/includes/class-cm-commerce-api.php';
		require_once plugin_dir_path( __FILE__ ) . '/includes/class-cm-commerce-products.php';
		require_once plugin_dir_path( __FILE__ ) . '/includes/class-cm-commerce-order.php';
		require_once plugin_dir_path( __FILE__ ) . '/includes/class-cm-commerce-recommendations.php';
		require_once plugin_dir_path( __FILE__ ) . '/includes/class-cm-commerce-feedback.php';
		require_once plugin_dir_path( __FILE__ ) . '/includes/class-cm-commerce-widget.php';
		require_once plugin_dir_path( __FILE__ ) . '/includes/class-cm-commerce-reviews.php';
	}


	/**
	 * Initial plugin hooks.
	 *
	 * @since 1.1.1
	 */
	public function hooks() {

		// Plugin updates
		add_action( 'admin_init', array( $this, 'check_version' ), 2 );

		// Add tracking script
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Helper functions
		add_action( 'plugins_loaded', array( $this, 'load_helper_functions' ) );

	}


	/**
	 * Textdomain.
	 *
	 * Load the textdomain based on WP language.
	 *
	 * @since 1.1.1
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'conversio-for-woocommerce', false, basename( dirname( __FILE__ ) ) . '/languages' );
	}


	/**
	 * Enqueue script.
	 *
	 * Enqueue CM Commerce tracking script to track click conversions.
	 *
	 * @since 1.0.2
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'receiptful-tracking', $this->script_url, array(), $this->version, true );
	}


	/**
	 * Check plugin version.
	 *
	 * Check the current plugin version and see if there is any
	 * data update required.
	 *
	 * @since 1.1.9
	 */
	public function check_version() {

		/**
		 * Version specific plugin updates
		 */

		// 1.1.9 - re-sync orders
		if ( version_compare( get_option( 'receiptful_woocommerce_version' ), '1.1.9', '<' ) ) {
			delete_option( 'receiptful_completed_initial_receipt_sync' );
		}

		// 1.2.5 - Re-sync products
		if ( version_compare( get_option( 'receiptful_woocommerce_version' ), '1.2.5', '<' ) ) {
			delete_option( 'receiptful_completed_initial_product_sync' );
		}

		// Update version number if its not the same
		if ( $this->version != get_option( 'receiptful_woocommerce_version' ) ) {
			update_option( 'receiptful_woocommerce_version', $this->version );
		}

	}


	/**
	 * Show WC version requirement notice.
	 *
	 * @since 1.4.0
	 */
	public function wc_version_required_notice() {
		?><div class="notice notice-error is-dismissible">
			<p><?php echo sprintf( __( 'CM Commercefor WooCommerce %s requires WooCommerce %s or higher to function', 'conversio-for-woocommerce' ), $this->version, '3.0.0' ); ?></p>
		</div><?php
	}


	/**
	 * Helper functions,
	 *
	 * Load helper functions after all plugins to prevent 'function already exists' errors.
	 *
	 * @since 1.0.4
	 */
	public function load_helper_functions() {
		require_once plugin_dir_path( __FILE__ ) . '/includes/cm-commerce-helper-functions.php';
		require_once plugin_dir_path( __FILE__ ) . '/includes/cm-commerce-cron-functions.php';
	}


	/**
	 * Add log.
	 *
	 * Add a new log to the WC Logger.
	 *
	 * @since 1.3.4
	 *
	 * @param string $message Message to add to the log.
	 */
	public function add_log( $message ) {
		$logger = new WC_Logger();
		$logger->add( 'CM Commerce', $message );
	}


}


/**
 * The main function responsible for returning the CM_Commerce_WooCommerce object.
 *
 * Use this function like you would a global variable, except without needing to declare the global.
 *
 * Example: <?php CM_Commerce()->method_name(); ?>
 *
 * @since 1.0.0
 *
 * @return object CM_Commerce_WooCommerce class object.
 */
if ( ! function_exists( 'CM_Commerce' ) ) {

	function CM_Commerce() {
		return CM_Commerce_WooCommerce::instance();
	}

}

if ( ! function_exists( 'Conversio' ) ) {
	function Conversio() {
		return CM_Commerce_WooCommerce::instance();
	}
}
CM_Commerce();

if ( ! function_exists( 'Receiptful' ) ) {
	function Receiptful() {
		return CM_Commerce(); // No notice, BC deprecation
	}
}
