<?php
/**
 * Title: page-cart
 * Slug: upa25/page-cart
 * Inserter: no
 */
?>
<!-- wp:template-part {"slug":"header","tagName":"header"} /-->

<!-- wp:woocommerce/page-content-wrapper {"page":"cart"} -->
<!-- wp:group {"tagName":"main","layout":{"type":"constrained"}} -->
<main class="wp-block-group"><!-- wp:woocommerce/store-notices /-->

<!-- wp:post-title {"level":1,"align":"wide"} /-->

<!-- wp:woocommerce/cart {"className":"wc-block-cart"} -->
<div class="wp-block-woocommerce-cart alignwide is-loading wc-block-cart"><!-- wp:woocommerce/filled-cart-block -->
<div class="wp-block-woocommerce-filled-cart-block"><!-- wp:woocommerce/cart-items-block -->
<div class="wp-block-woocommerce-cart-items-block"><!-- wp:woocommerce/cart-line-items-block -->
<div class="wp-block-woocommerce-cart-line-items-block"></div>
<!-- /wp:woocommerce/cart-line-items-block --></div>
<!-- /wp:woocommerce/cart-items-block -->

<!-- wp:woocommerce/cart-totals-block -->
<div class="wp-block-woocommerce-cart-totals-block"><!-- wp:woocommerce/cart-order-summary-block -->
<div class="wp-block-woocommerce-cart-order-summary-block"><!-- wp:woocommerce/cart-order-summary-heading-block -->
<div class="wp-block-woocommerce-cart-order-summary-heading-block"></div>
<!-- /wp:woocommerce/cart-order-summary-heading-block -->

<!-- wp:woocommerce/cart-order-summary-coupon-form-block -->
<div class="wp-block-woocommerce-cart-order-summary-coupon-form-block"></div>
<!-- /wp:woocommerce/cart-order-summary-coupon-form-block -->

<!-- wp:woocommerce/cart-order-summary-totals-block -->
<div class="wp-block-woocommerce-cart-order-summary-totals-block"><!-- wp:woocommerce/cart-order-summary-subtotal-block -->
<div class="wp-block-woocommerce-cart-order-summary-subtotal-block"></div>
<!-- /wp:woocommerce/cart-order-summary-subtotal-block -->

<!-- wp:woocommerce/cart-order-summary-fee-block -->
<div class="wp-block-woocommerce-cart-order-summary-fee-block"></div>
<!-- /wp:woocommerce/cart-order-summary-fee-block -->

<!-- wp:woocommerce/cart-order-summary-discount-block -->
<div class="wp-block-woocommerce-cart-order-summary-discount-block"></div>
<!-- /wp:woocommerce/cart-order-summary-discount-block -->

<!-- wp:woocommerce/cart-order-summary-shipping-block -->
<div class="wp-block-woocommerce-cart-order-summary-shipping-block"></div>
<!-- /wp:woocommerce/cart-order-summary-shipping-block -->

<!-- wp:woocommerce/cart-order-summary-taxes-block -->
<div class="wp-block-woocommerce-cart-order-summary-taxes-block"></div>
<!-- /wp:woocommerce/cart-order-summary-taxes-block --></div>
<!-- /wp:woocommerce/cart-order-summary-totals-block --></div>
<!-- /wp:woocommerce/cart-order-summary-block -->

<!-- wp:woocommerce/cart-express-payment-block -->
<div class="wp-block-woocommerce-cart-express-payment-block"></div>
<!-- /wp:woocommerce/cart-express-payment-block -->

<!-- wp:woocommerce/proceed-to-checkout-block -->
<div class="wp-block-woocommerce-proceed-to-checkout-block"></div>
<!-- /wp:woocommerce/proceed-to-checkout-block -->

<!-- wp:woocommerce/cart-accepted-payment-methods-block -->
<div class="wp-block-woocommerce-cart-accepted-payment-methods-block"></div>
<!-- /wp:woocommerce/cart-accepted-payment-methods-block --></div>
<!-- /wp:woocommerce/cart-totals-block --></div>
<!-- /wp:woocommerce/filled-cart-block -->

<!-- wp:woocommerce/empty-cart-block -->
<div class="wp-block-woocommerce-empty-cart-block"><!-- wp:heading {"textAlign":"center","className":"with-empty-cart-icon wc-block-cart__empty-cart__title"} -->
<h2 class="wp-block-heading has-text-align-center with-empty-cart-icon wc-block-cart__empty-cart__title"><?php esc_html_e('Your cart is currently empty!', 'upa25');?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center"><?php /* Translators: 1. is the start of a 'a' HTML element, 2. is the end of a 'a' HTML element */ 
echo sprintf( esc_html__( '%1$sBrowse store%2$s', 'upa25' ), '<a href="' . esc_url( 'http://localhost:10199/shop/store/' ) . '">', '</a>' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:separator {"className":"is-style-dots"} -->
<hr class="wp-block-separator has-alpha-channel-opacity is-style-dots"/>
<!-- /wp:separator -->

<!-- wp:heading {"textAlign":"center"} -->
<h2 class="wp-block-heading has-text-align-center"><?php esc_html_e('New in store', 'upa25');?></h2>
<!-- /wp:heading -->

<!-- wp:woocommerce/product-new {"columns":4,"rows":1} /--></div>
<!-- /wp:woocommerce/empty-cart-block --></div>
<!-- /wp:woocommerce/cart --></main>
<!-- /wp:group -->
<!-- /wp:woocommerce/page-content-wrapper -->

<!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->