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
    return array(
        array(
            'name'   => 'Drop 002 — Floral Fitteds',
            'desc'   => 'Hand-stitched floral patches on classic fitted caps.',
            'status' => 'upcoming',
            'date'   => 'Coming August 2026',
        ),
    );
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
add_action( 'wp_footer', 'headies_nav_scroll_script' );

