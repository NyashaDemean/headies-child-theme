<?php
/**
 * Headies proceed-to-checkout button — overrides
 * woocommerce/templates/cart/proceed-to-checkout-button.php.
 */

defined( 'ABSPATH' ) || exit;
?>
<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="headies-btn-primary headies-checkout-btn">
	<?php esc_html_e( 'Checkout', 'headies' ); ?>
</a>
