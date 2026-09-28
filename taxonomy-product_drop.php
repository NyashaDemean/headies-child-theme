<?php
/**
 * Single Drop page — /drop/{slug}/
 *
 * Hero (tagline + countdown while upcoming, "Now Available" once it's out),
 * the full write-up, then the product grid: the same hats-card component
 * used everywhere else, rendered "mystery style" (hide_info — no name/price,
 * no link through to checkout) while the drop hasn't happened yet, and
 * normally (badged "Exclusive") once it has.
 */
get_header();

$headies_drop_term = get_queried_object();
$headies_drop       = headies_build_drop_array( $headies_drop_term );
$headies_is_upcoming = 'upcoming' === $headies_drop['status'];

$headies_hero_image = $headies_drop['hero_image'] ?: ( $headies_drop['main_image'] ?: $headies_drop['image'] );

$headies_drop_products = new WP_Query( array(
	'post_type'      => 'product',
	'posts_per_page' => -1,
	'tax_query'      => array(
		array(
			'taxonomy' => 'product_drop',
			'field'    => 'term_id',
			'terms'    => $headies_drop['id'],
		),
	),
) );
?>

<section class="drop-hero" style="background-image: url('<?php echo esc_url( $headies_hero_image ); ?>');">
	<div class="drop-hero-overlay">
		<a href="<?php echo esc_url( home_url( '/drops' ) ); ?>" class="drop-hero-back">&larr; All Drops</a>

		<?php if ( $headies_is_upcoming ) : ?>
			<span class="drop-hero-countdown" data-dropdate="<?php echo esc_attr( $headies_drop['drop_datetime'] ); ?>">Loading&hellip;</span>
		<?php else : ?>
			<span class="drop-hero-status">Now Available</span>
		<?php endif; ?>
	</div>
</section>

<div class="drop-name-bar">
	<div class="drop-name-bar-inner">
		<h1 class="drop-hero-name"><?php echo esc_html( $headies_drop['name'] ); ?></h1>
		<?php if ( $headies_drop['tagline'] ) : ?>
			<p class="drop-hero-tagline"><?php echo esc_html( $headies_drop['tagline'] ); ?></p>
		<?php endif; ?>
		<?php if ( $headies_drop['full_desc'] ) : ?>
			<div class="drop-description">
				<?php echo wp_kses_post( wpautop( $headies_drop['full_desc'] ) ); ?>
			</div>
		<?php endif; ?>
	</div>
</div>

<?php if ( $headies_drop['video'] ) : ?>
	<section class="drop-video-section">
		<video class="drop-video" autoplay muted loop playsinline preload="auto" poster="<?php echo esc_url( $headies_hero_image ); ?>">
			<source src="<?php echo esc_url( $headies_drop['video'] ); ?>" type="video/mp4">
		</video>
	</section>
<?php endif; ?>

<div class="drop-page">

	<?php if ( $headies_is_upcoming ) : ?>
		<p class="drop-reveal-note">The lineup — full names, prices, and the ability to buy unlock the moment this drop goes live. Tap the heart on any piece to wishlist it now.</p>
	<?php endif; ?>

	<?php if ( $headies_drop_products->have_posts() ) : ?>
		<div class="hats-grid">
			<?php
			while ( $headies_drop_products->have_posts() ) :
				$headies_drop_products->the_post();
				headies_render_hats_card( get_the_ID(), array( 'hide_info' => $headies_is_upcoming ) );
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	<?php else : ?>
		<p style="text-align:center; padding: 60px 0;">No pieces added to this drop yet — check back soon.</p>
	<?php endif; ?>

</div>

<style>
/* Force nav transparent over the hero, same treatment as the Hats page. */
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

.drop-hero{
  position: relative;
  width: 100%; min-height: 460px;
  background-size: cover; background-position: center;
  display: flex; align-items: flex-end;
}
.drop-hero::before{
  content: ""; position: absolute; inset: 0;
  /* Scrim now anchors to the bottom, where the back link/status badge sit,
     so they read clearly instead of fighting the fixed top nav for space. */
  background: linear-gradient(0deg, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0.15) 40%, rgba(0,0,0,0) 70%);
}
.drop-hero-overlay{ position: relative; z-index: 1; padding: 40px 48px; }
.drop-hero-back{
  display: inline-block; margin-bottom: 18px;
  font-family: 'Inter', sans-serif; font-size: 12px; font-weight: 800;
  letter-spacing: 0.1em; text-transform: uppercase; color: #fff; text-decoration: none; opacity: .85;
}
.drop-hero-back:hover{ opacity: 1; }
.drop-hero-countdown{
  display: block; font-family: 'Inter', sans-serif; font-size: 13px; font-weight: 800;
  letter-spacing: 0.12em; text-transform: uppercase; color: var(--headies-accent);
}
.drop-hero-status{
  display: inline-block; font-family: 'Inter', sans-serif; font-size: 12px; font-weight: 800;
  letter-spacing: 0.12em; text-transform: uppercase; color: #fff; background: var(--headies-primary);
  padding: 6px 14px; border-radius: 999px;
}

/* Solid name + description bar right under the hero photo — matches the
   Hat Club reference (photo, then a black band with the drop's write-up). */
.drop-name-bar{ background: #111; }
.drop-name-bar-inner{ max-width: 900px; margin: 0 auto; padding: 40px 48px; }
.drop-hero-name{
  font-family: 'Fredoka', cursive; font-size: 40px; text-transform: uppercase;
  color: #fff; margin: 0 0 12px; line-height: 1.05;
}
.drop-hero-tagline{
  font-family: 'Inter', sans-serif; font-size: 16px; color: #ccc; margin: 0 0 20px;
}

.drop-video-section{ width: 100%; line-height: 0; background: #000; }
.drop-video{ width: 100%; max-height: 640px; object-fit: cover; display: block; }

.drop-page{ background: #fff; padding: 40px 40px 20px; max-width: 1400px; margin: 0 auto; }
.drop-description{
  font-family: 'Inter', sans-serif;
  font-size: 15px; line-height: 1.75; color: #ddd;
}
.drop-description p{ margin: 0 0 14px; }
.drop-reveal-note{
  max-width: 720px; margin: 0 auto 34px; padding: 16px 20px;
  background: #eef3fa; border-left: 3px solid var(--headies-primary);
  font-family: 'Inter', sans-serif; font-size: 13.5px; line-height: 1.6; color: #333;
}

@media (max-width: 900px){
  .drop-hero{ min-height: 320px; }
  .drop-name-bar-inner{ padding: 32px 24px; }
  .drop-hero-name{ font-size: 30px; }
  .drop-page{ padding: 30px 20px 10px; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
  var el = document.querySelector('.drop-hero-countdown');
  if (!el) return;
  var target = new Date(el.getAttribute('data-dropdate').replace(' ', 'T')).getTime();
  if (isNaN(target)) {
    el.textContent = 'Coming soon';
    return;
  }
  function pad(n) { return n < 10 ? '0' + n : n; }
  var timer = setInterval(update, 1000);
  update();
  function update() {
    var diff = target - new Date().getTime();
    if (diff <= 0) {
      el.textContent = 'Out now';
      clearInterval(timer);
      return;
    }
    var days = Math.floor(diff / 86400000);
    var hours = Math.floor((diff % 86400000) / 3600000);
    var mins = Math.floor((diff % 3600000) / 60000);
    var secs = Math.floor((diff % 60000) / 1000);
    el.textContent = 'Drops in ' + (days > 0 ? days + 'd ' : '') + pad(hours) + ':' + pad(mins) + ':' + pad(secs);
  }
});
</script>

<?php get_footer(); ?>
