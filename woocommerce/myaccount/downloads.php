<?php
/**
 * My Account downloads — Headies child theme override.
 *
 * @package headies
 */

defined( 'ABSPATH' ) || exit;

$downloads = WC()->customer->get_downloadable_products();
?>

<h2 class="headies-section-title headies-section-title--lg">Downloads</h2>

<?php if ( ! empty( $downloads ) ) :
  do_action( 'woocommerce_before_available_downloads' ); ?>

  <table class="woocommerce-table woocommerce-table--order-downloads headies-table shop_table shop_table_responsive order_details">
    <thead>
      <tr>
        <?php foreach ( wc_get_account_downloads_columns() as $column_id => $column_name ) : ?>
          <th class="<?php echo esc_attr( $column_id ); ?>"><span class="nobr"><?php echo esc_html( $column_name ); ?></span></th>
        <?php endforeach; ?>
      </tr>
    </thead>
    <tbody>
      <?php foreach ( $downloads as $download ) : ?>
        <tr>
          <?php foreach ( wc_get_account_downloads_columns() as $column_id => $column_name ) : ?>
            <td class="<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_name ); ?>">
              <?php if ( has_action( 'woocommerce_account_downloads_column_' . $column_id ) ) : ?>
                <?php do_action( 'woocommerce_account_downloads_column_' . $column_id, $download ); ?>
              <?php elseif ( 'download-file' === $column_id ) : ?>
                <a href="<?php echo esc_url( $download['download_url'] ); ?>" class="headies-link-action"><?php echo esc_html( $download['download_name'] ); ?></a>
              <?php elseif ( 'download-remaining' === $column_id ) : ?>
                <?php echo is_numeric( $download['downloads_remaining'] ) ? esc_html( $download['downloads_remaining'] ) : esc_html__( '&infin;', 'headies' ); ?>
              <?php elseif ( 'download-expires' === $column_id ) : ?>
                <?php if ( ! empty( $download['access_expires'] ) ) : ?>
                  <time datetime="<?php echo esc_attr( date( 'Y-m-d', strtotime( $download['access_expires'] ) ) ); ?>"><?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $download['access_expires'] ) ) ); ?></time>
                <?php else : ?>
                  <?php esc_html_e( 'Never', 'headies' ); ?>
                <?php endif; ?>
              <?php elseif ( 'download-product' === $column_id ) : ?>
                <?php if ( $download['product_url'] ) : ?>
                  <a href="<?php echo esc_url( $download['product_url'] ); ?>"><?php echo esc_html( $download['product_name'] ); ?></a>
                <?php else : ?>
                  <?php echo esc_html( $download['product_name'] ); ?>
                <?php endif; ?>
              <?php endif; ?>
            </td>
          <?php endforeach; ?>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <?php do_action( 'woocommerce_after_available_downloads' ); ?>

<?php else : ?>

  <div class="headies-card headies-empty">
    <p>No downloads available yet.</p>
    <a class="headies-btn-outline" href="<?php echo esc_url( home_url( '/hats' ) ); ?>">Browse hats</a>
  </div>

<?php endif; ?>
