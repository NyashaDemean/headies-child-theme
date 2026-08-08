<?php
/**
 * Headies child theme functions
 */

function headies_enqueue_styles() {
    wp_enqueue_style( 'storefront-parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'headies-child-style', get_stylesheet_directory_uri() . '/style.css', array( 'storefront-parent-style' ), filemtime( get_stylesheet_directory() . '/style.css' ) );
}
add_action( 'wp_enqueue_scripts', 'headies_enqueue_styles' );

// Storefront shows its default blog sidebar on every ordinary Page whenever
// the "Blog Sidebar" widget area has widgets in it. My Account isn't a blog
// page, so drop the sidebar there and let the content area go full width.
function headies_remove_account_sidebar() {
    if ( function_exists( 'is_account_page' ) && is_account_page() ) {
        remove_action( 'storefront_sidebar', 'storefront_get_sidebar', 10 );
        add_filter( 'body_class', 'headies_account_full_width_body_class' );
    }
}
add_action( 'wp', 'headies_remove_account_sidebar' );

function headies_account_full_width_body_class( $classes ) {
    $classes[] = 'storefront-full-width-content';
    if ( ! is_user_logged_in() ) {
        $classes[] = 'woocommerce-account-login';
    }
    return $classes;
}

// The Wishlist page has no hero image behind the header either, so it needs
// the same "solid nav" treatment as My Account — see the .page-wishlist
// rules in style.css.
function headies_wishlist_body_class( $classes ) {
    if ( is_page( 'wishlist' ) ) {
        $classes[] = 'page-wishlist';
    }
    return $classes;
}
add_filter( 'body_class', 'headies_wishlist_body_class' );

// The registration form asks for First/Last name, which WooCommerce doesn't
// collect by default — require them and save them onto the new account.
function headies_require_registration_name_fields( $errors ) {
    if ( empty( $_POST['reg_first_name'] ) ) {
        $errors->add( 'reg_first_name_error', __( 'First name is required.', 'headies' ) );
    }
    if ( empty( $_POST['reg_last_name'] ) ) {
        $errors->add( 'reg_last_name_error', __( 'Last name is required.', 'headies' ) );
    }
    return $errors;
}
add_filter( 'woocommerce_registration_errors', 'headies_require_registration_name_fields' );

function headies_save_registration_name_fields( $customer_id ) {
    if ( ! empty( $_POST['reg_first_name'] ) ) {
        update_user_meta( $customer_id, 'first_name', sanitize_text_field( wp_unslash( $_POST['reg_first_name'] ) ) );
    }
    if ( ! empty( $_POST['reg_last_name'] ) ) {
        update_user_meta( $customer_id, 'last_name', sanitize_text_field( wp_unslash( $_POST['reg_last_name'] ) ) );
    }
}
add_action( 'woocommerce_created_customer', 'headies_save_registration_name_fields' );

function headies_get_drops() {
    $terms = get_terms( array(
        'taxonomy'   => 'product_drop',
        'hide_empty' => false,
    ) );

    if ( is_wp_error( $terms ) || empty( $terms ) ) {
        return array();
    }

    $now = current_time( 'timestamp' );
    $drops = array();

    foreach ( $terms as $term ) {
        $start_raw = get_term_meta( $term->term_id, 'drop_datetime', true );
        $end_raw   = get_term_meta( $term->term_id, 'drop_end_datetime', true );
        $image_id  = get_term_meta( $term->term_id, 'drop_image_id', true );

        $start_ts = $start_raw ? strtotime( $start_raw ) : 0;
        $end_ts   = $end_raw ? strtotime( $end_raw ) : 0;

        // Auto-calculate status from dates
        if ( $start_ts && $now < $start_ts ) {
            $status = 'upcoming';
            $date_label = 'Coming ' . date_i18n( 'F Y', $start_ts );
        } elseif ( $end_ts && $now > $end_ts ) {
            $status = 'past';
            $date_label = 'Dropped ' . date_i18n( 'F Y', $start_ts );
        } else {
            $status = 'live';
            $date_label = 'Available now';
        }
	$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : '';


        $drops[] = array(
            'id'            => $term->term_id,
            'slug'          => $term->slug,
            'name'          => $term->name,
            'desc'          => $term->description,
            'status'        => $status,
            'date'          => $date_label,
            'drop_datetime' => $start_raw,
            'image'         => $image_url,
            'image_id'      => $image_id,
        );
    }

    return $drops;
}


function headies_nav_scroll_script() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var nav = document.getElementById('masthead');
        if (!nav) return;
        function checkScroll() {
            if (window.scrollY > 40) {
                nav.classList.add('nav-scrolled');
            } else {
                nav.classList.remove('nav-scrolled');
            }
        }
        window.addEventListener('scroll', checkScroll);
        checkScroll();
    });
    </script>
    <?php
}
// ===== DROPS TAXONOMY =====
function headies_register_drop_taxonomy() {
    register_taxonomy( 'product_drop', 'product', array(
        'labels' => array(
            'name'          => 'Drops',
            'singular_name' => 'Drop',
            'add_new_item'  => 'Add New Drop',
            'edit_item'     => 'Edit Drop',
        ),
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
        'rewrite'           => array( 'slug' => 'drop' ),
    ) );
}
add_action( 'init', 'headies_register_drop_taxonomy' );

// Add custom fields when creating a new drop
function headies_drop_add_fields() {
    ?>
    <div class="form-field">
        <label for="drop_datetime">Drop Start Date/Time</label>
        <input type="text" name="drop_datetime" id="drop_datetime" placeholder="2026-08-15 10:00:00">
        <p>Format: YYYY-MM-DD HH:MM:SS (24hr). This is when the drop goes live.</p>
    </div>
    <div class="form-field">
        <label for="drop_end_datetime">Drop End Date/Time</label>
        <input type="text" name="drop_end_datetime" id="drop_end_datetime" placeholder="2026-08-22 10:00:00">
        <p>When this drop becomes "past." Leave blank to stay live indefinitely.</p>
    </div>
    <div class="form-field">
        <label for="drop_image_id">Drop Image (Attachment ID)</label>
        <input type="number" name="drop_image_id" id="drop_image_id">
        <p>Upload the image to Media Library first, then paste its Attachment ID here.</p>
    </div>
    <?php
}
add_action( 'product_drop_add_form_fields', 'headies_drop_add_fields' );

// Add custom fields when editing an existing drop
function headies_drop_edit_fields( $term ) {
    $drop_datetime     = get_term_meta( $term->term_id, 'drop_datetime', true );
    $drop_end_datetime = get_term_meta( $term->term_id, 'drop_end_datetime', true );
    $drop_image_id     = get_term_meta( $term->term_id, 'drop_image_id', true );
    ?>
    <tr class="form-field">
        <th><label for="drop_datetime">Drop Start Date/Time</label></th>
        <td><input type="text" name="drop_datetime" id="drop_datetime" value="<?php echo esc_attr( $drop_datetime ); ?>" placeholder="2026-08-15 10:00:00"></td>
    </tr>
    <tr class="form-field">
        <th><label for="drop_end_datetime">Drop End Date/Time</label></th>
        <td><input type="text" name="drop_end_datetime" id="drop_end_datetime" value="<?php echo esc_attr( $drop_end_datetime ); ?>" placeholder="2026-08-22 10:00:00"></td>
    </tr>
    <tr class="form-field">
        <th><label for="drop_image_id">Drop Image (Attachment ID)</label></th>
        <td><input type="number" name="drop_image_id" id="drop_image_id" value="<?php echo esc_attr( $drop_image_id ); ?>"></td>
    </tr>
    <?php
}
add_action( 'product_drop_edit_form_fields', 'headies_drop_edit_fields' );

// Save the custom fields
function headies_save_drop_fields( $term_id ) {
    if ( isset( $_POST['drop_datetime'] ) ) {
        update_term_meta( $term_id, 'drop_datetime', sanitize_text_field( $_POST['drop_datetime'] ) );
    }
    if ( isset( $_POST['drop_end_datetime'] ) ) {
        update_term_meta( $term_id, 'drop_end_datetime', sanitize_text_field( $_POST['drop_end_datetime'] ) );
    }
    if ( isset( $_POST['drop_image_id'] ) ) {
        update_term_meta( $term_id, 'drop_image_id', absint( $_POST['drop_image_id'] ) );
    }
}
add_action( 'created_product_drop', 'headies_save_drop_fields' );
add_action( 'edited_product_drop', 'headies_save_drop_fields' );

add_action( 'wp_footer', 'headies_nav_scroll_script' );

// ===== WISHLIST =====
// Logged-in customers get their wishlist stored on the account (user meta);
// guests get a cookie. Both store [ product_id => added timestamp ].

define( 'HEADIES_WISHLIST_COOKIE', 'headies_wishlist' );

function headies_get_wishlist() {
    if ( is_user_logged_in() ) {
        $wishlist = get_user_meta( get_current_user_id(), '_headies_wishlist', true );
        return is_array( $wishlist ) ? $wishlist : array();
    }
    if ( empty( $_COOKIE[ HEADIES_WISHLIST_COOKIE ] ) ) {
        return array();
    }
    $decoded = json_decode( wp_unslash( $_COOKIE[ HEADIES_WISHLIST_COOKIE ] ), true );
    return is_array( $decoded ) ? $decoded : array();
}

function headies_save_wishlist( $wishlist ) {
    if ( is_user_logged_in() ) {
        update_user_meta( get_current_user_id(), '_headies_wishlist', $wishlist );
        return;
    }
    $value = wp_json_encode( $wishlist );
    setcookie( HEADIES_WISHLIST_COOKIE, $value, time() + YEAR_IN_SECONDS, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, is_ssl(), true );
    $_COOKIE[ HEADIES_WISHLIST_COOKIE ] = $value;
}

function headies_wishlist_contains( $product_id ) {
    $wishlist = headies_get_wishlist();
    return isset( $wishlist[ absint( $product_id ) ] );
}

function headies_wishlist_count() {
    return count( headies_get_wishlist() );
}

function headies_wishlist_toggle( $product_id ) {
    $product_id = absint( $product_id );
    $wishlist   = headies_get_wishlist();
    if ( isset( $wishlist[ $product_id ] ) ) {
        unset( $wishlist[ $product_id ] );
        $in_wishlist = false;
    } else {
        $wishlist[ $product_id ] = time();
        $in_wishlist = true;
    }
    headies_save_wishlist( $wishlist );
    return $in_wishlist;
}

// Merge a guest's cookie wishlist into their account the moment they log in,
// so items they hearted before signing in aren't lost.
function headies_merge_guest_wishlist_on_login( $user_login, $user ) {
    if ( empty( $_COOKIE[ HEADIES_WISHLIST_COOKIE ] ) ) {
        return;
    }
    $guest_wishlist = json_decode( wp_unslash( $_COOKIE[ HEADIES_WISHLIST_COOKIE ] ), true );
    if ( ! is_array( $guest_wishlist ) || empty( $guest_wishlist ) ) {
        return;
    }
    $account_wishlist = get_user_meta( $user->ID, '_headies_wishlist', true );
    $account_wishlist = is_array( $account_wishlist ) ? $account_wishlist : array();
    update_user_meta( $user->ID, '_headies_wishlist', $account_wishlist + $guest_wishlist );
    setcookie( HEADIES_WISHLIST_COOKIE, '', time() - YEAR_IN_SECONDS, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, is_ssl(), true );
}
add_action( 'wp_login', 'headies_merge_guest_wishlist_on_login', 10, 2 );

function headies_wishlist_button( $product_id ) {
    $product_id  = absint( $product_id );
    $in_wishlist = headies_wishlist_contains( $product_id );
    printf(
        '<button type="button" class="headies-wishlist-toggle%1$s" data-product-id="%2$d" aria-pressed="%3$s" aria-label="%4$s"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8z"/></svg></button>',
        $in_wishlist ? ' is-active' : '',
        $product_id,
        $in_wishlist ? 'true' : 'false',
        $in_wishlist ? esc_attr__( 'Remove from wishlist', 'headies' ) : esc_attr__( 'Add to wishlist', 'headies' )
    );
}

function headies_single_product_wishlist_button() {
    global $product;
    if ( ! $product ) {
        return;
    }
    echo '<div class="headies-product-wishlist">';
    headies_wishlist_button( $product->get_id() );
    echo '</div>';
}
add_action( 'woocommerce_single_product_summary', 'headies_single_product_wishlist_button', 6 );

function headies_enqueue_wishlist_script() {
    $path = get_stylesheet_directory() . '/js/wishlist.js';
    wp_enqueue_script( 'headies-wishlist', get_stylesheet_directory_uri() . '/js/wishlist.js', array(), file_exists( $path ) ? filemtime( $path ) : false, true );
    wp_localize_script( 'headies-wishlist', 'headiesWishlist', array(
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'headies_wishlist' ),
        'cartUrl' => wc_get_cart_url(),
    ) );
}
add_action( 'wp_enqueue_scripts', 'headies_enqueue_wishlist_script' );

function headies_ajax_toggle_wishlist() {
    check_ajax_referer( 'headies_wishlist', 'nonce' );
    $product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
    if ( ! $product_id || 'product' !== get_post_type( $product_id ) ) {
        wp_send_json_error();
    }
    wp_send_json_success( array(
        'in_wishlist'    => headies_wishlist_toggle( $product_id ),
        'wishlist_count' => headies_wishlist_count(),
    ) );
}
add_action( 'wp_ajax_headies_toggle_wishlist', 'headies_ajax_toggle_wishlist' );
add_action( 'wp_ajax_nopriv_headies_toggle_wishlist', 'headies_ajax_toggle_wishlist' );

function headies_ajax_add_to_cart() {
    check_ajax_referer( 'headies_wishlist', 'nonce' );
    $product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
    if ( ! $product_id || ! WC()->cart || ! WC()->cart->add_to_cart( $product_id ) ) {
        wp_send_json_error();
    }
    wp_send_json_success( array(
        'cart_count' => WC()->cart->get_cart_contents_count(),
    ) );
}
add_action( 'wp_ajax_headies_add_to_cart', 'headies_ajax_add_to_cart' );
add_action( 'wp_ajax_nopriv_headies_add_to_cart', 'headies_ajax_add_to_cart' );

function headies_ajax_add_all_wishlist_to_cart() {
    check_ajax_referer( 'headies_wishlist', 'nonce' );
    if ( ! WC()->cart ) {
        wp_send_json_error();
    }
    foreach ( array_keys( headies_get_wishlist() ) as $product_id ) {
        WC()->cart->add_to_cart( absint( $product_id ) );
    }
    wp_send_json_success( array(
        'cart_count' => WC()->cart->get_cart_contents_count(),
        'redirect'   => wc_get_cart_url(),
    ) );
}
add_action( 'wp_ajax_headies_add_all_wishlist_to_cart', 'headies_ajax_add_all_wishlist_to_cart' );
add_action( 'wp_ajax_nopriv_headies_add_all_wishlist_to_cart', 'headies_ajax_add_all_wishlist_to_cart' );

