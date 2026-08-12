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

<div class="headies-address-row">
  <?php foreach ( $get_addresses as $name => $address_title ) :
    $address = wc_get_account_formatted_address( $name );
    ?>
    <div class="headies-address-block">
      <h3><?php echo esc_html( $address_title ); ?></h3>
      <address><?php echo $address ? wp_kses_post( $address ) : esc_html__( 'You have not set up this type of address yet.', 'headies' ); ?></address>
      <a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', $name ) ); ?>">
        <?php echo $address ? esc_html__( 'Edit', 'headies' ) : esc_html__( 'Add', 'headies' ); ?>
      </a>
    </div>
  <?php endforeach; ?>
</div>
