<?php
/**
 * Title: Hero-05
 * Slug: upa25/hero-05
 * Categories: upa25/heros
 * Viewport width: 1920
 * Description: Hero section with headline, three-column content, buttons, and avatar stack.
 */
?>
<!-- wp:cover {"url":"<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/placeholders/placeholder-image-4.webp","dimRatio":60,"overlayColor":"contrast-2","isUserOverlayColor":true,"minHeight":75,"minHeightUnit":"dvh","contentPosition":"bottom center","tagName":"section","sizeSlug":"large","align":"full","layout":{"type":"constrained"}} -->
<section class="wp-block-cover alignfull has-custom-content-position is-position-bottom-center" style="min-height:75dvh">
	<img class="wp-block-cover__image-background size-large" alt="" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/placeholders/placeholder-image-4.webp" data-object-fit="cover"/>
	<span aria-hidden="true" class="wp-block-cover__background has-contrast-2-background-color has-background-dim-60 has-background-dim"></span>
	<div class="wp-block-cover__inner-container">

		<!-- wp:spacer {"height":"var:preset|spacing|50"} -->
		<div style="height:var(--wp--preset--spacing--50)" aria-hidden="true" class="wp-block-spacer"></div>
		<!-- /wp:spacer -->

		<!-- wp:group {"align":"wide","layout":{"type":"constrained","justifyContent":"left"}} -->
		<div class="wp-block-group alignwide">

			<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|10"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
			<div class="wp-block-group">
				<!-- wp:paragraph {"fontSize":"small"} -->
				<p class="has-small-font-size"><?php echo esc_html__( 'Rated 4.5 out of 5', 'upa25' ); ?></p>
				<!-- /wp:paragraph -->

				<!-- wp:paragraph {"fontSize":"small"} -->
				<p class="has-small-font-size"><?php echo esc_html__( '★★★★★', 'upa25' ); ?></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->

			<!-- wp:heading {"level":1,"style":{"typography":{"fontSize":"5rem"}}} -->
			<h1 class="wp-block-heading" style="font-size:5rem"><?php echo esc_html__( 'Place your website headline here', 'upa25' ); ?></h1>
			<!-- /wp:heading -->

			<!-- wp:columns -->
			<div class="wp-block-columns">

				<!-- wp:column -->
				<div class="wp-block-column">
					<!-- wp:separator -->
					<hr class="wp-block-separator has-alpha-channel-opacity"/>
					<!-- /wp:separator -->

					<!-- wp:paragraph -->
					<p><?php echo esc_html__( 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor.', 'upa25' ); ?></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:column -->

				<!-- wp:column -->
				<div class="wp-block-column">
					<!-- wp:separator -->
					<hr class="wp-block-separator has-alpha-channel-opacity"/>
					<!-- /wp:separator -->

					<!-- wp:paragraph -->
					<p><?php echo esc_html__( 'Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.', 'upa25' ); ?></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:column -->

				<!-- wp:column -->
				<div class="wp-block-column">
					<!-- wp:separator -->
					<hr class="wp-block-separator has-alpha-channel-opacity"/>
					<!-- /wp:separator -->

					<!-- wp:paragraph -->
					<p><?php echo esc_html__( 'Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim.', 'upa25' ); ?></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:column -->

			</div>
			<!-- /wp:columns -->

			<!-- wp:spacer {"height":"var:preset|spacing|20"} -->
			<div style="height:var(--wp--preset--spacing--20)" aria-hidden="true" class="wp-block-spacer"></div>
			<!-- /wp:spacer -->

			<!-- wp:group {"layout":{"type":"constrained","justifyContent":"left"}} -->
			<div class="wp-block-group">

				<!-- wp:buttons {"layout":{"type":"flex"}} -->
				<div class="wp-block-buttons">
					<!-- wp:button {"width":50} -->
					<div class="wp-block-button has-custom-width wp-block-button__width-50">
						<a class="wp-block-button__link wp-element-button"><?php echo esc_html__( 'Click here', 'upa25' ); ?></a>
					</div>
					<!-- /wp:button -->
				</div>
				<!-- /wp:buttons -->

				<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|10"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
				<div class="wp-block-group">
					<!-- wp:group {"style":{"spacing":{"blockGap":"0"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
					<div class="wp-block-group">

						<!-- wp:image {"lightbox":{"enabled":false},"width":"28px","sizeSlug":"full","linkDestination":"none","style":{"border":{"radius":"999px","width":"1px"}},"borderColor":"base"} -->
						<figure class="wp-block-image size-full is-resized has-custom-border">
							<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/placeholders/placeholder-avatar-1.webp" alt="" class="has-border-color has-base-border-color" style="border-width:1px;border-radius:999px;width:28px"/>
						</figure>
						<!-- /wp:image -->

						<!-- wp:image {"lightbox":{"enabled":false},"width":"28px","sizeSlug":"full","linkDestination":"none","style":{"border":{"radius":"999px","width":"1px"},"spacing":{"margin":{"left":"-12px"}}},"borderColor":"base"} -->
						<figure class="wp-block-image size-full is-resized has-custom-border" style="margin-left:-12px">
							<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/placeholders/placeholder-avatar-2.webp" alt="" class="has-border-color has-base-border-color" style="border-width:1px;border-radius:999px;width:28px"/>
						</figure>
						<!-- /wp:image -->

						<!-- wp:image {"lightbox":{"enabled":false},"width":"28px","sizeSlug":"full","linkDestination":"none","style":{"border":{"radius":"999px","width":"1px"},"spacing":{"margin":{"left":"-12px"}}},"borderColor":"base"} -->
						<figure class="wp-block-image size-full is-resized has-custom-border" style="margin-left:-12px">
							<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/placeholders/placeholder-avatar-3.webp" alt="" class="has-border-color has-base-border-color" style="border-width:1px;border-radius:999px;width:28px"/>
						</figure>
						<!-- /wp:image -->

						<!-- wp:image {"lightbox":{"enabled":false},"width":"28px","sizeSlug":"large","linkDestination":"none","style":{"border":{"radius":"999px","width":"1px"},"spacing":{"margin":{"left":"-12px"}}},"borderColor":"base"} -->
						<figure class="wp-block-image size-large is-resized has-custom-border" style="margin-left:-12px">
							<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/placeholders/placeholder-avatar-4.webp" alt="" class="has-border-color has-base-border-color" style="border-width:1px;border-radius:999px;width:28px"/>
						</figure>
						<!-- /wp:image -->

					</div>
					<!-- /wp:group -->

					<!-- wp:paragraph -->
					<p><?php echo esc_html__( '1000+ satisfied customers', 'upa25' ); ?></p>
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
</section>
<!-- /wp:cover -->
