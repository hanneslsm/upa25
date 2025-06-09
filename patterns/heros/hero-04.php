<?php
/**
 * Title: Hero-04
 * Slug: upa25/hero-04
 * Categories: upa25/heros
 * Viewport width: 1920
 * Description: Hero section with cover background, overline, heading, and buttons.
 */
?>
<!-- wp:cover {"url":"<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/placeholders/placeholder-image-2.webp","dimRatio":60,"overlayColor":"contrast-2","isUserOverlayColor":true,"minHeight":75,"minHeightUnit":"dvh","contentPosition":"bottom center","tagName":"section","sizeSlug":"large","align":"full","layout":{"type":"constrained"}} -->
<section class="wp-block-cover alignfull has-custom-content-position is-position-bottom-center" style="min-height:75dvh">
	<img class="wp-block-cover__image-background size-large" alt="" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/placeholders/placeholder-image-2.webp" data-object-fit="cover"/>
	<span aria-hidden="true" class="wp-block-cover__background has-contrast-2-background-color has-background-dim-60 has-background-dim"></span>
	<div class="wp-block-cover__inner-container">

		<!-- wp:spacer {"height":"var:preset|spacing|50"} -->
		<div style="height:var(--wp--preset--spacing--50)" aria-hidden="true" class="wp-block-spacer"></div>
		<!-- /wp:spacer -->

		<!-- wp:columns {"verticalAlignment":"bottom","align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|30"}}}} -->
		<div class="wp-block-columns alignwide are-vertically-aligned-bottom">
			<!-- wp:column {"verticalAlignment":"bottom","width":"50%"} -->
			<div class="wp-block-column is-vertically-aligned-bottom" style="flex-basis:50%"></div>
			<!-- /wp:column -->

			<!-- wp:column {"verticalAlignment":"bottom"} -->
			<div class="wp-block-column is-vertically-aligned-bottom">
				<!-- wp:paragraph {"fontSize":"small"} -->
				<p class="has-small-font-size"><?php echo esc_html__( 'Rated 4.5 out of 5 ★★★★★', 'upa25' ); ?></p>
				<!-- /wp:paragraph -->

				<!-- wp:paragraph {"className":"is-style-overline"} -->
				<p class="is-style-overline"><?php echo esc_html__( 'This is your overline', 'upa25' ); ?></p>
				<!-- /wp:paragraph -->

				<!-- wp:heading {"level":1,"align":"wide"} -->
				<h1 class="wp-block-heading alignwide"><?php echo esc_html__( 'Place your website headline here', 'upa25' ); ?></h1>
				<!-- /wp:heading -->

				<!-- wp:buttons -->
				<div class="wp-block-buttons">
					<!-- wp:button -->
					<div class="wp-block-button">
						<a class="wp-block-button__link wp-element-button"><?php echo esc_html__( 'Click here', 'upa25' ); ?></a>
					</div>
					<!-- /wp:button -->

					<!-- wp:button {"className":"is-style-outline"} -->
					<div class="wp-block-button is-style-outline">
						<a class="wp-block-button__link wp-element-button"><?php echo esc_html__( 'Learn more', 'upa25' ); ?></a>
					</div>
					<!-- /wp:button -->
				</div>
				<!-- /wp:buttons -->
			</div>
			<!-- /wp:column -->
		</div>
		<!-- /wp:columns -->

		<!-- wp:spacer {"height":"var:preset|spacing|50"} -->
		<div style="height:var(--wp--preset--spacing--50)" aria-hidden="true" class="wp-block-spacer"></div>
		<!-- /wp:spacer -->

	</div>
</section>
<!-- /wp:cover -->
