<?php
/**
 * Title: component-avatar-stack-02
 * Slug: upa25/component-avatar-stack-02
 * Categories: upa25/components
 * Viewport width: 320
 */
?>
<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|10"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group">
	<!-- wp:group {"style":{"spacing":{"blockGap":"0"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
	<div class="wp-block-group">
		<!-- wp:image {"lightbox":{"enabled":false},"width":"28px","sizeSlug":"full","linkDestination":"none","style":{"border":{"radius":"999px","width":"1px"}},"borderColor":"base"} -->
		<figure class="wp-block-image size-full is-resized has-custom-border">
			<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/placeholders/placeholder-avatar-1.webp" alt="" class="has-border-color has-base-border-color" style="border-width:1px;border-radius:999px;width:28px"/>
		</figure>
		<!-- /wp:image -->

		<!-- wp:image {"lightbox":{"enabled":false},"width":"28px","sizeSlug":"full","linkDestination":"none","style":{"border":{"radius":"999px","width":"1px"},"spacing":{"margin":{"left":"-12px"}}},"borderColor":"base"} -->
		<figure class="wp-block-image size-full is-resized has-custom-border" style="margin-left:-12px">
			<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/placeholders/placeholder-avatar-2.webp" alt="" class="has-border-color has-base-border-color" style="border-width:1px;border-radius:999px;width:28px"/>
		</figure>
		<!-- /wp:image -->

		<!-- wp:image {"lightbox":{"enabled":false},"width":"28px","sizeSlug":"full","linkDestination":"none","style":{"border":{"radius":"999px","width":"1px"},"spacing":{"margin":{"left":"-12px"}}},"borderColor":"base"} -->
		<figure class="wp-block-image size-full is-resized has-custom-border" style="margin-left:-12px">
			<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/placeholders/placeholder-avatar-3.webp" alt="" class="has-border-color has-base-border-color" style="border-width:1px;border-radius:999px;width:28px"/>
		</figure>
		<!-- /wp:image -->

		<!-- wp:image {"lightbox":{"enabled":false},"width":"28px","sizeSlug":"large","linkDestination":"none","style":{"border":{"radius":"999px","width":"1px"},"spacing":{"margin":{"left":"-12px"}}},"borderColor":"base"} -->
		<figure class="wp-block-image size-large is-resized has-custom-border" style="margin-left:-12px">
			<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/placeholders/placeholder-avatar-4.webp" alt="" class="has-border-color has-base-border-color" style="border-width:1px;border-radius:999px;width:28px"/>
		</figure>
		<!-- /wp:image -->
	</div>
	<!-- /wp:group -->

	<!-- wp:paragraph -->
	<p><?php echo esc_html__( '1000+ satisfied customers', 'upa25' ); ?></p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
