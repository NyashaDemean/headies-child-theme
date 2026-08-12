document.addEventListener( 'DOMContentLoaded', function () {

	var drawer = document.getElementById( 'headies-cart-drawer' );
	if ( ! drawer || typeof headiesWishlist === 'undefined' ) {
		return;
	}

	var panel        = drawer.querySelector( '.headies-drawer-panel' );
	var body         = drawer.querySelector( '.headies-drawer-body' );
	var countEls     = drawer.querySelectorAll( '.headies-drawer-count' );
	var subtotalEl   = drawer.querySelector( '.headies-drawer-subtotal-value' );
	var busy         = false;

	function setState( data ) {
		body.innerHTML = data.itemsHtml;
		countEls.forEach( function ( el ) { el.textContent = data.count; } );
		if ( subtotalEl ) {
			subtotalEl.innerHTML = data.subtotalHtml;
		}
		panel.classList.toggle( 'is-empty', !! data.isEmpty );
		document.querySelectorAll( '.cart-count' ).forEach( function ( el ) {
			el.textContent = data.count;
		} );
	}

	function post( action, data ) {
		var payload = new URLSearchParams( Object.assign( { action: action, nonce: headiesWishlist.nonce }, data || {} ) );
		return fetch( headiesWishlist.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: payload.toString(),
		} ).then( function ( res ) { return res.json(); } );
	}

	function refresh() {
		if ( busy ) {
			return Promise.resolve();
		}
		busy = true;
		return post( 'headies_cart_drawer_refresh' ).then( function ( response ) {
			busy = false;
			if ( response.success ) {
				setState( response.data );
			}
		} ).catch( function () { busy = false; } );
	}

	function open() {
		drawer.hidden = false;
		document.body.style.overflow = 'hidden';
		window.requestAnimationFrame( function () {
			drawer.classList.add( 'is-open' );
		} );
	}

	function close() {
		drawer.classList.remove( 'is-open' );
		document.body.style.overflow = '';
		window.setTimeout( function () {
			drawer.hidden = true;
		}, 300 );
	}

	window.headiesOpenCartDrawer = function () {
		refresh();
		open();
	};

	// --- Nav cart icon opens the drawer instead of navigating to /cart/ ---
	document.addEventListener( 'click', function ( e ) {
		var trigger = e.target.closest( '.nav-cart' );
		if ( ! trigger ) {
			return;
		}
		e.preventDefault();
		window.headiesOpenCartDrawer();
	} );

	// --- Any add-to-cart flow on the site reports in here ---
	document.addEventListener( 'headies:cart-updated', function ( e ) {
		refresh();
		if ( e.detail && e.detail.open ) {
			open();
		}
	} );

	// --- Close: X button, overlay click, Escape ---
	drawer.querySelectorAll( '[data-drawer-close]' ).forEach( function ( el ) {
		el.addEventListener( 'click', close );
	} );
	document.addEventListener( 'keydown', function ( e ) {
		if ( 'Escape' === e.key && drawer.classList.contains( 'is-open' ) ) {
			close();
		}
	} );

	// --- Quantity steppers + remove, delegated since items re-render ---
	body.addEventListener( 'click', function ( e ) {
		var qtyBtn = e.target.closest( '.headies-drawer-qty-btn' );
		var removeBtn = e.target.closest( '.headies-drawer-item-remove' );

		if ( qtyBtn ) {
			var item = qtyBtn.closest( '.headies-drawer-item' );
			var key  = item ? item.getAttribute( 'data-cart-item-key' ) : '';
			if ( ! key || busy ) {
				return;
			}
			busy = true;
			post( 'headies_cart_drawer_update', { cart_item_key: key, op: qtyBtn.getAttribute( 'data-op' ) } )
				.then( function ( response ) {
					busy = false;
					if ( response.success ) {
						setState( response.data );
					}
				} )
				.catch( function () { busy = false; } );
			return;
		}

		if ( removeBtn ) {
			var removeItem = removeBtn.closest( '.headies-drawer-item' );
			var removeKey  = removeItem ? removeItem.getAttribute( 'data-cart-item-key' ) : '';
			if ( ! removeKey || busy ) {
				return;
			}
			busy = true;
			post( 'headies_cart_drawer_remove', { cart_item_key: removeKey } )
				.then( function ( response ) {
					busy = false;
					if ( response.success ) {
						setState( response.data );
					}
				} )
				.catch( function () { busy = false; } );
		}
	} );

} );
