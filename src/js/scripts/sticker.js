window.addEventListener( 'DOMContentLoaded', () => {
	document.querySelectorAll( '.sticker' ).forEach( ( figure ) => {
		let isDragging = false;
		let offsetX = 0;
		let offsetY = 0;
		let lastTap = 0;

		const vw = document.documentElement.clientWidth;
		const vh = document.documentElement.clientHeight;
		const rect = figure.getBoundingClientRect();

		const maxLeft = vw - rect.width;
		const maxTop = vh - rect.height;

		const randomLeft = Math.floor( Math.random() * maxLeft );
		const randomTop = Math.floor( Math.random() * maxTop );

		const floatDuration = ( 1.5 + Math.random() ).toFixed( 2 );
		const floatDelay = ( Math.random() * 2 ).toFixed( 2 );
		figure.style.animation = `float ${ floatDuration }s ease-in-out ${ floatDelay }s infinite`;

		figure.style.left = `${ randomLeft }px`;
		figure.style.top = `${ randomTop }px`;
		figure.style.animation = `float ${ floatDuration }s ease-in-out ${ floatDelay }s infinite`;

		requestAnimationFrame( () => {
			figure.style.opacity = '1';
		} );

		const moveAt = ( x, y ) => {
			figure.style.left = `${ x - offsetX }px`;
			figure.style.top = `${ y - offsetY }px`;
		};

		const stopDrag = () => {
			isDragging = false;
			figure.style.cursor = 'grab';
			figure.style.transform = 'scale(1)';
			document.removeEventListener( 'mousemove', onMouseMove );
			document.removeEventListener( 'mouseup', onMouseUp );
			document.removeEventListener( 'touchmove', onTouchMove );
			document.removeEventListener( 'touchend', onTouchEnd );
		};

		const onMouseMove = ( e ) => {
			if ( ! isDragging ) return;
			moveAt( e.clientX, e.clientY );
		};

		const onTouchMove = ( e ) => {
			if ( ! isDragging || ! e.touches[ 0 ] ) return;
			moveAt( e.touches[ 0 ].clientX, e.touches[ 0 ].clientY );
		};

		const onMouseUp = stopDrag;
		const onTouchEnd = stopDrag;

		figure.addEventListener( 'mousedown', ( e ) => {
			isDragging = true;
			offsetX = e.clientX - figure.offsetLeft;
			offsetY = e.clientY - figure.offsetTop;
			figure.style.cursor = 'grabbing';
			figure.style.transform = 'scale(0.9)';
			document.addEventListener( 'mousemove', onMouseMove );
			document.addEventListener( 'mouseup', onMouseUp );
			e.preventDefault();
		} );

		figure.addEventListener( 'touchstart', ( e ) => {
			if ( ! e.touches[ 0 ] ) return;

			const now = Date.now();
			const timeSince = now - lastTap;
			lastTap = now;

			if ( timeSince < 300 ) {
				// double tap detected
				figure.style.animation = 'none';
				figure.offsetHeight;
				figure.style.animation = 'poof 0.4s ease-out forwards';
				figure.style.pointerEvents = 'none';
				setTimeout( () => {
					figure.style.display = 'none';
				}, 400 );
				return;
			}

			isDragging = true;
			offsetX = e.touches[ 0 ].clientX - figure.offsetLeft;
			offsetY = e.touches[ 0 ].clientY - figure.offsetTop;
			figure.style.transform = 'scale(0.9)';
			document.addEventListener( 'touchmove', onTouchMove, {
				passive: false,
			} );
			document.addEventListener( 'touchend', onTouchEnd );
			e.preventDefault();
		} );

		figure.addEventListener( 'dblclick', () => {
			// Reset animation to force reflow
			figure.style.animation = 'none';
			figure.offsetHeight; // force reflow

			// Apply poof animation
			figure.style.animation = 'poof 0.4s ease-out forwards';
			figure.style.pointerEvents = 'none';

			setTimeout( () => {
				figure.style.display = 'none';
			}, 400 );
		} );
	} );
} );
