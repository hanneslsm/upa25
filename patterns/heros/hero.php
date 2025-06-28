<?php
/**
 * Title: Hero-01
 * Slug: upa25/hero-01
 * Categories: upa25/heros
 * Viewport width: 1920
 * Description: Hero section with a cover image, headline, and buttons.
 */
?>
<!-- wp:cover {"url":"http://upa25.local/wp-content/themes/upa25/assets/images/placeholders/placeholder-image-1.webp","hasParallax":true,"dimRatio":80,"overlayColor":"brand-5","isUserOverlayColor":true,"minHeight":65,"minHeightUnit":"dvh","isDark":false,"tagName":"section","sizeSlug":"large","align":"full","layout":{"type":"constrained"}} -->
<section class="wp-block-cover alignfull is-light has-parallax" style="min-height:65dvh"><div class="wp-block-cover__image-background  size-large has-parallax" style="background-position:50% 50%;background-image:url(http://upa25.local/wp-content/themes/upa25/assets/images/placeholders/placeholder-image-1.webp)"></div><span aria-hidden="true" class="wp-block-cover__background has-brand-5-background-color has-background-dim-80 has-background-dim"></span><div class="wp-block-cover__inner-container"><!-- wp:spacer {"height":"var:preset|spacing|50","style":{"layout":[]}} -->
<div style="height:var(--wp--preset--spacing--50)" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|30"}}}} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"verticalAlignment":"center","width":"50%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:50%"><!-- wp:paragraph {"className":"is-style-overline"} -->
<p class="is-style-overline"><?php esc_html_e('This is your overline', 'upa25');?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading"><?php esc_html_e('Place your medium headline here.', 'upa25');?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut vel turpis elementum, posuere dui eget, consectetur leo. Nulla sapien elit, consectetur ut porttitor ut, molestie vel sapien.', 'upa25');?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button"><?php esc_html_e('Click here', 'upa25');?></a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button"><?php esc_html_e('Learn more', 'upa25');?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"%"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"large","linkDestination":"none","className":""} -->
<figure class="wp-block-image size-large"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/placeholders/placeholder-image-4.webp" alt="" style="aspect-ratio:1;object-fit:cover"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:spacer {"height":"var:preset|spacing|50","style":{"layout":[]}} -->
<div style="height:var(--wp--preset--spacing--50)" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer --></div></section>
<!-- /wp:cover -->
