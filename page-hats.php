<?php
/**
 * Template Name: Hats Page
 */
get_header(); ?>

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
          <div class="hats-card-icons">
            <button class="hats-icon-btn hats-wishlist" aria-label="Add to wishlist">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M12 21s-7.5-4.6-10-9.2C0.3 8 2 4 6 4c2.2 0 3.7 1.2 6 4.2C14.3 5.2 15.8 4 18 4c4 0 5.7 4 4 7.8-2.5 4.6-10 9.2-10 9.2z"/></svg>
            </button>
            <button class="hats-icon-btn hats-addbag" aria-label="Add to bag">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 8h16l-1.5 12.5a1 1 0 0 1-1 .9H6.5a1 1 0 0 1-1-.9L4 8Z"/><path d="M8 8V6a4 4 0 0 1 8 0v2"/></svg>
            </button>
          </div>

          <a href="<?php the_permalink(); ?>" class="hats-card-image">
            <img class="hats-img-front" src="<?php echo esc_url( $front_image ); ?>" alt="<?php the_title_attribute(); ?>">
            <?php if ( $back_image ) : ?>
              <img class="hats-img-back" src="<?php echo esc_url( $back_image ); ?>" alt="<?php the_title_attribute(); ?> underbrim">
            <?php endif; ?>
          </a>

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
.hats-page{ background:#ffffff; padding: 0 40px 40px; }
.hats-page-header{ padding: 34px 0 18px; }
.hats-page-header h1{
  font-family: 'Cleo Folk', Georgia, serif;
  font-size: 34px; font-weight: 800; text-transform: uppercase;
  letter-spacing: 0.03em; color:#111;
}

.hats-grid{
  display:grid;
  grid-template-columns:repeat(4, 1fr);
  border-top:1px solid #e7e7e7;
  border-left:1px solid #e7e7e7;
}
@media (max-width: 900px){
  .hats-grid{ grid-template-columns:repeat(2, 1fr); }
}

.hats-card{
  border-right:1px solid #e7e7e7;
  border-bottom:1px solid #e7e7e7;
  padding:16px 16px 20px;
  position:relative;
}
.hats-card-icons{ display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; }
.hats-icon-btn{
  background:none; border:none; padding:0; cursor:pointer;
  width:22px; height:22px; color:#111;
  transition: transform .15s ease, color .15s ease;
}
.hats-icon-btn:hover{ color:#2359A9; transform: scale(1.08); }
.hats-icon-btn svg{ width:100%; height:100%; }

.hats-card-image{
  position:relative; display:block;
  width:100%; aspect-ratio: 1 / 1; overflow:hidden; background:#fff;
}
.hats-card-image img{
  position:absolute; inset:0; width:100%; height:100%;
  object-fit:contain; transition: opacity .25s ease;
}
.hats-img-back{ opacity:0; }
.hats-card:hover .hats-img-front{ opacity:0; }
.hats-card:hover .hats-img-back{ opacity:1; }

.hats-card-info{
  display:flex; justify-content:space-between; align-items:flex-start;
  margin-top:14px; font-size:13.5px; font-family: 'Nunito', sans-serif;
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
