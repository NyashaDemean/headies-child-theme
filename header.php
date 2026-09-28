<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="preload" href="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/fonts/Inter-Regular.woff2' ); ?>" as="font" type="font/woff2" crossorigin>
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="hfeed site">

  <?php
$headies_drops_bar = headies_get_drops();
$headies_live_drop_bar = null;
foreach ( $headies_drops_bar as $d ) {
    if ( $d['status'] === 'live' ) { $headies_live_drop_bar = $d; break; }
}
?>
<?php if ( function_exists( 'is_checkout' ) && is_checkout() && ! is_wc_endpoint_url( 'order-received' ) ) : ?>
<header id="masthead" class="headies-nav headies-nav--checkout">
    <div class="nav-inner nav-inner--checkout">
      <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="headies-checkout-back">&larr; Back to Cart</a>
      <div class="nav-logo">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
          <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/images/logos/logo-solid-blue.png' ); ?>" alt="Headies">
        </a>
      </div>
      <span class="headies-checkout-secure">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
        Secure Checkout
      </span>
    </div>
  </header>
<?php else : ?>
<header id="masthead" class="headies-nav">
  <?php if ( $headies_live_drop_bar ) : ?>
  <div class="drop-bar">
    <?php echo esc_html( strtoupper( $headies_live_drop_bar['name'] ) ); ?> · LIMITED CUSTOM CAPS · WHEN THEY'RE GONE, THEY'RE GONE
  </div>
  <?php endif; ?>

    <div class="nav-inner">

      <button type="button" class="nav-hamburger" aria-label="Menu" aria-expanded="false">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
      </button>

      <div class="nav-links">
        <a href="<?php echo esc_url( home_url( '/drops' ) ); ?>">Drops</a>
        <a href="<?php echo esc_url( home_url( '/hats' ) ); ?>">Hats</a>
        <a href="<?php echo esc_url( home_url( '/accessories' ) ); ?>">Accessories</a>
      </div>

      <div class="nav-logo">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
          <span class="nav-logo-wordmark">
            <img class="logo-default" src="<?php echo esc_url( get_stylesheet_directory_uri() . '/images/logos/logo-solid-white.png' ); ?>" alt="Headies">
            <img class="logo-hover" src="<?php echo esc_url( get_stylesheet_directory_uri() . '/images/logos/headies-logo-black.png' ); ?>" alt="Headies">
          </span>
          <span class="nav-logo-icon">
            <img class="logo-default" src="<?php echo esc_url( get_stylesheet_directory_uri() . '/images/logos/icon-cap-white.png' ); ?>" alt="Headies">
            <img class="logo-hover" src="<?php echo esc_url( get_stylesheet_directory_uri() . '/images/logos/icon-cap-black.png' ); ?>" alt="Headies">
          </span>
        </a>
      </div>

      <div class="nav-icons">
        <a href="#" class="nav-icon nav-search-toggle" aria-label="Search">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </a>
        <a href="<?php echo esc_url( home_url( '/wishlist' ) ); ?>" class="nav-icon nav-wishlist" aria-label="Wishlist">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8z"/></svg>
          <span class="wishlist-count"><?php echo esc_html( function_exists( 'headies_wishlist_count' ) ? headies_wishlist_count() : 0 ); ?></span>
        </a>
        <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="nav-icon" aria-label="Account">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/></svg>
        </a>
        <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="nav-icon nav-cart" aria-label="Cart">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6h15l-1.5 9h-12z"/><path d="M6 6L5 3H2"/><circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/></svg>
          <span class="cart-count"><?php echo esc_html( WC()->cart ? WC()->cart->get_cart_contents_count() : 0 ); ?></span>
        </a>
      </div>

      <div class="nav-search-bar">
        <?php echo get_search_form(); ?>

        <?php
        $headies_popular_searches  = headies_get_popular_searches();
        $headies_trending_ids      = headies_get_trending_products( 4 );
        ?>
        <?php if ( $headies_popular_searches || $headies_trending_ids ) : ?>
          <div class="nav-search-default">
            <?php if ( $headies_popular_searches ) : ?>
              <div class="nav-search-popular">
                <h3 class="nav-search-panel-heading">Popular Searches</h3>
                <ul class="nav-search-popular-list">
                  <?php foreach ( $headies_popular_searches as $headies_term ) : ?>
                    <li>
                      <a href="<?php echo esc_url( add_query_arg( 's', $headies_term, home_url( '/' ) ) ); ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <?php echo esc_html( $headies_term ); ?>
                      </a>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            <?php endif; ?>

            <?php if ( $headies_trending_ids ) : ?>
              <div class="nav-search-trending">
                <h3 class="nav-search-panel-heading">Trending Now</h3>
                <div class="nav-search-results-grid">
                  <?php foreach ( $headies_trending_ids as $headies_trending_id ) : ?>
                    <?php headies_search_result_card( $headies_trending_id ); ?>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endif; ?>
          </div>
        <?php endif; ?>

        <div class="nav-search-results" hidden>
          <div class="nav-search-results-grid"></div>
          <a href="#" class="nav-search-view-all">View all results</a>
        </div>
      </div>

    </div>
  </header>
<?php endif; ?>

  <div id="content" class="site-content">

