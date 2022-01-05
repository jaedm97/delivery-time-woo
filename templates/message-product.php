<?php
/**
 * Display delivery time message in Single product page
 */

defined( 'ABSPATH' ) || exit;


if ( $delivery_time = dtwoo_get_delivery_time() ) : ?>

    <div class="dtwoo-message-wrap <?php echo dtwoo_get_delivery_desc() ? esc_attr( 'has-delivery-desc' ) : ''; ?>" data-product-id="<?php the_ID(); ?>">
        <p style="color: <?php echo esc_attr( dtwoo()->get_option( 'dtwoo_display_color', 'inherit' ) ); ?>">
            <strong><?php printf( _n( 'Delivery time: %d day', 'Delivery time: %d days', $delivery_time, 'delivery-time-woo' ), number_format_i18n( $delivery_time ) ); ?></strong>
        </p>
    </div>

<?php endif;