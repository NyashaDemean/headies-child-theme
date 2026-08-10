<?php
/**
 * Headies checkout form — overrides woocommerce/templates/checkout/form-checkout.php.
 *
 * Layout: main column carries Contact + Delivery (form-billing.php), the
 * sidebar carries the order review + payment + place-order button
 * (#order_review, left intact so WooCommerce's native AJAX totals refresh
 * keeps working). Order notes render in form-shipping.php.
 *
 * @see https://woocommerce.com/document/template-structure/
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_checkout_form', $checkout );

if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}
?>

<div class="headies-checkout-page">

	<div class="headies-checkout-title-row">
		<h1>Checkout</h1>
		<nav class="headies-checkout-steps" aria-label="Checkout steps">
			<a href="#step-details">01 Details</a>
			<a href="#step-delivery">02 Delivery</a>
			<a href="#step-payment">03 Payment</a>
		</nav>
	</div>

	<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data" aria-label="<?php echo esc_attr__( 'Checkout', 'woocommerce' ); ?>">

		<div class="headies-checkout-layout">

			<div class="headies-checkout-main">
				<?php if ( $checkout->get_checkout_fields() ) : ?>
					<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>
					<div id="customer_details">
						<?php do_action( 'woocommerce_checkout_billing' ); ?>
						<?php do_action( 'woocommerce_checkout_shipping' ); ?>
					</div>
					<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
				<?php endif; ?>
			</div>

			<div class="headies-checkout-sidebar" id="step-payment">
				<h2 class="headies-section-title">Your Order</h2>

				<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

				<div id="order_review" class="woocommerce-checkout-review-order">
					<?php do_action( 'woocommerce_checkout_order_review' ); ?>
				</div>

				<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>

				<?php
				$headies_drops     = function_exists( 'headies_get_drops' ) ? headies_get_drops() : array();
				$headies_live_drop = null;
				foreach ( $headies_drops as $headies_drop ) {
					if ( 'live' === $headies_drop['status'] ) {
						$headies_live_drop = $headies_drop;
						break;
					}
				}
				?>
				<?php if ( $headies_live_drop ) : ?>
					<div class="headies-summary-drop-note">
						<p class="drop-note-title"><?php echo esc_html( strtoupper( $headies_live_drop['name'] ) ); ?> is live</p>
						<p class="drop-note-body">Limited custom caps. Stock is only held once payment clears.</p>
					</div>
				<?php endif; ?>
			</div>

		</div>

	</form>

</div>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
