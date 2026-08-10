<?php
/**
 * Headies order-confirmation page — overrides woocommerce/templates/checkout/thankyou.php.
 *
 * @var WC_Order $order
 */

defined( 'ABSPATH' ) || exit;

if ( ! $order ) {
	?>
	<div class="headies-account headies-thankyou-page">
		<div class="headies-empty">
			<p><?php esc_html_e( "We couldn't find that order.", 'headies' ); ?></p>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="headies-btn-primary"><?php esc_html_e( 'Back to Home', 'woocommerce' ); ?></a>
		</div>
	</div>
	<?php
	return;
}

do_action( 'woocommerce_before_thankyou', $order->get_id() );

if ( $order->has_status( 'failed' ) ) {
	?>
	<div class="headies-account headies-thankyou-page">
		<div class="headies-empty">
			<p><?php esc_html_e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce' ); ?></p>
			<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="headies-btn-primary"><?php esc_html_e( 'Pay', 'woocommerce' ); ?></a>
			<?php if ( is_user_logged_in() ) : ?>
				<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="headies-btn-outline"><?php esc_html_e( 'My Account', 'woocommerce' ); ?></a>
			<?php endif; ?>
		</div>
	</div>
	<?php
	do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() );
	do_action( 'woocommerce_thankyou', $order->get_id() );
	return;
}

$headies_is_pickup = false;
foreach ( $order->get_items( 'shipping' ) as $headies_shipping_item ) {
	if ( 0 === strpos( $headies_shipping_item->get_method_id(), 'local_pickup' ) ) {
		$headies_is_pickup = true;
		break;
	}
}
$headies_pickup_location = $order->get_meta( '_headies_pickup_location' );
$headies_payment_method  = $order->get_payment_method();
?>

<div class="headies-account headies-thankyou-page">

	<div class="headies-account-header">
		<p class="headies-account-crumbs"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / Order Confirmed</p>
		<h1>Order Confirmed</h1>
	</div>

	<div class="headies-thankyou-layout">

		<div class="headies-thankyou-main">

			<div class="headies-card headies-thankyou-summary">
				<p class="headies-thankyou-message">
					<?php
					echo esc_html(
						apply_filters(
							'woocommerce_thankyou_order_received_text',
							__( "Thank you — we've got your order and we're on it.", 'headies' ),
							$order
						)
					);
					?>
				</p>

				<dl class="headies-thankyou-meta">
					<div>
						<dt>Order Number</dt>
						<dd>#<?php echo esc_html( $order->get_order_number() ); ?></dd>
					</div>
					<div>
						<dt>Date</dt>
						<dd><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></dd>
					</div>
					<div>
						<dt>Email</dt>
						<dd><?php echo esc_html( $order->get_billing_email() ); ?></dd>
					</div>
					<div>
						<dt>Total</dt>
						<dd><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></dd>
					</div>
					<?php if ( $order->get_payment_method_title() ) : ?>
						<div>
							<dt>Payment Method</dt>
							<dd><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></dd>
						</div>
					<?php endif; ?>
					<?php if ( $order->get_shipping_method() ) : ?>
						<div>
							<dt>Delivery</dt>
							<dd><?php echo esc_html( $order->get_shipping_method() ); ?></dd>
						</div>
					<?php endif; ?>
				</dl>
			</div>

			<div class="headies-card headies-thankyou-next">
				<h2 class="headies-section-title">What's Next</h2>

				<?php if ( $headies_is_pickup ) : ?>
					<p>
						We don't have a physical Headies store, so pickup happens at your chosen Melusi Home Designs branch.
						<?php if ( $headies_pickup_location ) : ?>
							We'll have your order ready at <strong><?php echo esc_html( $headies_pickup_location ); ?></strong> — we'll message you once it's set to collect.
						<?php else : ?>
							We'll be in touch to confirm your pickup branch.
						<?php endif; ?>
					</p>
					<?php if ( 'headies_paynow' === $headies_payment_method ) : ?>
						<p>We'll send a Paynow payment request to your phone or card shortly. Your order is held once payment clears.</p>
					<?php elseif ( 'cod' === $headies_payment_method ) : ?>
						<p>Pay in cash or by card when you collect — no need to pay anything online.</p>
					<?php endif; ?>
				<?php else : ?>
					<p>Your order is on its way via <strong><?php echo esc_html( $order->get_shipping_method() ); ?></strong>.</p>
					<?php if ( 'headies_paynow' === $headies_payment_method ) : ?>
						<p>We'll send a Paynow payment request to your phone or card shortly — delivery is confirmed once payment clears.</p>
					<?php endif; ?>
				<?php endif; ?>

				<p class="headies-checkout-help">
					Confused about anything? Reply to your confirmation email or reach out — we're happy to help.
				</p>
			</div>

		</div>

		<div class="headies-card headies-thankyou-sidebar">
			<h2 class="headies-section-title">Order Items</h2>

			<div class="headies-order-items">
				<?php foreach ( $order->get_items() as $headies_item ) : ?>
					<?php $headies_product = $headies_item->get_product(); ?>
					<div class="headies-order-item">
						<?php if ( $headies_product ) : ?>
							<div class="headies-order-item-thumb"><?php echo $headies_product->get_image( 'woocommerce_thumbnail' ); // phpcs:ignore ?></div>
						<?php endif; ?>
						<div class="headies-order-item-info">
							<span class="headies-order-item-name"><?php echo esc_html( $headies_item->get_name() ); ?></span>
							<span class="headies-order-item-qty"><?php echo esc_html( sprintf( 'Qty %d', $headies_item->get_quantity() ) ); ?></span>
						</div>
						<div class="headies-order-item-total"><?php echo wp_kses_post( $order->get_formatted_line_subtotal( $headies_item ) ); ?></div>
					</div>
				<?php endforeach; ?>
			</div>

			<table class="headies-summary-table">
				<tr>
					<th><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
					<td><?php echo wp_kses_post( wc_price( $order->get_subtotal() ) ); ?></td>
				</tr>
				<?php if ( $order->get_shipping_total() || $order->get_shipping_method() ) : ?>
					<tr>
						<th><?php esc_html_e( 'Delivery', 'woocommerce' ); ?></th>
						<td><?php echo 0 == $order->get_shipping_total() ? esc_html__( 'Free', 'woocommerce' ) : wp_kses_post( wc_price( $order->get_shipping_total() ) ); ?></td>
					</tr>
				<?php endif; ?>
				<tr class="order-total">
					<th><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
					<td><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></td>
				</tr>
			</table>
		</div>

	</div>

	<div class="headies-thankyou-actions">
		<a href="<?php echo esc_url( home_url( '/hats' ) ); ?>" class="headies-btn-primary">Continue Shopping</a>
		<?php if ( is_user_logged_in() ) : ?>
			<a href="<?php echo esc_url( wc_get_endpoint_url( 'orders', '', wc_get_page_permalink( 'myaccount' ) ) ); ?>" class="headies-btn-outline">View My Orders</a>
		<?php endif; ?>
	</div>

</div>

<?php
do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() );
do_action( 'woocommerce_thankyou', $order->get_id() );
?>
