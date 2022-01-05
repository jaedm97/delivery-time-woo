<?php
/**
 * All Functions
 *
 * @author Pluginbazar
 */


if ( ! function_exists( 'dtwoo_has_delivery_desc' ) ) {
	/**
	 * Check if a product has delivery description or not
	 *
	 * @param string $product_id
	 *
	 * @return bool
	 */
	function dtwoo_has_delivery_desc( $product_id = '' ) {
		return ! empty( dtwoo_get_delivery_desc( $product_id ) );
	}
}


if ( ! function_exists( 'dtwoo_get_delivery_desc' ) ) {
	/**
	 * return delivery description based on product ID
	 *
	 * @param string $product_id
	 *
	 * @return mixed|void
	 */
	function dtwoo_get_delivery_desc( $product_id = '' ) {

		$product_id     = $product_id === 0 || empty( $product_id ) ? get_the_ID() : $product_id;
		$_delivery_desc = dtwoo()->get_meta( '_delivery_desc', $product_id );

		return apply_filters( 'dtwoo_filters_delivery_desc', $_delivery_desc, $product_id );
	}
}


if ( ! function_exists( 'dtwoo_get_delivery_time' ) ) {
	/**
	 * Return delivery time, If it returns false, that means no delivery time should display
	 *
	 * @param string $product_id
	 *
	 * @return false|int
	 */
	function dtwoo_get_delivery_time( $product_id = '' ) {

		$product_id     = $product_id === 0 || empty( $product_id ) ? get_the_ID() : $product_id;
		$_delivery_time = (int) dtwoo()->get_meta( '_delivery_time', $product_id );

		if ( empty( $_delivery_time ) || $_delivery_time === 0 ) {
			if ( empty( $delivery_time = (int) dtwoo()->get_option( 'dtwoo_delivery_time' ) ) || $delivery_time === 0 ) {
				return false;
			}

			return $delivery_time;
		}

		if ( $_delivery_time > 0 ) {
			return $_delivery_time;
		}

		return false;
	}
}


if ( ! function_exists( 'dtwoo_get_template_part' ) ) {
	/**
	 * Get Template Part
	 *
	 * @param $slug
	 * @param string $name
	 * @param array $args
	 * @param bool $main_template | When you call a template from extensions you can use this param as true to check from main template only
	 */
	function dtwoo_get_template_part( $slug, $name = '', $args = array(), $main_template = false ) {

		$template   = '';
		$plugin_dir = DTWOO_PLUGIN_DIR;

		/**
		 * Locate template
		 */
		if ( $name ) {
			$template = locate_template( array(
				"{$slug}-{$name}.php",
				"dtwoo/{$slug}-{$name}.php"
			) );
		}

		/**
		 * Check directory for templates from Addons
		 */
		$backtrace      = debug_backtrace( 2, true );
		$backtrace      = empty( $backtrace ) ? array() : $backtrace;
		$backtrace      = reset( $backtrace );
		$backtrace_file = isset( $backtrace['file'] ) ? $backtrace['file'] : '';


		/**
		 * Search for Template in Plugin
		 *
		 * @in Plugin
		 */
		if ( ! $template && $name && file_exists( untrailingslashit( $plugin_dir ) . "/templates/{$slug}-{$name}.php" ) ) {
			$template = untrailingslashit( $plugin_dir ) . "/templates/{$slug}-{$name}.php";
		}


		/**
		 * Search for Template in Theme
		 *
		 * @in Theme
		 */
		if ( ! $template ) {
			$template = locate_template( array( "{$slug}.php", "dtwoo/{$slug}.php" ) );
		}


		/**
		 * Allow 3rd party plugins to filter template file from their plugin.
		 *
		 * @filter dtwoo_filters_get_template_part
		 */
		$template = apply_filters( 'dtwoo_filters_get_template_part', $template, $slug, $name );


		if ( $template ) {
			load_template( $template, false );
		}
	}
}


if ( ! function_exists( 'dtwoo_get_template' ) ) {
	/**
	 * Get Template
	 *
	 * @param $template_name
	 * @param array $args
	 * @param string $template_path
	 * @param string $default_path
	 * @param bool $main_template | When you call a template from extensions you can use this param as true to check from main template only
	 *
	 */
	function dtwoo_get_template( $template_name, $args = array(), $template_path = '', $default_path = '', $main_template = false ) {

		if ( ! empty( $args ) && is_array( $args ) ) {
			extract( $args ); // @codingStandardsIgnoreLine
		}

		/**
		 * Check directory for templates from Addons
		 */
		$backtrace      = debug_backtrace( 2, true );
		$backtrace      = empty( $backtrace ) ? array() : $backtrace;
		$backtrace      = reset( $backtrace );
		$backtrace_file = isset( $backtrace['file'] ) ? $backtrace['file'] : '';

		$located = dtwoo_locate_template( $template_name, $template_path, $default_path, $backtrace_file, $main_template );


		if ( file_exists( $located ) ) {

			$located = apply_filters( 'dtwoo_filters_get_template', $located, $template_name, $args, $template_path, $default_path );

			do_action( 'dtwoo_before_template_part', $template_name, $template_path, $located, $args );

			include $located;

			do_action( 'dtwoo_after_template_part', $template_name, $template_path, $located, $args );
		}
	}
}


if ( ! function_exists( 'dtwoo_locate_template' ) ) {
	/**
	 *  Locate template
	 *
	 * @param $template_name
	 * @param string $template_path
	 * @param string $default_path
	 * @param string $backtrace_file
	 * @param bool $main_template | When you call a template from extensions you can use this param as true to check from main template only
	 *
	 * @return mixed|void
	 */
	function dtwoo_locate_template( $template_name, $template_path = '', $default_path = '', $backtrace_file = '', $main_template = false ) {

		$plugin_dir = DTWOO_PLUGIN_DIR;

		/**
		 * Template path in Theme
		 */
		if ( ! $template_path ) {
			$template_path = 'dtwoo/';
		}

		/**
		 * Template default path from Plugin
		 */
		if ( ! $default_path ) {
			$default_path = untrailingslashit( $plugin_dir ) . '/templates/';
		}

		/**
		 * Look within passed path within the theme - this is priority.
		 */
		$template = locate_template(
			array(
				trailingslashit( $template_path ) . $template_name,
				$template_name,
			)
		);

		/**
		 * Get default template
		 */
		if ( ! $template ) {
			$template = $default_path . $template_name;
		}

		/**
		 * Return what we found with allowing 3rd party to override
		 *
		 * @filter dtwoo_filters_locate_template
		 */
		return apply_filters( 'dtwoo_filters_locate_template', $template, $template_name, $template_path );
	}
}


if ( ! function_exists( 'dtwoo' ) ) {
	/**
	 * Return global $dtwoo
	 *
	 * @return DTWOO_Functions
	 */
	function dtwoo() {
		global $dtwoo;

		if ( empty( $dtwoo ) ) {
			$dtwoo = new DTWOO_Functions();
		}

		return $dtwoo;
	}
}