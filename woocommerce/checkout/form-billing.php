<?php
/**
 * Headies checkout contact + delivery fields — overrides
 * woocommerce/templates/checkout/form-billing.php.
 *
 * WooCommerce only gives us one 'billing' field group, but the design calls
 * for two visually distinct steps (Contact, Delivery). We split the same
 * field array into two <section>s here rather than fighting the framework
 * with a second, unused field group.
 *
 * @global WC_Checkout $checkout
 */

defined( 'ABSPATH' ) || exit;

$headies_all_fields = $checkout->get_checkout_fields( 'billing' );

$headies_contact_keys  = array( 'billing_first_name', 'billing_last_name', 'billing_email', 'billing_phone' );
$headies_delivery_keys = array( 'billing_address_1', 'billing_address_2', 'billing_city', 'billing_state' );
?>
<section id="step-details" class="headies-checkout-section">
	<h2 class="headies-section-title">Contact</h2>

	<?php do_action( 'woocommerce_before_checkout_billing_form', $checkout ); ?>

	<div class="headies-checkout-fields">
		<?php foreach ( $headies_contact_keys as $key ) : ?>
			<?php if ( isset( $headies_all_fields[ $key ] ) ) : ?>
				<?php woocommerce_form_field( $key, $headies_all_fields[ $key ], $checkout->get_value( $key ) ); ?>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
</section>

<section id="step-delivery" class="headies-checkout-section">
	<h2 class="headies-section-title">Delivery</h2>

	<div class="headies-delivery-methods">
		<h3 class="headies-subheading">Delivery Method</h3>
		<p class="headies-checkout-help">
			We don't have a physical Headies store — <strong>Collect In Store</strong> means pickup at any Melusi Home Designs branch.
		</p>
		<div class="headies-delivery-methods-inner">
			<?php if ( WC()->cart->needs_shipping() ) : ?>
				<?php wc_cart_totals_shipping_html(); ?>
			<?php endif; ?>
		</div>
	</div>

	<?php if ( function_exists( 'headies_pickup_location_field' ) ) : ?>
		<?php headies_pickup_location_field(); ?>
	<?php endif; ?>

	<div class="headies-delivery-address-fields">
		<h3 class="headies-subheading">Delivery Address</h3>

		<div class="headies-checkout-fields">
			<?php foreach ( $headies_delivery_keys as $key ) : ?>
				<?php if ( isset( $headies_all_fields[ $key ] ) ) : ?>
					<?php woocommerce_form_field( $key, $headies_all_fields[ $key ], $checkout->get_value( $key ) ); ?>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
	</div>

	<?php
	// Everything else in the billing field group (currently just the hidden
	// country field) still needs to render so it gets posted, but isn't part
	// of either visible field block above.
	foreach ( $headies_all_fields as $key => $field ) {
		if ( in_array( $key, $headies_contact_keys, true ) || in_array( $key, $headies_delivery_keys, true ) ) {
			continue;
		}
		woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
	}
	?>

	<?php do_action( 'woocommerce_after_checkout_billing_form', $checkout ); ?>
</section>

<?php if ( ! is_user_logged_in() && $checkout->is_registration_enabled() ) : ?>
	<section class="headies-checkout-section headies-checkout-account">
		<?php if ( ! $checkout->is_registration_required() ) : ?>
			<p class="form-row form-row-wide create-account">
				<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
					<input class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" id="createaccount" <?php checked( ( true === $checkout->get_value( 'createaccount' ) || ( true === apply_filters( 'woocommerce_create_account_default_checked', false ) ) ), true ); ?> type="checkbox" name="createaccount" value="1" /> <span><?php esc_html_e( 'Create an account?', 'woocommerce' ); ?></span>
				</label>
			</p>
		<?php endif; ?>

		<?php do_action( 'woocommerce_before_checkout_registration_form', $checkout ); ?>

		<?php if ( $checkout->get_checkout_fields( 'account' ) ) : ?>
			<div class="create-account">
				<?php foreach ( $checkout->get_checkout_fields( 'account' ) as $key => $field ) : ?>
					<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
				<?php endforeach; ?>
				<div class="clear"></div>
			</div>
		<?php endif; ?>

		<?php do_action( 'woocommerce_after_checkout_registration_form', $checkout ); ?>
	</section>
<?php endif; ?>
