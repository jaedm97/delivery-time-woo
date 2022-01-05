<?php
/**
 * Class Hooks
 */


if ( ! class_exists( 'DTWOO_Hooks' ) ) {
	/**
	 * Class DTWOO_Hooks
	 */
	class DTWOO_Hooks {

		/**
		 * DTWOO_Hooks constructor.
		 */
		function __construct() {

			add_filter( 'woocommerce_get_sections_shipping', array( $this, 'add_settings_tab' ) );
			add_filter( 'woocommerce_get_settings_shipping', array( $this, 'add_settings_fields' ), 10, 2 );

			add_filter( 'woocommerce_product_data_tabs', array( $this, 'add_product_tabs' ), 10, 1 );
			add_action( 'woocommerce_product_data_panels', array( $this, 'render_product_tabs_content' ), 10, 1 );
			add_action( 'woocommerce_process_product_meta', array( $this, 'save_product_tabs_fields' ), 10, 1 );

			add_action( 'woocommerce_single_product_summary', array( $this, 'display_delivery_time_message' ), 11 );
			add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'display_delivery_time_message' ), 11 );

			add_action( 'wp_ajax_dtwoo_get_delivery_desc', array( $this, 'ajax_get_delivery_desc' ) );
			add_action( 'wp_ajax_nopriv_dtwoo_get_delivery_desc', array( $this, 'ajax_get_delivery_desc' ) );
		}


		/**
		 * Handle aJax request and send response data
		 */
		function ajax_get_delivery_desc() {

			$product_id = isset( $_POST['product_id'] ) ? sanitize_text_field( $_POST['product_id'] ) : '';

			if ( empty( $product_id ) || $product_id == 0 ) {
				wp_send_json_error( esc_html__( 'Invalid product ID', 'delivery-time-woo' ) );
			}

			wp_send_json_success( sprintf( '<p class="dtwoo-delivery-desc">%s</p>', dtwoo_get_delivery_desc( $product_id ) ) );
		}


		/**
		 * Display delivery time message on multiple places
		 */
		function display_delivery_time_message() {

			$dtwoo_display_places_product = dtwoo()->get_option( 'dtwoo_display_places_product', 'yes' );
			$dtwoo_display_places_archive = dtwoo()->get_option( 'dtwoo_display_places_archive', 'no' );

			/**
			 * Check both conditions for displaying in multiple places
			 *
			 * --- If single product page and display no: return from here
			 * --- If shop page or product category page and display no: return from here
			 *
			 * return from here means, the template will not load and message will not display
			 */
			if (
				( is_singular( 'product' ) && $dtwoo_display_places_product == 'no' ) ||
				( ( is_post_type_archive( 'product' ) || is_product_category() ) && $dtwoo_display_places_archive == 'no' )
			) {
				return;
			}

			dtwoo_get_template( 'message-product.php' );
		}


		/**
		 * Save delivery time as product meta data
		 *
		 * @param $post_id
		 */
		function save_product_tabs_fields( $post_id ) {

			if ( isset( $_POST['_delivery_time'] ) ) {
				update_post_meta( $post_id, '_delivery_time', sanitize_text_field( $_POST['_delivery_time'] ) );
			}

			if ( isset( $_POST['_delivery_desc'] ) ) {
				update_post_meta( $post_id, '_delivery_desc', sanitize_text_field( $_POST['_delivery_desc'] ) );
			}
		}


		/**
		 * Render product tabs content
		 */
		function render_product_tabs_content() {

			ob_start();

			// Field: _delivery_time
			woocommerce_wp_text_input( array(
				'id'          => '_delivery_time',
				'type'        => 'number',
				'value'       => get_post_meta( get_the_ID(), '_delivery_time', true ),
				'label'       => esc_html__( 'Delivery Time', 'delivery-time-woo' ),
				'desc_tip'    => true,
				'description' => esc_html__( 'Please select a delivery time for this product.', 'delivery-time-woo' ),
			) );

			woocommerce_wp_textarea_input(
				array(
					'id'          => '_delivery_desc',
					'value'       => get_post_meta( get_the_ID(), '_delivery_desc', true ),
					'label'       => esc_html__( 'Description', 'delivery-time-woo' ),
					'desc_tip'    => true,
					'description' => esc_html__( 'Enter an optional description for delivery time.', 'delivery-time-woo' ),
				)
			);

			printf( '<div id="dtwoo_product_data" class="panel woocommerce_options_panel hidden">%s</div>', ob_get_clean() );
		}


		/**
		 * Add delivery time tab in product data metabox
		 *
		 * @param $tabs
		 *
		 * @return mixed
		 */
		function add_product_tabs( $tabs ) {

			$tabs['dtwoo'] = array(
				'label'    => esc_html__( 'Delivery Time', 'delivery-time-woo' ),
				'priority' => 100,
				'class'    => array( 'show_if_simple', 'show_if_variable', 'show_if_grouped', 'show_if_external', 'hide_if_virtual' ),
				'target'   => 'dtwoo_product_data',
			);

			return $tabs;
		}


		/**
		 * Add settings in WooCommerce > Settings > Shipping > Delivery Time
		 *
		 * @param $settings
		 * @param $current_section
		 *
		 * @return mixed|void
		 */
		function add_settings_fields( $settings, $current_section ) {

			if ( $current_section == 'delivery_time' ) {
				return dtwoo()->get_plugin_settings();
			}

			return $settings;
		}


		/**
		 * Add settings tab in WooCommerce > Settings > Shipping
		 *
		 * @param $settings_tab
		 *
		 * @return mixed
		 */
		function add_settings_tab( $settings_tab ) {

			$settings_tab['delivery_time'] = esc_html__( 'Delivery Time', 'delivery-time-woo' );

			return $settings_tab;
		}
	}

	new DTWOO_Hooks();
}