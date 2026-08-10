<?php
/**
 * Search results — reuses the Hats page's product grid design so results
 * feel like part of the same catalog instead of Storefront's default
 * blog-style search template (sidebar, plain post loop).
 */
get_header();

$headies_search_term = get_search_query();
?>

<div class="hats-page">

  <div class="search-results-header">
    <p class="search-results-label"><?php esc_html_e( 'Search results for', 'headies' ); ?></p>
    <h1 class="search-results-term">&#8220;<?php echo esc_html( $headies_search_term ); ?>&#8221;</h1>
  </div>

  <?php
  $search_query = headies_get_search_hats_query( $headies_search_term, 1 );

  if ( $search_query->have_posts() ) : ?>

    <div class="hats-grid">
      <?php while ( $search_query->have_posts() ) : $search_query->the_post();
        headies_render_hats_card( get_the_ID() );
      endwhile; ?>
    </div>

    <?php if ( $search_query->max_num_pages > 1 ) : ?>
      <div class="hats-load-more-wrap"
        data-page="1"
        data-max-pages="<?php echo esc_attr( $search_query->max_num_pages ); ?>"
        data-ajax-action="headies_load_more_search"
        data-search="<?php echo esc_attr( $headies_search_term ); ?>">
        <button type="button" class="hats-load-more">Load More</button>
      </div>
    <?php endif; ?>

    <?php wp_reset_postdata(); ?>

  <?php else : ?>
    <p style="text-align:center; padding: 60px 0;">
      <?php printf( esc_html__( 'No results found for "%s".', 'headies' ), esc_html( $headies_search_term ) ); ?>
    </p>
  <?php endif; ?>

</div>

<?php get_footer(); ?>
