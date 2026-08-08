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
            <span><?php echo esc_html( $label ); ?></span>
            <span class="nav-arrow" aria-hidden="true">&rarr;</span>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </nav>

  <?php
  // Promote the current or next drop, using the same data source as the homepage.
  if ( function_exists( 'headies_get_drops' ) ) :
    $headies_account_drops = headies_get_drops();
    $headies_account_drop  = null;
    foreach ( $headies_account_drops as $d ) {
      if ( in_array( $d['status'], array( 'live', 'upcoming' ), true ) ) { $headies_account_drop = $d; break; }
    }
    if ( $headies_account_drop ) : ?>
      <div class="headies-account-dropcard">
        <p class="dropcard-title"><?php echo 'live' === $headies_account_drop['status'] ? 'Live now' : 'Next drop'; ?></p>
        <p class="dropcard-body">
          <?php echo esc_html( $headies_account_drop['name'] ); ?><?php if ( ! empty( $headies_account_drop['date'] ) ) : ?> &middot; <?php echo esc_html( $headies_account_drop['date'] ); ?><?php endif; ?>
        </p>
      </div>
    <?php endif;
  endif;
  ?>

</div>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
