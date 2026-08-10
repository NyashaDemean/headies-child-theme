<?php
/**
 * Template Name: Accessories Page
 */
get_header(); ?>

<section class="hats-hero" style="background-image: url('<?php echo esc_url( get_stylesheet_directory_uri() . '/images/accessories-hero.jpg' ); ?>');"></section>


<div class="hats-page">

  <div class="hats-page-header">
    <h1>All Accessories</h1>
  </div>

  <?php
  $accessories_query = headies_get_accessories_query( 1 );

  if ( $accessories_query->have_posts() ) : ?>

    <div class="hats-grid">
      <?php while ( $accessories_query->have_posts() ) : $accessories_query->the_post();
        headies_render_hats_card( get_the_ID() );
      endwhile; ?>
    </div>

    <?php if ( $accessories_query->max_num_pages > 1 ) : ?>
      <div class="hats-load-more-wrap" data-page="1" data-max-pages="<?php echo esc_attr( $accessories_query->max_num_pages ); ?>" data-ajax-action="headies_load_more_accessories">
        <button type="button" class="hats-load-more">Load More</button>
      </div>
    <?php endif; ?>

    <?php wp_reset_postdata(); ?>

  <?php else : ?>
    <p style="text-align:center; padding: 60px 0;">No accessories added yet — check back soon.</p>
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
