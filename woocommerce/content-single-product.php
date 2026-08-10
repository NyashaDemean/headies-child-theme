<?php
/**
 * Headies single product content — overrides
 * woocommerce/templates/content-single-product.php.
 *
 * @see https://woocommerce.com/document/template-structure/
 */

defined( 'ABSPATH' ) || exit;

global $product;

do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // phpcs:ignore
	return;
}

$gallery_ids = $product->get_gallery_image_ids();
$image_ids   = array_values( array_filter( array_merge( array( $product->get_image_id() ), $gallery_ids ) ) );
$main_image  = $image_ids ? wp_get_attachment_image_url( $image_ids[0], 'woocommerce_single' ) : wc_placeholder_img_src( 'woocommerce_single' );
$drop             = headies_get_product_drop( $product->get_id() );
$is_upcoming_drop = $drop && 'upcoming' === $drop->status;
$cat_terms   = get_the_terms( $product->get_id(), 'product_cat' );
$cat_names   = ( $cat_terms && ! is_wp_error( $cat_terms ) ) ? wp_list_pluck( $cat_terms, 'name' ) : array();
$eyebrow     = implode( ' · ', array_diff( $cat_names, array( 'Hats' ) ) );
// Simple products can carry an optional, non-variation "Size" custom
// attribute (comma-separated) for the rare multi-size item. Everything
// else in the catalog is one-size, so that's the default shown.
$size_attr   = $product->get_attribute( 'size' );
$size_values = $size_attr ? array_map( 'trim', explode( ',', $size_attr ) ) : array( 'One Size' );
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?>>

	<nav class="headies-product-crumbs">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> /
		<a href="<?php echo esc_url( home_url( '/hats' ) ); ?>">Hats</a> /
		<?php the_title(); ?>
	</nav>

	<div class="headies-product-layout">

		<div class="headies-gallery">
			<?php if ( count( $image_ids ) > 1 ) : ?>
				<div class="headies-gallery-thumbs">
					<?php foreach ( $image_ids as $index => $image_id ) : ?>
						<button type="button"
							class="headies-gallery-thumb<?php echo 0 === $index ? ' is-active' : ''; ?>"
							data-full="<?php echo esc_url( wp_get_attachment_image_url( $image_id, 'woocommerce_single' ) ); ?>">
							<?php echo wp_get_attachment_image( $image_id, 'woocommerce_thumbnail' ); ?>
						</button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<div class="headies-gallery-main">
				<?php if ( $drop ) : ?>
					<span class="headies-gallery-badge"><?php echo esc_html( $drop->name ); ?> Drop</span>
				<?php endif; ?>
				<?php if ( $is_upcoming_drop ) : ?>
					<span class="headies-gallery-badge headies-gallery-badge--soon"><?php echo esc_html( $drop->date ); ?></span>
				<?php endif; ?>
				<?php headies_wishlist_button( get_the_ID() ); ?>
				<img class="headies-gallery-image" src="<?php echo esc_url( $main_image ); ?>" alt="<?php the_title_attribute(); ?>">
			</div>
		</div>

		<div class="summary entry-summary">

			<?php if ( $eyebrow ) : ?>
				<p class="headies-product-eyebrow"><?php echo esc_html( strtoupper( $eyebrow ) ); ?></p>
			<?php endif; ?>

			<h1 class="product_title entry-title"><?php the_title(); ?></h1>

			<p class="price"><?php echo wp_kses_post( $product->get_price_html() ); ?></p>
			<p class="headies-product-payline">
				Delivery in Harare in 1–2 days via courier, or collect free at any Melusi Home Designs branch.
			</p>

			<?php if ( $size_values && ! $is_upcoming_drop ) : ?>
				<div class="headies-size-block">
					<div class="headies-size-head">
						<p class="headies-size-label">Size</p>
						<?php if ( count( $size_values ) > 1 ) : ?>
							<a href="<?php echo esc_url( home_url( '/size-guide' ) ); ?>">Size guide</a>
						<?php endif; ?>
					</div>
					<div class="headies-size-grid">
						<?php foreach ( $size_values as $i => $size ) : ?>
							<button type="button" class="headies-size<?php echo 0 === $i ? ' is-active' : ''; ?>" data-size="<?php echo esc_attr( $size ); ?>">
								<?php echo esc_html( $size ); ?>
							</button>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( $is_upcoming_drop ) : ?>
				<div class="headies-drop-notice">
					<p><strong><?php echo esc_html( $drop->name ); ?></strong> <?php echo esc_html( lcfirst( $drop->date ) ); ?> — add it to your wishlist now and we'll have it ready to buy the moment it's live.</p>
				</div>
			<?php else : ?>
				<?php
				/**
				 * Woo's own add-to-cart form (quantity + button, or the variation form
				 * for variable products). Styled by the CSS below; the size buttons
				 * above drive its variation select via single-product.js.
				 */
				woocommerce_template_single_add_to_cart();
				?>
			<?php endif; ?>

			<div class="headies-product-assurance">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 3l8 4v5c0 5-3.5 8-8 9-4.5-1-8-4-8-9V7z"/></svg>
				<p>Each bedazzled cap is finished by hand, so no two are identical. Custom drop pieces are final sale.</p>
			</div>

			<div class="headies-accordion">
				<?php
				$panels = array(
					'Details'                => $product->get_description(),
					'Fit &amp; care'         => get_post_meta( $product->get_id(), '_headies_fit_care', true ),
					'Delivery &amp; returns' => get_post_meta( $product->get_id(), '_headies_delivery', true ),
				);
				$first = true;
				foreach ( $panels as $title => $body ) :
					if ( ! $body ) {
						continue;
					}
					?>
					<div class="headies-accordion-item<?php echo $first ? ' is-open' : ''; ?>">
						<button type="button" class="headies-accordion-toggle">
							<span><?php echo wp_kses_post( $title ); ?></span>
							<span class="headies-accordion-marker" aria-hidden="true"><?php echo $first ? '–' : '+'; ?></span>
						</button>
						<div class="headies-accordion-body"><div class="headies-accordion-body-inner"><?php echo wp_kses_post( wpautop( $body ) ); ?></div></div>
					</div>
					<?php
					$first = false;
				endforeach;
				?>
			</div>

		</div><!-- .summary -->
	</div><!-- .headies-product-layout -->

	<?php
	$related_ids = wc_get_related_products( $product->get_id(), 4 );
	if ( $related_ids ) :
		?>
		<section class="headies-related">
			<h2>You might also like</h2>
			<div class="headies-related-grid">
				<?php
				foreach ( $related_ids as $related_id ) :
					$related = wc_get_product( $related_id );
					if ( ! $related ) {
						continue;
					}
					?>
					<a href="<?php echo esc_url( get_permalink( $related_id ) ); ?>" class="headies-related-card">
						<div class="headies-related-image">
							<img src="<?php echo esc_url( get_the_post_thumbnail_url( $related_id, 'woocommerce_single' ) ); ?>" alt="<?php echo esc_attr( $related->get_name() ); ?>">
						</div>
						<div class="headies-related-info">
							<span class="headies-related-name"><?php echo esc_html( $related->get_name() ); ?></span>
							<span class="headies-related-price"><?php echo wp_kses_post( wc_price( $related->get_price() ) ); ?></span>
						</div>
					</a>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>

	<div class="headies-bag-bar">
		<div class="headies-bag-bar-inner">
			<div class="headies-bag-thumb"><img src="" alt=""></div>
			<div>
				<p class="headies-bag-line"></p>
				<p class="headies-bag-total"></p>
			</div>
			<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="headies-bag-checkout">Checkout</a>
		</div>
	</div>

</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>
