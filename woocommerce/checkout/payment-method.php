<?php
/**
 * Headies single payment method row — overrides
 * woocommerce/templates/checkout/payment-method.php.
 */

defined( 'ABSPATH' ) || exit;
?>
<li class="wc_payment_method payment_method_<?php echo esc_attr( $gateway->id ); ?> headies-payment-option <?php echo $gateway->chosen ? 'is-selected' : ''; ?>">
	<label for="payment_method_<?php echo esc_attr( $gateway->id ); ?>">
		<input id="payment_method_<?php echo esc_attr( $gateway->id ); ?>" type="radio" class="input-radio" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?> data-order_button_text="<?php echo esc_attr( $gateway->order_button_text ); ?>" />
		<span class="headies-payment-option-info">
			<span class="headies-payment-option-name"><?php echo wp_kses_post( $gateway->get_title() ); ?></span>
			<?php if ( $gateway->get_icon() ) : ?>
				<span class="headies-payment-option-icon"><?php echo wp_kses_post( $gateway->get_icon() ); ?></span>
			<?php endif; ?>
		</span>
	</label>
	<?php if ( $gateway->has_fields() || $gateway->get_description() ) : ?>
		<div class="payment_box payment_method_<?php echo esc_attr( $gateway->id ); ?>" <?php if ( ! $gateway->chosen ) : ?>style="display:none;"<?php endif; ?>>
			<?php $gateway->payment_fields(); ?>
		</div>
	<?php endif; ?>
</li>
