document.addEventListener( 'DOMContentLoaded', function () {
	var grid = document.querySelector( '.hats-grid' );
	var wrap = document.querySelector( '.hats-load-more-wrap' );
	if ( ! grid || ! wrap || typeof headiesHats === 'undefined' ) {
		return;
	}

	var btn        = wrap.querySelector( '.hats-load-more' );
	var loading    = false;
	var ajaxAction = wrap.getAttribute( 'data-ajax-action' ) || 'headies_load_more_hats';
	var searchTerm = wrap.getAttribute( 'data-search' ) || '';

	btn.addEventListener( 'click', function () {
		if ( loading ) {
			return;
		}
		loading = true;

		var originalText = btn.textContent;
		btn.textContent  = 'Loading…';
		btn.disabled     = true;

		var nextPage = parseInt( wrap.getAttribute( 'data-page' ), 10 ) + 1;
		var maxPages = parseInt( wrap.getAttribute( 'data-max-pages' ), 10 );

		var formData = new FormData();
		formData.append( 'action', ajaxAction );
		formData.append( 'nonce', headiesHats.nonce );
		formData.append( 'page', nextPage );
		if ( searchTerm ) {
			formData.append( 'search', searchTerm );
		}

		fetch( headiesHats.ajaxUrl, { method: 'POST', body: formData } )
			.then( function ( res ) { return res.json(); } )
			.then( function ( data ) {
				if ( data.success && data.data.html ) {
					grid.insertAdjacentHTML( 'beforeend', data.data.html );
					wrap.setAttribute( 'data-page', nextPage );
					if ( data.data.maxPages ) {
						maxPages = data.data.maxPages;
					}
				}

				if ( nextPage >= maxPages ) {
					wrap.remove();
					return;
				}

				btn.textContent = originalText;
				btn.disabled    = false;
				loading         = false;
			} )
			.catch( function () {
				btn.textContent = originalText;
				btn.disabled    = false;
				loading         = false;
			} );
	} );
} );
