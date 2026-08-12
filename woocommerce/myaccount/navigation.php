<?php
/**
 * My Account navigation — Headies child theme override.
 *
 * @package headies
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_account_navigation' );
?>

<div class="headies-account-side">

  <nav class="woocommerce-MyAccount-navigation">
    <ul>
      <?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
        <li class="<?php echo esc_attr( wc_get_account_menu_item_classes( $endpoint ) ); ?>">
          <a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>">
            <?php echo esc_html( $label ); ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </nav>

</div>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
