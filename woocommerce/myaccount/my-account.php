<?php
/**
 * My Account page — Headies child theme override.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package headies
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="headies-account">

  <div class="headies-account-header">
    <p class="headies-account-crumbs"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / My Account</p>
    <h1>My Account</h1>
  </div>

  <div class="headies-account-layout">
    <?php do_action( 'woocommerce_account_navigation' ); ?>

    <div class="woocommerce-MyAccount-content">
      <?php do_action( 'woocommerce_account_content' ); ?>
    </div>
  </div>

</div>
