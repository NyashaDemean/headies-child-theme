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

?>

<?php if ( ! empty( $groups['upcoming'] ) ) : ?>
  <?php foreach ( $groups['upcoming'] as $drop ) : ?>
    <div class="upcoming-drop">
      <div class="upcoming-drop-image">
        <img src="<?php echo esc_url( $drop['image'] ); ?>" alt="<?php echo esc_attr( $drop['name'] ); ?>">
      </div>
      <div class="upcoming-drop-info">
        <span class="upcoming-drop-countdown" data-dropdate="<?php echo esc_attr( $drop['drop_datetime'] ); ?>">Loading...</span>
        <h2 class="upcoming-drop-name"><?php echo esc_html( $drop['name'] ); ?></h2>
        <p class="upcoming-drop-desc"><?php echo esc_html( $drop['desc'] ); ?></p>
        <a href="#" class="upcoming-drop-cta">Shop Now</a>
      </div>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

<div class="drops-page">
  <h1>Drops</h1>
  <p class="drops-intro">Everyday caps are always in the shop. These are the occasional limited customization runs — see what's live, what's next, and what's already sold out.</p>


  <?php if ( ! empty( $groups['past'] ) ) : ?>
    <h2 class="drops-section-title">Past Drops</h2>
    <div class="past-drops-scroll">
      <?php foreach ( $groups['past'] as $drop ) : ?>
        <a href="#" class="past-drop-card">
          <div class="past-drop-image">
            <img src="<?php echo esc_url( $drop['image'] ); ?>" alt="<?php echo esc_attr( $drop['name'] ); ?>">
          </div>
          <h3 class="past-drop-name"><?php echo esc_html( $drop['name'] ); ?></h3>
          <span class="past-drop-date"><?php echo esc_html( $drop['date'] ); ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<style>
.drops-page{ background:#ffffff; padding: 0 40px 40px; }
.drops-page h1{
  font-family: 'Cleo Folk', Georgia, serif;
  font-size: 34px; font-weight: 800; text-transform: uppercase;
  letter-spacing: 0.03em; color:#111; padding: 34px 0 12px;
}
.drops-intro{
  font-family: 'Nunito', sans-serif; font-size:15px; color:#444;
  max-width:640px; padding-bottom: 30px;
}

.upcoming-drop{
  position:relative; width:100%; aspect-ratio: 16/9; overflow:hidden;
  display:flex; align-items:flex-end;
}
.upcoming-drop-image{
  position:absolute; inset:0; width:100%; height:100%; z-index:1;
}
.upcoming-drop-image img{ width:100%; height:100%; object-fit:cover; }
.upcoming-drop::after{
  content:""; position:absolute; inset:0; z-index:2;
  background: linear-gradient(180deg, rgba(0,0,0,0) 40%, rgba(0,0,0,0.75) 100%);
}
.upcoming-drop-info{
  position:relative; z-index:3; padding: 40px 50px;
}
.upcoming-drop-countdown{
  font-family: 'Nunito', sans-serif; font-size:12px; font-weight:700;
  text-transform:uppercase; letter-spacing:0.1em; color:#41AAF5;
}
.upcoming-drop-countdown.is-live{ color:#4ADE80; }
.upcoming-drop-name{
  font-family: 'Cleo Folk', Georgia, serif; font-size:38px;
  text-transform:uppercase; margin:10px 0 14px; color:#fff;
}
.upcoming-drop-desc{
  font-family: 'Nunito', sans-serif; font-size:15px; color:#eee;
  max-width:480px; margin-bottom:24px;
}
.upcoming-drop-cta{
  display:inline-block; background:#41AAF5; color:#111;
  padding:14px 40px; font-size:13px; font-weight:800; letter-spacing:0.05em;
  text-decoration:none; border-radius:4px;
  transition: background .15s ease;
}
.upcoming-drop-cta:hover{ background:#2359A9; color:#fff; }

@media (max-width: 800px){
  .upcoming-drop{ aspect-ratio: 4/5; }
  .upcoming-drop-name{ font-size:28px; }
}

.drops-section-title{
  font-family: 'Cleo Folk', Georgia, serif; font-size:24px;
  text-transform:uppercase; color:#111; margin-bottom:20px;
}
.past-drops-scroll{
  display:flex; gap:0; overflow-x:auto; border-top:1px solid #e7e7e7;
  border-left:1px solid #e7e7e7; scroll-behavior:smooth;
}
.past-drop-card{
  flex: 0 0 33.333%; min-width:260px;
  border-right:1px solid #e7e7e7; border-bottom:1px solid #e7e7e7;
  padding:16px 16px 20px; text-decoration:none; color:inherit;
}
.past-drop-image{
  width:100%; aspect-ratio: 1/1; overflow:hidden; background:#f4f4f4;
}
.past-drop-image img{
  width:100%; height:100%; object-fit:cover; transition: transform .3s ease;
}
.past-drop-card:hover .past-drop-image img{ transform: scale(1.04); }
.past-drop-name{
  font-family: 'Nunito', sans-serif; font-size:13.5px; font-weight:700;
  text-transform:uppercase; letter-spacing:0.02em; color:#111; margin:14px 0 4px;
}
.past-drop-date{
  font-family: 'Nunito', sans-serif; font-size:12px; color:#777;
}

@media (max-width: 900px){
  .past-drop-card{ flex: 0 0 60%; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  var countdownEls = document.querySelectorAll('.upcoming-drop-countdown');
  countdownEls.forEach(function(el) {
    var target = new Date(el.getAttribute('data-dropdate').replace(' ', 'T')).getTime();

    function pad(n) { return n < 10 ? '0' + n : n; }

    var timer = setInterval(update, 1000);
    update();

    function update() {
      var now = new Date().getTime();
      var diff = target - now;

      if (diff <= 0) {
        el.textContent = 'OUT NOW';
        el.classList.add('is-live');
        clearInterval(timer);
        return;
      }

      var days = Math.floor(diff / (1000 * 60 * 60 * 24));
      var hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      var mins = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
      var secs = Math.floor((diff % (1000 * 60)) / 1000);

      el.textContent = days > 0
        ? 'DROPS IN ' + days + 'd ' + pad(hours) + ':' + pad(mins) + ':' + pad(secs)
        : 'DROPS IN ' + pad(hours) + ':' + pad(mins) + ':' + pad(secs);
    }
  });
});
</script>

<?php get_footer(); ?>

