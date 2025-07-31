<?php
/**
 * Title: taxonomy-product_cat
 * Slug: upa25/taxonomy-product_cat
 * Inserter: no
 */
?>
<!-- wp:group {"metadata":{"name":"Wrapper"},"align":"wide","style":{"spacing":{"blockGap":"0"}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignwide"><!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"0"}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignwide"><!-- wp:template-part {"slug":"header","area":"header","align":"wide"} /-->

<!-- wp:group {"align":"wide","className":"is-style-section-brand-5","style":{"spacing":{"padding":{"bottom":"var:preset|spacing|20","top":"var:preset|spacing|30"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide is-style-section-brand-5" style="padding-top:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--20)"><!-- wp:woocommerce/breadcrumbs /-->

<!-- wp:query-title {"type":"archive","showPrefix":false,"align":"wide"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"tagName":"main","metadata":{"name":"Main"},"style":{"spacing":{"padding":{"top":"var:preset|spacing|30"}}},"layout":{"type":"constrained"}} -->
<main class="wp-block-group" style="padding-top:var(--wp--preset--spacing--30)"><!-- wp:woocommerce/product-collection {"queryId":21,"query":{"woocommerceAttributes":[],"woocommerceStockStatus":["instock","outofstock","onbackorder"],"taxQuery":[],"isProductCollectionBlock":true,"perPage":10,"pages":0,"offset":0,"postType":"product","order":"asc","orderBy":"title","author":"","search":"","exclude":[],"sticky":"","inherit":true},"tagName":"div","displayLayout":{"type":"flex","columns":3,"shrinkColumns":true},"dimensions":{"widthType":"fill","fixedWidth":""},"queryContextIncludes":["collection"],"__privatePreviewState":{"isPreview":false,"previewMessage":"Actual products will vary depending on the page being viewed."},"align":"wide"} -->
<div class="wp-block-woocommerce-product-collection alignwide"><!-- wp:woocommerce/product-template -->
<!-- wp:woocommerce/product-image {"showSaleBadge":false,"isDescendentOfQueryLoop":true} -->
<!-- wp:woocommerce/product-sale-badge {"align":"right"} /-->
<!-- /wp:woocommerce/product-image -->

<!-- wp:post-title {"textAlign":"center","isLink":true,"style":{"typography":{"lineHeight":"1.4"}},"fontSize":"medium","__woocommerceNamespace":"woocommerce/product-collection/product-title"} /-->

<!-- wp:woocommerce/product-price {"isDescendentOfQueryLoop":true,"textAlign":"center","fontSize":"small","style":{"spacing":{"margin":{"bottom":"1rem"}}}} /-->

<!-- wp:woocommerce/product-button {"textAlign":"center","isDescendentOfQueryLoop":true,"fontSize":"small","style":{"spacing":{"margin":{"bottom":"1rem"}}}} /-->
<!-- /wp:woocommerce/product-template -->

<!-- wp:query-pagination {"layout":{"type":"flex","justifyContent":"center"}} -->
<!-- wp:query-pagination-previous /-->

<!-- wp:query-pagination-numbers /-->

<!-- wp:query-pagination-next /-->
<!-- /wp:query-pagination -->

<!-- wp:woocommerce/product-collection-no-results -->
<!-- wp:group {"layout":{"type":"flex","orientation":"vertical","justifyContent":"center","flexWrap":"wrap"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"fontSize":"medium"} -->
<p class="has-medium-font-size"><?php /* Translators: 1. is the start of a 'strong' HTML element, 2. is the end of a 'strong' HTML element */ 
echo sprintf( esc_html__( '%1$sNo results found%2$s', 'upa25' ), '<strong>', '</strong>' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><?php /* Translators: 1. is the start of a 'a' HTML element, 2. is the end of a 'a' HTML element, 3. is the start of a 'a' HTML element, 4. is the end of a 'a' HTML element */ 
echo sprintf( esc_html__( 'You can try %1$sclearing any filters%2$s or head to our %3$sstore\'s home%4$s', 'upa25' ), '<a class="wc-link-clear-any-filters" href="' . esc_url( '#' ) . '">', '</a>', '<a class="wc-link-stores-home" href="' . esc_url( '#' ) . '">', '</a>' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->
<!-- /wp:woocommerce/product-collection-no-results --></div>
<!-- /wp:woocommerce/product-collection -->

<!-- wp:group {"tagName":"section","metadata":{"categories":["upa25/cards"],"patternName":"upa25/card-6","name":"Card-6"},"align":"full","className":"is-style-section-brand-5","backgroundColor":"brand-6","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-section-brand-5 has-brand-6-background-color has-background"><!-- wp:group {"align":"wide","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide"><!-- wp:spacer {"height":"var:preset|spacing|20"} -->
<div style="height:var(--wp--preset--spacing--20)" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:woocommerce/store-notices /-->

<!-- wp:group {"className":"alignwide","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
<div class="wp-block-group alignwide"><!-- wp:woocommerce/product-results-count /-->

<!-- wp:woocommerce/catalog-sorting /--></div>
<!-- /wp:group -->

<!-- wp:spacer {"height":"var:preset|spacing|20"} -->
<div style="height:var(--wp--preset--spacing--20)" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer --></div>
<!-- /wp:group --></section>
<!-- /wp:group --></main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer","tagName":"footer"} /--></div>
<!-- /wp:group -->