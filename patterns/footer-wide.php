<?php
/**
 * Title: footer-wide
 * Slug: upa25/footer-wide
 * Categories: footer
 * Block Types: core/template-part/footer
 */
?>
<!-- wp:group {"metadata":{"categories":["footer"],"patternName":"upa25/footer-wide"},"className":"is-style-footer","style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}},"color":{"gradient":"linear-gradient(180deg,rgba(245,245,245,1) 0%,rgba(255,255,255,1) 100%)"}},"layout":{"inherit":true,"type":"constrained"}} -->
<div class="wp-block-group is-style-footer has-background" style="background:linear-gradient(180deg,rgba(245,245,245,1) 0%,rgba(255,255,255,1) 100%);padding-top:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30)"><!-- wp:group {"align":"wide","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide"><!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"bottom","justifyContent":"space-between"}} -->
<div class="wp-block-group"><!-- wp:site-logo {"width":80} /-->

<!-- wp:social-links {"iconBackgroundColor":"primary","iconBackgroundColorValue":"#cccccc","size":"has-small-icon-size"} -->
<ul class="wp-block-social-links has-small-icon-size has-icon-background-color"><!-- wp:social-link {"url":"#","service":"facebook"} /-->

<!-- wp:social-link {"url":"#","service":"instagram"} /-->

<!-- wp:social-link {"url":"#","service":"youtube"} /--></ul>
<!-- /wp:social-links --></div>
<!-- /wp:group -->

<!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"var:preset|spacing|40"}}}} -->
<div class="wp-block-columns"><!-- wp:column {"width":"50%"} -->
<div class="wp-block-column" style="flex-basis:50%"><!-- wp:paragraph -->
<p><?php /* Translators: 1. is a 'br' HTML element */
echo sprintf( esc_html__( 'Your Company Name – your tagline goes here. %1$sBrief description or mission statement here.', 'upa25' ), '<br>' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><?php /* Translators: 1. is the start of a 'a' HTML element, 2. is the end of a 'a' HTML element */
echo sprintf( esc_html__( 'Design & Implementation by %1$sYour Agency Name%2$s', 'upa25' ), '<a href="' . esc_url( '#' ) . '" target="_blank" rel="noreferrer noopener">', '</a>' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"align":"right","className":"on-medium-left"} -->
<p class="has-text-align-right on-medium-left"><?php /* Translators: 1. is the start of a 'strong' HTML element, 2. is the end of a 'strong' HTML element, 3. is a 'br' HTML element, 4. is a 'br' HTML element, 5. is a 'br' HTML element, 6. is a 'br' HTML element */
echo sprintf( esc_html__( '%1$sYour Company Name%2$s%3$s123 Placeholder Street%4$s00000 City%5$syouremail@example.com%6$sT: 0123 456 789', 'upa25' ), '<strong>', '</strong>', '<br>', '<br>', '<br>', '<br>' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:navigation {"overlayMenu":"never","layout":{"type":"flex","justifyContent":"center","orientation":"horizontal"}} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
