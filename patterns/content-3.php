<?php

/**
 * Title: Content-3
 * Slug: upa25/content-3
 * Categories: upa25/content
 * Viewport width: 1280
 * Description: Content section with image and text
 */
?>
<!-- wp:group {"metadata":{"name":"Section"},"align":"full","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull"><!-- wp:group {"align":"wide","layout":{"type":"default"}} -->
	<div class="wp-block-group alignwide"><!-- wp:spacer {"height":"var:preset|spacing|40"} -->
		<div style="height:var(--wp--preset--spacing--40)" aria-hidden="true" class="wp-block-spacer"></div>
		<!-- /wp:spacer -->

		<!-- wp:columns {"verticalAlignment":null} -->
		<div class="wp-block-columns">
			<!-- wp:column {"verticalAlignment":"center","width":"","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}}} -->
			<div class="wp-block-column is-vertically-aligned-center" style="padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><!-- wp:heading -->
				<h2 class="wp-block-heading"><?php esc_html_e('TEST PATTERN 3 TEST PATTERN 3', 'upa25'); ?></h2>
				<!-- /wp:heading -->

				<!-- wp:paragraph -->
				<p><?php esc_html_e('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'upa25'); ?></p>
				<!-- /wp:paragraph -->

				<!-- wp:heading {"level":3,"fontSize":"large"} -->
				<h3 class="wp-block-heading has-large-font-size"><?php esc_html_e('Place your subheadline here.', 'upa25'); ?></h3>
				<!-- /wp:heading -->

				<!-- wp:list -->
				<ul class="wp-block-list"><!-- wp:list-item -->
					<li><?php esc_html_e('Lorem ipsum dolor sit amet.', 'upa25'); ?></li>
					<!-- /wp:list-item -->

					<!-- wp:list-item -->
					<li><?php esc_html_e('Consectetur adipiscing elit.', 'upa25'); ?></li>
					<!-- /wp:list-item -->

					<!-- wp:list-item -->
					<li><?php esc_html_e('Sed do eiusmod tempor.', 'upa25'); ?></li>
					<!-- /wp:list-item -->

					<!-- wp:list-item -->
					<li><?php esc_html_e('Incididunt ut labore et dolore.', 'upa25'); ?></li>
					<!-- /wp:list-item -->

					<!-- wp:list-item -->
					<li><?php esc_html_e('Magna aliqua ut enim ad minim.', 'upa25'); ?></li>
					<!-- /wp:list-item -->

					<!-- wp:list-item -->
					<li><?php esc_html_e('Quis nostrud exercitation.', 'upa25'); ?></li>
					<!-- /wp:list-item -->
				</ul>
				<!-- /wp:list -->

				<!-- wp:paragraph {"className":"is-style-indicator"} -->
				<p class="is-style-indicator"><?php esc_html_e('Placeholder indicator text', 'upa25'); ?></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:column -->

			<!-- wp:column {"width":"33.33%"} -->
			<div class="wp-block-column" style="flex-basis:33.33%"><!-- wp:image {"sizeSlug":"large","linkDestination":"none"} -->
				<figure class="wp-block-image size-large"><img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/placeholder-1.webp" alt="" class="" /></figure>
				<!-- /wp:image -->
			</div>
			<!-- /wp:column -->
		</div>
		<!-- /wp:columns -->

		<!-- wp:spacer {"height":"var:preset|spacing|40"} -->
		<div style="height:var(--wp--preset--spacing--40)" aria-hidden="true" class="wp-block-spacer"></div>
		<!-- /wp:spacer -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
