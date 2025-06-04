<?php
/**
 * Title: Hero-04
 * Slug: upa25/hero-04
 * Categories: upa25/heros
 * Viewport width: 1920
 * Description: Hero section with background image and centered content.
 */
?>
<!-- wp:cover {"url":"<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/placeholders/placeholder-image-2.webp","dimRatio":60,"overlayColor":"brand-4","isUserOverlayColor":true,"minHeight":60,"minHeightUnit":"dvh","isDark":false,"tagName":"section","align":"full","layout":{"type":"constrained"}} -->
<section class="wp-block-cover alignfull is-light" style="min-height:60dvh"><img class="wp-block-cover__image-background" alt="" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/placeholders/placeholder-image-2.webp" data-object-fit="cover"/><span aria-hidden="true" class="wp-block-cover__background has-brand-4-background-color has-background-dim-60 has-background-dim"></span><div class="wp-block-cover__inner-container"><!-- wp:spacer {"height":"var:preset|spacing|50"} -->
<div style="height:var(--wp--preset--spacing--50)" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading {"textAlign":"center","level":1} -->
<h1 class="wp-block-heading has-text-align-center"><?php esc_html_e('Add your catchy headline here.', 'upa25'); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center"><?php esc_html_e('Brief description to support the headline.', 'upa25'); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button"><?php esc_html_e('Learn more', 'upa25'); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->

<!-- wp:spacer {"height":"var:preset|spacing|50"} -->
<div style="height:var(--wp--preset--spacing--50)" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer --></div></section>
<!-- /wp:cover -->
