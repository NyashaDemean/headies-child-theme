<?php
$headies_search_id = wp_unique_id( 'headies-search-field-' );
?>
<form role="search" method="get" class="nav-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<button type="submit" class="nav-search-icon-btn" aria-label="<?php esc_attr_e( 'Search', 'headies' ); ?>">
		<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
	</button>
	<label for="<?php echo esc_attr( $headies_search_id ); ?>" class="screen-reader-text"><?php echo esc_html_x( 'Search for:', 'label', 'headies' ); ?></label>
	<input type="search" id="<?php echo esc_attr( $headies_search_id ); ?>" class="search-field"
		placeholder="<?php esc_attr_e( 'Search hats, drops, and more', 'headies' ); ?>"
		value="<?php echo esc_attr( get_search_query() ); ?>" name="s" />
</form>
