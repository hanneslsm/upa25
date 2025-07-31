<?php
/**
 * Title: coming-soon
 * Slug: upa25/coming-soon
 * Inserter: no
 */
?>
<!-- wp:template-part {"slug":"header","area":"header","align":"wide"} /-->

<!-- wp:group {"tagName":"main","metadata":{"name":"Main"},"style":{"spacing":{"margin":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}},"dimensions":{"minHeight":"100%"}},"layout":{"type":"constrained"}} -->
<main class="wp-block-group" style="min-height:100%;margin-top:var(--wp--preset--spacing--40);margin-bottom:var(--wp--preset--spacing--40)"><!-- wp:spacer {"height":"var:preset|spacing|50"} -->
<div style="height:var(--wp--preset--spacing--50)" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:group {"align":"wide","className":"is-style-section-base-2","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"8px","width":"1px"}},"borderColor":"base-3","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide is-style-section-base-2 has-border-color has-base-3-border-color" style="border-width:1px;border-radius:8px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><!-- wp:site-logo {"align":"center"} /-->

<!-- wp:group {"style":{"spacing":{"blockGap":"0"}},"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"className":"is-style-overline"} -->
<p class="is-style-overline"><?php /* Translators: 1. is the start of a 'strong' HTML element, 2. is the end of a 'strong' HTML element */ 
echo sprintf( esc_html__( '%1$sStay tuned!%2$s', 'upa25' ), '<strong>', '</strong>' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"textAlign":"center","level":1,"style":{"typography":{"fontSize":"4rem"}}} -->
<h1 class="wp-block-heading has-text-align-center" style="font-size:4rem"><?php esc_html_e('Coming Soon…', 'upa25');?></h1>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:spacer {"height":"var:preset|spacing|30"} -->
<div style="height:var(--wp--preset--spacing--30)" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:site-title {"level":0,"textAlign":"center","fontSize":"small"} /--></div>
<!-- /wp:group -->

<!-- wp:spacer {"height":"var:preset|spacing|50"} -->
<div style="height:var(--wp--preset--spacing--50)" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer --></main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->