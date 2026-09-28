<?php get_header(); ?>

<?php
// A "?share=<token>" link (from the Share List popup) shows someone else's
// wishlist snapshot read-only — bulk actions are hidden since they'd operate
// on the viewer's own list/cart, not the list on screen. Per-item hearts and
// "Add to Cart" still work normally, since those are already scoped by
// product ID rather than by whose wishlist this is.
$headies_shared_token   = isset( $_GET['share'] ) ? sanitize_text_field( wp_unslash( $_GET['share'] ) ) : '';
$headies_shared_ids     = $headies_shared_token ? get_transient( 'headies_wl_share_' . $headies_shared_token ) : false;
$headies_is_shared      = is_array( $headies_shared_ids );
$headies_share_expired  = $headies_shared_token && ! $headies_is_shared;

if ( $headies_is_shared ) {
	$headies_wishlist = array_fill_keys( array_map( 'absint', $headies_shared_ids ), 0 );
} else {
	$headies_wishlist = headies_get_wishlist();
	// Newest-added first.
	arsort( $headies_wishlist );
}
$headies_wishlist_count = count( $headies_wishlist );

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
			<h1><?php echo $headies_is_shared ? 'Shared Wishlist' : 'Wishlist'; ?> <span class="headies-wishlist-count">(<?php echo (int) $headies_wishlist_count; ?>)</span></h1>

			<?php if ( $headies_wishlist_count && ! $headies_is_shared ) : ?>
				<div class="headies-wishlist-actions">
					<div class="headies-wishlist-share-wrap">
						<button type="button" class="headies-btn-outline headies-wishlist-share">Share List</button>
						<div class="headies-share-popover" hidden>
							<a class="headies-share-option" data-network="whatsapp" href="#" target="_blank" rel="noopener">
								<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5.1-1.3A10 10 0 1 0 12 2zm0 18.2a8.1 8.1 0 0 1-4.2-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8-.2-.1-.4-.1-.6.1s-.7.8-.9 1c-.2.2-.3.2-.6.1a6.7 6.7 0 0 1-2-1.2 7.4 7.4 0 0 1-1.4-1.7c-.1-.2 0-.4.1-.5l.4-.4.2-.4a.5.5 0 0 0 0-.4c-.1-.1-.6-1.4-.8-1.9-.2-.5-.4-.4-.6-.4h-.5a1 1 0 0 0-.7.3 3 3 0 0 0-.9 2.2c0 1.3 1 2.6 1.1 2.7.1.2 2 3 4.7 4.2a8 8 0 0 0 1.7.6 3.9 3.9 0 0 0 1.8.1c.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.2-1.2-.1-.1-.3-.2-.5-.3z"/></svg>
								WhatsApp
							</a>
							<a class="headies-share-option" data-network="facebook" href="#" target="_blank" rel="noopener">
								<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M13.5 21v-7.6h2.6l.4-3h-3v-1.9c0-.9.2-1.5 1.5-1.5h1.6V4.3A21 21 0 0 0 14.2 4c-2.3 0-3.9 1.4-3.9 4v2.4H7.7v3h2.6V21z"/></svg>
								Facebook
							</a>
							<a class="headies-share-option" data-network="twitter" href="#" target="_blank" rel="noopener">
								<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.9 3h3.1l-6.8 7.7L23 21h-6.3l-4.9-6.4L6.2 21H3.1l7.3-8.3L2.5 3h6.4l4.4 5.9zm-1.1 16.2h1.7L7.3 4.7H5.5z"/></svg>
								X
							</a>
							<a class="headies-share-option" data-network="email" href="#">
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 5h16v14H4z"/><path d="m4 6 8 7 8-7"/></svg>
								Email
							</a>
							<button type="button" class="headies-share-option headies-share-copy" data-network="copy">
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="9" y="9" width="12" height="12" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
								Copy Link
							</button>
						</div>
					</div>
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
						<?php if ( ! $headies_is_shared ) : ?>
							<p class="headies-wishlist-added">Added <?php echo esc_html( date_i18n( 'j M', $headies_added_ts ) ); ?></p>
						<?php endif; ?>

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

	<?php elseif ( $headies_share_expired ) : ?>

		<div class="headies-empty">
			<p>This shared list link has expired or is no longer available.</p>
			<a href="<?php echo esc_url( home_url( '/hats' ) ); ?>" class="headies-shop-cta">Shop Caps</a>
		</div>

	<?php else : ?>

		<div class="headies-empty">
			<p>Your wishlist is empty — tap the heart on any cap to save it here.</p>
			<a href="<?php echo esc_url( home_url( '/hats' ) ); ?>" class="headies-shop-cta">Shop Caps</a>
		</div>

	<?php endif; ?>

</div>

<?php get_footer(); ?>
