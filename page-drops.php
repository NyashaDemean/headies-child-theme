<?php get_header(); ?>

<?php
$drops = headies_get_drops();
$groups = array( 'upcoming' => array(), 'past' => array() );
foreach ( $drops as $drop ) {
    if ( $drop['status'] === 'past' ) {
        $groups['past'][] = $drop;
    } else {
        $groups['upcoming'][] = $drop;
    }
}

$hero_drop = null;
foreach ( $groups['upcoming'] as $drop_row ) {
    if ( 'petals-pennants' === $drop_row['slug'] ) {
        $hero_drop = $drop_row;
    }
}
if ( ! $hero_drop ) {
    $hero_drop = ! empty( $groups['upcoming'] ) ? $groups['upcoming'][0] : ( ! empty( $groups['past'] ) ? $groups['past'][0] : null );
}
if ( $hero_drop ) {
    $hero_image = $hero_drop['hero_image'] ? $hero_drop['hero_image'] : ( $hero_drop['main_image'] ? $hero_drop['main_image'] : $hero_drop['image'] );
    $hero_link  = get_term_link( (int) $hero_drop['id'], 'product_drop' );
    $hero_link  = is_wp_error( $hero_link ) ? '#' : $hero_link;
}
?>

<?php if ( $hero_drop ) : ?>
  <section class="drops-hero" style="background-image: url('<?php echo esc_url( $hero_image ); ?>');">
    <?php if ( $hero_drop['video'] ) : ?>
      <video class="drops-hero-video" autoplay muted loop playsinline preload="auto" poster="<?php echo esc_url( $hero_image ); ?>">
        <source src="<?php echo esc_url( $hero_drop['video'] ); ?>" type="video/mp4">
      </video>
    <?php endif; ?>
    <div class="drops-hero-content">
      <h1 class="drops-hero-name"><?php echo esc_html( $hero_drop['name'] ); ?></h1>
      <a href="<?php echo esc_url( $hero_link ); ?>" class="headies-shop-cta">See Details</a>
    </div>
  </section>
<?php endif; ?>

<div class="drops-page">

  <h2 class="drops-section-title">Upcoming Drops</h2>
  <div class="drop-article-grid">
    <?php foreach ( $groups['upcoming'] as $drop ) : headies_render_drop_card( $drop ); endforeach; ?>
  </div>

  <h2 class="drops-section-title">Past Drops</h2>
  <div class="drop-article-grid">
    <?php foreach ( $groups['past'] as $drop ) : headies_render_drop_card( $drop ); endforeach; ?>
  </div>

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

.drops-hero{
  position: relative;
  width: 100%; min-height: 90vh;
  background-size: cover; background-position: center;
  display: flex; align-items: flex-end; justify-content: flex-end;
}
.drops-hero::before{
  content: ""; position: absolute; inset: 0; z-index: 1;
  background: linear-gradient(0deg, rgba(0,0,0,0.55) 0%, rgba(0,0,0,0) 45%);
}
.drops-hero-video{
  position: absolute; inset: 0; width: 100%; height: 100%;
  object-fit: cover;
}
.drops-hero-content{
  position: relative; z-index: 1;
  max-width: 480px; padding: 56px;
  text-align: right;
}
.drops-hero-name{
  font-family: 'Fredoka', cursive; font-size: 50px; text-transform: uppercase;
  color: #fff; margin: 0 0 16px; line-height: 1.05;
}
.drops-page{ background: #fff; padding: 0 16px 40px; }
.drops-section-title{
  font-family: 'Fredoka', cursive;
  font-size: 56px; text-transform: uppercase; color: #111;
  margin: 64px 0 24px; padding: 0 16px;
}

.drop-article-grid{
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
}
.drop-article-card{
  position: relative; display: block;
  aspect-ratio: 4 / 5;
  overflow: hidden;
  border-radius: 8px;
  text-decoration: none;
}
.drop-article-card__image{ position: absolute; inset: 0; background: #f2f2f2; }
.drop-article-card__image img{ width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .4s ease; }
.drop-article-card:hover .drop-article-card__image img{ transform: scale(1.04); }
.drop-article-card::after{
  content: ""; position: absolute; inset: 0;
  background: linear-gradient(0deg, rgba(0,0,0,0.65) 0%, rgba(0,0,0,0) 40%);
  pointer-events: none;
}
.drop-article-card__date{
  position: absolute; top: 8px; right: 8px; z-index: 2;
  background: var(--headies-accent); color: #0b2a4a;
  font-family: 'Inter', sans-serif; font-size: 12px; font-weight: 800;
  padding: 4px 12px; border-radius: 4px;
}
.drop-article-card__content{
  position: absolute; z-index: 2; bottom: 20px; left: 0; width: 100%;
  padding: 0 16px;
  display: flex; align-items: flex-end; justify-content: space-between; gap: 12px;
}
.drop-article-card__name{
  font-family: 'Inter', sans-serif; font-size: 18px; font-weight: 800; color: #fff;
}
.drop-article-card__arrow{ width: 20px; height: auto; color: #fff; flex: none; }

@media (max-width: 900px){
  .drops-hero{ min-height: 70vh; }
  .drops-hero-content{ padding: 32px 24px; max-width: 100%; }
  .drops-hero-name{ font-size: 34px; }
  .drops-section-title{ font-size: 34px; margin: 44px 0 18px; }
  .drop-article-grid{ grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 500px){
  .drop-article-grid{ grid-template-columns: 1fr; }
}
</style>

<?php get_footer(); ?>
