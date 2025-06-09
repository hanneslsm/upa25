<?php
/**
 * Title: Hero-06
 * Slug: upa25/hero-06
 * Categories: upa25/heros
 * Viewport width: 1920
 * Description: Hero section with centered headline, subheadline, description, and CTA.
 */
?>
<!-- wp:group {"metadata":{"name":"Section"},"align":"full","className":"is-style-section-base-2","style":{"dimensions":{"minHeight":"60dvh"}},"layout":{"type":"flex","orientation":"vertical","verticalAlignment":"center","justifyContent":"center"}} -->
<div class="wp-block-group alignfull is-style-section-base-2" style="min-height:60dvh">

	<!-- wp:group {"align":"wide","layout":{"type":"constrained"}} -->
	<div class="wp-block-group alignwide">

		<!-- wp:spacer {"height":"var:preset|spacing|50"} -->
		<div style="height:var(--wp--preset--spacing--50)" aria-hidden="true" class="wp-block-spacer"></div>
		<!-- /wp:spacer -->

		<!-- wp:paragraph {"align":"center","className":"is-style-overline"} -->
		<p class="has-text-align-center is-style-overline"><?php echo esc_html__( 'This is your overline', 'upa25' ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:heading {"textAlign":"center","level":1,"align":"wide","style":{"typography":{"fontSize":"6rem"}}} -->
		<h1 class="wp-block-heading alignwide has-text-align-center" style="font-size:6rem"><?php echo esc_html__( 'Place your website headline here!', 'upa25' ); ?></h1>
		<!-- /wp:heading -->

		<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"right":"2dvw","left":"2dvw"}}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"center","verticalAlignment":"bottom"}} -->
		<div class="wp-block-group alignwide" style="padding-right:2dvw;padding-left:2dvw">

			<!-- wp:group {"layout":{"type":"constrained"}} -->
			<div class="wp-block-group">

				<!-- wp:paragraph {"align":"center"} -->
				<p class="has-text-align-center"><?php echo esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu.', 'upa25' ); ?></p>
				<!-- /wp:paragraph -->

				<!-- wp:spacer {"height":"var:preset|spacing|10"} -->
				<div style="height:var(--wp--preset--spacing--10)" aria-hidden="true" class="wp-block-spacer"></div>
				<!-- /wp:spacer -->

				<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|10"}},"layout":{"type":"default"}} -->
				<div class="wp-block-group">

					<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
					<div class="wp-block-buttons">

						<!-- wp:button {"className":"is-style-fill"} -->
						<div class="wp-block-button is-style-fill">
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

					<!-- wp:paragraph {"align":"center","fontSize":"small"} -->
					<p class="has-text-align-center has-small-font-size"><?php echo esc_html__( 'More than 100+ happy customers!', 'upa25' ); ?></p>
					<!-- /wp:paragraph -->

				</div>
				<!-- /wp:group -->

			</div>
			<!-- /wp:group -->

		</div>
		<!-- /wp:group -->

		<!-- wp:spacer {"height":"var:preset|spacing|50"} -->
		<div style="height:var(--wp--preset--spacing--50)" aria-hidden="true" class="wp-block-spacer"></div>
		<!-- /wp:spacer -->

	</div>
	<!-- /wp:group -->

</div>
<!-- /wp:group -->
