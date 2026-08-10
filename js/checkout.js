document.addEventListener( 'DOMContentLoaded', function () {

	// --- Pickup vs delivery: swap the pickup-branch field and delivery
	// address fields depending on which shipping method is chosen. Collect
	// In Store needs no address; courier methods need no branch. ---
	function syncPickupField() {
		var pickupField   = document.querySelector( '.headies-pickup-field' );
		var addressFields = document.querySelector( '.headies-delivery-address-fields' );
		if ( ! pickupField && ! addressFields ) {
			return;
		}
		var checked = document.querySelector( 'input.shipping_method:checked' );
		// A single available method renders as a hidden input instead of a radio.
		if ( ! checked ) {
			checked = document.querySelector( 'input.shipping_method[type="hidden"]' );
		}
		var isPickup = !! ( checked && checked.value && checked.value.indexOf( 'local_pickup' ) === 0 );

		if ( pickupField ) {
			pickupField.style.display = isPickup ? '' : 'none';
			var select = document.getElementById( 'headies_pickup_location' );
			if ( select ) {
				select.required = isPickup;
			}
		}

		if ( addressFields ) {
			addressFields.style.display = isPickup ? 'none' : '';
			addressFields.querySelectorAll( 'input, select' ).forEach( function ( field ) {
				field.required = ! isPickup && field.dataset.headiesRequired !== 'false';
			} );
		}
	}

	// Remember which delivery-address fields WooCommerce originally marked
	// required, so we know what to restore when switching back off pickup.
	document.querySelectorAll( '.headies-delivery-address-fields input, .headies-delivery-address-fields select' ).forEach( function ( field ) {
		field.dataset.headiesRequired = field.required ? 'true' : 'false';
	} );

	syncPickupField();

	// Delivery-method radios get replaced wholesale on every AJAX totals
	// refresh (see headies_delivery_methods_fragment in functions.php), so
	// listen on the document rather than binding to elements directly.
	document.addEventListener( 'change', function ( e ) {
		if ( e.target.matches && e.target.matches( 'input.shipping_method' ) ) {
			syncPickupField();
		}
	} );

	if ( window.jQuery ) {
		window.jQuery( document.body ).on( 'updated_checkout', syncPickupField );
	}

	// --- Step nav: highlight the section currently in view ---
	var stepLinks = document.querySelectorAll( '.headies-checkout-steps a' );
	var sections  = Array.prototype.map.call( stepLinks, function ( link ) {
		return document.querySelector( link.getAttribute( 'href' ) );
	} );

	function syncActiveStep() {
		var scrollPos = window.scrollY + 140;
		var activeIndex = 0;
		sections.forEach( function ( section, i ) {
			if ( section && section.offsetTop <= scrollPos ) {
				activeIndex = i;
			}
		} );
		stepLinks.forEach( function ( link, i ) {
			link.classList.toggle( 'is-active', i === activeIndex );
		} );
	}

	if ( stepLinks.length ) {
		syncActiveStep();
		window.addEventListener( 'scroll', syncActiveStep, { passive: true } );
	}

} );
