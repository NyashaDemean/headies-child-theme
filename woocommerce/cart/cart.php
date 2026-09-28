<?php
/**
 * Headies cart page — overrides woocommerce/templates/cart/cart.php.
 *
 * @see https://woocommerce.com/document/template-structure/
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' );
?>

<div class="headies-cart-page">

	<div class="headies-account-header">
		<p class="headies-account-crumbs"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / Cart</p>
		<h1>Cart</h1>
	</div>

	<div class="headies-cart-layout">

		<div class="headies-cart-main">

			<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
				<?php do_action( 'woocommerce_before_cart_table' ); ?>

				<table class="headies-cart-table">
					<thead>
						<tr>
							<th class="col-item"><?php esc_html_e( 'Item', 'woocommerce' ); ?></th>
							<th class="col-price"><?php esc_html_e( 'Price', 'woocommerce' ); ?></th>
							<th class="col-qty"><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></th>
							<th class="col-total"><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php do_action( 'woocommerce_before_cart_contents' ); ?>

						<?php
						foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
							$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
							$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
							$visible    = apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key );

							if ( ! ( $_product instanceof WC_Product ) || ! $_product->exists() || $cart_item['quantity'] <= 0 || ! $visible ) {
								continue;
							}

							$product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
							$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
							$thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'woocommerce_thumbnail' ), $cart_item, $cart_item_key );

							if ( $_product->is_sold_individually() ) {
								$min_quantity = 1;
								$max_quantity = 1;
							} else {
								$min_quantity = 0;
								$max_quantity = $_product->get_max_purchase_quantity();
							}
							?>
							<tr class="headies-cart-row <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

								<td class="col-item" data-title="Item">
									<div class="headies-cart-item">
										<div class="headies-cart-item-image">
											<?php if ( $product_permalink ) : ?>
												<a href="<?php echo esc_url( $product_permalink ); ?>"><?php echo $thumbnail; // phpcs:ignore ?></a>
											<?php else : ?>
												<?php echo $thumbnail; // phpcs:ignore ?>
											<?php endif; ?>
										</div>
										<div class="headies-cart-item-info">
											<span class="headies-cart-item-name">
												<?php
												if ( $product_permalink ) {
													echo wp_kses_post( sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ) );
												} else {
													echo wp_kses_post( $product_name );
												}
												?>
											</span>
											<?php echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore ?>
											<?php
											echo apply_filters( // phpcs:ignore
												'woocommerce_cart_item_remove_link',
												sprintf(
													'<a role="button" href="%s" class="headies-cart-remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">%s</a>',
													esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
													esc_attr( sprintf( __( 'Remove %s from cart', 'woocommerce' ), wp_strip_all_tags( $product_name ) ) ),
													esc_attr( $product_id ),
													esc_attr( $_product->get_sku() ),
													esc_html__( 'Remove', 'headies' )
												),
												$cart_item_key
											);
											?>
										</div>
									</div>
								</td>

								<td class="col-price" data-title="Price">
									<?php echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); // phpcs:ignore ?>
								</td>

								<td class="col-qty" data-title="Quantity">
									<div class="headies-qty-stepper">
										<button type="button" class="qty-btn qty-minus" aria-label="Decrease quantity">&minus;</button>
										<?php
										echo apply_filters( // phpcs:ignore
											'woocommerce_cart_item_quantity',
											woocommerce_quantity_input(
												array(
													'input_name'   => "cart[{$cart_item_key}][qty]",
													'input_value'  => $cart_item['quantity'],
													'max_value'    => $max_quantity,
													'min_value'    => $min_quantity,
													'product_name' => $product_name,
												),
												$_product,
												false
											),
											$cart_item_key,
											$cart_item
										);
										?>
										<button type="button" class="qty-btn qty-plus" aria-label="Increase quantity">+</button>
									</div>
								</td>

								<td class="col-total" data-title="Total">
									<?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // phpcs:ignore ?>
								</td>
							</tr>
							<?php
						}
						?>

						<?php do_action( 'woocommerce_cart_contents' ); ?>
						<?php do_action( 'woocommerce_after_cart_contents' ); ?>
					</tbody>
				</table>

				<?php do_action( 'woocommerce_after_cart_table' ); ?>

				<div class="headies-cart-footer-row">
					<?php if ( wc_coupons_enabled() ) : ?>
						<div class="headies-cart-coupon">
							<label for="coupon_code" class="screen-reader-text"><?php esc_html_e( 'Coupon:', 'woocommerce' ); ?></label>
							<input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Promo code', 'woocommerce' ); ?>" />
							<button type="submit" class="headies-btn-outline headies-btn-outline--sm" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>"><?php esc_html_e( 'Apply', 'woocommerce' ); ?></button>
							<?php do_action( 'woocommerce_cart_coupon' ); ?>
						</div>
					<?php endif; ?>

					<a href="<?php echo esc_url( home_url( '/hats' ) ); ?>" class="headies-link-action headies-continue-shopping"><?php esc_html_e( 'Continue Shopping', 'woocommerce' ); ?></a>

					<button type="submit" class="headies-cart-update-btn" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>"><?php esc_html_e( 'Update cart', 'woocommerce' ); ?></button>

					<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
				</div>

				<?php do_action( 'woocommerce_cart_actions' ); ?>
			</form>

		</div>

		<div class="headies-cart-sidebar">
			<?php do_action( 'woocommerce_cart_collaterals' ); ?>
		</div>

	</div>

</div>

<?php do_action( 'woocommerce_after_cart' ); ?>
