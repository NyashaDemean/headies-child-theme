<?php
/**
 * Headies child theme functions
 */

function headies_enqueue_styles() {
    wp_enqueue_style( 'storefront-parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'headies-child-style', get_stylesheet_directory_uri() . '/style.css', array( 'storefront-parent-style' ), filemtime( get_stylesheet_directory() . '/style.css' ) );
}
add_action( 'wp_enqueue_scripts', 'headies_enqueue_styles' );

// Storefront's default 'woocommerce' theme support caps the "single" product
// image (used for both the hats-card front/back photos and the single
// product gallery) at 416px wide. That's soft on any 2x/3x-DPR phone, which
// is most of them. Re-declaring the same support at a later priority so it
// overrides Storefront's instead of being overwritten by it — bumped to
// 800px, still comfortably under our source photos' native width.
function headies_increase_product_image_sizes() {
    add_theme_support( 'woocommerce', array(
        'single_image_width'    => 800,
        'thumbnail_image_width' => 324,
        'product_grid'          => array(
            'default_rows'    => 4,
            'min_rows'        => 1,
            'default_columns' => 3,
            'min_columns'     => 1,
            'max_columns'     => 6,
        ),
    ) );
}
add_action( 'after_setup_theme', 'headies_increase_product_image_sizes', 20 );

// Storefront pulls "Source Sans Pro" from the Google Fonts CDN by default.
// Our own CSS overrides font-family everywhere it would show up, so it's
// dead weight — but it's still an external request, which defeats the
// point of self-hosting Fredoka/Inter. Drop it.
function headies_dequeue_storefront_fonts() {
    wp_dequeue_style( 'storefront-fonts' );
    wp_deregister_style( 'storefront-fonts' );
    wp_dequeue_style( 'source-sans-pro' );
    wp_deregister_style( 'source-sans-pro' );
}
add_action( 'wp_enqueue_scripts', 'headies_dequeue_storefront_fonts', 20 );

// Restrict front-end search to products. Deliberately done here via
// pre_get_posts rather than a `post_type=product` hidden field on the
// search form — setting post_type in the query string itself makes
// WordPress flag the request as is_post_type_archive('product') too,
// which makes WooCommerce's template loader force archive-product.php
// (the plain shop template) instead of search.php. pre_get_posts runs
// after those flags are already resolved, so search.php still wins.
function headies_search_only_products( $query ) {
    if ( ! is_admin() && $query->is_main_query() && $query->is_search() ) {
        $query->set( 'post_type', 'product' );
    }
}
add_action( 'pre_get_posts', 'headies_search_only_products' );

// Storefront shows its default blog sidebar on every ordinary Page whenever
// the "Blog Sidebar" widget area has widgets in it. My Account isn't a blog
// page, so drop the sidebar there and let the content area go full width.
function headies_remove_account_sidebar() {
    if ( ( function_exists( 'is_account_page' ) && is_account_page() )
        || ( function_exists( 'is_cart' ) && is_cart() )
        || ( function_exists( 'is_checkout' ) && is_checkout() )
        || is_product() ) {
        remove_action( 'storefront_sidebar', 'storefront_get_sidebar', 10 );
        add_filter( 'body_class', 'headies_account_full_width_body_class' );
    }
}
add_action( 'wp', 'headies_remove_account_sidebar' );

function headies_account_full_width_body_class( $classes ) {
    $classes[] = 'storefront-full-width-content';
    // This class drives the split-screen login/register layout (see the
    // "LOGIN (split-screen)" rules in style.css) — it must only fire on the
    // actual My Account page, not on Cart/Checkout/Product for guests, which
    // also run through this same sidebar-removal filter.
    if ( is_account_page() && ! is_user_logged_in() ) {
        $classes[] = 'woocommerce-account-login';
    }
    return $classes;
}

// My Account nav, trimmed to what the store actually has (no downloads,
// no rewards/interests — this isn't Shopify) and relabelled to match.
function headies_account_menu_items( $items ) {
    $keep = array(
        'dashboard'      => 'My Account',
        'edit-account'   => 'Account Details',
        'edit-address'   => 'Address Book',
        'orders'         => 'Order History',
        'customer-logout' => 'Logout',
    );
    $out = array();
    foreach ( $keep as $endpoint => $label ) {
        if ( isset( $items[ $endpoint ] ) ) {
            $out[ $endpoint ] = $label;
        }
    }
    return $out;
}
add_filter( 'woocommerce_account_menu_items', 'headies_account_menu_items' );

// The current endpoint's own label doubles as the page's big H1 (matching
// the New Era reference — "My Account", "Address Book", "Order History"
// each get their own page title instead of a single static one).
function headies_account_page_title() {
    $endpoint = WC()->query->get_current_endpoint();
    if ( ! $endpoint || 'dashboard' === $endpoint ) {
        return 'My Account';
    }
    $items = wc_get_account_menu_items();
    return isset( $items[ $endpoint ] ) ? $items[ $endpoint ] : 'My Account';
}

/**
 * "New address" pill + prev/next arrows next to the Address Book heading.
 * WooCommerce only supports two fixed address slots (billing/shipping) —
 * there's no arbitrary multi-address book to page through — so "New address"
 * routes to whichever of the two isn't set up yet (shipping first, since
 * that's the one usually missing), and the arrows are inert once there's
 * nothing left to page between. Shared by the dashboard preview and the
 * full Address Book page so both stay in sync.
 */
function headies_render_address_book_actions() {
    $has_shipping = ! wc_ship_to_billing_address_only() && (bool) wc_get_account_formatted_address( 'shipping' );
    $has_billing  = (bool) wc_get_account_formatted_address( 'billing' );

    if ( ! $has_shipping && ! wc_ship_to_billing_address_only() ) {
        $new_address_url = wc_get_endpoint_url( 'edit-address', 'shipping' );
    } elseif ( ! $has_billing ) {
        $new_address_url = wc_get_endpoint_url( 'edit-address', 'billing' );
    } else {
        $new_address_url = wc_get_endpoint_url( 'edit-address', wc_ship_to_billing_address_only() ? 'billing' : 'shipping' );
    }

    ob_start();
    ?>
    <div class="headies-address-book-actions">
        <a href="<?php echo esc_url( $new_address_url ); ?>" class="headies-pill-btn headies-pill-btn--dark">New address</a>
        <button type="button" class="headies-address-nav-arrow" aria-label="Previous address" disabled>&lsaquo;</button>
        <button type="button" class="headies-address-nav-arrow" aria-label="Next address" disabled>&rsaquo;</button>
    </div>
    <?php
    return ob_get_clean();
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

/**
 * Auto-derive a drop's status ('upcoming' | 'live' | 'past') and a display
 * date label from its drop_datetime / drop_end_datetime term meta. Shared by
 * headies_get_drops() and any product card that needs to know whether the
 * drop it belongs to is out yet (badge text, Add to Cart gating).
 */
function headies_get_drop_status( $term_id ) {
    $start_raw = get_term_meta( $term_id, 'drop_datetime', true );
    $end_raw   = get_term_meta( $term_id, 'drop_end_datetime', true );

    $start_ts = $start_raw ? strtotime( $start_raw ) : 0;
    $end_ts   = $end_raw ? strtotime( $end_raw ) : 0;
    $now      = current_time( 'timestamp' );

    if ( $start_ts && $now < $start_ts ) {
        $status     = 'upcoming';
        $date_label = 'Drops ' . date_i18n( 'F j, Y', $start_ts );
    } elseif ( $end_ts && $now > $end_ts ) {
        $status     = 'past';
        $date_label = 'Dropped ' . date_i18n( 'F Y', $start_ts );
    } else {
        $status     = 'live';
        $date_label = 'Available now';
    }

    return array(
        'status'        => $status,
        'date'          => $date_label,
        // Compact MM.DD.YY badge date, matching the Drops page card design.
        'date_short'    => $start_ts ? date_i18n( 'm.d.y', $start_ts ) : '',
        'drop_datetime' => $start_raw,
    );
}

/**
 * Assemble the full display array (dates, status, every image role, both
 * description fields) for one drop term. Shared by headies_get_drops() and
 * the single-drop page, which only needs one term.
 */
function headies_build_drop_array( $term ) {
    $status_info   = headies_get_drop_status( $term->term_id );
    $image_id      = get_term_meta( $term->term_id, 'drop_image_id', true );
    $main_image_id = get_term_meta( $term->term_id, 'drop_main_image_id', true );
    $hero_image_id = get_term_meta( $term->term_id, 'drop_hero_image_id', true );
    $video_id      = get_term_meta( $term->term_id, 'drop_video_id', true );

    return array(
        'id'            => $term->term_id,
        'slug'          => $term->slug,
        'name'          => $term->name,
        'desc'          => $term->description,
        'tagline'       => get_term_meta( $term->term_id, 'drop_tagline', true ),
        'full_desc'     => get_term_meta( $term->term_id, 'drop_full_description', true ),
        'status'        => $status_info['status'],
        'date'          => $status_info['date'],
        'date_short'    => $status_info['date_short'],
        'drop_datetime' => $status_info['drop_datetime'],
        'image'         => $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : '',
        'image_id'      => $image_id,
        'main_image'    => $main_image_id ? wp_get_attachment_image_url( $main_image_id, 'full' ) : '',
        'main_image_id' => $main_image_id,
        'hero_image'    => $hero_image_id ? wp_get_attachment_image_url( $hero_image_id, 'full' ) : '',
        'hero_image_id' => $hero_image_id,
        'video'         => $video_id ? wp_get_attachment_url( $video_id ) : '',
        'video_id'      => $video_id,
    );
}

function headies_get_drops() {
    $terms = get_terms( array(
        'taxonomy'   => 'product_drop',
        'hide_empty' => false,
    ) );

    if ( is_wp_error( $terms ) || empty( $terms ) ) {
        return array();
    }

    return array_map( 'headies_build_drop_array', $terms );
}

/**
 * The drop (term) a product belongs to, or null. Products can only carry one
 * drop at a time in this catalog, so the first term is authoritative.
 */
function headies_get_product_drop( $product_id ) {
    $terms = get_the_terms( $product_id, 'product_drop' );
    if ( ! $terms || is_wp_error( $terms ) ) {
        return null;
    }
    $term          = $terms[0];
    $status_info   = headies_get_drop_status( $term->term_id );
    $term->status  = $status_info['status'];
    $term->date    = $status_info['date'];
    return $term;
}


function headies_nav_scroll_script() {
    $ajax_url    = admin_url( 'admin-ajax.php' );
    $search_nonce = wp_create_nonce( 'headies_live_search' );
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var headiesSearchAjax = {
            ajaxUrl: '<?php echo esc_js( $ajax_url ); ?>',
            nonce: '<?php echo esc_js( $search_nonce ); ?>'
        };
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

        // --- Mobile menu toggle ---
        var hamburger = nav.querySelector( '.nav-hamburger' );
        var navLinks  = nav.querySelector( '.nav-links' );
        if ( hamburger && navLinks ) {
            hamburger.addEventListener( 'click', function () {
                var isOpen = navLinks.classList.toggle( 'is-open' );
                hamburger.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );
            } );
            document.addEventListener( 'click', function ( e ) {
                if ( ! navLinks.classList.contains( 'is-open' ) ) {
                    return;
                }
                if ( navLinks.contains( e.target ) || hamburger.contains( e.target ) ) {
                    return;
                }
                navLinks.classList.remove( 'is-open' );
                hamburger.setAttribute( 'aria-expanded', 'false' );
            } );
            document.addEventListener( 'keydown', function ( e ) {
                if ( 'Escape' === e.key && navLinks.classList.contains( 'is-open' ) ) {
                    navLinks.classList.remove( 'is-open' );
                    hamburger.setAttribute( 'aria-expanded', 'false' );
                }
            } );
        }

        // --- Search toggle ---
        var searchToggle = nav.querySelector( '.nav-search-toggle' );
        var searchBar     = nav.querySelector( '.nav-search-bar' );
        var searchField   = searchBar ? searchBar.querySelector( '.search-field' ) : null;
        if ( ! searchToggle || ! searchBar ) {
            return;
        }

        function openSearch() {
            searchBar.classList.add( 'is-open' );
            searchToggle.setAttribute( 'aria-expanded', 'true' );
            if ( searchField ) {
                window.setTimeout( function () { searchField.focus(); }, 150 );
            }
        }
        function closeSearch() {
            searchBar.classList.remove( 'is-open' );
            searchToggle.setAttribute( 'aria-expanded', 'false' );
            var results = searchBar.querySelector( '.nav-search-results' );
            if ( results ) {
                results.hidden = true;
                var grid = results.querySelector( '.nav-search-results-grid' );
                if ( grid ) {
                    grid.innerHTML = '';
                }
            }
            var defaults = searchBar.querySelector( '.nav-search-default' );
            if ( defaults ) {
                defaults.hidden = false;
            }
            if ( searchField ) {
                searchField.value = '';
            }
        }

        searchToggle.setAttribute( 'aria-expanded', 'false' );
        searchToggle.addEventListener( 'click', function ( e ) {
            e.preventDefault();
            if ( searchBar.classList.contains( 'is-open' ) ) {
                closeSearch();
            } else {
                openSearch();
            }
        } );

        document.addEventListener( 'click', function ( e ) {
            if ( ! searchBar.classList.contains( 'is-open' ) ) {
                return;
            }
            if ( searchBar.contains( e.target ) || searchToggle.contains( e.target ) ) {
                return;
            }
            closeSearch();
        } );

        document.addEventListener( 'keydown', function ( e ) {
            if ( 'Escape' === e.key && searchBar.classList.contains( 'is-open' ) ) {
                closeSearch();
            }
        } );

        // --- Live search-as-you-type ---
        var resultsWrap  = searchBar.querySelector( '.nav-search-results' );
        var resultsGrid  = resultsWrap ? resultsWrap.querySelector( '.nav-search-results-grid' ) : null;
        var viewAllLink  = resultsWrap ? resultsWrap.querySelector( '.nav-search-view-all' ) : null;
        var defaultPanel = searchBar.querySelector( '.nav-search-default' );
        var searchDebounce;

        function hideResults() {
            if ( ! resultsWrap ) {
                return;
            }
            resultsWrap.hidden = true;
            resultsGrid.innerHTML = '';
            if ( defaultPanel ) {
                defaultPanel.hidden = false;
            }
        }

        if ( searchField && resultsWrap && resultsGrid ) {
            searchField.addEventListener( 'input', function () {
                var term = searchField.value.trim();
                window.clearTimeout( searchDebounce );

                if ( ! term ) {
                    hideResults();
                    return;
                }

                if ( defaultPanel ) {
                    defaultPanel.hidden = true;
                }

                searchDebounce = window.setTimeout( function () {
                    var formData = new FormData();
                    formData.append( 'action', 'headies_live_search' );
                    formData.append( 'nonce', headiesSearchAjax.nonce );
                    formData.append( 'term', term );

                    fetch( headiesSearchAjax.ajaxUrl, { method: 'POST', body: formData } )
                        .then( function ( res ) { return res.json(); } )
                        .then( function ( data ) {
                            if ( ! data.success || term !== searchField.value.trim() ) {
                                return;
                            }
                            if ( data.data.count > 0 ) {
                                resultsGrid.innerHTML = data.data.html;
                                if ( viewAllLink ) {
                                    viewAllLink.href = '<?php echo esc_js( home_url( '/' ) ); ?>?s=' + encodeURIComponent( term );
                                }
                                resultsWrap.hidden = false;
                            } else {
                                hideResults();
                            }
                        } )
                        .catch( function () { hideResults(); } );
                }, 250 );
            } );
        }
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
        <label for="drop_image_id">Banner Image (Attachment ID)</label>
        <input type="number" name="drop_image_id" id="drop_image_id">
        <p>Full-bleed image for the Drops page "upcoming" carousel. Upload to Media Library first, then paste its Attachment ID here.</p>
    </div>
    <div class="form-field">
        <label for="drop_main_image_id">Card Image (Attachment ID)</label>
        <input type="number" name="drop_main_image_id" id="drop_main_image_id">
        <p>Used for the homepage Drops row and the Past Drops thumbnail on the Drops page.</p>
    </div>
    <div class="form-field">
        <label for="drop_hero_image_id">Detail Page Hero (Attachment ID)</label>
        <input type="number" name="drop_hero_image_id" id="drop_hero_image_id">
        <p>Hero photo shown at the top of this drop's own page.</p>
    </div>
    <div class="form-field">
        <label for="drop_video_id">Showcase Video (Attachment ID)</label>
        <input type="number" name="drop_video_id" id="drop_video_id">
        <p>Optional. Upload to Media Library first, then paste its Attachment ID here. Plays on this drop's own page (below the write-up) and, if this is the featured Upcoming Drop, as the background on the /drops page.</p>
    </div>
    <div class="form-field">
        <label for="drop_tagline">Tagline</label>
        <input type="text" name="drop_tagline" id="drop_tagline">
        <p>Short line shown under the drop name on its own page (the taxonomy Description field above is the short blurb used on the homepage carousel).</p>
    </div>
    <div class="form-field">
        <label for="drop_full_description">Full Description</label>
        <textarea name="drop_full_description" id="drop_full_description" rows="5" cols="40"></textarea>
        <p>Longer write-up shown on this drop's own page.</p>
    </div>
    <?php
}
add_action( 'product_drop_add_form_fields', 'headies_drop_add_fields' );

// Add custom fields when editing an existing drop
function headies_drop_edit_fields( $term ) {
    $drop_datetime     = get_term_meta( $term->term_id, 'drop_datetime', true );
    $drop_end_datetime = get_term_meta( $term->term_id, 'drop_end_datetime', true );
    $drop_image_id     = get_term_meta( $term->term_id, 'drop_image_id', true );
    $drop_main_image_id = get_term_meta( $term->term_id, 'drop_main_image_id', true );
    $drop_hero_image_id = get_term_meta( $term->term_id, 'drop_hero_image_id', true );
    $drop_video_id      = get_term_meta( $term->term_id, 'drop_video_id', true );
    $drop_tagline       = get_term_meta( $term->term_id, 'drop_tagline', true );
    $drop_full_description = get_term_meta( $term->term_id, 'drop_full_description', true );
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
        <th><label for="drop_image_id">Banner Image (Attachment ID)</label></th>
        <td><input type="number" name="drop_image_id" id="drop_image_id" value="<?php echo esc_attr( $drop_image_id ); ?>">
        <p>Full-bleed image for the Drops page "upcoming" carousel.</p></td>
    </tr>
    <tr class="form-field">
        <th><label for="drop_main_image_id">Card Image (Attachment ID)</label></th>
        <td><input type="number" name="drop_main_image_id" id="drop_main_image_id" value="<?php echo esc_attr( $drop_main_image_id ); ?>">
        <p>Homepage Drops row + Past Drops thumbnail.</p></td>
    </tr>
    <tr class="form-field">
        <th><label for="drop_hero_image_id">Detail Page Hero (Attachment ID)</label></th>
        <td><input type="number" name="drop_hero_image_id" id="drop_hero_image_id" value="<?php echo esc_attr( $drop_hero_image_id ); ?>">
        <p>Hero photo on this drop's own page.</p></td>
    </tr>
    <tr class="form-field">
        <th><label for="drop_video_id">Showcase Video (Attachment ID)</label></th>
        <td><input type="number" name="drop_video_id" id="drop_video_id" value="<?php echo esc_attr( $drop_video_id ); ?>">
        <p>Optional. Plays on this drop's own page (below the write-up) and, if this is the featured Upcoming Drop, as the background on the /drops page.</p></td>
    </tr>
    <tr class="form-field">
        <th><label for="drop_tagline">Tagline</label></th>
        <td><input type="text" name="drop_tagline" id="drop_tagline" value="<?php echo esc_attr( $drop_tagline ); ?>">
        <p>Short line shown under the drop name on its own page.</p></td>
    </tr>
    <tr class="form-field">
        <th><label for="drop_full_description">Full Description</label></th>
        <td><textarea name="drop_full_description" id="drop_full_description" rows="5" cols="40"><?php echo esc_textarea( $drop_full_description ); ?></textarea>
        <p>Longer write-up shown on this drop's own page.</p></td>
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
    if ( isset( $_POST['drop_main_image_id'] ) ) {
        update_term_meta( $term_id, 'drop_main_image_id', absint( $_POST['drop_main_image_id'] ) );
    }
    if ( isset( $_POST['drop_hero_image_id'] ) ) {
        update_term_meta( $term_id, 'drop_hero_image_id', absint( $_POST['drop_hero_image_id'] ) );
    }
    if ( isset( $_POST['drop_video_id'] ) ) {
        update_term_meta( $term_id, 'drop_video_id', absint( $_POST['drop_video_id'] ) );
    }
    if ( isset( $_POST['drop_tagline'] ) ) {
        update_term_meta( $term_id, 'drop_tagline', sanitize_text_field( $_POST['drop_tagline'] ) );
    }
    if ( isset( $_POST['drop_full_description'] ) ) {
        update_term_meta( $term_id, 'drop_full_description', sanitize_textarea_field( $_POST['drop_full_description'] ) );
    }
}
add_action( 'created_product_drop', 'headies_save_drop_fields' );
add_action( 'edited_product_drop', 'headies_save_drop_fields' );

add_action( 'wp_footer', 'headies_nav_scroll_script' );

// The sticky "added to bag" bar markup lives inline in
// woocommerce/content-single-product.php (so it sits next to the related
// products it needs `wc_get_cart_url()` etc. from); js/single-product.js
// fills it in and reveals it after a successful AJAX add-to-cart.

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

function headies_enqueue_cart_drawer_script() {
    if ( ! function_exists( 'WC' ) ) {
        return;
    }
    $path = get_stylesheet_directory() . '/js/cart-drawer.js';
    wp_enqueue_script( 'headies-cart-drawer', get_stylesheet_directory_uri() . '/js/cart-drawer.js', array( 'headies-wishlist' ), file_exists( $path ) ? filemtime( $path ) : false, true );
}
add_action( 'wp_enqueue_scripts', 'headies_enqueue_cart_drawer_script' );

/**
 * Shared product card — the Hats grid, Accessories grid, Search results,
 * and (with hide_info) the pre-drop reveal grid on a drop's own page all
 * render through this one component so they stay visually identical.
 *
 * $args:
 *   hide_info (bool) — omit the name/price row entirely (the "mystery"
 *   treatment used for a not-yet-live drop's own page).
 */
function headies_render_hats_card( $product_id, $args = array() ) {
    $product = wc_get_product( $product_id );
    if ( ! $product ) {
        return;
    }
    $hide_info = ! empty( $args['hide_info'] );

    $gallery_ids = $product->get_gallery_image_ids();
    $back_image  = ! empty( $gallery_ids ) ? wp_get_attachment_image_url( $gallery_ids[0], 'woocommerce_single' ) : '';
    $front_image = get_the_post_thumbnail_url( $product_id, 'woocommerce_single' );

    $is_sold_out  = ! $product->is_in_stock();
    $drop         = headies_get_product_drop( $product_id );
    $badge_label  = 'New';
    $badge_class  = 'hats-badge';
    if ( $drop ) {
        if ( 'upcoming' === $drop->status ) {
            $badge_label = 'Coming Soon';
            $badge_class = 'hats-badge hats-badge--soon';
        } else {
            $badge_label = 'Exclusive';
            $badge_class = 'hats-badge hats-badge--exclusive';
        }
    }
    if ( $is_sold_out ) {
        $badge_label = 'Sold Out';
        $badge_class = 'hats-badge hats-badge--soldout';
    }
    ?>
    <div class="hats-card<?php echo $is_sold_out ? ' hats-card--soldout' : ''; ?>">
      <a href="<?php echo esc_url( get_permalink( $product_id ) ); ?>" class="hats-card-image">
        <span class="<?php echo esc_attr( $badge_class ); ?>"><?php echo esc_html( $badge_label ); ?></span>
        <img class="hats-img-front" src="<?php echo esc_url( $front_image ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>">
        <?php if ( $back_image ) : ?>
          <img class="hats-img-back" src="<?php echo esc_url( $back_image ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?> underbrim">
        <?php endif; ?>
      </a>
      <?php headies_wishlist_button( $product_id ); ?>

      <?php if ( ! $hide_info ) : ?>
        <div class="hats-card-info">
          <span class="hats-name"><?php echo esc_html( $product->get_name() ); ?></span>
          <span class="hats-price"><?php echo wc_price( $product->get_price() ); ?></span>
        </div>
      <?php endif; ?>
    </div>
    <?php
}

/**
 * Drop card for the /drops page — matches Hat Club's drops-archive design:
 * a single full-bleed image (no border/gap treatment), a date pill top-right,
 * and the drop name + arrow overlaid bottom-left on a dark gradient. Used
 * identically for both the Upcoming and Past grids.
 */
function headies_render_drop_card( $drop ) {
    $link  = get_term_link( (int) $drop['id'], 'product_drop' );
    $link  = is_wp_error( $link ) ? '#' : $link;
    $image = $drop['main_image'] ? $drop['main_image'] : $drop['image'];
    ?>
    <a href="<?php echo esc_url( $link ); ?>" class="drop-article-card">
        <?php if ( $drop['date_short'] ) : ?>
            <span class="drop-article-card__date"><?php echo esc_html( $drop['date_short'] ); ?></span>
        <?php endif; ?>
        <div class="drop-article-card__image">
            <img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $drop['name'] ); ?>" loading="lazy">
        </div>
        <div class="drop-article-card__content">
            <span class="drop-article-card__name"><?php echo esc_html( $drop['name'] ); ?></span>
            <svg class="drop-article-card__arrow" viewBox="0 0 25 18" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M24.0607 8.96042L15.1002 0L14.103 0.997233L21.4265 8.32077L0 8.32076V9.73106L21.2955 9.73107L14.103 16.9236L15.1002 17.9208L24.0607 8.96042Z" fill="currentColor"/></svg>
        </div>
    </a>
    <?php
}

function headies_get_products_by_cat_query( $cat_slug, $paged ) {
    return new WP_Query( array(
        'post_type'      => 'product',
        'posts_per_page' => 8,
        'paged'          => $paged,
        'tax_query'      => array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => $cat_slug,
            ),
        ),
    ) );
}

function headies_get_hats_query( $paged ) {
    return headies_get_products_by_cat_query( 'hats', $paged );
}

function headies_get_accessories_query( $paged ) {
    return headies_get_products_by_cat_query( 'accessories', $paged );
}

function headies_get_search_hats_query( $search_term, $paged ) {
    return new WP_Query( array(
        's'              => $search_term,
        'post_type'      => 'product',
        'posts_per_page' => 8,
        'paged'          => $paged,
    ) );
}

function headies_enqueue_hats_script() {
    if ( ! is_page_template( 'page-hats.php' ) && ! is_page_template( 'page-accessories.php' ) && ! is_search() ) {
        return;
    }
    $path = get_stylesheet_directory() . '/js/hats.js';
    wp_enqueue_script( 'headies-hats', get_stylesheet_directory_uri() . '/js/hats.js', array(), file_exists( $path ) ? filemtime( $path ) : false, true );
    wp_localize_script( 'headies-hats', 'headiesHats', array(
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'headies_load_more_hats' ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'headies_enqueue_hats_script' );

function headies_ajax_load_more_hats() {
    check_ajax_referer( 'headies_load_more_hats', 'nonce' );

    $paged      = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
    $hats_query = headies_get_hats_query( $paged );

    ob_start();
    if ( $hats_query->have_posts() ) {
        while ( $hats_query->have_posts() ) {
            $hats_query->the_post();
            headies_render_hats_card( get_the_ID() );
        }
    }
    wp_reset_postdata();
    $html = ob_get_clean();

    wp_send_json_success( array(
        'html'      => $html,
        'maxPages'  => (int) $hats_query->max_num_pages,
    ) );
}
add_action( 'wp_ajax_headies_load_more_hats', 'headies_ajax_load_more_hats' );
add_action( 'wp_ajax_nopriv_headies_load_more_hats', 'headies_ajax_load_more_hats' );

function headies_ajax_load_more_accessories() {
    check_ajax_referer( 'headies_load_more_hats', 'nonce' );

    $paged              = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
    $accessories_query  = headies_get_accessories_query( $paged );

    ob_start();
    if ( $accessories_query->have_posts() ) {
        while ( $accessories_query->have_posts() ) {
            $accessories_query->the_post();
            headies_render_hats_card( get_the_ID() );
        }
    }
    wp_reset_postdata();
    $html = ob_get_clean();

    wp_send_json_success( array(
        'html'      => $html,
        'maxPages'  => (int) $accessories_query->max_num_pages,
    ) );
}
add_action( 'wp_ajax_headies_load_more_accessories', 'headies_ajax_load_more_accessories' );
add_action( 'wp_ajax_nopriv_headies_load_more_accessories', 'headies_ajax_load_more_accessories' );

function headies_ajax_load_more_search() {
    check_ajax_referer( 'headies_load_more_hats', 'nonce' );

    $paged        = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
    $search_term  = isset( $_POST['search'] ) ? sanitize_text_field( wp_unslash( $_POST['search'] ) ) : '';
    $search_query = headies_get_search_hats_query( $search_term, $paged );

    ob_start();
    if ( $search_query->have_posts() ) {
        while ( $search_query->have_posts() ) {
            $search_query->the_post();
            headies_render_hats_card( get_the_ID() );
        }
    }
    wp_reset_postdata();
    $html = ob_get_clean();

    wp_send_json_success( array(
        'html'      => $html,
        'maxPages'  => (int) $search_query->max_num_pages,
    ) );
}
add_action( 'wp_ajax_headies_load_more_search', 'headies_ajax_load_more_search' );
add_action( 'wp_ajax_nopriv_headies_load_more_search', 'headies_ajax_load_more_search' );

function headies_highlight_match( $text, $term ) {
    $term = trim( $term );
    $safe_text = esc_html( $text );
    if ( '' === $term ) {
        return $safe_text;
    }
    $pattern = '/' . preg_quote( esc_html( $term ), '/' ) . '/i';
    return preg_replace( $pattern, '<mark>$0</mark>', $safe_text );
}

/**
 * Curated shortcuts shown in the search overlay before anyone types —
 * there's no team taxonomy in the catalog to pull this from automatically,
 * so it's a short editable list. Update as the catalog's team mix changes.
 */
function headies_get_popular_searches() {
    return apply_filters( 'headies_popular_searches', array( 'Yankees', 'Dodgers', 'White Sox', 'Braves' ) );
}

// Shared by live search results and the "Trending Now" default panel below,
// so both render the identical card markup/styling.
function headies_search_result_card( $product_id, $term = '' ) {
    $product = wc_get_product( $product_id );
    if ( ! $product ) {
        return;
    }
    ?>
    <a href="<?php echo esc_url( get_permalink( $product_id ) ); ?>" class="nav-search-result">
        <div class="nav-search-result-image"><?php echo get_the_post_thumbnail( $product_id, 'thumbnail' ); ?></div>
        <span class="nav-search-result-name"><?php echo wp_kses_post( headies_highlight_match( $product->get_name(), $term ) ); ?></span>
        <span class="nav-search-result-price"><?php echo wp_kses_post( wc_price( $product->get_price() ) ); ?></span>
    </a>
    <?php
}

/**
 * The 4 best-selling published products, for the search overlay's "Trending
 * Now" panel — falls back to the most recent products for a fresh catalog
 * with no sales yet.
 */
function headies_get_trending_products( $limit = 4 ) {
    $query = new WP_Query( array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => $limit,
        'meta_key'       => 'total_sales',
        'orderby'        => 'meta_value_num',
        'order'          => 'DESC',
    ) );
    $ids = wp_list_pluck( $query->posts, 'ID' );
    wp_reset_postdata();

    if ( count( $ids ) < $limit ) {
        $fallback = new WP_Query( array(
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'posts_per_page' => $limit,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'post__not_in'   => $ids,
        ) );
        $ids = array_merge( $ids, wp_list_pluck( $fallback->posts, 'ID' ) );
        wp_reset_postdata();
    }

    return array_slice( $ids, 0, $limit );
}

function headies_ajax_live_search() {
    check_ajax_referer( 'headies_live_search', 'nonce' );

    $term = isset( $_POST['term'] ) ? sanitize_text_field( wp_unslash( $_POST['term'] ) ) : '';

    if ( '' === $term ) {
        wp_send_json_success( array( 'html' => '', 'count' => 0 ) );
    }

    $query = new WP_Query( array(
        's'              => $term,
        'post_type'      => 'product',
        'posts_per_page' => 4,
    ) );

    ob_start();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            headies_search_result_card( get_the_ID(), $term );
        }
    }
    wp_reset_postdata();
    $html = ob_get_clean();

    wp_send_json_success( array(
        'html'  => $html,
        'count' => (int) $query->found_posts,
    ) );
}
add_action( 'wp_ajax_headies_live_search', 'headies_ajax_live_search' );
add_action( 'wp_ajax_nopriv_headies_live_search', 'headies_ajax_live_search' );

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
    $quantity   = isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : 1;
    if ( ! $product_id || ! WC()->cart || ! WC()->cart->add_to_cart( $product_id, $quantity ) ) {
        wp_send_json_error();
    }
    $product = wc_get_product( $product_id );
    wp_send_json_success( array(
        'cart_count'   => WC()->cart->get_cart_contents_count(),
        'product_name' => $product ? $product->get_name() : '',
        'product_image' => $product ? wp_get_attachment_image_url( $product->get_image_id(), 'thumbnail' ) : '',
        'line_total'   => $product ? wp_strip_all_tags( wc_price( $product->get_price() * $quantity ) ) : '',
        'quantity'     => $quantity,
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

/**
 * Snapshot the caller's current wishlist under a random token so it can be
 * viewed by anyone with the link — logged-in wishlists live in user meta and
 * guest ones in a cookie, neither of which is reachable by a third party, so
 * "Share List" needs its own public-readable copy. Re-shares the same token
 * (refreshing its expiry) while it's still valid, so repeat clicks don't
 * spawn a new link every time.
 */
function headies_ajax_get_wishlist_share_link() {
    check_ajax_referer( 'headies_wishlist', 'nonce' );

    $product_ids = array_map( 'absint', array_keys( headies_get_wishlist() ) );
    if ( empty( $product_ids ) ) {
        wp_send_json_error();
    }

    $token = '';
    if ( is_user_logged_in() ) {
        $existing = get_user_meta( get_current_user_id(), '_headies_wishlist_share_token', true );
        if ( $existing && get_transient( 'headies_wl_share_' . $existing ) ) {
            $token = $existing;
        }
    }
    if ( ! $token ) {
        $token = wp_generate_password( 20, false );
        if ( is_user_logged_in() ) {
            update_user_meta( get_current_user_id(), '_headies_wishlist_share_token', $token );
        }
    }
    set_transient( 'headies_wl_share_' . $token, $product_ids, 90 * DAY_IN_SECONDS );

    wp_send_json_success( array(
        'url' => add_query_arg( 'share', $token, home_url( '/wishlist/' ) ),
    ) );
}
add_action( 'wp_ajax_headies_get_wishlist_share_link', 'headies_ajax_get_wishlist_share_link' );
add_action( 'wp_ajax_nopriv_headies_get_wishlist_share_link', 'headies_ajax_get_wishlist_share_link' );

function headies_add_to_bag_text() {
    return __( 'Add to Bag', 'headies' );
}
add_filter( 'woocommerce_product_single_add_to_cart_text', 'headies_add_to_bag_text' );

// ===== CART & CHECKOUT =====

// --- Slide-out cart drawer (New Era-style) — full page at /cart/ stays as
// a fallback, this is the fast add/update/remove path from the nav icon. ---

function headies_cart_drawer_state() {
    $cart = function_exists( 'WC' ) ? WC()->cart : null;
    if ( ! $cart ) {
        return array(
            'items_html'    => '',
            'count'         => 0,
            'subtotal_html' => '',
            'is_empty'      => true,
        );
    }

    $is_empty = $cart->is_empty();

    if ( $is_empty ) {
        $items_html = sprintf(
            '<div class="headies-drawer-empty"><p>Your bag is empty.</p><a href="%s" class="headies-pill-btn headies-pill-btn--dark">Continue Shopping</a></div>',
            esc_url( home_url( '/hats' ) )
        );
    } else {
        ob_start();
        foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
            $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
            if ( ! ( $_product instanceof WC_Product ) || ! $_product->exists() || $cart_item['quantity'] <= 0 ) {
                continue;
            }
            $permalink = $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '';
            $thumbnail = $_product->get_image( 'thumbnail' );
            $name      = $_product->get_name();
            ?>
            <div class="headies-drawer-item" data-cart-item-key="<?php echo esc_attr( $cart_item_key ); ?>">
                <div class="headies-drawer-item-image">
                    <?php if ( $permalink ) : ?>
                        <a href="<?php echo esc_url( $permalink ); ?>"><?php echo $thumbnail; // phpcs:ignore ?></a>
                    <?php else : ?>
                        <?php echo $thumbnail; // phpcs:ignore ?>
                    <?php endif; ?>
                </div>
                <div class="headies-drawer-item-info">
                    <span class="headies-drawer-item-name">
                        <?php if ( $permalink ) : ?>
                            <a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $name ); ?></a>
                        <?php else : ?>
                            <?php echo esc_html( $name ); ?>
                        <?php endif; ?>
                    </span>
                    <?php echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore ?>
                    <span class="headies-drawer-item-price"><?php echo wp_kses_post( WC()->cart->get_product_price( $_product ) ); ?></span>
                    <div class="headies-drawer-item-actions">
                        <div class="headies-drawer-qty">
                            <button type="button" class="headies-drawer-qty-btn" data-op="minus" aria-label="Decrease quantity">&minus;</button>
                            <span class="headies-drawer-qty-value"><?php echo esc_html( $cart_item['quantity'] ); ?></span>
                            <button type="button" class="headies-drawer-qty-btn" data-op="plus" aria-label="Increase quantity">+</button>
                        </div>
                        <button type="button" class="headies-drawer-item-remove">Remove</button>
                    </div>
                </div>
            </div>
            <?php
        }
        $items_html = ob_get_clean();
    }

    return array(
        'items_html'    => $items_html,
        'count'         => $cart->get_cart_contents_count(),
        'subtotal_html' => $cart->get_cart_subtotal(),
        'is_empty'      => $is_empty,
    );
}

function headies_render_cart_drawer() {
    if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
        return;
    }
    $state = headies_cart_drawer_state();
    ?>
    <div class="headies-drawer" id="headies-cart-drawer" hidden>
        <div class="headies-drawer-overlay" data-drawer-close></div>
        <div class="headies-drawer-panel<?php echo $state['is_empty'] ? ' is-empty' : ''; ?>" role="dialog" aria-modal="true" aria-labelledby="headies-cart-drawer-title">
            <div class="headies-drawer-head">
                <h2 id="headies-cart-drawer-title">Your Bag (<span class="headies-drawer-count"><?php echo esc_html( $state['count'] ); ?></span>)</h2>
                <button type="button" class="headies-drawer-close" data-drawer-close aria-label="Close">&times;</button>
            </div>
            <div class="headies-drawer-body"><?php echo $state['items_html']; // phpcs:ignore ?></div>
            <div class="headies-drawer-foot">
                <div class="headies-drawer-subtotal">
                    <span>Subtotal</span>
                    <span class="headies-drawer-subtotal-value"><?php echo wp_kses_post( $state['subtotal_html'] ); ?></span>
                </div>
                <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="headies-btn-primary headies-drawer-checkout">Checkout</a>
                <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="headies-drawer-view-cart">View Bag</a>
            </div>
        </div>
    </div>
    <?php
}
add_action( 'wp_footer', 'headies_render_cart_drawer' );

function headies_ajax_cart_drawer_response() {
    if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
        wp_send_json_error();
    }
    WC()->cart->calculate_totals();
    $state = headies_cart_drawer_state();
    wp_send_json_success( array(
        'itemsHtml'    => $state['items_html'],
        'count'        => $state['count'],
        'subtotalHtml' => wp_kses_post( $state['subtotal_html'] ),
        'isEmpty'      => $state['is_empty'],
    ) );
}

function headies_ajax_cart_drawer_refresh() {
    check_ajax_referer( 'headies_wishlist', 'nonce' );
    headies_ajax_cart_drawer_response();
}
add_action( 'wp_ajax_headies_cart_drawer_refresh', 'headies_ajax_cart_drawer_refresh' );
add_action( 'wp_ajax_nopriv_headies_cart_drawer_refresh', 'headies_ajax_cart_drawer_refresh' );

function headies_ajax_cart_drawer_update() {
    check_ajax_referer( 'headies_wishlist', 'nonce' );
    $cart_item_key = isset( $_POST['cart_item_key'] ) ? sanitize_text_field( wp_unslash( $_POST['cart_item_key'] ) ) : '';
    $op            = isset( $_POST['op'] ) ? sanitize_text_field( wp_unslash( $_POST['op'] ) ) : '';
    $cart          = function_exists( 'WC' ) ? WC()->cart : null;

    if ( ! $cart || ! $cart_item_key || ! isset( $cart->get_cart()[ $cart_item_key ] ) ) {
        wp_send_json_error();
    }

    $current = $cart->get_cart()[ $cart_item_key ]['quantity'];
    $new_qty = 'plus' === $op ? $current + 1 : $current - 1;

    if ( $new_qty < 1 ) {
        $cart->remove_cart_item( $cart_item_key );
    } else {
        $cart->set_quantity( $cart_item_key, $new_qty, true );
    }

    headies_ajax_cart_drawer_response();
}
add_action( 'wp_ajax_headies_cart_drawer_update', 'headies_ajax_cart_drawer_update' );
add_action( 'wp_ajax_nopriv_headies_cart_drawer_update', 'headies_ajax_cart_drawer_update' );

function headies_ajax_cart_drawer_remove() {
    check_ajax_referer( 'headies_wishlist', 'nonce' );
    $cart_item_key = isset( $_POST['cart_item_key'] ) ? sanitize_text_field( wp_unslash( $_POST['cart_item_key'] ) ) : '';
    $cart          = function_exists( 'WC' ) ? WC()->cart : null;

    if ( ! $cart || ! $cart_item_key || ! isset( $cart->get_cart()[ $cart_item_key ] ) ) {
        wp_send_json_error();
    }

    $cart->remove_cart_item( $cart_item_key );
    headies_ajax_cart_drawer_response();
}
add_action( 'wp_ajax_headies_cart_drawer_remove', 'headies_ajax_cart_drawer_remove' );
add_action( 'wp_ajax_nopriv_headies_cart_drawer_remove', 'headies_ajax_cart_drawer_remove' );

// Keep the cart sidebar to just the order summary — no cross-sell upsells.
remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display', 10 );

// WooCommerce core always renders its own default, unstyled order-details
// table on the woocommerce_thankyou action — our custom thankyou.php already
// shows the same info in the styled summary/sidebar cards, so drop the
// duplicate. (Leaving woocommerce_thankyou itself firing for other plugins.)
remove_action( 'woocommerce_thankyou', 'woocommerce_order_details_table', 10 );

// The cart page already has a promo-code field, so drop the default
// "Have a coupon?" prompt at the top of checkout — it's redundant clutter.
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );

// Zimbabwe has no province list in WooCommerce core, so the "Province" field
// falls back to a plain text box. Add one so it renders as a dropdown.
function headies_add_zw_states( $states ) {
    $states['ZW'] = array(
        'HA' => 'Harare',
        'BU' => 'Bulawayo',
        'MA' => 'Manicaland',
        'MC' => 'Mashonaland Central',
        'ME' => 'Mashonaland East',
        'MW' => 'Mashonaland West',
        'MV' => 'Masvingo',
        'MN' => 'Matabeleland North',
        'MS' => 'Matabeleland South',
        'MI' => 'Midlands',
    );
    return $states;
}
add_filter( 'woocommerce_states', 'headies_add_zw_states' );

// We only deliver within Zimbabwe, so there's no need to ask for a separate
// shipping address — billing address doubles as the delivery address.
add_filter( 'pre_option_woocommerce_ship_to_destination', function () {
    return 'billing_only';
} );

// Pickup happens at Melusi Home Designs' physical stores — keep the branch
// list in one place so the checkout field and order displays stay in sync.
function headies_get_pickup_locations() {
    return array(
        'eastlea'   => array(
            'name'    => 'Eastlea Branch',
            'address' => '232 Samora Machel, opposite Water World',
            'phone'   => '+263 78 014 37800',
        ),
        'town'      => array(
            'name'    => 'Town Branch (cnr First St & Samora)',
            'address' => 'Melusi Home Designs, opposite NMB Bank',
            'phone'   => '0771 490 402',
        ),
        'belgravia' => array(
            'name'    => 'Belgravia Branch',
            'address' => '36 East Road',
            'phone'   => '+263 78 695 2265',
        ),
    );
}

// Reduce the checkout form to what we actually need: contact details and a
// Zimbabwe-shaped delivery address. Country is fixed to ZW (hidden field,
// still posted so shipping-zone matching and order data stay correct).
function headies_customize_checkout_fields( $fields ) {
    $fields['billing']['billing_first_name']['priority'] = 10;
    $fields['billing']['billing_last_name']['priority']  = 20;
    $fields['billing']['billing_email']['priority']      = 30;
    $fields['billing']['billing_phone']['priority']      = 40;
    $fields['billing']['billing_phone']['description']   = 'Used for delivery updates and payment confirmation.';

    unset( $fields['billing']['billing_company'] );
    unset( $fields['billing']['billing_postcode'] );

    $fields['billing']['billing_country']['type']     = 'hidden';
    $fields['billing']['billing_country']['default']  = 'ZW';
    $fields['billing']['billing_country']['required'] = false;

    $fields['billing']['billing_address_1']['label']       = 'Street Address';
    $fields['billing']['billing_address_1']['placeholder'] = 'House number and street name';
    $fields['billing']['billing_address_1']['priority']    = 50;
    $fields['billing']['billing_address_1']['class']       = array( 'form-row-wide' );

    $fields['billing']['billing_address_2']['label']       = 'Suburb';
    $fields['billing']['billing_address_2']['placeholder'] = '';
    $fields['billing']['billing_address_2']['required']    = true;
    $fields['billing']['billing_address_2']['priority']    = 60;
    $fields['billing']['billing_address_2']['class']       = array( 'form-row-wide' );

    $fields['billing']['billing_city']['label']    = 'City';
    $fields['billing']['billing_city']['priority'] = 70;
    $fields['billing']['billing_city']['class']    = array( 'form-row-first' );

    $fields['billing']['billing_state']['label']    = 'Province';
    $fields['billing']['billing_state']['priority'] = 80;
    $fields['billing']['billing_state']['class']    = array( 'form-row-last' );

    if ( isset( $fields['order']['order_comments'] ) ) {
        $fields['order']['order_comments']['label']       = 'Message (optional)';
        $fields['order']['order_comments']['placeholder']  = 'Gate code, landmark, or a note for the rider.';
    }

    return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'headies_customize_checkout_fields' );

add_filter( 'default_checkout_billing_state', function () {
    return 'HA';
} );

// --- Pickup location field (shown only when Collect In Store is chosen) ---
function headies_pickup_location_field() {
    $locations = headies_get_pickup_locations();
    ?>
    <div class="headies-pickup-field" style="display:none;">
        <p class="form-row form-row-wide">
            <label for="headies_pickup_location">Pickup Branch <span class="required">*</span></label>
            <select name="headies_pickup_location" id="headies_pickup_location" class="select">
                <option value="">Choose a branch&hellip;</option>
                <?php foreach ( $locations as $key => $loc ) : ?>
                    <option value="<?php echo esc_attr( $loc['name'] ); ?>"><?php echo esc_html( $loc['name'] . ' — ' . $loc['address'] . ' — ' . $loc['phone'] ); ?></option>
                <?php endforeach; ?>
            </select>
        </p>
    </div>
    <?php
}
// Rendered directly from woocommerce/checkout/form-billing.php's Delivery
// section (next to the delivery-method radios), not via a hook — see that
// template for why.

// WC_Checkout builds its cached fields array (and applies
// woocommerce_checkout_fields) the moment WC()->checkout() is first touched
// in a request — which happens before process_checkout() has saved this
// same submission's shipping-method choice to the session. So on the
// checkout POST itself, session state is one step behind; trust the
// just-submitted $_POST first and only fall back to session for plain page
// renders (GET requests, before any shipping_method has been posted).
function headies_is_pickup_chosen() {
    if ( isset( $_POST['shipping_method'] ) && is_array( $_POST['shipping_method'] ) ) {
        foreach ( $_POST['shipping_method'] as $method ) {
            if ( is_string( $method ) && 0 === strpos( $method, 'local_pickup' ) ) {
                return true;
            }
        }
        return false;
    }
    $chosen = WC()->session ? WC()->session->get( 'chosen_shipping_methods' ) : array();
    if ( is_array( $chosen ) ) {
        foreach ( $chosen as $method ) {
            if ( 0 === strpos( $method, 'local_pickup' ) ) {
                return true;
            }
        }
    }
    return false;
}

// Collect In Store needs no delivery address, so don't require one — the
// fields are also hidden client-side (see js/checkout.js).
function headies_relax_delivery_address_when_pickup( $fields ) {
    if ( ! headies_is_pickup_chosen() ) {
        return $fields;
    }
    foreach ( array( 'billing_address_1', 'billing_address_2', 'billing_city', 'billing_state' ) as $key ) {
        if ( isset( $fields['billing'][ $key ] ) ) {
            $fields['billing'][ $key ]['required'] = false;
        }
    }
    return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'headies_relax_delivery_address_when_pickup', 20 );

function headies_validate_pickup_location( $data, $errors ) {
    if ( headies_is_pickup_chosen() && empty( $_POST['headies_pickup_location'] ) ) {
        $errors->add( 'validation', 'Please choose a pickup branch.' );
    }
}
add_action( 'woocommerce_after_checkout_validation', 'headies_validate_pickup_location', 10, 2 );

function headies_save_pickup_location( $order, $data ) {
    if ( ! empty( $_POST['headies_pickup_location'] ) ) {
        $order->update_meta_data( '_headies_pickup_location', sanitize_text_field( wp_unslash( $_POST['headies_pickup_location'] ) ) );
    }
}
add_action( 'woocommerce_checkout_create_order', 'headies_save_pickup_location', 10, 2 );

function headies_admin_show_pickup_location( $order ) {
    $loc = $order->get_meta( '_headies_pickup_location' );
    if ( $loc ) {
        echo '<p><strong>Pickup Branch:</strong> ' . esc_html( $loc ) . '</p>';
    }
}
add_action( 'woocommerce_admin_order_data_after_shipping_address', 'headies_admin_show_pickup_location' );

// woocommerce_order_details_table() is hooked to BOTH woocommerce_thankyou
// and woocommerce_view_order in WC core (wc-template-hooks.php). We only
// remove it from woocommerce_thankyou above (the custom thankyou.php shows
// pickup info inline already), so this still needs to fire on the My
// Account > Orders > View Order page, which uses woocommerce_view_order and
// has no other pickup-branch display of its own.
function headies_thankyou_show_pickup_location( $order ) {
    $loc = $order->get_meta( '_headies_pickup_location' );
    if ( $loc ) {
        echo '<p class="headies-pickup-note"><strong>Pickup Branch:</strong> ' . esc_html( $loc ) . '</p>';
    }
}
add_action( 'woocommerce_order_details_after_order_table', 'headies_thankyou_show_pickup_location' );

function headies_email_pickup_location( $fields, $sent_to_admin, $order ) {
    $loc = $order->get_meta( '_headies_pickup_location' );
    if ( $loc ) {
        $fields['pickup_location'] = array(
            'label' => 'Pickup Branch',
            'value' => $loc,
        );
    }
    return $fields;
}
add_filter( 'woocommerce_email_order_meta_fields', 'headies_email_pickup_location', 10, 3 );

// --- Shipping zone bootstrap (runs once) ---
function headies_bootstrap_shipping_zone() {
    if ( get_option( 'headies_shipping_bootstrap_v1' ) || ! class_exists( 'WC_Shipping_Zone' ) ) {
        return;
    }

    $zone = new WC_Shipping_Zone();
    $zone->set_zone_name( 'Zimbabwe' );
    $zone->add_location( 'ZW', 'country' );
    $zone->save();

    $methods = array(
        array(
            'method_id' => 'flat_rate',
            'title'     => 'Harare Courier',
            'cost'      => '5',
        ),
        array(
            'method_id' => 'local_pickup',
            'title'     => 'Collect In Store',
            'cost'      => '0',
        ),
    );

    $local_pickup_instance_id = null;

    foreach ( $methods as $method ) {
        $instance_id = $zone->add_shipping_method( $method['method_id'] );
        if ( ! $instance_id ) {
            continue;
        }
        $option_key         = 'woocommerce_' . $method['method_id'] . '_' . $instance_id . '_settings';
        $settings            = get_option( $option_key, array() );
        $settings['title']  = $method['title'];
        $settings['cost']   = $method['cost'];
        $settings['enabled'] = 'yes';
        update_option( $option_key, $settings );

        if ( 'local_pickup' === $method['method_id'] ) {
            $local_pickup_instance_id = $instance_id;
        }
    }

    // "Pay at Pickup" (Cash on Delivery) only makes sense when collecting in-store.
    if ( $local_pickup_instance_id ) {
        $cod_settings                       = get_option( 'woocommerce_cod_settings', array() );
        $cod_settings['enabled']            = 'yes';
        $cod_settings['title']              = 'Pay at Pickup';
        $cod_settings['description']        = 'Pay in cash or by card when you collect your order in-store.';
        $cod_settings['enable_for_methods'] = array( 'local_pickup:' . $local_pickup_instance_id );
        $cod_settings['enable_for_virtual'] = 'no';
        update_option( 'woocommerce_cod_settings', $cod_settings );
    }

    update_option( 'headies_shipping_bootstrap_v1', 1 );
}
add_action( 'init', 'headies_bootstrap_shipping_zone' );

// --- Paynow gateway ---
// No live Paynow API credentials yet (see README). This collects the order
// and puts it on hold pending manual payment confirmation via EcoCash,
// OneMoney, ZimSwitch or card — swap process_payment() for a real Paynow
// redirect once the account/API keys are in place.
function headies_add_paynow_gateway( $gateways ) {
    $gateways[] = 'Headies_Paynow_Gateway';
    return $gateways;
}
add_filter( 'woocommerce_payment_gateways', 'headies_add_paynow_gateway' );

function headies_init_paynow_gateway() {
    if ( ! class_exists( 'WC_Payment_Gateway' ) || class_exists( 'Headies_Paynow_Gateway' ) ) {
        return;
    }

    class Headies_Paynow_Gateway extends WC_Payment_Gateway {

        public function __construct() {
            $this->id                 = 'headies_paynow';
            $this->has_fields         = false;
            $this->method_title       = 'Paynow';
            $this->method_description = 'Accepts EcoCash, OneMoney, ZimSwitch and card payments via Paynow.';

            $this->init_form_fields();
            $this->init_settings();

            $this->title       = $this->get_option( 'title', 'Paynow' );
            $this->description = $this->get_option( 'description', "Pay with EcoCash, OneMoney, ZimSwitch or Visa/Mastercard. We'll send a payment request to your phone or card after you place the order." );
            $this->enabled     = $this->get_option( 'enabled', 'yes' );

            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        }

        public function init_form_fields() {
            $this->form_fields = array(
                'enabled'     => array(
                    'title'   => 'Enable/Disable',
                    'type'    => 'checkbox',
                    'label'   => 'Enable Paynow',
                    'default' => 'yes',
                ),
                'title'       => array(
                    'title'   => 'Title',
                    'type'    => 'text',
                    'default' => 'Paynow',
                ),
                'description' => array(
                    'title'   => 'Description',
                    'type'    => 'textarea',
                    'default' => "Pay with EcoCash, OneMoney, ZimSwitch or Visa/Mastercard. We'll send a payment request to your phone or card after you place the order.",
                ),
            );
        }

        public function process_payment( $order_id ) {
            $order = wc_get_order( $order_id );
            $order->update_status( 'on-hold', __( 'Awaiting Paynow payment confirmation.', 'headies' ) );

            if ( function_exists( 'WC' ) && WC()->cart ) {
                WC()->cart->empty_cart();
            }

            return array(
                'result'   => 'success',
                'redirect' => $this->get_return_url( $order ),
            );
        }
    }
}
// Themes load after 'plugins_loaded' has already fired, so hook this to
// 'init' instead — by then WooCommerce's autoloader has WC_Payment_Gateway
// available regardless.
add_action( 'init', 'headies_init_paynow_gateway' );

// The delivery-method radios live in the main content column (form-billing.php),
// outside the #order_review sidebar that WooCommerce's checkout AJAX normally
// refreshes. Add them as their own fragment so they still update live when
// the address changes, same as the order totals and payment methods do.
function headies_delivery_methods_fragment( $fragments ) {
    ob_start();
    ?>
    <div class="headies-delivery-methods-inner">
        <?php if ( WC()->cart->needs_shipping() ) : ?>
            <?php wc_cart_totals_shipping_html(); ?>
        <?php endif; ?>
    </div>
    <?php
    $fragments['.headies-delivery-methods-inner'] = ob_get_clean();
    return $fragments;
}
add_filter( 'woocommerce_update_order_review_fragments', 'headies_delivery_methods_fragment' );

function headies_enqueue_cart_checkout_scripts() {
    if ( function_exists( 'is_cart' ) && is_cart() ) {
        $path = get_stylesheet_directory() . '/js/cart.js';
        wp_enqueue_script( 'headies-cart', get_stylesheet_directory_uri() . '/js/cart.js', array( 'jquery' ), file_exists( $path ) ? filemtime( $path ) : false, true );
    }
    if ( function_exists( 'is_checkout' ) && is_checkout() && ! is_wc_endpoint_url( 'order-received' ) ) {
        $path = get_stylesheet_directory() . '/js/checkout.js';
        wp_enqueue_script( 'headies-checkout', get_stylesheet_directory_uri() . '/js/checkout.js', array( 'jquery' ), file_exists( $path ) ? filemtime( $path ) : false, true );
    }
    if ( is_product() ) {
        // Depends on 'headies-wishlist' (enqueued sitewide) for the
        // headiesWishlist ajaxUrl/nonce object it reuses.
        $path = get_stylesheet_directory() . '/js/single-product.js';
        wp_enqueue_script( 'headies-single-product', get_stylesheet_directory_uri() . '/js/single-product.js', array( 'headies-wishlist' ), file_exists( $path ) ? filemtime( $path ) : false, true );
    }
}
add_action( 'wp_enqueue_scripts', 'headies_enqueue_cart_checkout_scripts' );

// ===== MY ACCOUNT / LOGIN =====

/**
 * WooCommerce's login handler runs the WP_Error message from wp_signon()
 * through this exact filter before turning it into a notice (see
 * WC_Form_Handler::process_login()) — so it's the one place that catches
 * every wrong-credential case ("invalid username", "unknown email",
 * "incorrect password"...) and lets us show one plain, simple message
 * instead of WordPress's verbose, username-revealing defaults.
 */
function headies_simplify_login_error() {
    return __( 'Incorrect email or password.', 'headies' );
}
add_filter( 'login_errors', 'headies_simplify_login_error' );

