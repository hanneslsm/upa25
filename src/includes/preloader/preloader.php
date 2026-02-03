<?php
/**
 * Front Page Preloader
 *
 * Instant-loading preloader with inlined critical CSS/JS.
 * Appears immediately as the first visual element for optimal UX.
 *
 * @package upa25
 * @version 2.1.0
 */

declare( strict_types=1 );

/**
 * Check if preloader should load (cached result).
 *
 * @return bool Whether to load preloader.
 */
function upa25_should_load_preloader(): bool {
	static $should_load = null;

	if ( null === $should_load ) {
		$should_load = is_front_page();

		/**
		 * Filter whether to show the preloader.
		 *
		 * @since 2.1.0
		 *
		 * @param bool $should_load Whether to load the preloader.
		 */
		$should_load = apply_filters( 'upa25_preloader_enabled', $should_load );
	}

	return $should_load;
}

/**
 * Inline critical preloader CSS in document head.
 * This ensures the preloader appears instantly without waiting for CSS files.
 *
 * @return void
 */
function upa25_inline_preloader_css(): void {
	if ( ! upa25_should_load_preloader() ) {
		return;
	}

	$css_file = get_theme_file_path( 'build/includes/preloader/style.css' );
	if ( ! file_exists( $css_file ) ) {
		return;
	}

	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local theme file.
	$css = file_get_contents( $css_file );
	if ( $css ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS from trusted theme file.
		echo '<style id="upa25-preloader-css">' . wp_strip_all_tags( $css ) . '</style>';
	}
}
add_action( 'wp_head', 'upa25_inline_preloader_css', 1 );

/**
 * Inline critical preloader JS in document head.
 * Loads synchronously for immediate execution.
 *
 * @return void
 */
function upa25_inline_preloader_js(): void {
	if ( ! upa25_should_load_preloader() ) {
		return;
	}

	$js_file = get_theme_file_path( 'build/includes/preloader/preloader.js' );
	if ( ! file_exists( $js_file ) ) {
		return;
	}

	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- Local theme file.
	$js = file_get_contents( $js_file );
	if ( $js ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JS from trusted theme file.
		echo '<script id="upa25-preloader-js">' . $js . '</script>';
	}
}
add_action( 'wp_head', 'upa25_inline_preloader_js', 2 );

/**
 * Add preloader HTML to the page body.
 *
 * @return void
 */
function upa25_render_preloader(): void {
	if ( ! upa25_should_load_preloader() ) {
		return;
	}

	// Get the site logo.
	$custom_logo_id = get_theme_mod( 'custom_logo' );
	$logo_url       = $custom_logo_id
		? wp_get_attachment_image_url( $custom_logo_id, 'full' )
		: get_theme_file_uri( 'assets/images/logo.png' );
	$site_name      = get_bloginfo( 'name' );

	/**
	 * Filter the preloader logo URL.
	 *
	 * @since 2.1.0
	 *
	 * @param string $logo_url The logo URL.
	 */
	$logo_url = apply_filters( 'upa25_preloader_logo_url', $logo_url );

	?>
	<div id="upa25-preloader" class="upa25-preloader" role="status" aria-live="polite" aria-label="<?php esc_attr_e( 'Loading page', 'upa25' ); ?>">
		<span class="screen-reader-text"><?php esc_html_e( 'Loading page content, please wait.', 'upa25' ); ?></span>
		<div class="upa25-preloader__content">
			<div class="upa25-preloader__logo-wrapper">
				<?php if ( $logo_url ) : ?>
					<img
						src="<?php echo esc_url( $logo_url ); ?>"
						alt=""
						class="upa25-preloader__logo"
						loading="eager"
						decoding="async"
						aria-hidden="true"
					/>
				<?php else : ?>
					<div class="upa25-preloader__logo-text" aria-hidden="true">
						<?php echo esc_html( $site_name ); ?>
					</div>
				<?php endif; ?>
			</div>
			<div
				class="upa25-preloader__progress"
				role="progressbar"
				aria-valuenow="0"
				aria-valuemin="0"
				aria-valuemax="100"
				aria-label="<?php esc_attr_e( 'Loading progress', 'upa25' ); ?>"
			>
				<div class="upa25-preloader__progress-fill"></div>
			</div>
		</div>
	</div>
	<?php
}
add_action( 'wp_body_open', 'upa25_render_preloader', 1 );

/**
 * Preload the logo image for instant display.
 *
 * @return void
 */
function upa25_preload_logo(): void {
	if ( ! upa25_should_load_preloader() ) {
		return;
	}

	$custom_logo_id = get_theme_mod( 'custom_logo' );
	$logo_url       = $custom_logo_id
		? wp_get_attachment_image_url( $custom_logo_id, 'full' )
		: get_theme_file_uri( 'assets/images/logo.png' );

	/** This filter is documented in src/includes/preloader/preloader.php */
	$logo_url = apply_filters( 'upa25_preloader_logo_url', $logo_url );

	if ( $logo_url ) {
		printf(
			'<link rel="preload" href="%s" as="image" />',
			esc_url( $logo_url )
		);
	}
}
add_action( 'wp_head', 'upa25_preload_logo', 3 );
