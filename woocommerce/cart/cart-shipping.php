<?php
/**
 * Headies shipping method rows — overrides woocommerce/templates/cart/cart-shipping.php.
 * Used both on the Cart page and (via wc_cart_totals_shipping_html()) on the
 * Checkout "Delivery" step, so the same bordered radio-card style renders in
 * both places.
 *
 * @see https://woocommerce.com/document/template-structure/
 */

defined( 'ABSPATH' ) || exit;

$formatted_destination = isset( $formatted_destination ) ? $formatted_destination : WC()->countries->get_formatted_address( $package['destination'], ', ' );
?>
<?php if ( ! empty( $available_methods ) && is_array( $available_methods ) ) : ?>

	<ul id="shipping_method" class="headies-shipping-methods">
		<?php foreach ( $available_methods as $method ) : ?>
			<?php
			$is_pickup = ( false !== strpos( $method->id, 'local_pickup' ) );
			$sub       = $is_pickup ? 'At any Melusi Home Designs branch' : '';
			?>
			<li class="headies-shipping-option <?php echo checked( $method->id, $chosen_method, false ) ? 'is-selected' : ''; ?>" data-method="<?php echo esc_attr( $method->id ); ?>">
				<label for="shipping_method_<?php echo esc_attr( $index ); ?>_<?php echo esc_attr( sanitize_title( $method->id ) ); ?>">
					<?php
					if ( 1 < count( $available_methods ) ) {
						printf( '<input type="radio" name="shipping_method[%1$d]" data-index="%1$d" id="shipping_method_%1$d_%2$s" value="%3$s" class="shipping_method" %4$s />', $index, esc_attr( sanitize_title( $method->id ) ), esc_attr( $method->id ), checked( $method->id, $chosen_method, false ) ); // phpcs:ignore
					} else {
						printf( '<input type="hidden" name="shipping_method[%1$d]" data-index="%1$d" id="shipping_method_%1$d_%2$s" value="%3$s" class="shipping_method" checked="checked" />', $index, esc_attr( sanitize_title( $method->id ) ), esc_attr( $method->id ) ); // phpcs:ignore
					}
					?>
					<span class="headies-shipping-option-info">
						<span class="headies-shipping-option-name"><?php echo esc_html( $method->label ); ?></span>
						<?php if ( $sub ) : ?>
							<span class="headies-shipping-option-sub"><?php echo esc_html( $sub ); ?></span>
						<?php endif; ?>
					</span>
					<span class="headies-shipping-option-price">
						<?php echo 0 == $method->cost ? esc_html__( 'Free', 'woocommerce' ) : wp_kses_post( wc_price( $method->cost ) ); // phpcs:ignore ?>
					</span>
				</label>
				<?php do_action( 'woocommerce_after_shipping_rate', $method, $index ); ?>
			</li>
		<?php endforeach; ?>
	</ul>

<?php elseif ( ! $has_calculated_shipping || ! $formatted_destination ) : ?>

	<p class="headies-shipping-note">
		<?php echo wp_kses_post( apply_filters( 'woocommerce_shipping_may_be_available_html', __( 'Enter your delivery address to see delivery options.', 'woocommerce' ) ) ); ?>
	</p>

<?php else : ?>

	<p class="headies-shipping-note">
		<?php esc_html_e( 'No delivery options were found for your address. Please double-check it, or leave a message below and we\'ll help sort it out.', 'headies' ); ?>
	</p>

<?php endif; ?>
