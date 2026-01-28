/**
 * Handles the sticky header animation so it hides on downward scrolls but
 * reappears when the user scrolls up. Adjusts offsets to accommodate the
 * WordPress admin bar across breakpoints and honours reduced-motion settings.
 *
 * @package upa25
 * @version 0.1.0
 * @since upa25 0.2.0
 */

document.addEventListener( 'DOMContentLoaded', () => {
	const header = document.querySelector(
		'header.wp-block-template-part .wp-block-group.is-style-header-fixed'
	);

	if ( ! header ) {
		return;
	}

	const root = document.documentElement;
	const body = document.body;
	const adminBar = document.getElementById( 'wpadminbar' );
	const prefersReducedMotion = window.matchMedia
		? window.matchMedia( '(prefers-reduced-motion: reduce)' )
		: null;

	let headerHeight = 0;
	let adminBarHeight = 0;
	let adminBarIsFixed = false;
	let previousScrollY =
		window.pageYOffset || document.documentElement.scrollTop || 0;
	let scrollTicking = false;

	/**
	 * Convert a CSS length value to a finite number.
	 *
	 * @param {string} value The CSS length value.
	 * @return {number} The parsed numeric value or 0.
	 */
	const parsePixelValue = ( value ) => {
		const parsed = parseFloat( value );
		return Number.isFinite( parsed ) ? parsed : 0;
	};

	/**
	 * Determine the current admin bar height and positioning mode.
	 *
	 * @return {{height: number, isFixed: boolean}} State describing the admin bar.
	 */
	const readAdminBarState = () => {
		if ( ! adminBar || ! body.classList.contains( 'admin-bar' ) ) {
			return { height: 0, isFixed: false };
		}

		const rect = adminBar.getBoundingClientRect();
		const style = window.getComputedStyle( adminBar );
		const height =
			Math.round(
				rect.height ||
					parsePixelValue( style.height ) ||
					parsePixelValue(
						window
							.getComputedStyle( document.documentElement )
							.getPropertyValue( '--wp-admin--admin-bar--height' )
					)
			) || 0;
		const position = style.position || '';
		const isFixed =
			position === 'fixed' ||
			position === 'sticky';
		return { height, isFixed };
	};

	/**
	 * Sync the offset for mobile admin bar behaviour (absolute positioning).
	 */
	const updateDynamicOffset = () => {
		const scrollY =
			window.pageYOffset || document.documentElement.scrollTop || 0;
		const dynamicOffset =
			! adminBarIsFixed && adminBarHeight
				? Math.max( adminBarHeight - scrollY, 0 )
				: 0;

		root.style.setProperty(
			'--header-fixed-dynamic-offset',
			`${ dynamicOffset }px`
		);
	};

	/**
	 * Check if the user requested reduced motion.
	 *
	 * @return {boolean} True when reduced motion should be honoured.
	 */
	const shouldReduceMotion = () =>
		Boolean( prefersReducedMotion && prefersReducedMotion.matches );

	/**
	 * Detect whether the navigation menu is open in responsive mode.
	 *
	 * @return {boolean} True when the responsive menu is open.
	 */
	const isNavigationOpen = () =>
		Boolean(
			document.querySelector(
				'.wp-block-navigation__responsive-container.is-menu-open, .wp-block-navigation.is-menu-open'
			)
		);

	/**
	 * Toggle the header visibility based on scroll direction and thresholds.
	 *
	 * @param {boolean} [forceShow=false] Force the header to show.
	 */
	const updateVisibility = ( forceShow = false ) => {
		const scrollY =
			window.pageYOffset || document.documentElement.scrollTop || 0;
		const isScrollingDown = scrollY > previousScrollY;
		const hideThreshold = headerHeight + adminBarHeight;

		if ( forceShow || shouldReduceMotion() || isNavigationOpen() ) {
			header.classList.remove( 'is-hidden' );
			previousScrollY = scrollY;
			return;
		}

		header.classList.toggle(
			'is-hidden',
			isScrollingDown && scrollY > hideThreshold
		);

		previousScrollY = scrollY;
	};

	/**
	 * Measure the header and admin bar to update spacing/offset variables.
	 */
	const setRootSpacing = () => {
		headerHeight = Math.round(
			header.getBoundingClientRect().height || header.offsetHeight || 0
		);

		const adminState = readAdminBarState();
		adminBarHeight = adminState.height;
		adminBarIsFixed = adminState.isFixed;

		root.style.setProperty(
			'--header-fixed-height',
			`${ headerHeight }px`
		);
		root.style.setProperty(
			'--header-fixed-admin-offset',
			adminBarIsFixed ? `${ adminBarHeight }px` : '0px'
		);

		body.classList.remove( 'has-hxi-header-fixed' );
		body.classList.add( 'has-header-fixed' );

		updateDynamicOffset();
		updateVisibility( true );
	};

	/**
	 * Handle scroll events using requestAnimationFrame to limit work.
	 */
	const handleScroll = () => {
		if ( scrollTicking ) {
			return;
		}

		scrollTicking = true;

		window.requestAnimationFrame( () => {
			updateDynamicOffset();
			updateVisibility();
			scrollTicking = false;
		} );
	};

	/**
	 * Recalculate offsets when the viewport resizes or observed nodes change.
	 */
	const handleResize = () => {
		setRootSpacing();
	};

	/**
	 * Reset header state when the reduced-motion preference updates.
	 */
	const handleMotionPreferenceChange = () => {
		if ( shouldReduceMotion() ) {
			header.classList.remove( 'is-hidden' );
		}
	};

	setRootSpacing();

	window.addEventListener( 'scroll', handleScroll, { passive: true } );
	window.addEventListener( 'resize', handleResize );

	if ( 'ResizeObserver' in window ) {
		const resizeObserver = new ResizeObserver( handleResize );

		resizeObserver.observe( header );

		if ( adminBar ) {
			resizeObserver.observe( adminBar );
		}
	}

	if ( prefersReducedMotion ) {
		if ( typeof prefersReducedMotion.addEventListener === 'function' ) {
			prefersReducedMotion.addEventListener(
				'change',
				handleMotionPreferenceChange
			);
		} else if ( typeof prefersReducedMotion.addListener === 'function' ) {
			prefersReducedMotion.addListener( handleMotionPreferenceChange );
		}
	}
} );
