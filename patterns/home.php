<?php
/**
 * Title: home
 * Slug: upa25/home
 * Inserter: no
 */
?>
<!-- wp:group {"metadata":{"name":"Wrapper"},"align":"wide","style":{"spacing":{"blockGap":"0"}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignwide"><!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"0"}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignwide"><!-- wp:template-part {"slug":"header","area":"header","align":"full"} /-->

<!-- wp:group {"align":"wide","className":"is-style-section-brand-5","style":{"spacing":{"padding":{"bottom":"var:preset|spacing|40","top":"var:preset|spacing|50"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide is-style-section-brand-5" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--40)"><!-- wp:group {"align":"wide","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide"><!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading"><?php esc_html_e('Neuigkeiten', 'upa25');?></h1>
<!-- /wp:heading --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"tagName":"main","metadata":{"name":"Main"},"layout":{"type":"constrained"}} -->
<main class="wp-block-group"><!-- wp:group {"align":"wide","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide"><!-- wp:spacer {"height":"var:preset|spacing|40"} -->
<div style="height:var(--wp--preset--spacing--40)" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:query {"queryId":0,"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"only","inherit":false,"parents":[],"format":[]},"layout":{"type":"default"}} -->
<div class="wp-block-query"><!-- wp:post-template {"layout":{"type":"default","columnCount":1}} -->
<!-- wp:columns {"className":"is-style-section-brand-5","style":{"spacing":{"blockGap":{"left":"0"}},"border":{"width":"1px"}},"borderColor":"brand-3"} -->
<div class="wp-block-columns is-style-section-brand-5 has-border-color has-brand-3-border-color" style="border-width:1px"><!-- wp:column {"width":"33.33%","className":"with-display-none-when-empty"} -->
<div class="wp-block-column with-display-none-when-empty" style="flex-basis:33.33%"><!-- wp:post-featured-image {"isLink":true,"aspectRatio":"3/2","width":"100%","height":"100%","style":{"layout":{"selfStretch":"fit","flexSize":null}}} /--></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"","style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}}} -->
<div class="wp-block-column is-vertically-aligned-center" style="padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)"><!-- wp:post-title {"level":3,"isLink":true,"fontSize":"x-large"} /-->

<!-- wp:post-excerpt {"showMoreOnNewLine":false,"excerptLength":100} /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
<!-- /wp:post-template --></div>
<!-- /wp:query -->

<!-- wp:query {"queryId":0,"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"exclude","inherit":false,"parents":[],"format":[]},"layout":{"type":"default"}} -->
<div class="wp-block-query"><!-- wp:post-template {"layout":{"type":"default","columnCount":1}} -->
<!-- wp:columns {"className":"is-style-section-base","style":{"border":{"width":"1px"},"shadow":"var:preset|shadow|small"},"borderColor":"brand-4"} -->
<div class="wp-block-columns is-style-section-base has-border-color has-brand-4-border-color" style="border-width:1px;box-shadow:var(--wp--preset--shadow--small)"><!-- wp:column {"verticalAlignment":"center","width":"","style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30","right":"var:preset|spacing|30"}}}} -->
<div class="wp-block-column is-vertically-aligned-center" style="padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)"><!-- wp:post-title {"level":3,"isLink":true,"style":{"elements":{"link":{"color":{"text":"var:preset|color|contrast-3"}}},"typography":{"textDecoration":"none"}},"textColor":"contrast-3","fontSize":"xx-large"} /-->

<!-- wp:post-excerpt {"showMoreOnNewLine":false,"excerptLength":100} /--></div>
<!-- /wp:column -->

<!-- wp:column {"width":"33.33%","className":"with-display-none-when-empty"} -->
<div class="wp-block-column with-display-none-when-empty" style="flex-basis:33.33%"><!-- wp:post-featured-image {"isLink":true,"aspectRatio":"3/2","width":"100%","height":"100%","style":{"layout":{"selfStretch":"fit","flexSize":null}}} /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->
<!-- /wp:post-template -->

<!-- wp:query-no-results -->
<!-- wp:paragraph {"placeholder":"Add text or blocks that will display when a query returns no results."} -->
<p><?php /* Translators: 1. is the start of a 'em' HTML element, 2. is the end of a 'em' HTML element */ 
echo sprintf( esc_html__( '%1$sKeine Blogbeiträge gefunden.%2$s', 'upa25' ), '<em>', '</em>' ); ?></p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results --></div>
<!-- /wp:query -->

<!-- wp:spacer {"height":"var:preset|spacing|40"} -->
<div style="height:var(--wp--preset--spacing--40)" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer --></div>
<!-- /wp:group --></main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer","tagName":"footer"} /--></div>
<!-- /wp:group -->