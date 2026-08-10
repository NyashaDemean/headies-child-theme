<?php
/**
 * My Account dashboard — Headies child theme override.
 *
 * @package headies
 */

defined( 'ABSPATH' ) || exit;

$headies_user       = get_user_by( 'id', get_current_user_id() );
$headies_first_name = $headies_user ? $headies_user->first_name : '';
$headies_name       = $headies_first_name ? $headies_first_name : ( $headies_user ? $headies_user->display_name : '' );
$headies_order_count = wc_get_customer_order_count( get_current_user_id() );

$headies_recent = wc_get_orders( array(
  'customer' => get_current_user_id(),
  'limit'    => 1,
  'orderby'  => 'date',
  'order'    => 'DESC',
) );
?>

<div class="headies-card headies-account-greeting">
  <div>
    <h2>Hi <?php echo esc_html( $headies_name ); ?></h2>
    <p>
      <?php
      printf(
        /* translators: %s: logout url */
        wp_kses_post( __( 'Not %1$s? <a href="%2$s">Log out</a>. From here you can track orders, manage addresses and edit your details.', 'headies' ) ),
        esc_html( $headies_name ),
        esc_url( wc_logout_url() )
      );
      ?>
    </p>
  </div>
  <a class="headies-shop-cta" href="<?php echo esc_url( home_url( '/hats' ) ); ?>">Shop caps</a>
</div>

<div class="headies-stat-grid">
  <div class="headies-card headies-stat">
    <p class="stat-label">Orders</p>
    <p class="stat-value"><?php echo esc_html( $headies_order_count ); ?></p>
  </div>
  <div class="headies-card headies-stat">
    <p class="stat-label">Downloads</p>
    <p class="stat-value"><?php echo esc_html( count( (array) WC()->customer->get_downloadable_products() ) ); ?></p>
  </div>
  <div class="headies-card headies-stat">
    <p class="stat-label">Drop access</p>
    <p class="stat-value">Early</p>
  </div>
</div>

<?php if ( ! empty( $headies_recent ) ) :
  $headies_order = $headies_recent[0];
  $headies_items = $headies_order->get_items();
  $headies_item  = ! empty( $headies_items ) ? reset( $headies_items ) : null;
  $headies_thumb = $headies_item ? $headies_item->get_product() : null;
?>
  <h3 class="headies-section-title">Recent order</h3>
  <div class="headies-card headies-recent-order">
    <div class="recent-thumb">
      <?php echo $headies_thumb ? wp_kses_post( $headies_thumb->get_image( 'woocommerce_thumbnail' ) ) : ''; ?>
    </div>
    <div class="recent-meta">
      <p class="recent-name"><?php echo $headies_item ? esc_html( $headies_item->get_name() ) : ''; ?></p>
      <p class="recent-sub">
        #<?php echo esc_html( $headies_order->get_order_number() ); ?> &middot;
        <?php echo esc_html( wc_format_datetime( $headies_order->get_date_created() ) ); ?> &middot;
        <?php echo esc_html( $headies_order->get_item_count() ); ?> item(s)
      </p>
    </div>
    <span class="headies-status headies-status--<?php echo esc_attr( $headies_order->get_status() ); ?>">
      <?php echo esc_html( wc_get_order_status_name( $headies_order->get_status() ) ); ?>
    </span>
    <span class="recent-total"><?php echo wp_kses_post( $headies_order->get_formatted_order_total() ); ?></span>
  </div>
<?php endif; ?>

<?php do_action( 'woocommerce_account_dashboard' ); ?>
