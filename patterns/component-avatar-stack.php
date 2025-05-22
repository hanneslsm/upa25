<?php
/**
 * Title: component-avatar-stack
 * Slug: upa25/component-avatar-stack
 * Categories: upa25/components
 * Viewport width: 320
 */
?>
<!-- wp:group {"metadata":{"name":"Component"},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group"><!-- wp:group {"style":{"spacing":{"blockGap":"0"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group"><!-- wp:image {"lightbox":{"enabled":false},"width":"52px","height":"auto","scale":"cover","sizeSlug":"full","linkDestination":"none","className":"is-style-default on-hover-scale z-1000","style":{"border":{"radius":"999px","width":"2px"}},"borderColor":"base"} -->
<figure class="wp-block-image size-full is-resized has-custom-border is-style-default on-hover-scale z-1000"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/placeholders/placeholder-avatar-1.webp" alt="" class="has-border-color has-base-border-color " style="border-width:2px;border-radius:999px;object-fit:cover;width:52px;height:auto"/></figure>
<!-- /wp:image -->

<!-- wp:image {"lightbox":{"enabled":false},"width":"52px","aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none","className":"is-style-default on-hover-scale z-2000","style":{"border":{"radius":"999px","width":"2px"},"spacing":{"margin":{"left":"-24px"}}},"borderColor":"base"} -->
<figure class="wp-block-image size-full is-resized has-custom-border is-style-default on-hover-scale z-2000" style="margin-left:-24px"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/placeholders/placeholder-avatar-2.webp" alt="" class="has-border-color has-base-border-color " style="border-width:2px;border-radius:999px;aspect-ratio:1;object-fit:cover;width:52px"/></figure>
<!-- /wp:image -->

<!-- wp:image {"lightbox":{"enabled":false},"width":"52px","aspectRatio":"1","scale":"cover","sizeSlug":"full","linkDestination":"none","className":"is-style-default on-hover-scale z-3000","style":{"border":{"radius":"999px","width":"2px"},"spacing":{"margin":{"left":"-24px"}}},"borderColor":"base"} -->
<figure class="wp-block-image size-full is-resized has-custom-border is-style-default on-hover-scale z-3000" style="margin-left:-24px"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/placeholders/placeholder-avatar-3.webp" alt="" class="has-border-color has-base-border-color " style="border-width:2px;border-radius:999px;aspect-ratio:1;object-fit:cover;width:52px"/></figure>
<!-- /wp:image --></div>
<!-- /wp:group -->

<!-- wp:group {"style":{"spacing":{"blockGap":"0"}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size"><?php /* Translators: 1. is the start of a 'mark' HTML element, 2. is the end of a 'mark' HTML element, 3. is the start of a 'strong' HTML element, 4. is the end of a 'strong' HTML element */
echo sprintf( esc_html__( '%1$s★★★★★%2$s %3$s5,0%4$s', 'upa25' ), '<mark style="background-color:rgba(0, 0, 0, 0);color:#f8c761" class="has-inline-color">', '</mark>', '<strong>', '</strong>' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"fontSize":"small"} -->
<p class="has-small-font-size"><?php esc_html_e('Google-Bewertung', 'upa25');?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
