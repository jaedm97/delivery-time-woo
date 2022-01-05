<?php
/**
 * Plugin Name: Delivery Time for WooCommerce
 * Plugin URI: https://jaed.pro/
 * Description: Manage Delivery Time for WooCommerce
 * Version: 1.0.0
 * Author: Jaed
 * Text Domain: delivery-time-woo
 * Domain Path: /languages/
 * Author URI: https://jaed.pro
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

defined( 'ABSPATH' ) || exit;
defined( 'DTWOO_PLUGIN_URL' ) || define( 'DTWOO_PLUGIN_URL', WP_PLUGIN_URL . '/' . plugin_basename( dirname( __FILE__ ) ) . '/' );
defined( 'DTWOO_PLUGIN_DIR' ) || define( 'DTWOO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
defined( 'DTWOO_PLUGIN_FILE' ) || define( 'DTWOO_PLUGIN_FILE', plugin_basename( __FILE__ ) );

if ( ! class_exists( 'DTWOO_Main' ) ) {
	/**
	 * Class DTWOO_Main
	 */
	class DTWOO_Main {

		protected static $_instance = null;

		/**
		 * DTWOO_Main constructor.
		 */
		function __construct() {

			$this->load_scripts();
			$this->define_classes_functions();

			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		}


		/**
		 * @return \DTWOO_Main|null
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}


		/**
		 * Loading TextDomain
		 */
		function load_textdomain() {
			load_plugin_textdomain( 'delivery-time-woo', false, plugin_basename( dirname( __FILE__ ) ) . '/languages/' );
		}


		/**
		 * Loading classes and functions
		 */
		function define_classes_functions() {
			require_once DTWOO_PLUGIN_DIR . 'includes/class-functions.php';
			require_once DTWOO_PLUGIN_DIR . 'includes/functions.php';
			require_once DTWOO_PLUGIN_DIR . 'includes/class-hooks.php';
		}


		/**
		 * Return data that will pass on pluginObject
		 *
		 * @return array
		 */
		function localize_scripts_data() {
			return array(
				'ajaxURL' => admin_url( 'admin-ajax.php' ),
			);
		}


		/**
		 * Loading scripts to backend
		 */
		function admin_scripts() {

			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );

			wp_enqueue_script( 'dtwoo-admin', plugins_url( 'assets/admin/js/scripts.js', __FILE__ ), array( 'jquery' ) );
			wp_localize_script( 'dtwoo-admin', 'dtwoo_object', $this->localize_scripts_data() );
		}


		/**
		 * Loading scripts to the frontend
		 */
		function front_scripts() {

			wp_enqueue_style( 'dtwoo-front', DTWOO_PLUGIN_URL . 'assets/front/css/style.css', array(), time() );

			wp_enqueue_script( 'dtwoo-front', plugins_url( 'assets/front/js/scripts.js', __FILE__ ), array( 'jquery' ), time() );
			wp_localize_script( 'dtwoo-front', 'dtwoo_object', $this->localize_scripts_data() );
		}


		/**
		 * Loading scripts
		 */
		function load_scripts() {

			add_action( 'wp_enqueue_scripts', array( $this, 'front_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		}
	}
}

DTWOO_Main::instance();