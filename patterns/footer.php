<?php
/**
 * Title: Footer
 * Slug: upa25/footer
 * Categories: footer
 * Block Types: core/template-part/footer
 */
?>
<!-- wp:group {"className":"is-style-footer","style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}}},"layout":{"inherit":true,"type":"constrained"}} -->
<div class="wp-block-group is-style-footer" style="padding-top:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30)"><!-- wp:group {"align":"wide","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide"><!-- wp:image {"lightbox":{"enabled":false},"width":"100px","sizeSlug":"full","linkDestination":"none","className":"activate-rainbow","style":{"border":{"radius":"0px"}}} -->
<figure class="wp-block-image size-full is-resized has-custom-border activate-rainbow"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/logo.png" alt="" class="" style="border-radius:0px;width:100px"/></figure>
<!-- /wp:image -->

<!-- wp:columns -->
<div class="wp-block-columns"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph -->
<p><?php esc_html_e('Enter a short description of your company here. This helps your visitors to understand what you do and improves your ranking in search engines. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'upa25');?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"align":"right","className":"on-medium-left"} -->
<p class="has-text-align-right on-medium-left"><?php /* Translators: 1. is the start of a 'em' HTML element, 2. is the end of a 'em' HTML element, 3. is a 'br' HTML element, 4. is a 'br' HTML element */
echo sprintf( esc_html__( '%1$sYour Name%2$s%3$sYour address%4$sContact info', 'upa25' ), '<em>', '</em>', '<br>', '<br>' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:navigation {"overlayMenu":"never","layout":{"type":"flex","justifyContent":"center"}} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
