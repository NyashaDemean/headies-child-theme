<?php get_header(); ?>

<?php
$headies_drops = headies_get_drops();

$headies_featured_drop = null;
$headies_trending_drop = null;
foreach ( $headies_drops as $headies_drop_row ) {
	if ( 'ivory-league' === $headies_drop_row['slug'] || 'the-ivory-league' === $headies_drop_row['slug'] ) {
		$headies_featured_drop = $headies_drop_row;
	}
	if ( 'petals-pennants' === $headies_drop_row['slug'] ) {
		$headies_trending_drop = $headies_drop_row;
	}
}
if ( ! $headies_featured_drop && ! empty( $headies_drops ) ) {
	$headies_featured_drop = $headies_drops[0];
}
if ( ! $headies_trending_drop && ! empty( $headies_drops ) ) {
	$headies_trending_drop = $headies_drops[0];
}

$headies_home_drop_image_id  = get_option( 'headies_home_drop_image_id' );
$headies_featured_banner_img = $headies_home_drop_image_id ? wp_get_attachment_image_url( $headies_home_drop_image_id, 'full' ) : '';
?>

<section class="headies-hero">
  <video id="headies-hero-video" class="hero-video" autoplay muted loop playsinline preload="auto"></video>
  <div class="hero-overlay">
    <h1>Exclusive wear.</h1>
    <a href="<?php echo esc_url( home_url( '/hats' ) ); ?>" class="headies-shop-cta">Shop now</a>
  </div>
</section>
<script>
(function () {
  // <source media="..."> only switches sources inside <picture> — <video> has
  // no native responsive-source mechanism, so the desktop/mobile cut has to
  // be picked and assigned in JS instead.
  var video = document.getElementById( 'headies-hero-video' );
  if ( ! video ) {
    return;
  }
  // Deferred a tick so the viewport has definitely settled before the
  // breakpoint check runs (some environments report a stale/zero width if
  // this reads synchronously at parse time).
  window.requestAnimationFrame( function () {
    var isMobile = window.matchMedia( '(max-width: 700px)' ).matches;
    video.src = isMobile
      ? '<?php echo esc_js( get_stylesheet_directory_uri() . '/images/' . rawurlencode( 'website videos' ) . '/' . rawurlencode( 'mobile hero.mp4' ) ); ?>'
      : '<?php echo esc_js( get_stylesheet_directory_uri() . '/images/' . rawurlencode( 'website videos' ) . '/' . rawurlencode( 'desktop hero.mp4' ) ); ?>';
    video.load();
    video.play().catch( function () {} );
  } );
})();
</script>

<section class="drops-section">

  <?php if ( $headies_featured_drop ) :
    $headies_featured_link = get_term_link( (int) $headies_featured_drop['id'], 'product_drop' );
    $headies_featured_link = is_wp_error( $headies_featured_link ) ? '#' : $headies_featured_link;
    $headies_featured_bg   = $headies_featured_banner_img ? $headies_featured_banner_img : $headies_featured_drop['main_image'];
  ?>
    <a href="<?php echo esc_url( $headies_featured_link ); ?>" class="featured-drop" style="background-image: url('<?php echo esc_url( $headies_featured_bg ); ?>');">
      <div class="featured-drop-info">
        <span class="featured-drop-eyebrow"><?php echo esc_html( 'upcoming' === $headies_featured_drop['status'] ? 'Upcoming Drop' : 'Now Available' ); ?></span>
        <h3 class="featured-drop-name"><?php echo esc_html( $headies_featured_drop['name'] ); ?></h3>
        <p class="featured-drop-desc"><?php echo esc_html( ( $headies_featured_drop['tagline'] ? $headies_featured_drop['tagline'] : $headies_featured_drop['desc'] ) . ' — ' . $headies_featured_drop['date'] . '.' ); ?></p>
        <span class="headies-shop-cta">View Drop</span>
      </div>
    </a>
  <?php endif; ?>

  <h2>Drops</h2>

  <div class="collections-grid">
    <?php foreach ( $headies_drops as $drop ) :
      $collection_image = ! empty( $drop['main_image'] ) ? $drop['main_image'] : ( ! empty( $drop['image'] ) ? $drop['image'] : get_stylesheet_directory_uri() . '/images/' . rawurlencode( 'website images' ) . '/hero-cap.jpg' );
      $drop_link         = get_term_link( (int) $drop['id'], 'product_drop' );
    ?>
      <a href="<?php echo esc_url( is_wp_error( $drop_link ) ? '#' : $drop_link ); ?>" class="collection-card">
        <div class="collection-image">
          <img src="<?php echo esc_url( $collection_image ); ?>" alt="<?php echo esc_attr( $drop['name'] ); ?>">
        </div>
        <p class="collection-name"><?php echo esc_html( $drop['name'] ); ?></p>
        <p class="collection-date"><?php echo esc_html( $drop['date'] ); ?></p>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<?php if ( $headies_trending_drop ) :
  $headies_trending_image = ! empty( $headies_trending_drop['hero_image'] ) ? $headies_trending_drop['hero_image'] : ( ! empty( $headies_trending_drop['main_image'] ) ? $headies_trending_drop['main_image'] : get_stylesheet_directory_uri() . '/images/' . rawurlencode( 'website images' ) . '/hero-cap.jpg' );
  $headies_trending_link  = get_term_link( (int) $headies_trending_drop['id'], 'product_drop' );
  $headies_trending_link  = is_wp_error( $headies_trending_link ) ? home_url( '/hats' ) : $headies_trending_link;
?>
<section class="trending-now" style="background-image: url('<?php echo esc_url( $headies_trending_image ); ?>');">
  <h2 class="trending-heading">Trending Now</h2>
  <div class="trending-info">
    <p class="trending-drop-name"><?php echo esc_html( $headies_trending_drop['name'] ); ?></p>
    <a href="<?php echo esc_url( $headies_trending_link ); ?>" class="headies-shop-cta">Shop Now</a>
  </div>
</section>
<?php endif; ?>

<?php get_footer(); ?>
