<?php
/**
 * Headies empty-cart state — overrides woocommerce/templates/cart/cart-empty.php.
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_cart_is_empty' );
?>
<div class="headies-cart-page">

	<div class="headies-account-header">
		<p class="headies-account-crumbs"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / Cart</p>
		<h1>Cart</h1>
	</div>

	<div class="headies-empty">
		<p><?php esc_html_e( "Your cart is empty. Time to go find your next cap.", 'woocommerce' ); ?></p>
		<a href="<?php echo esc_url( home_url( '/hats' ) ); ?>" class="headies-shop-cta"><?php esc_html_e( 'Shop Caps', 'woocommerce' ); ?></a>
	</div>

</div>
