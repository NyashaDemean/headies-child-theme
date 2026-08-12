document.addEventListener( 'DOMContentLoaded', function () {

	var layout = document.querySelector( '.headies-product-layout' );
	if ( ! layout ) {
		return;
	}

	// --- Gallery: thumbnail swaps the main image with a quick crossfade ---
	var mainImage = layout.querySelector( '.headies-gallery-image' );
	layout.querySelectorAll( '.headies-gallery-thumb' ).forEach( function ( thumb ) {
		thumb.addEventListener( 'click', function () {
			var full = thumb.getAttribute( 'data-full' );
			if ( ! full || full === mainImage.src ) {
				return;
			}
			layout.querySelectorAll( '.headies-gallery-thumb' ).forEach( function ( t ) {
				t.classList.remove( 'is-active' );
			} );
			thumb.classList.add( 'is-active' );

			mainImage.classList.add( 'is-fading' );
			window.setTimeout( function () {
				mainImage.src = full;
				mainImage.classList.remove( 'is-fading' );
			}, 160 );
		} );
	} );

	// --- Size buttons drive WooCommerce's (hidden) variation select, when present ---
	var sizeSelect = layout.querySelector( '.variations select' );
	var sizeLabel  = layout.querySelector( '.headies-size-current' );

	layout.querySelectorAll( '.headies-size' ).forEach( function ( button ) {
		button.addEventListener( 'click', function () {
			layout.querySelectorAll( '.headies-size' ).forEach( function ( b ) {
				b.classList.remove( 'is-active' );
			} );
			button.classList.add( 'is-active' );

			var size = button.getAttribute( 'data-size' );
			if ( sizeLabel ) {
				sizeLabel.textContent = size;
			}
			if ( sizeSelect ) {
				sizeSelect.value = size;
				sizeSelect.dispatchEvent( new Event( 'change', { bubbles: true } ) );
			}
		} );
	} );

	// Grey out sizes WooCommerce reports as unpurchasable.
	if ( sizeSelect ) {
		layout.querySelectorAll( '.headies-size' ).forEach( function ( button ) {
			var size   = button.getAttribute( 'data-size' );
			var option = Array.prototype.find.call( sizeSelect.options, function ( o ) {
				return o.value === size;
			} );
			if ( ! option ) {
				button.disabled = true;
			}
		} );
	}

	// --- Quantity stepper wrapped around Woo's number input ---
	var quantity = layout.querySelector( '.quantity' );
	var qtyInput = quantity ? quantity.querySelector( '.qty' ) : null;
	if ( qtyInput ) {
		[ 'minus', 'plus' ].forEach( function ( kind ) {
			var step = document.createElement( 'button' );
			step.type = 'button';
			step.className = 'headies-qty-step';
			step.textContent = 'minus' === kind ? '−' : '+';
			step.setAttribute( 'aria-label', 'minus' === kind ? 'Decrease quantity' : 'Increase quantity' );
			step.addEventListener( 'click', function () {
				var current = parseInt( qtyInput.value, 10 ) || 1;
				var next    = 'minus' === kind ? Math.max( 1, current - 1 ) : current + 1;
				qtyInput.value = next;
				qtyInput.dispatchEvent( new Event( 'change', { bubbles: true } ) );
			} );
			if ( 'minus' === kind ) {
				quantity.insertBefore( step, qtyInput );
			} else {
				quantity.appendChild( step );
			}
		} );
	}

	// --- Accordion ---
	layout.querySelectorAll( '.headies-accordion-toggle' ).forEach( function ( toggle ) {
		toggle.addEventListener( 'click', function () {
			var item = toggle.closest( '.headies-accordion-item' );
			var open = item.classList.contains( 'is-open' );
			layout.querySelectorAll( '.headies-accordion-item' ).forEach( function ( i ) {
				i.classList.remove( 'is-open' );
				var marker = i.querySelector( '.headies-accordion-marker' );
				if ( marker ) {
					marker.textContent = '+';
				}
			} );
			if ( ! open ) {
				item.classList.add( 'is-open' );
				item.querySelector( '.headies-accordion-marker' ).textContent = '–';
			}
		} );
	} );

	// --- Add to cart via AJAX, then open the cart drawer ---
	// WooCommerce's native ajax_add_to_cart / added_to_cart event isn't wired
	// up on single-product pages by default (that's a shop-loop feature), so
	// this posts to the same headies_add_to_cart endpoint the wishlist
	// "quick add" buttons use, then tells the cart drawer to refresh + open.
	var form = layout.querySelector( 'form.cart' );

	if ( form && typeof headiesWishlist !== 'undefined' ) {
		form.addEventListener( 'submit', function ( e ) {
			e.preventDefault();

			var button    = form.querySelector( '.single_add_to_cart_button' );
			var productId = button ? button.value : '';
			var quantity  = qtyInput ? qtyInput.value : 1;
			if ( ! productId ) {
				return;
			}

			button.disabled = true;
			var originalText = button.textContent;
			button.textContent = 'Adding…';

			var body = new URLSearchParams( {
				action: 'headies_add_to_cart',
				nonce: headiesWishlist.nonce,
				product_id: productId,
				quantity: quantity,
			} );

			fetch( headiesWishlist.ajaxUrl, {
				method: 'POST',
				credentials: 'same-origin',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: body.toString(),
			} )
				.then( function ( res ) { return res.json(); } )
				.then( function ( response ) {
					button.disabled = false;
					button.textContent = originalText;
					if ( ! response.success ) {
						return;
					}

					document.querySelectorAll( '.cart-count' ).forEach( function ( el ) {
						el.textContent = response.data.cart_count;
					} );

					document.dispatchEvent( new CustomEvent( 'headies:cart-updated', { detail: { open: true } } ) );
				} )
				.catch( function () {
					button.disabled = false;
					button.textContent = originalText;
				} );
		} );
	}

} );
