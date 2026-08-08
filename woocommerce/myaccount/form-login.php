<?php
/**
 * Custom Login Form - Headies
 * Overrides woocommerce/templates/myaccount/form-login.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hide_register_link = 'no' === get_option( 'woocommerce_enable_myaccount_registration' ) ? true : false;
$show_reg_password   = 'no' === get_option( 'woocommerce_registration_generate_password' );
?>

<div class="headies-login-wrapper">

	<div class="headies-login-image">
		<img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/images/' . rawurlencode( 'website images' ) . '/my-account-login.jpg' ); ?>" alt="" class="headies-login-photo">
	</div>

	<div class="headies-login-form-panel">

		<div class="headies-login-view" id="login">

			<h1 class="headies-login-heading"><?php esc_html_e( 'Welcome Back', 'headies-child' ); ?></h1>
			<p class="headies-login-subtext"><?php esc_html_e( 'Enter your info below to access your account', 'headies-child' ); ?></p>

			<?php do_action( 'woocommerce_login_form_start' ); ?>

			<form class="woocommerce-form woocommerce-form-login login headies-login-form" method="post">

				<p class="form-row form-row-wide">
					<label class="screen-reader-text" for="username"><?php esc_html_e( 'Email address', 'headies-child' ); ?></label>
					<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" placeholder="<?php esc_attr_e( 'Email', 'headies-child' ); ?>" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" />
				</p>

				<p class="form-row form-row-wide">
					<label class="screen-reader-text" for="password"><?php esc_html_e( 'Password', 'headies-child' ); ?></label>
					<input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" placeholder="<?php esc_attr_e( 'Password', 'headies-child' ); ?>" />
				</p>

				<?php do_action( 'woocommerce_login_form' ); ?>

				<p class="form-row headies-login-forgot">
					<?php if ( wc_get_page_id( 'myaccount' ) ) : ?>
						<a class="woocommerce-LostPassword lost_password" href="<?php echo esc_url( wp_lostpassword_url() ); ?>">
							<?php esc_html_e( 'Forgot your password?', 'headies-child' ); ?>
						</a>
					<?php endif; ?>
				</p>

				<p class="form-row">
					<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
					<button type="submit" class="woocommerce-button button woocommerce-form-login__submit headies-login-button" name="login" value="<?php esc_attr_e( 'Login', 'headies-child' ); ?>">
						<?php esc_html_e( 'Login', 'headies-child' ); ?>
					</button>
				</p>

			</form>

			<?php do_action( 'woocommerce_login_form_end' ); ?>

			<?php if ( ! $hide_register_link ) : ?>
				<p class="headies-login-register-link">
					<?php esc_html_e( "Don't have an account?", 'headies-child' ); ?>
					<a href="#register"><?php esc_html_e( 'Create account', 'headies-child' ); ?></a>
				</p>
			<?php endif; ?>

		</div>

		<?php if ( ! $hide_register_link ) : ?>

			<div class="headies-login-view headies-register-view" id="register">

				<h1 class="headies-login-heading"><?php esc_html_e( 'Create an Account', 'headies-child' ); ?></h1>
				<p class="headies-login-subtext"><?php esc_html_e( 'Enter your info below to create your account', 'headies-child' ); ?></p>

				<?php do_action( 'woocommerce_register_form_start' ); ?>

				<form method="post" class="woocommerce-form woocommerce-form-register register headies-login-form" <?php do_action( 'woocommerce_register_form_tag' ); ?>>

					<p class="form-row form-row-wide">
						<label class="screen-reader-text" for="reg_first_name"><?php esc_html_e( 'First name', 'headies-child' ); ?></label>
						<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="reg_first_name" id="reg_first_name" placeholder="<?php esc_attr_e( 'First name', 'headies-child' ); ?>" value="<?php echo ( ! empty( $_POST['reg_first_name'] ) ) ? esc_attr( wp_unslash( $_POST['reg_first_name'] ) ) : ''; ?>" />
					</p>

					<p class="form-row form-row-wide">
						<label class="screen-reader-text" for="reg_last_name"><?php esc_html_e( 'Last name', 'headies-child' ); ?></label>
						<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="reg_last_name" id="reg_last_name" placeholder="<?php esc_attr_e( 'Last name', 'headies-child' ); ?>" value="<?php echo ( ! empty( $_POST['reg_last_name'] ) ) ? esc_attr( wp_unslash( $_POST['reg_last_name'] ) ) : ''; ?>" />
					</p>

					<p class="form-row form-row-wide">
						<label class="screen-reader-text" for="reg_email"><?php esc_html_e( 'Email address', 'headies-child' ); ?></label>
						<input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" placeholder="<?php esc_attr_e( 'Email', 'headies-child' ); ?>" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" />
					</p>

					<?php if ( $show_reg_password ) : ?>
						<p class="form-row form-row-wide headies-login-password-row">
							<label class="screen-reader-text" for="reg_password"><?php esc_html_e( 'Password', 'headies-child' ); ?></label>
							<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" placeholder="<?php esc_attr_e( 'Password', 'headies-child' ); ?>" />
						</p>
						<p class="form-row headies-login-show-password">
							<label>
								<input type="checkbox" class="headies-show-password-toggle" data-target="reg_password" />
								<?php esc_html_e( 'Show Password', 'headies-child' ); ?>
							</label>
						</p>
					<?php else : ?>
						<p><?php esc_html_e( 'A link to set a new password will be sent to your email address.', 'headies-child' ); ?></p>
					<?php endif; ?>

					<?php do_action( 'woocommerce_register_form' ); ?>

					<p class="form-row">
						<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
						<button type="submit" class="woocommerce-Button woocommerce-button button woocommerce-form-register__submit headies-login-button" name="register" value="<?php esc_attr_e( 'Create Your Account', 'headies-child' ); ?>">
							<?php esc_html_e( 'Create Your Account', 'headies-child' ); ?>
						</button>
					</p>

				</form>

				<?php do_action( 'woocommerce_register_form_end' ); ?>

				<p class="headies-login-register-link">
					<?php esc_html_e( 'Already have an account?', 'headies-child' ); ?>
					<a href="#login"><?php esc_html_e( 'Sign in now', 'headies-child' ); ?></a>
				</p>

			</div>

		<?php endif; ?>

	</div>

</div>

<?php if ( ! $hide_register_link ) : ?>
<script>
document.addEventListener( 'DOMContentLoaded', function () {
	// Swap the Login/Create Account views without letting the #register /
	// #login anchor trigger the browser's native scroll-to-target jump —
	// the CSS :target rule stays in place as a no-JS fallback.
	var loginView    = document.getElementById( 'login' );
	var registerView = document.getElementById( 'register' );

	function showView( view, hide ) {
		if ( hide ) {
			hide.style.display = 'none';
		}
		if ( view ) {
			view.style.display = 'block';
		}
	}

	document.querySelectorAll( 'a[href="#register"]' ).forEach( function ( link ) {
		link.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			showView( registerView, loginView );
			history.replaceState( null, '', '#register' );
		} );
	} );

	document.querySelectorAll( 'a[href="#login"]' ).forEach( function ( link ) {
		link.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			showView( loginView, registerView );
			history.replaceState( null, '', '#login' );
		} );
	} );

	<?php if ( $show_reg_password ) : ?>
	var toggle = document.querySelector( '.headies-show-password-toggle' );
	if ( toggle ) {
		toggle.addEventListener( 'change', function () {
			var field = document.getElementById( toggle.getAttribute( 'data-target' ) );
			if ( field ) {
				field.type = toggle.checked ? 'text' : 'password';
			}
		} );
	}
	<?php endif; ?>
} );
</script>
<?php endif; ?>
