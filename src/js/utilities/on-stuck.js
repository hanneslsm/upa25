/**
 * .on-stuck-has-padding
 */

const stickyElementsWithPadding = document.querySelectorAll(
	'.on-stuck-has-padding'
);

const handlePaddingOnStuck = () => {
	stickyElementsWithPadding.forEach( ( element ) => {
		const rect = element.getBoundingClientRect();
		const topOffset = parseInt( getComputedStyle( element ).top ) || 0;

		if ( rect.top <= topOffset ) {
			element.classList.add( 'is-stuck' );
		} else {
			element.classList.remove( 'is-stuck' );
		}
	} );
};

let tickingPadding = false;

window.addEventListener( 'scroll', function () {
	if ( ! tickingPadding ) {
		window.requestAnimationFrame( function () {
			handlePaddingOnStuck();
			tickingPadding = false;
		} );
		tickingPadding = true;
	}
} );

// Initial check for all sticky elements with padding
handlePaddingOnStuck();

/**
 * .on-stuck-has-layers
 */
const stickyElements = document.querySelectorAll( '.on-stuck-has-layers' );

const handleStickyClasses = () => {
	stickyElements.forEach( ( element, index ) => {
		const rect = element.getBoundingClientRect();
		const topOffset = parseInt( getComputedStyle( element ).top ) || 0;

		let isStuck = rect.top <= topOffset;
		let isTop = false;

		// Update '.is-stuck' class on the current element
		if ( isStuck ) {
			element.classList.add( 'is-stuck' );
		} else {
			element.classList.remove( 'is-stuck' );
		}

		if ( index > 0 ) {
			const prevElement = stickyElements[ index - 1 ];
			const prevRect = prevElement.getBoundingClientRect();

			// Check for overlap with the previous element
			if (
				rect.top <= prevRect.bottom &&
				rect.bottom > prevRect.bottom
			) {
				element.classList.add( 'is-top' );
				isTop = true;
			} else {
				element.classList.remove( 'is-top' );
			}

			// Update '.is-behind' class on the previous element
			if ( isStuck && ! isTop ) {
				prevElement.classList.add( 'is-behind' );
			} else {
				prevElement.classList.remove( 'is-behind' );
			}
		} else {
			// Ensure '.is-top' is not applied to the first element
			element.classList.remove( 'is-top' );
		}
	} );
};

let ticking = false;

window.addEventListener( 'scroll', function () {
	if ( ! ticking ) {
		window.requestAnimationFrame( function () {
			handleStickyClasses();
			ticking = false;
		} );
		ticking = true;
	}
} );

// Initial check on page load
handleStickyClasses();
