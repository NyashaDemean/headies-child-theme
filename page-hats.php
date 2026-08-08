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
  $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

  $hats_query = new WP_Query( array(
    'post_type'      => 'product',
    'posts_per_page' => 8,
    'paged'          => $paged,
    'tax_query'      => array(
      array(
        'taxonomy' => 'product_cat',
        'field'    => 'slug',
        'terms'    => 'hats',
      ),
    ),
  ) );

  if ( $hats_query->have_posts() ) : ?>

    <div class="hats-grid">
      <?php while ( $hats_query->have_posts() ) : $hats_query->the_post();
        global $product;
        $gallery_ids = $product->get_gallery_image_ids();
        $back_image  = ! empty( $gallery_ids ) ? wp_get_attachment_image_url( $gallery_ids[0], 'woocommerce_single' ) : '';
        $front_image = get_the_post_thumbnail_url( get_the_ID(), 'woocommerce_single' );
      ?>
        <div class="hats-card">
          <a href="<?php the_permalink(); ?>" class="hats-card-image">
            <img class="hats-img-front" src="<?php echo esc_url( $front_image ); ?>" alt="<?php the_title_attribute(); ?>">
            <?php if ( $back_image ) : ?>
              <img class="hats-img-back" src="<?php echo esc_url( $back_image ); ?>" alt="<?php the_title_attribute(); ?> underbrim">
            <?php endif; ?>
          </a>
          <?php headies_wishlist_button( get_the_ID() ); ?>

          <div class="hats-card-info">
            <span class="hats-name"><?php the_title(); ?></span>
            <span class="hats-price"><?php echo wc_price( $product->get_price() ); ?></span>
          </div>
        </div>
      <?php endwhile; ?>
    </div>

    <div class="hats-load-more-wrap">
      <?php
      $next_link = get_next_posts_link( 'Load More', $hats_query->max_num_pages );
      if ( $next_link ) {
        echo str_replace( '<a', '<a class="hats-load-more"', $next_link );
      }
      ?>
    </div>

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

.hats-page{ background:#ffffff; padding: 0; }
.hats-page-header{ padding: 34px 40px 18px; }
.hats-page-header h1{
  font-family: 'Cleo Folk', Georgia, serif;
  font-size: 34px; font-weight: 800; text-transform: uppercase;
  letter-spacing: 0.03em; color:#111;
}

.hats-grid{
  display:grid;
  grid-template-columns:repeat(3, 1fr);
  gap:0;
  max-width:1400px;
  margin:0 auto;
  border-top:1px solid #e7e7e7;
  border-left:1px solid #e7e7e7;
}
@media (max-width: 900px){
  .hats-grid{ grid-template-columns:repeat(2, 1fr); }
  .hats-hero{ min-height:220px; }
}

.hats-card{
  border-right:1px solid #e7e7e7;
  border-bottom:1px solid #e7e7e7;
}

.hats-card-image{
  position:relative; display:block;
  width:100%; aspect-ratio: 4 / 3; overflow:hidden; background:#f4f4f4;
}
.hats-card-image img{
  position:absolute; inset:0; width:100%; height:100%;
  object-fit:contain; transition: opacity .25s ease;
}
.hats-img-back{ opacity:0; }
.hats-card:hover .hats-img-front{ opacity:0; }
.hats-card:hover .hats-img-back{ opacity:1; }

.hats-card-info{
  display:flex; justify-content:space-between; align-items:center;
  padding: 12px 14px; font-size:13.5px; font-family: 'Nunito', sans-serif;
  border-top:1px solid #e7e7e7;
}
.hats-name{ font-weight:700; text-transform:uppercase; letter-spacing:0.02em; max-width:75%; line-height:1.35; }
.hats-price{ font-weight:700; color:#111; white-space:nowrap; }

.hats-load-more-wrap{ display:flex; justify-content:center; padding: 46px 0 30px; }
.hats-load-more{
  border:1px solid #111; background:#fff; padding:14px 46px;
  font-size:12px; font-weight:800; letter-spacing:0.14em; text-transform:uppercase;
  text-decoration:none; color:#111; display:inline-block;
  transition: background .15s ease, color .15s ease;
}
.hats-load-more:hover{ background:#2359A9; color:#fff; border-color:#2359A9; }
</style>

<?php get_footer(); ?>

