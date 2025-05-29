<?php
/**
 * Title: Layout-C-01
 * Slug: upa25/layout-c-01
 * Categories: upa25/layout-c
 * Viewport width: 1920
 */
?>
<!-- wp:group {"tagName":"section","align":"full","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull"><!-- wp:group {"align":"wide","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide"><!-- wp:spacer {"height":"var:preset|spacing|40"} -->
<div style="height:var(--wp--preset--spacing--40)" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:columns {"className":"on-medium-reverse-order","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|40"}}}} -->
<div class="wp-block-columns on-medium-reverse-order"><!-- wp:column {"verticalAlignment":"center","width":"50%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:50%"><!-- wp:paragraph {"className":"is-style-overline"} -->
<p class="is-style-overline"><?php esc_html_e('This is your overline', 'upa25');?></p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading"><?php esc_html_e('Place your medium headline here.', 'upa25');?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut vel turpis elementum, posuere dui eget, consectetur leo. Nulla sapien elit, consectetur ut porttitor ut, molestie vel sapien. Proin vitae suscipit risus, ac congue est. Integer interdum aliquet velit vel facilisis. Morbi vulputate pellentesque dolor vitae dapibus. ', 'upa25');?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"50%","className":""} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:50%"><!-- wp:image {"sizeSlug":"large","linkDestination":"none","className":""} -->
<figure class="wp-block-image size-large"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/placeholder-image-1.webp" alt=""/></figure>
<!-- /wp:image --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:spacer {"height":"var:preset|spacing|40"} -->
<div style="height:var(--wp--preset--spacing--40)" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
