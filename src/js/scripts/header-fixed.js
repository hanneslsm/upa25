///
/// Header fixed
///
/**
 * Header fixed, with admin bar offset for logged-in admins
 */
document.addEventListener( 'DOMContentLoaded', () => {
	const header = document.querySelector( 'header .is-style-header-fixed' );
	if ( ! header ) {
		return;
	}

	const adminBar = document.getElementById( 'wpadminbar' );
	let headerHeight, adminBarHeight, hideThreshold;

	function adjustContentOffset() {
		adminBarHeight = adminBar ? adminBar.offsetHeight : 0;
		headerHeight = header.getBoundingClientRect().height;
		hideThreshold = adminBarHeight + headerHeight;

		// only push content by header height, not including admin bar
		document.body.style.paddingTop = headerHeight + 'px';
		// position header below admin bar
		header.style.top = adminBarHeight + 'px';

		return hideThreshold;
	}

	hideThreshold = adjustContentOffset();

	window.addEventListener( 'resize', () => {
		hideThreshold = adjustContentOffset();
	} );

	const isNavOpen = () =>
		Boolean(
			document.querySelector(
				'.wp-block-navigation__responsive-container.is-menu-open,' +
					'.wp-block-navigation.is-menu-open'
			)
		);

	let lastScroll = 0;
	window.addEventListener( 'scroll', () => {
		const scrollY =
			window.pageYOffset || document.documentElement.scrollTop;

		if ( isNavOpen() ) {
			header.style.transform = 'translateY(0)';
		} else if ( scrollY > lastScroll && scrollY > hideThreshold ) {
			header.style.transform = 'translateY(-100%)';
		} else {
			header.style.transform = 'translateY(0)';
		}

		lastScroll = scrollY;
	} );
} );
