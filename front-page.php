<?php get_header(); ?>

<?php $headies_drops = headies_get_drops(); ?>

<section class="headies-hero" style="background-image: url('<?php echo esc_url( get_stylesheet_directory_uri() . '/images/hero-cap.jpg' ); ?>');">
  <div class="hero-overlay">
    <h1>Good caps, always. Great ones, sometimes.</h1>
    <p>Reliable everyday headwear you can grab anytime. Bedazzled and floral customizations drop occasionally — worth the wait.</p>
    <a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="hero-cta">Shop caps</a>
  </div>
</section>

<section class="recent-collections">
  <h2>Recent Collections</h2>
  <div class="collections-grid">
    <?php
    $headies_recent = array_slice( $headies_drops, 0, 4 );
    foreach ( $headies_recent as $drop ) :
    ?>
      <div class="collection-card">
        <div class="collection-image">
          <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/images/hero-cap.jpg' ); ?>" alt="<?php echo esc_attr( $drop['name'] ); ?>">
        </div>
        <p class="collection-name"><?php echo esc_html( $drop['name'] ); ?></p>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<?php get_footer(); ?>
