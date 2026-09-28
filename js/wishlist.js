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
			document.dispatchEvent( new CustomEvent( 'headies:cart-updated', { detail: { open: true } } ) );
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

	// --- Share List: fetches a public, read-only link to the current
	// wishlist (private account/cookie state isn't reachable by anyone else)
	// then either hands it to the OS share sheet or opens a platform popover. ---
	var shareButton = document.querySelector( '.headies-wishlist-share' );
	var sharePopover = document.querySelector( '.headies-share-popover' );

	function copyText( text, onDone ) {
		if ( navigator.clipboard && navigator.clipboard.writeText ) {
			navigator.clipboard.writeText( text ).then( function () {
				onDone( true );
			} ).catch( function () {
				onDone( false );
			} );
			return;
		}
		// Clipboard API needs a secure context (https, or localhost) — falls
		// back to a hidden-textarea copy everywhere else (e.g. local dev over http).
		var textarea = document.createElement( 'textarea' );
		textarea.value = text;
		textarea.style.position = 'fixed';
		textarea.style.opacity = '0';
		document.body.appendChild( textarea );
		textarea.focus();
		textarea.select();
		var copied = false;
		try {
			copied = document.execCommand( 'copy' );
		} catch ( err ) {
			copied = false;
		}
		document.body.removeChild( textarea );
		onDone( copied );
	}

	function closeSharePopover() {
		if ( sharePopover ) {
			sharePopover.hidden = true;
		}
	}

	if ( shareButton && sharePopover ) {
		var shareLinks = sharePopover.querySelectorAll( '.headies-share-option[data-network]' );
		var copyButton = sharePopover.querySelector( '.headies-share-copy' );

		shareButton.addEventListener( 'click', function ( e ) {
			e.stopPropagation();

			if ( ! sharePopover.hidden ) {
				closeSharePopover();
				return;
			}

			var original = shareButton.textContent;
			shareButton.disabled = true;
			shareButton.textContent = 'Preparing…';

			post( 'headies_get_wishlist_share_link', {} ).then( function ( response ) {
				shareButton.disabled = false;
				shareButton.textContent = original;

				if ( ! response.success || ! response.data.url ) {
					return;
				}
				var url = response.data.url;

				if ( navigator.share ) {
					navigator.share( { title: 'My Headies Wishlist', url: url } ).catch( function () {} );
					return;
				}

				var text = 'Check out my Headies wishlist';
				shareLinks.forEach( function ( link ) {
					switch ( link.getAttribute( 'data-network' ) ) {
						case 'whatsapp':
							link.href = 'https://wa.me/?text=' + encodeURIComponent( text + ' ' + url );
							break;
						case 'facebook':
							link.href = 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent( url );
							break;
						case 'twitter':
							link.href = 'https://twitter.com/intent/tweet?url=' + encodeURIComponent( url ) + '&text=' + encodeURIComponent( text );
							break;
						case 'email':
							link.href = 'mailto:?subject=' + encodeURIComponent( text ) + '&body=' + encodeURIComponent( text + '\n' + url );
							break;
					}
				} );

				if ( copyButton ) {
					copyButton.onclick = function () {
						copyText( url, function ( ok ) {
							var copyOriginal = copyButton.lastChild.textContent;
							copyButton.lastChild.textContent = ok ? ' Link Copied' : ' Copy Link';
							if ( ok ) {
								setTimeout( function () {
									copyButton.lastChild.textContent = copyOriginal;
									closeSharePopover();
								}, 1200 );
							} else {
								window.prompt( 'Copy this link:', url );
							}
						} );
					};
				}

				sharePopover.hidden = false;
			} ).catch( function () {
				shareButton.disabled = false;
				shareButton.textContent = original;
			} );
		} );

		document.addEventListener( 'click', function ( e ) {
			if ( ! sharePopover.hidden && ! sharePopover.contains( e.target ) ) {
				closeSharePopover();
			}
		} );
		document.addEventListener( 'keydown', function ( e ) {
			if ( 'Escape' === e.key ) {
				closeSharePopover();
			}
		} );
		sharePopover.querySelectorAll( 'a.headies-share-option' ).forEach( function ( link ) {
			link.addEventListener( 'click', function () {
				closeSharePopover();
			} );
		} );
	}

} );
