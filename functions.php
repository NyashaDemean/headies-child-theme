<?php
/**
 * Headies child theme functions
 */

function headies_enqueue_styles() {
    wp_enqueue_style( 'storefront-parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'headies-child-style', get_stylesheet_directory_uri() . '/style.css', array( 'storefront-parent-style' ) );
}
add_action( 'wp_enqueue_scripts', 'headies_enqueue_styles' );

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
        'hierarchical'      => false,
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

