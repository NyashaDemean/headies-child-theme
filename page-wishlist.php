<?php get_header(); ?>

<?php
$headies_wishlist       = headies_get_wishlist();
$headies_wishlist_count = count( $headies_wishlist );
// Newest-added first.
arsort( $headies_wishlist );
$headies_cart_product_ids = array();
if ( WC()->cart ) {
	foreach ( WC()->cart->get_cart() as $headies_cart_item ) {
		$headies_cart_product_ids[] = $headies_cart_item['product_id'];
	}
}
?>

<div class="headies-account headies-wishlist-page">

	<div class="headies-account-header">
		<p class="headies-account-crumbs"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / Wishlist</p>

		<div class="headies-wishlist-title-row">
			<h1>Wishlist <span class="headies-wishlist-count">(<?php echo (int) $headies_wishlist_count; ?>)</span></h1>

			<?php if ( $headies_wishlist_count ) : ?>
				<div class="headies-wishlist-actions">
					<button type="button" class="headies-btn-outline headies-wishlist-share">Share List</button>
					<button type="button" class="headies-btn-primary headies-wishlist-add-all">Add All to Cart</button>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<?php if ( $headies_wishlist_count ) : ?>

		<div class="headies-wishlist-grid">
			<?php foreach ( $headies_wishlist as $headies_product_id => $headies_added_ts ) :
				$headies_product = wc_get_product( $headies_product_id );
				if ( ! $headies_product ) {
					continue;
				}

				$headies_in_stock  = $headies_product->is_in_stock();
				$headies_stock_qty = $headies_product->get_stock_quantity();
				$headies_low_stock = $headies_in_stock && $headies_product->managing_stock() && null !== $headies_stock_qty && $headies_stock_qty <= wc_get_low_stock_amount( $headies_product );
				$headies_in_cart   = in_array( $headies_product_id, $headies_cart_product_ids, true );
				?>
				<div class="headies-wishlist-card headies-card">
					<div class="headies-wishlist-image">
						<a href="<?php echo esc_url( get_permalink( $headies_product_id ) ); ?>">
							<?php echo $headies_product->get_image( 'woocommerce_single' ); ?>
						</a>
						<?php headies_wishlist_button( $headies_product_id ); ?>
						<?php if ( ! $headies_in_stock ) : ?>
							<span class="headies-wishlist-badge headies-wishlist-badge--out">Sold Out</span>
						<?php elseif ( $headies_low_stock ) : ?>
							<span class="headies-wishlist-badge">Low Stock</span>
						<?php endif; ?>
					</div>

					<div class="headies-wishlist-info">
						<div class="headies-wishlist-row">
							<span class="headies-wishlist-name"><?php echo esc_html( $headies_product->get_name() ); ?></span>
							<span class="headies-wishlist-price"><?php echo wp_kses_post( wc_price( $headies_product->get_price() ) ); ?></span>
						</div>
						<p class="headies-wishlist-added">Added <?php echo esc_html( date_i18n( 'j M', $headies_added_ts ) ); ?></p>

						<?php if ( $headies_in_cart ) : ?>
							<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="headies-btn-primary headies-wishlist-add-to-cart is-in-cart">In Cart</a>
						<?php elseif ( $headies_in_stock ) : ?>
							<button type="button" class="headies-btn-primary headies-wishlist-add-to-cart" data-product-id="<?php echo (int) $headies_product_id; ?>">Add to Cart</button>
						<?php else : ?>
							<button type="button" class="headies-btn-primary headies-wishlist-add-to-cart" disabled>Sold Out</button>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

	<?php else : ?>

		<div class="headies-empty">
			<p>Your wishlist is empty — tap the heart on any cap to save it here.</p>
			<a href="<?php echo esc_url( home_url( '/hats' ) ); ?>" class="headies-shop-cta">Shop Caps</a>
		</div>

	<?php endif; ?>

</div>

<?php get_footer(); ?>
