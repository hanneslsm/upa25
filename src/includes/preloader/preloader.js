/**
 * Front Page Preloader - Minimal Framer-style Animation
 *
 * Simple, elegant preloader inspired by Framer Motion.
 * Features smooth logo fade-in and progress bar animation.
 *
 * @package upa25
 * @version 2.1.0
 */

( function () {
	'use strict';

	/**
	 * Preloader controller class.
	 */
	class PreloaderController {
		/**
		 * Constructor.
		 */
		constructor() {
			this.preloader = document.getElementById( 'upa25-preloader' );
			this.progressBar = null;
			this.progressFill = null;
			this.animationFrame = null;
			this.loadHandler = null;
			this.startTime = performance.now();
			this.minDisplayTime = 600;
			this.isComplete = false;
			this.progress = 0;

			this.init();
		}

		/**
		 * Initialize the preloader.
		 */
		init() {
			if ( ! this.preloader ) {
				return;
			}

			// Add loading class to body.
			document.body.classList.add( 'upa25-preloading' );

			this.progressBar = this.preloader.querySelector( '.upa25-preloader__progress' );
			this.progressFill = this.preloader.querySelector( '.upa25-preloader__progress-fill' );

			// Prevent scroll during loading.
			document.documentElement.style.overflow = 'hidden';

			// Start progress animation.
			this.animateProgress();

			// Bind handler for cleanup.
			this.loadHandler = this.onLoadComplete.bind( this );

			// Listen for page load.
			if ( document.readyState === 'complete' ) {
				this.onLoadComplete();
			} else {
				window.addEventListener( 'load', this.loadHandler );
			}

			// Fallback timeout for slow connections.
			setTimeout( () => {
				if ( ! this.isComplete ) {
					this.complete();
				}
			}, 4000 );
		}

		/**
		 * Animate progress bar with RAF.
		 */
		animateProgress() {
			if ( this.isComplete ) {
				return;
			}

			const elapsed = performance.now() - this.startTime;

			// Calculate progress (reaches ~95% asymptotically).
			const targetProgress = Math.min( ( elapsed / 1500 ) * 95, 95 );
			this.progress += ( targetProgress - this.progress ) * 0.1;

			if ( this.progressFill ) {
				this.progressFill.style.transform = 'scaleX(' + ( this.progress / 100 ) + ')';
			}

			// Update ARIA value for accessibility.
			if ( this.progressBar ) {
				this.progressBar.setAttribute( 'aria-valuenow', Math.round( this.progress ) );
			}

			this.animationFrame = requestAnimationFrame( () => this.animateProgress() );
		}

		/**
		 * Handle page load complete.
		 */
		onLoadComplete() {
			const elapsed = performance.now() - this.startTime;
			const remainingTime = Math.max( 0, this.minDisplayTime - elapsed );

			setTimeout( () => {
				this.complete();
			}, remainingTime );
		}

		/**
		 * Complete and animate out.
		 */
		complete() {
			if ( this.isComplete ) {
				return;
			}
			this.isComplete = true;

			// Cancel animation frame.
			if ( this.animationFrame ) {
				cancelAnimationFrame( this.animationFrame );
				this.animationFrame = null;
			}

			// Remove load event listener.
			if ( this.loadHandler ) {
				window.removeEventListener( 'load', this.loadHandler );
				this.loadHandler = null;
			}

			// Complete progress bar.
			if ( this.progressFill ) {
				this.progressFill.style.transform = 'scaleX(1)';
			}
			if ( this.progressBar ) {
				this.progressBar.setAttribute( 'aria-valuenow', '100' );
			}

			// Check for Web Animations API support.
			if ( typeof this.preloader.animate !== 'function' ) {
				this.cleanup();
				return;
			}

			// Fade out with Web Animations API.
			const fadeOut = this.preloader.animate(
				[
					{ opacity: 1 },
					{ opacity: 0 }
				],
				{
					duration: 400,
					easing: 'cubic-bezier(0.4, 0, 0.2, 1)',
					fill: 'forwards'
				}
			);

			fadeOut.finished
				.then( () => this.cleanup() )
				.catch( () => this.cleanup() );
		}

		/**
		 * Cleanup after preloader completes.
		 */
		cleanup() {
			// Remove loading class from body.
			document.body.classList.remove( 'upa25-preloading' );

			// Restore scroll.
			document.documentElement.style.overflow = '';

			// Remove from DOM.
			if ( this.preloader && this.preloader.parentNode ) {
				this.preloader.parentNode.removeChild( this.preloader );
			}

			// Dispatch custom event for other scripts.
			window.dispatchEvent( new CustomEvent( 'upa25PreloaderComplete' ) );
		}
	}

	/**
	 * Initialize when DOM is ready.
	 */
	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', () => {
			new PreloaderController();
		} );
	} else {
		new PreloaderController();
	}
} )();
