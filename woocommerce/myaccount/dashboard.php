<?php
/**
 * My Account dashboard — Headies child theme override.
 *
 * Combined overview (Account Details, Address book, Order History previews)
 * matching the New Era account landing page — no rewards/interests, no
 * stat-grid/greeting-card chrome, just plain content with thin dividers.
 *
 * @package headies
 */

defined( 'ABSPATH' ) || exit;

$headies_user = wp_get_current_user();

$headies_billing_address  = wc_get_account_formatted_address( 'billing' );
$headies_shipping_address = wc_ship_to_billing_address_only() ? '' : wc_get_account_formatted_address( 'shipping' );

$headies_recent_orders = wc_get_orders( array(
	'customer' => get_current_user_id(),
	'limit'    => 3,
	'orderby'  => 'date',
	'order'    => 'DESC',
) );
?>

<div class="headies-account-section">
	<h2 class="headies-account-h2">Account Details</h2>
	<div class="headies-account-plain">
		<p><?php echo esc_html( trim( $headies_user->first_name . ' ' . $headies_user->last_name ) ? trim( $headies_user->first_name . ' ' . $headies_user->last_name ) : $headies_user->display_name ); ?></p>
		<p><?php echo esc_html( $headies_user->user_email ); ?></p>
	</div>
</div>

<div class="headies-account-section">
	<div class="headies-account-section-head">
		<h2 class="headies-account-h2">Address book</h2>
		<?php echo headies_render_address_book_actions(); // phpcs:ignore ?>
	</div>
	<div class="headies-address-row">
		<div class="headies-address-block">
			<h3>Billing address</h3>
			<?php if ( $headies_billing_address ) : ?>
				<address><?php echo wp_kses_post( $headies_billing_address ); ?></address>
			<?php else : ?>
				<address>No billing address on file yet.</address>
			<?php endif; ?>
			<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', 'billing' ) ); ?>"><?php echo $headies_billing_address ? 'Edit' : 'Add'; ?></a>
		</div>
		<?php if ( ! wc_ship_to_billing_address_only() ) : ?>
			<div class="headies-address-block">
				<h3>Shipping address</h3>
				<?php if ( $headies_shipping_address ) : ?>
					<address><?php echo wp_kses_post( $headies_shipping_address ); ?></address>
				<?php else : ?>
					<address>No shipping address on file yet.</address>
				<?php endif; ?>
				<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', 'shipping' ) ); ?>"><?php echo $headies_shipping_address ? 'Edit' : 'Add'; ?></a>
			</div>
		<?php endif; ?>
	</div>
</div>

<div class="headies-account-section">
	<h2 class="headies-account-h2">Order History</h2>
	<?php if ( ! empty( $headies_recent_orders ) ) : ?>
		<?php foreach ( $headies_recent_orders as $headies_order ) :
			$headies_items = $headies_order->get_items();
			$headies_item  = ! empty( $headies_items ) ? reset( $headies_items ) : null;
			$headies_thumb = $headies_item ? $headies_item->get_product() : null;
			?>
			<div class="headies-order-preview-row">
				<div class="headies-order-preview-thumb">
					<?php echo $headies_thumb ? wp_kses_post( $headies_thumb->get_image( 'woocommerce_thumbnail' ) ) : ''; ?>
				</div>
				<div class="headies-order-preview-meta">
					<p class="headies-order-preview-name">
						<a href="<?php echo esc_url( $headies_order->get_view_order_url() ); ?>">#<?php echo esc_html( $headies_order->get_order_number() ); ?></a>
						&mdash; <?php echo $headies_item ? esc_html( $headies_item->get_name() ) : ''; ?>
					</p>
					<p class="headies-order-preview-sub"><?php echo esc_html( wc_format_datetime( $headies_order->get_date_created() ) ); ?></p>
				</div>
				<span class="headies-status headies-status--<?php echo esc_attr( $headies_order->get_status() ); ?>">
					<?php echo esc_html( wc_get_order_status_name( $headies_order->get_status() ) ); ?>
				</span>
				<span class="headies-order-preview-total"><?php echo wp_kses_post( $headies_order->get_formatted_order_total() ); ?></span>
			</div>
		<?php endforeach; ?>
		<a class="headies-account-view-all" href="<?php echo esc_url( wc_get_endpoint_url( 'orders' ) ); ?>">View all orders</a>
	<?php else : ?>
		<p class="headies-account-plain">You haven't placed any orders yet.</p>
	<?php endif; ?>
</div>

<?php do_action( 'woocommerce_account_dashboard' ); ?>
