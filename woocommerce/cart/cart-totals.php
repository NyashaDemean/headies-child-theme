<?php
/**
 * Headies cart totals — overrides woocommerce/templates/cart/cart-totals.php.
 *
 * @see https://woocommerce.com/document/template-structure/
 */

defined( 'ABSPATH' ) || exit;

$headies_drops     = function_exists( 'headies_get_drops' ) ? headies_get_drops() : array();
$headies_live_drop = null;
foreach ( $headies_drops as $headies_drop ) {
	if ( 'live' === $headies_drop['status'] ) {
		$headies_live_drop = $headies_drop;
		break;
	}
}
?>
<div class="headies-order-summary headies-card <?php echo ( WC()->customer->has_calculated_shipping() ) ? 'calculated_shipping' : ''; ?>">

	<?php do_action( 'woocommerce_before_cart_totals' ); ?>

	<h2 class="headies-section-title">Order Summary</h2>

	<table class="headies-summary-table">

		<tr class="cart-subtotal">
			<th><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
			<td data-title="<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>"><?php wc_cart_totals_subtotal_html(); ?></td>
		</tr>

		<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
				<th><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
				<td data-title="<?php echo esc_attr( wc_cart_totals_coupon_label( $coupon, false ) ); ?>"><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
			</tr>
		<?php endforeach; ?>

		<tr class="shipping">
			<th><?php esc_html_e( 'Delivery', 'woocommerce' ); ?></th>
			<td data-title="Delivery">
				<?php if ( WC()->cart->needs_shipping() ) : ?>
					<?php esc_html_e( 'Calculated at checkout', 'headies' ); ?>
				<?php else : ?>
					<?php esc_html_e( 'Free', 'woocommerce' ); ?>
				<?php endif; ?>
			</td>
		</tr>

		<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<tr class="fee">
				<th><?php echo esc_html( $fee->name ); ?></th>
				<td data-title="<?php echo esc_attr( $fee->name ); ?>"><?php wc_cart_totals_fee_html( $fee ); ?></td>
			</tr>
		<?php endforeach; ?>

		<?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>

		<tr class="order-total">
			<th><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
			<td data-title="<?php esc_attr_e( 'Total', 'woocommerce' ); ?>"><?php wc_cart_totals_order_total_html(); ?></td>
		</tr>

		<?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>

	</table>

	<div class="wc-proceed-to-checkout">
		<?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
	</div>

	<?php if ( $headies_live_drop ) : ?>
		<div class="headies-summary-drop-note">
			<p class="drop-note-title"><?php echo esc_html( strtoupper( $headies_live_drop['name'] ) ); ?> is live</p>
			<p class="drop-note-body">Limited custom caps. Once the drop sells out it doesn't come back — items in a cart aren't reserved.</p>
		</div>
	<?php endif; ?>

	<?php do_action( 'woocommerce_after_cart_totals' ); ?>

</div>
