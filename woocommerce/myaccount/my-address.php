<?php
/**
 * My Account addresses — Headies child theme override.
 *
 * @package headies
 */

defined( 'ABSPATH' ) || exit;

$customer_id = get_current_user_id();

if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) {
  $get_addresses = apply_filters( 'woocommerce_my_account_get_addresses', array(
    'billing'  => __( 'Billing address', 'headies' ),
    'shipping' => __( 'Shipping address', 'headies' ),
  ), $customer_id );
} else {
  $get_addresses = apply_filters( 'woocommerce_my_account_get_addresses', array(
    'billing' => __( 'Billing address', 'headies' ),
  ), $customer_id );
}
?>

<h2 class="headies-section-title headies-section-title--lg">Addresses</h2>
<p class="headies-account-intro"><?php echo apply_filters( 'woocommerce_my_account_my_address_description', esc_html__( 'The following addresses will be used on the checkout page by default.', 'headies' ) ); ?></p>

<div class="headies-address-grid">
  <?php foreach ( $get_addresses as $name => $address_title ) :
    $address = wc_get_account_formatted_address( $name );
    ?>
    <div class="headies-card headies-address">
      <h3><?php echo esc_html( $address_title ); ?></h3>
      <address><?php echo $address ? wp_kses_post( $address ) : esc_html__( 'You have not set up this type of address yet.', 'headies' ); ?></address>
      <a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', $name ) ); ?>" class="headies-btn-outline headies-btn-outline--sm">
        <?php echo $address ? esc_html__( 'Edit', 'headies' ) : esc_html__( 'Add', 'headies' ); ?>
      </a>
    </div>
  <?php endforeach; ?>
</div>
