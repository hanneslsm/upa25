<?php
/**
 * Title: Hero-03
 * Slug: upa25/hero-03
 * Categories: upa25/heros
 * Viewport width: 1920
 * Description: Hero section with a cover image, headline, and buttons.
 */
?>

<!-- wp:group {"tagName":"section","metadata":{"name":"Section","categories":["text"],"patternName":"upa25/text-02"},"align":"full","className":"is-style-section-base-2","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-section-base-2"><!-- wp:group {"align":"wide","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide"><!-- wp:spacer {"height":"var:preset|spacing|40"} -->
<div style="height:var(--wp--preset--spacing--40)" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"var:preset|spacing|40"}}}} -->
<div class="wp-block-columns"><!-- wp:column {"verticalAlignment":"bottom","width":"33.33%"} -->
<div class="wp-block-column is-vertically-aligned-bottom" style="flex-basis:33.33%"></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"bottom","width":"66.66%"} -->
<div class="wp-block-column is-vertically-aligned-bottom" style="flex-basis:66.66%"><!-- wp:spacer {"height":"10dvh"} -->
<div style="height:10dvh" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:paragraph {"className":"is-style-overline"} -->
<p class="is-style-overline"><?php esc_html_e('This is your overline', 'upa25');?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading"><?php esc_html_e('Place your website headline here.', 'upa25');?></h1>
<!-- /wp:heading --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:columns {"className":"on-mobile-with-reverse-order","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|40"}}}} -->
<div class="wp-block-columns on-mobile-with-reverse-order"><!-- wp:column {"verticalAlignment":"bottom","width":"33.33%"} -->
<div class="wp-block-column is-vertically-aligned-bottom" style="flex-basis:33.33%"><!-- wp:paragraph -->
<p><?php /* Translators: 1. is a 'br' HTML element */
echo sprintf( esc_html__( 'Donec augue arcu, sollicitudin et nulla et, %1$sporttitor imperdiet elit. ', 'upa25' ), '<br>' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"bottom","width":"66.66%"} -->
<div class="wp-block-column is-vertically-aligned-bottom" style="flex-basis:66.66%"><!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size"><?php esc_html_e('Sed placerat, odio in condimentum lacinia, augue justo ultricies lacus, in porta lectus leo euismod ipsum. Curabitur pretium, leo vel porttitor dictum, dolor metus congue metus, non hendrerit metus tortor sit amet purus. ', 'upa25');?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:spacer {"height":"var:preset|spacing|40"} -->
<div style="height:var(--wp--preset--spacing--40)" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer --></div>
<!-- /wp:group -->

<!-- wp:cover {"url":"<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/placeholders/placeholder-image-6.webp","dimRatio":0,"isUserOverlayColor":true,"minHeight":50,"minHeightUnit":"dvh","isDark":false,"sizeSlug":"large","align":"full","layout":{"type":"constrained"}} -->
<div class="wp-block-cover alignfull is-light" style="min-height:50dvh"><img class="wp-block-cover__image-background  size-large" alt="" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/placeholders/placeholder-image-6.webp" data-object-fit="cover"/><span aria-hidden="true" class="wp-block-cover__background has-background-dim-0 has-background-dim"></span><div class="wp-block-cover__inner-container"><!-- wp:paragraph {"align":"center","placeholder":"Write title…","fontSize":"large"} -->
<p class="has-text-align-center has-large-font-size"></p>
<!-- /wp:paragraph --></div></div>
<!-- /wp:cover --></section>
<!-- /wp:group -->
