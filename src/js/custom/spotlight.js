///
/// Spotlight
/// @verion 1.1.0
///

document.addEventListener( 'DOMContentLoaded', function () {
	const gradient = document.querySelector( '.is-style-spotlight' );
	if ( ! gradient ) {
		return;
	}

	// Grain-Canvas anlegen
	const grainCanvas = document.createElement( 'canvas' );
	const grainCtx = grainCanvas.getContext( '2d' );
	grainCanvas.className = 'spotlight-grain';
	grainCanvas.style.position = 'absolute';
	grainCanvas.style.top = '0';
	grainCanvas.style.left = '0';
	grainCanvas.style.width = '100%';
	grainCanvas.style.height = '100%';
	grainCanvas.style.pointerEvents = 'none';
	grainCanvas.style.mixBlendMode = 'overlay';
	gradient.appendChild( grainCanvas );

	function resizeCanvas() {
		grainCanvas.width = gradient.clientWidth;
		grainCanvas.height = gradient.clientHeight;
		generateGrain();
	}

	function generateGrain() {
		const w = grainCanvas.width;
		const h = grainCanvas.height;
		const imgData = grainCtx.createImageData( w, h );

		for ( let i = 0; i < imgData.data.length; i += 4 ) {
			const val = Math.random() * 255;
			imgData.data[ i ] = val;
			imgData.data[ i + 1 ] = val;
			imgData.data[ i + 2 ] = val;
			imgData.data[ i + 3 ] = 16; // Alpha ~6%
		}

		grainCtx.putImageData( imgData, 0, 0 );
	}

	// Initiales Setup
	resizeCanvas();
	window.addEventListener( 'resize', resizeCanvas );

	function updateGradient( x, y ) {
		gradient.style.backgroundImage =
			'radial-gradient(at ' +
			x +
			'px ' +
			y +
			'px, rgba(0, 0, 0, 0) 0, var(--wp--preset--color--base) 100%)';
	}

	// Start-Spot beim Laden (Mitte)
	updateGradient( window.innerWidth / 2, window.innerHeight / 2 );

	// für Maus und Touch einheitlich
	document.addEventListener( 'pointermove', function ( event ) {
		updateGradient( event.clientX, event.clientY );
	} );

	document.addEventListener(
		'touchmove',
		function ( event ) {
			if ( event.touches && event.touches.length ) {
				updateGradient(
					event.touches[ 0 ].clientX,
					event.touches[ 0 ].clientY
				);
			}
		},
		{ passive: true }
	);
} );
