<?php
/**
 * Class Functions
 *
 * @author Pluginbazar
 */

if ( ! class_exists( 'DTWOO_Functions' ) ) {
	/**
	 * Class DTWOO_Functions
	 */
	class DTWOO_Functions {


		/**
		 * Return plugin settings fields
		 *
		 * @return mixed|void
		 */
		function get_plugin_settings() {

			return apply_filters( 'dtwoo_filters_general_settings_fields',
				array(
					array(
						'name' => esc_html__( 'Delivery Time Settings', 'delivery-time-woo' ),
						'type' => 'title',
						'desc' => esc_html__( 'Update settings for delivery time in global scope.', 'delivery-time-woo' ),
						'id'   => 'general_settings_fields'
					),
					array(
						'name'     => esc_html__( 'Delivery Time', 'delivery-time-woo' ),
						'type'     => 'number',
						'desc'     => esc_html__( 'Display what time will be displayed as delivery time', 'delivery-time-woo' ),
						'id'       => 'dtwoo_delivery_time',
						'desc_tip' => true,
					),
					array(
						'name'          => esc_html__( 'Displays on', 'delivery-time-woo' ),
						'desc'          => esc_html__( 'Single product page', 'delivery-time-woo' ),
						'type'          => 'checkbox',
						'checkboxgroup' => 'start',
						'id'            => 'dtwoo_display_places_product',
						'default'       => 'yes',
					),
					array(
						'name'          => esc_html__( 'Displays on', 'delivery-time-woo' ),
						'desc'          => esc_html__( 'Product archive page', 'delivery-time-woo' ),
						'type'          => 'checkbox',
						'checkboxgroup' => 'end',
						'id'            => 'dtwoo_display_places_archive',
					),
					array(
						'name'     => esc_html__( 'Color', 'delivery-time-woo' ),
						'type'     => 'text',
						'desc'     => esc_html__( 'Set any color for delivery time notice.', 'delivery-time-woo' ),
						'id'       => 'dtwoo_display_color',
						'desc_tip' => true,
					),
					array(
						'type' => 'sectionend',
						'id'   => 'general_settings_fields'
					),
				)
			);
		}


		/**
		 * Return Post Meta Value
		 *
		 * @param bool $meta_key
		 * @param bool $post_id
		 * @param string $default
		 *
		 * @return mixed|string|void
		 */
		function get_meta( $meta_key = false, $post_id = false, $default = '' ) {

			if ( ! $meta_key ) {
				return '';
			}

			$post_id    = ! $post_id ? get_the_ID() : $post_id;
			$meta_value = get_post_meta( $post_id, $meta_key, true );
			$meta_value = empty( $meta_value ) ? $default : $meta_value;

			return apply_filters( 'eem_filters_get_meta', $meta_value, $meta_key, $post_id, $default );
		}


		/**
		 * Return option value
		 *
		 * @param string $option_key
		 * @param string $default_val
		 *
		 * @return mixed|string|void
		 */
		function get_option( $option_key = '', $default_val = '' ) {

			if ( empty( $option_key ) ) {
				return '';
			}

			$option_val = get_option( $option_key, $default_val );
			$option_val = empty( $option_val ) ? $default_val : $option_val;

			return apply_filters( 'dtwoo_filters_option_' . $option_key, $option_val );
		}


		/**
		 * Return Arguments Value
		 *
		 * @param string $key
		 * @param array $args
		 * @param string $default
		 *
		 * @return mixed|string
		 */
		function get_args_option( $key = '', $args = array(), $default = '' ) {

			$default = empty( $default ) ? '' : $default;
			$key     = empty( $key ) ? '' : $key;

			if ( isset( $args[ $key ] ) && ! empty( $args[ $key ] ) ) {
				return $args[ $key ];
			}

			return $default;
		}
	}
}