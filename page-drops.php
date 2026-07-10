<?php get_header(); ?>

<div class="drops-page">
  <h1>Drops</h1>
  <p class="drops-intro">Everyday caps are always in the shop. These are the occasional limited customization runs — see what's live, what's next, and what's already sold out.</p>

  <?php
  $drops = headies_get_drops();
  $groups = array( 'live' => array(), 'upcoming' => array(), 'past' => array() );
  foreach ( $drops as $drop ) {
      $groups[ $drop['status'] ][] = $drop;
  }

  $labels = array(
      'live'     => 'Live now',
      'upcoming' => 'Coming up',
      'past'     => 'Past drops',
  );

  foreach ( $labels as $key => $label ) :
      if ( empty( $groups[ $key ] ) ) continue;
  ?>
    <h2 class="drops-section-title"><?php echo esc_html( $label ); ?></h2>
    <div class="drops-grid">
      <?php foreach ( $groups[ $key ] as $drop ) : ?>
        <div class="drop-card drop-<?php echo esc_attr( $key ); ?>">
          <h3><?php echo esc_html( $drop['name'] ); ?></h3>
          <p><?php echo esc_html( $drop['desc'] ); ?></p>
          <span class="drop-date"><?php echo esc_html( $drop['date'] ); ?></span>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endforeach; ?>
</div>

<?php get_footer(); ?>
