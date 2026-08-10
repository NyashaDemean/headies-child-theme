<?php
/**
 * Template Name: Hats Page
 */
get_header(); ?>

<section class="hats-hero" style="background-image: url('<?php echo esc_url( get_stylesheet_directory_uri() . '/images/hats-hero-classic-black.jpg' ); ?>');"></section>


<div class="hats-page">

  <div class="hats-page-header">
    <h1>All Hats</h1>
  </div>

  <?php
  $hats_query = headies_get_hats_query( 1 );

  if ( $hats_query->have_posts() ) : ?>

    <div class="hats-grid">
      <?php while ( $hats_query->have_posts() ) : $hats_query->the_post();
        headies_render_hats_card( get_the_ID() );
      endwhile; ?>
    </div>

    <?php if ( $hats_query->max_num_pages > 1 ) : ?>
      <div class="hats-load-more-wrap" data-page="1" data-max-pages="<?php echo esc_attr( $hats_query->max_num_pages ); ?>">
        <button type="button" class="hats-load-more">Load More</button>
      </div>
    <?php endif; ?>

    <?php wp_reset_postdata(); ?>

  <?php else : ?>
    <p style="text-align:center; padding: 60px 0;">No hats added yet — check back soon.</p>
  <?php endif; ?>

</div>

<style>
/* Force nav transparent on this page specifically, overriding anything else */
body .headies-nav:not(.nav-scrolled):not(:hover){
  background: transparent !important;
}
body .headies-nav:not(.nav-scrolled):not(:hover) .nav-links a,
body .headies-nav:not(.nav-scrolled):not(:hover) .nav-icon{
  color: #fff !important;
}
body .headies-nav:not(.nav-scrolled):not(:hover) .nav-logo .logo-default{
  display: block !important;
}
body .headies-nav:not(.nav-scrolled):not(:hover) .nav-logo .logo-hover{
  display: none !important;
}

.hats-hero{
  width:100%;
  min-height:320px;
  background-size:cover;
  background-position:center 26%;
}
@media (max-width: 900px){
  .hats-hero{ min-height:220px; }
}
</style>

<?php get_footer(); ?>

