<?php
/**
 * Headies checkout "message" section — overrides
 * woocommerce/templates/checkout/form-shipping.php.
 *
 * The delivery address + method now live in form-billing.php's "Delivery"
 * section (see that file). Ship-to-different-address is disabled site-wide
 * (woocommerce_ship_to_destination = billing_only), so all that's left here
 * is the order notes field, relabeled "Message".
 *
 * @global WC_Checkout $checkout
 */

defined( 'ABSPATH' ) || exit;
?>
<section id="step-message" class="headies-checkout-section">

	<?php if ( apply_filters( 'woocommerce_enable_order_notes_field', 'yes' === get_option( 'woocommerce_enable_order_comments', 'yes' ) ) ) : ?>

		<h2 class="headies-section-title">Message</h2>
		<p class="headies-checkout-help">
			Confused about anything? Add a note below and we'll follow up, or call your nearest branch when you choose Collect In Store.
		</p>

		<div class="headies-checkout-fields">
			<?php foreach ( $checkout->get_checkout_fields( 'order' ) as $key => $field ) : ?>
				<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
			<?php endforeach; ?>
		</div>

	<?php endif; ?>

</section>
