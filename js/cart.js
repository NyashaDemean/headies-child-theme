document.addEventListener( 'DOMContentLoaded', function () {

	var form = document.querySelector( '.woocommerce-cart-form' );
	if ( ! form ) {
		return;
	}

	var submitTimer = null;

	function queueSubmit() {
		clearTimeout( submitTimer );
		submitTimer = setTimeout( function () {
			var updateBtn = form.querySelector( '[name="update_cart"]' );
			if ( updateBtn ) {
				updateBtn.click();
			}
		}, 500 );
	}

	form.addEventListener( 'click', function ( e ) {
		var button = e.target.closest( '.qty-btn' );
		if ( ! button ) {
			return;
		}
		var wrapper = button.closest( '.headies-qty-stepper' );
		var input   = wrapper && wrapper.querySelector( 'input.qty' );
		if ( ! input ) {
			return;
		}

		var step = parseFloat( input.step ) || 1;
		var min  = input.min !== '' ? parseFloat( input.min ) : 0;
		var max  = input.max !== '' ? parseFloat( input.max ) : Infinity;
		var value = parseFloat( input.value ) || 0;

		if ( button.classList.contains( 'qty-plus' ) ) {
			value = Math.min( max, value + step );
		} else {
			value = Math.max( min, value - step );
		}

		input.value = value;
		input.dispatchEvent( new Event( 'change', { bubbles: true } ) );
		queueSubmit();
	} );

	form.addEventListener( 'change', function ( e ) {
		if ( e.target.matches( '.headies-qty-stepper input.qty' ) ) {
			queueSubmit();
		}
	} );

} );
