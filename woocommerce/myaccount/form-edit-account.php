<?php
/**
 * Edit account form — Headies child theme override.
 *
 * @package headies
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_edit_account_form' );
?>

<h2 class="headies-section-title headies-section-title--lg">Account details</h2>

<form class="woocommerce-EditAccountForm edit-account headies-card headies-form" action="" method="post" <?php do_action( 'woocommerce_edit_account_form_tag' ); ?>>

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

  <fieldset class="headies-fieldset">
    <legend><?php esc_html_e( 'Password change', 'headies' ); ?></legend>
    <div class="headies-form-grid">
      <p class="woocommerce-form-row form-row form-row--wide">
        <label for="password_current"><?php esc_html_e( 'Current password (leave blank to leave unchanged)', 'headies' ); ?></label>
        <input type="password" class="woocommerce-Input input-text" name="password_current" id="password_current" autocomplete="off" />
      </p>
      <p class="woocommerce-form-row form-row form-row--wide">
        <label for="password_1"><?php esc_html_e( 'New password (leave blank to leave unchanged)', 'headies' ); ?></label>
        <input type="password" class="woocommerce-Input input-text" name="password_1" id="password_1" autocomplete="off" />
      </p>
      <p class="woocommerce-form-row form-row form-row--wide">
        <label for="password_2"><?php esc_html_e( 'Confirm new password', 'headies' ); ?></label>
        <input type="password" class="woocommerce-Input input-text" name="password_2" id="password_2" autocomplete="off" />
      </p>
    </div>
  </fieldset>

  <?php do_action( 'woocommerce_edit_account_form' ); ?>

  <p class="headies-form-actions">
    <?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
    <button type="submit" class="headies-btn-primary" name="save_account_details" value="<?php esc_attr_e( 'Save changes', 'headies' ); ?>"><?php esc_html_e( 'Save changes', 'headies' ); ?></button>
    <input type="hidden" name="action" value="save_account_details" />
  </p>

  <?php do_action( 'woocommerce_edit_account_form_end' ); ?>
</form>

<?php do_action( 'woocommerce_after_edit_account_form' ); ?>
