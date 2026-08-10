document.addEventListener( 'DOMContentLoaded', function () {

	if ( typeof headiesWishlist === 'undefined' ) {
		return;
	}

	function setCounts( selector, count ) {
		document.querySelectorAll( selector ).forEach( function ( el ) {
			el.textContent = count;
		} );
	}

	function post( action, data ) {
		var body = new URLSearchParams( Object.assign( { action: action, nonce: headiesWishlist.nonce }, data ) );
		return fetch( headiesWishlist.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: body.toString(),
		} ).then( function ( res ) {
			return res.json();
		} );
	}

	// --- Wishlist heart toggles (product cards, single product page) ---
	document.addEventListener( 'click', function ( e ) {
		var button = e.target.closest( '.headies-wishlist-toggle' );
		if ( ! button ) {
			return;
		}
		e.preventDefault();
		var productId = button.getAttribute( 'data-product-id' );
		button.disabled = true;

		post( 'headies_toggle_wishlist', { product_id: productId } ).then( function ( response ) {
			button.disabled = false;
			if ( ! response.success ) {
				return;
			}
			var inWishlist = response.data.in_wishlist;
			button.classList.toggle( 'is-active', inWishlist );
			button.setAttribute( 'aria-pressed', inWishlist ? 'true' : 'false' );
			setCounts( '.wishlist-count', response.data.wishlist_count );

			// On the wishlist page itself, removing an item drops its card.
			var card = button.closest( '.headies-wishlist-card' );
			if ( card && ! inWishlist ) {
				card.remove();
				var heading = document.querySelector( '.headies-wishlist-count' );
				if ( heading ) {
					heading.textContent = '(' + response.data.wishlist_count + ')';
				}
				if ( 0 === response.data.wishlist_count ) {
					window.location.reload();
				}
			}
		} ).catch( function () {
			button.disabled = false;
		} );
	} );

	// --- Add a single wishlist item to the cart ---
	document.addEventListener( 'click', function ( e ) {
		var button = e.target.closest( '.headies-wishlist-add-to-cart' );
		if ( ! button || button.classList.contains( 'is-in-cart' ) ) {
			return;
		}
		e.preventDefault();
		var productId = button.getAttribute( 'data-product-id' );
		button.disabled = true;
		button.textContent = 'Adding…';

		post( 'headies_add_to_cart', { product_id: productId } ).then( function ( response ) {
			button.disabled = false;
			if ( ! response.success ) {
				button.textContent = 'Add to Cart';
				return;
			}
			setCounts( '.cart-count', response.data.cart_count );
			button.textContent = 'In Cart';
			button.classList.add( 'is-in-cart' );
		} ).catch( function () {
			button.disabled = false;
			button.textContent = 'Add to Cart';
		} );
	} );

	// --- Add every wishlist item to the cart, then head to checkout ---
	var addAllButton = document.querySelector( '.headies-wishlist-add-all' );
	if ( addAllButton ) {
		addAllButton.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			addAllButton.disabled = true;
			addAllButton.textContent = 'Adding…';

			post( 'headies_add_all_wishlist_to_cart', {} ).then( function ( response ) {
				if ( response.success && response.data.redirect ) {
					window.location.href = response.data.redirect;
					return;
				}
				addAllButton.disabled = false;
				addAllButton.textContent = 'Add All to Cart';
			} ).catch( function () {
				addAllButton.disabled = false;
				addAllButton.textContent = 'Add All to Cart';
			} );
		} );
	}

	// --- Share the wishlist page link ---
	var shareButton = document.querySelector( '.headies-wishlist-share' );
	if ( shareButton ) {
		shareButton.addEventListener( 'click', function () {
			var url = window.location.href;
			if ( navigator.share ) {
				navigator.share( { title: document.title, url: url } ).catch( function () {} );
				return;
			}
			navigator.clipboard.writeText( url ).then( function () {
				var original = shareButton.textContent;
				shareButton.textContent = 'Link Copied';
				setTimeout( function () {
					shareButton.textContent = original;
				}, 2000 );
			} );
		} );
	}

} );
