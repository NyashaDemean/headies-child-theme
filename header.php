<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
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
<header id="masthead" class="headies-nav">
  <?php if ( $headies_live_drop_bar ) : ?>
  <div class="drop-bar">
    <?php echo esc_html( strtoupper( $headies_live_drop_bar['name'] ) ); ?> · LIMITED CUSTOM CAPS · WHEN THEY'RE GONE, THEY'RE GONE
  </div>
  <?php endif; ?>

    <div class="nav-inner">

      <div class="nav-links">
        <a href="<?php echo esc_url( home_url( '/drops' ) ); ?>">Drops</a>
        <a href="<?php echo esc_url( home_url( '/hats' ) ); ?>">Hats</a>
        <a href="<?php echo esc_url( home_url( '/accessories' ) ); ?>">Accessories</a>
      </div>

      <div class="nav-logo">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
          <img class="logo-default" src="<?php echo esc_url( get_stylesheet_directory_uri() . '/images/logo-solid-white.png' ); ?>" alt="Headies">
          <img class="logo-hover" src="<?php echo esc_url( get_stylesheet_directory_uri() . '/images/logo-solid-blue.png' ); ?>" alt="Headies">
        </a>
      </div>

      <div class="nav-icons">
        <a href="#" class="nav-icon nav-search-toggle" aria-label="Search">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </a>
        <a href="<?php echo esc_url( home_url( '/wishlist' ) ); ?>" class="nav-icon" aria-label="Wishlist">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8z"/></svg>
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
      </div>

    </div>
  </header>

  <div id="content" class="site-content">

