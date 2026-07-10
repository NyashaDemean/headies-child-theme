<?php get_header(); ?>

<?php
$headies_drops = headies_get_drops();
$headies_live_drop = null;
foreach ( $headies_drops as $d ) {
    if ( $d['status'] === 'live' ) { $headies_live_drop = $d; break; }
}
if ( $headies_live_drop ) : ?>
<div class="drop-bar">
  <?php echo esc_html( strtoupper( $headies_live_drop['name'] ) ); ?> · LIMITED CUSTOM CAPS · WHEN THEY'RE GONE, THEY'RE GONE
</div>
<?php endif; ?>

<section class="headies-hero" style="background-image: url('<?php echo esc_url( get_stylesheet_directory_uri() . '/images/hero-cap.jpg' ); ?>');">
  <div class="hero-overlay">
    <h1>Good caps, always. Great ones, sometimes.</h1>
    <p>Reliable everyday headwear you can grab anytime. Bedazzled and floral customizations drop occasionally — worth the wait.</p>
    <a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="hero-cta">Shop caps</a>
  </div>
</section>

<?php get_footer(); ?>
