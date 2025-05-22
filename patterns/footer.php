<?php
/**
 * Title: footer
 * Slug: upa25/footer
 * Categories: footer
 * Block Types: core/template-part/footer
 * Viewport width: 1920
 */
?>
<!-- wp:group {"metadata":{"categories":["footer"],"patternName":"upa25/footer-centered"},"className":"is-style-footer","style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}},"color":{"gradient":"linear-gradient(180deg,rgb(251,239,243) 0%,rgb(255,255,255) 100%)"},"border":{"top":{"color":"var:preset|color|brand-4","width":"1px"},"right":[],"bottom":[],"left":[]}},"layout":{"inherit":true,"type":"constrained"}} -->
<div class="wp-block-group is-style-footer has-background" style="border-top-color:var(--wp--preset--color--brand-4);border-top-width:1px;background:linear-gradient(180deg,rgb(251,239,243) 0%,rgb(255,255,255) 100%);padding-top:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30)"><!-- wp:group {"align":"wide","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide"><!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"bottom","justifyContent":"space-between"}} -->
<div class="wp-block-group"><!-- wp:site-logo {"width":80} /-->

<!-- wp:social-links {"iconBackgroundColor":"brand-3","iconBackgroundColorValue":"#EA618E","size":"has-small-icon-size"} -->
<ul class="wp-block-social-links has-small-icon-size has-icon-background-color"><!-- wp:social-link {"url":"https://www.facebook.com/poledance.darmstadt/","service":"facebook"} /-->

<!-- wp:social-link {"url":"https://www.instagram.com/uniquepoleart_darmstadt","service":"instagram"} /-->

<!-- wp:social-link {"url":"https://www.youtube.com/channel/UC-7uEaqJRPtDDOsf77UM2Yw","service":"youtube"} /--></ul>
<!-- /wp:social-links --></div>
<!-- /wp:group -->

<!-- wp:columns {"style":{"spacing":{"blockGap":{"left":"var:preset|spacing|40"}}}} -->
<div class="wp-block-columns"><!-- wp:column {"width":"50%"} -->
<div class="wp-block-column" style="flex-basis:50%"><!-- wp:paragraph -->
<p><?php /* Translators: 1. is a 'br' HTML element */
echo sprintf( esc_html__( 'Unique Pole Art – dein Pole-Dance- und Pole-Fitness-Studio in Darmstadt. %1$sIndividuelle Betreuung, modernes Ambiente und Kurse für Einsteiger bis Fortgeschrittene machen uns zur ersten Adresse für Pole Art und Fitness in Darmstadt', 'upa25' ), '<br>' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><?php /* Translators: 1. is the start of a 'a' HTML element, 2. is the end of a 'a' HTML element */
echo sprintf( esc_html__( 'Design & Umsetzung von %1$sStudio Leismann%2$s', 'upa25' ), '<a href="' . esc_url( 'https://studioleismann.com/' ) . '" target="_blank" rel="noreferrer noopener">', '</a>' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"align":"right","className":"on-medium-left"} -->
<p class="has-text-align-right on-medium-left"><?php /* Translators: 1. is the start of a 'strong' HTML element, 2. is the end of a 'strong' HTML element, 3. is a 'br' HTML element, 4. is a 'br' HTML element, 5. is a 'br' HTML element, 6. is a 'br' HTML element */
echo sprintf( esc_html__( '%1$sUnique Pole Art%2$s%3$sAm Kavalleriesand 47%4$s64295 Darmstadt%5$sinfo@poledance-darmstadt.de%6$sT: 0157 76 42 61 41', 'upa25' ), '<strong>', '</strong>', '<br>', '<br>', '<br>', '<br>' ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:navigation {"overlayMenu":"never","layout":{"type":"flex","justifyContent":"center","orientation":"horizontal"}} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
