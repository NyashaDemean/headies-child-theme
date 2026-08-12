<?php
/**
 * Headies order review table — overrides woocommerce/templates/checkout/review-order.php.
 * Lives inside the sticky sidebar; compact item rows instead of a full table.
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="woocommerce-checkout-review-order-table headies-order-review">

<div class="headies-order-items">
	<?php
	do_action( 'woocommerce_review_order_before_cart_contents' );

	foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
		$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

		if ( ! $_product || ! $_product->exists() || $cart_item['quantity'] <= 0 || ! apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
			continue;
		}
		?>
		<div class="headies-order-item">
			<div class="headies-order-item-thumb"><?php echo $_product->get_image( 'woocommerce_thumbnail' ); // phpcs:ignore ?></div>
			<div class="headies-order-item-info">
				<span class="headies-order-item-name"><?php echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) ); ?></span>
				<span class="headies-order-item-qty"><?php echo esc_html( sprintf( 'Qty %d', $cart_item['quantity'] ) ); ?></span>
				<?php echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore ?>
			</div>
			<div class="headies-order-item-total"><?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore ?></div>
		</div>
		<?php
	}

	do_action( 'woocommerce_review_order_after_cart_contents' );
	?>
</div>

<?php if ( wc_coupons_enabled() ) : ?>
	<div class="headies-checkout-coupon">
		<?php woocommerce_checkout_coupon_form(); ?>
	</div>
<?php endif; ?>

<table class="headies-summary-table">

	<tr class="cart-subtotal">
		<th><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
		<td><?php wc_cart_totals_subtotal_html(); ?></td>
	</tr>

	<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
		<tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
			<th><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
			<td><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
		</tr>
	<?php endforeach; ?>

	<?php
	$headies_chosen_rate    = null;
	$headies_chosen_methods = WC()->session ? WC()->session->get( 'chosen_shipping_methods', array() ) : array();
	if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) {
		foreach ( WC()->shipping()->get_packages() as $headies_package_index => $headies_package ) {
			$headies_chosen_id = isset( $headies_chosen_methods[ $headies_package_index ] ) ? $headies_chosen_methods[ $headies_package_index ] : '';
			if ( isset( $headies_package['rates'][ $headies_chosen_id ] ) ) {
				$headies_chosen_rate = $headies_package['rates'][ $headies_chosen_id ];
				break;
			}
		}
	}
	?>
	<?php if ( $headies_chosen_rate ) : ?>
		<tr class="shipping">
			<th><?php esc_html_e( 'Delivery', 'woocommerce' ); ?></th>
			<td><?php echo wp_kses_post( wc_cart_totals_shipping_method_label( $headies_chosen_rate ) ); ?></td>
		</tr>
	<?php endif; ?>

	<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
		<tr class="fee">
			<th><?php echo esc_html( $fee->name ); ?></th>
			<td><?php wc_cart_totals_fee_html( $fee ); ?></td>
		</tr>
	<?php endforeach; ?>

	<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

	<tr class="order-total">
		<th><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
		<td><?php wc_cart_totals_order_total_html(); ?></td>
	</tr>

	<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>

</table>

</div>
