<?php
/**
 * Edit account form — Headies child theme override.
 *
 * @package headies
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_edit_account_form' );
?>

<form class="woocommerce-EditAccountForm edit-account headies-form" action="" method="post" <?php do_action( 'woocommerce_edit_account_form_tag' ); ?>>

  <?php do_action( 'woocommerce_edit_account_form_start' ); ?>

  <div class="headies-form-grid">
    <p class="woocommerce-form-row form-row">
      <label for="account_first_name"><?php esc_html_e( 'First name', 'headies' ); ?>&nbsp;<span class="required">*</span></label>
      <input type="text" class="woocommerce-Input input-text" name="account_first_name" id="account_first_name" autocomplete="given-name" value="<?php echo esc_attr( $user->first_name ); ?>" />
    </p>
    <p class="woocommerce-form-row form-row">
      <label for="account_last_name"><?php esc_html_e( 'Last name', 'headies' ); ?>&nbsp;<span class="required">*</span></label>
      <input type="text" class="woocommerce-Input input-text" name="account_last_name" id="account_last_name" autocomplete="family-name" value="<?php echo esc_attr( $user->last_name ); ?>" />
    </p>
    <p class="woocommerce-form-row form-row form-row--wide">
      <label for="account_display_name"><?php esc_html_e( 'Display name', 'headies' ); ?>&nbsp;<span class="required">*</span></label>
      <input type="text" class="woocommerce-Input input-text" name="account_display_name" id="account_display_name" value="<?php echo esc_attr( $user->display_name ); ?>" />
    </p>
    <p class="woocommerce-form-row form-row form-row--wide">
      <label for="account_email"><?php esc_html_e( 'Email address', 'headies' ); ?>&nbsp;<span class="required">*</span></label>
      <input type="email" class="woocommerce-Input input-text" name="account_email" id="account_email" autocomplete="email" value="<?php echo esc_attr( $user->user_email ); ?>" />
    </p>
  </div>

  <p class="headies-account-password-trigger">
    <button type="button" class="headies-link-action" id="headies-open-password-modal">Change password</button>
  </p>

  <?php do_action( 'woocommerce_edit_account_form' ); ?>

  <p class="headies-form-actions">
    <?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
    <button type="submit" class="headies-btn-primary" name="save_account_details" value="<?php esc_attr_e( 'Save changes', 'headies' ); ?>"><?php esc_html_e( 'Save changes', 'headies' ); ?></button>
    <input type="hidden" name="action" value="save_account_details" />
  </p>

  <!-- Password fields stay inside this same form (WooCommerce's native
       save_account_details handler expects them here) — they're just
       presented as a popout instead of always-visible inline fields. -->
  <div class="headies-modal" id="headies-password-modal" hidden>
    <div class="headies-modal-overlay" data-modal-close></div>
    <div class="headies-modal-panel" role="dialog" aria-modal="true" aria-labelledby="headies-password-modal-title">
      <button type="button" class="headies-modal-close" data-modal-close aria-label="Close">&times;</button>
      <h2 id="headies-password-modal-title" class="headies-account-h2" style="font-size:24px;">Change password</h2>
      <div class="headies-form-grid">
        <p class="woocommerce-form-row form-row form-row--wide">
          <label for="password_current"><?php esc_html_e( 'Current password', 'headies' ); ?></label>
          <input type="password" class="woocommerce-Input input-text" name="password_current" id="password_current" autocomplete="off" />
        </p>
        <p class="woocommerce-form-row form-row form-row--wide">
          <label for="password_1"><?php esc_html_e( 'New password', 'headies' ); ?></label>
          <input type="password" class="woocommerce-Input input-text" name="password_1" id="password_1" autocomplete="off" />
        </p>
        <p class="woocommerce-form-row form-row form-row--wide">
          <label for="password_2"><?php esc_html_e( 'Confirm new password', 'headies' ); ?></label>
          <input type="password" class="woocommerce-Input input-text" name="password_2" id="password_2" autocomplete="off" />
        </p>
      </div>
      <p class="headies-modal-hint">Leave these blank if you don't want to change your password. They'll be saved together with the rest of your details when you hit Save changes.</p>
      <button type="button" class="headies-btn-primary" data-modal-close>Done</button>
    </div>
  </div>

  <?php do_action( 'woocommerce_edit_account_form_end' ); ?>
</form>

<script>
document.addEventListener( 'DOMContentLoaded', function () {
  var openBtn = document.getElementById( 'headies-open-password-modal' );
  var modal   = document.getElementById( 'headies-password-modal' );
  if ( ! openBtn || ! modal ) {
    return;
  }
  function open() {
    modal.hidden = false;
    document.body.style.overflow = 'hidden';
  }
  function close() {
    modal.hidden = true;
    document.body.style.overflow = '';
  }
  openBtn.addEventListener( 'click', open );
  modal.querySelectorAll( '[data-modal-close]' ).forEach( function ( el ) {
    el.addEventListener( 'click', close );
  } );
  document.addEventListener( 'keydown', function ( e ) {
    if ( 'Escape' === e.key && ! modal.hidden ) {
      close();
    }
  } );
} );
</script>

<?php do_action( 'woocommerce_after_edit_account_form' ); ?>
