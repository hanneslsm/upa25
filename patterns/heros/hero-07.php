
<?php
/**
 * Title: Hero-07
 * Slug: upa25/hero-07
 * Categories: upa25/heros
 * Viewport width: 1920
 * Description: Hero section with background image, two columns, headline, text, and button.
 */
?>
<!-- wp:cover {"url":"<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/placeholders/placeholder-image-2.webp","dimRatio":60,"overlayColor":"contrast-2","isUserOverlayColor":true,"minHeight":75,"minHeightUnit":"dvh","contentPosition":"bottom center","tagName":"section","sizeSlug":"large","align":"full","layout":{"type":"default"},"metadata":{"categories":["upa25/heros"],"patternName":"upa25/hero-07","name":"Hero-07"}} -->
<section class="wp-block-cover alignfull has-custom-content-position is-position-bottom-center" style="min-height:75dvh">
	<img class="wp-block-cover__image-background size-large" alt="" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/placeholders/placeholder-image-2.webp" data-object-fit="cover"/>
	<span aria-hidden="true" class="wp-block-cover__background has-contrast-2-background-color has-background-dim-60 has-background-dim"></span>
	<div class="wp-block-cover__inner-container">

		<!-- wp:spacer {"height":"var:preset|spacing|50"} -->
		<div style="height:var(--wp--preset--spacing--50)" aria-hidden="true" class="wp-block-spacer"></div>
		<!-- /wp:spacer -->

		<!-- wp:columns {"verticalAlignment":null,"align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|30"}}}} -->
		<div class="wp-block-columns alignwide">

			<!-- wp:column {"verticalAlignment":"stretch","width":"50%","layout":{"type":"constrained","justifyContent":"left"}} -->
			<div class="wp-block-column is-vertically-aligned-stretch" style="flex-basis:50%">

				<!-- wp:group {"style":{"dimensions":{"minHeight":"100%"}},"layout":{"type":"flex","orientation":"vertical","verticalAlignment":"space-between"}} -->
				<div class="wp-block-group" style="min-height:100%">

					<!-- wp:heading {"level":1,"align":"wide"} -->
					<h1 class="wp-block-heading alignwide"><?php echo esc_html__( 'Place your website headline here', 'upa25' ); ?></h1>
					<!-- /wp:heading -->

					<!-- wp:paragraph {"fontSize":"small"} -->
					<p class="has-small-font-size"><?php echo esc_html__( 'Rated 4.5 out of 5 ★★★★★', 'upa25' ); ?></p>
					<!-- /wp:paragraph -->

				</div>
				<!-- /wp:group -->

			</div>
			<!-- /wp:column -->

			<!-- wp:column {"verticalAlignment":"stretch","width":"50%","layout":{"type":"constrained","justifyContent":"left"}} -->
			<div class="wp-block-column is-vertically-aligned-stretch" style="flex-basis:50%">

				<!-- wp:group {"style":{"dimensions":{"minHeight":"100%"}},"layout":{"type":"flex","orientation":"vertical","verticalAlignment":"space-between"}} -->
				<div class="wp-block-group" style="min-height:100%">

					<!-- wp:paragraph -->
					<p><?php echo esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor.', 'upa25' ); ?></p>
					<!-- /wp:paragraph -->

					<!-- wp:buttons -->
					<div class="wp-block-buttons">
						<!-- wp:button -->
						<div class="wp-block-button">
							<a class="wp-block-button__link wp-element-button"><?php echo esc_html__( 'Click here', 'upa25' ); ?></a>
						</div>
						<!-- /wp:button -->
					</div>
					<!-- /wp:buttons -->

				</div>
				<!-- /wp:group -->

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
