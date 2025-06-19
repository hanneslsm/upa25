<?php
/**
 * Title: home-2
 * Slug: upa25/home-2
 * Inserter: no
 */
?>
<!-- wp:template-part {"slug":"header","area":"header","align":"wide"} /-->

<!-- wp:group {"tagName":"main","metadata":{"name":"Main"},"style":{"spacing":{"margin":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40"}},"dimensions":{"minHeight":"100%"}},"layout":{"type":"constrained"}} -->
<main class="wp-block-group" style="min-height:100%;margin-top:var(--wp--preset--spacing--40);margin-bottom:var(--wp--preset--spacing--40)"><!-- wp:query {"queryId":0,"query":{"perPage":10,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true},"align":"wide","layout":{"type":"default"}} -->
<div class="wp-block-query alignwide"><!-- wp:post-template -->
<!-- wp:columns {"align":"wide","style":{"spacing":{"margin":{"top":"var:preset|spacing|20","bottom":"var:preset|spacing|20"}}}} -->
<div class="wp-block-columns alignwide" style="margin-top:var(--wp--preset--spacing--20);margin-bottom:var(--wp--preset--spacing--20)"><!-- wp:column {"verticalAlignment":"top","width":"66.66%"} -->
<div class="wp-block-column is-vertically-aligned-top" style="flex-basis:66.66%"><!-- wp:post-title {"isLink":true,"style":{"typography":{"fontSize":"1.5rem"}}} /--></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":""} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:group {"className":"is-style-default","fontSize":"small","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"right"}} -->
<div class="wp-block-group is-style-default has-small-font-size"><!-- wp:post-date /-->

<!-- wp:post-author-name /--></div>
<!-- /wp:group --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:separator {"className":"alignwide is-style-wide","backgroundColor":"base-4"} -->
<hr class="wp-block-separator has-text-color has-base-4-color has-alpha-channel-opacity has-base-4-background-color has-background alignwide is-style-wide"/>
<!-- /wp:separator -->
<!-- /wp:post-template -->

<!-- wp:spacer {"height":"var:preset|spacing|30"} -->
<div style="height:var(--wp--preset--spacing--30)" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:query-pagination {"paginationArrow":"arrow","layout":{"type":"flex","justifyContent":"space-between"}} -->
<!-- wp:query-pagination-previous /-->

<!-- wp:query-pagination-numbers /-->

<!-- wp:query-pagination-next /-->
<!-- /wp:query-pagination -->

<!-- wp:query-no-results -->
<!-- wp:paragraph -->
<p><?php esc_html_e('No posts were found.', 'upa25');?></p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results --></div>
<!-- /wp:query --></main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->
