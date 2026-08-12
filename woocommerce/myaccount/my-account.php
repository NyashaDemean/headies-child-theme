<?php
/**
 * My Account page — Headies child theme override.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package headies
 */

defined( 'ABSPATH' ) || exit;

$headies_endpoint      = WC()->query->get_current_endpoint();
$headies_is_dashboard  = ! $headies_endpoint;
$headies_is_addresses  = 'edit-address' === $headies_endpoint;
$headies_user          = wp_get_current_user();
$headies_first_name    = $headies_user->first_name ? $headies_user->first_name : $headies_user->display_name;
?>

<div class="headies-account">

  <div class="headies-account-layout">
    <?php do_action( 'woocommerce_account_navigation' ); ?>

    <div class="headies-account-header">
      <div class="headies-account-header-titles">
        <h1><?php echo esc_html( headies_account_page_title() ); ?></h1>
        <?php if ( $headies_is_dashboard ) : ?>
          <p class="headies-account-welcome">Welcome back, <?php echo esc_html( $headies_first_name ); ?></p>
        <?php endif; ?>
      </div>
      <?php if ( $headies_is_addresses ) : ?>
        <?php echo headies_render_address_book_actions(); // phpcs:ignore ?>
      <?php endif; ?>
    </div>

    <div class="woocommerce-MyAccount-content">
      <?php do_action( 'woocommerce_account_content' ); ?>
    </div>
  </div>

</div>
