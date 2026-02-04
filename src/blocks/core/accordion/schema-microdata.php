<?php
/**
 * Schema.org microdata for Accordion block (FAQs).
 *
 * TODO: Update description, make it clearer
 * Adds schema.org microdata attributes to Accordion blocks when activated
 *
 * @package upa25
 * @version 0.1.0
 * @link https://developer.wordpress.org/news/snippets/schema-org-microdata-for-accordion-block-faqs/
 */




add_filter( 'render_block_core/accordion', 'projectslug_render_accordion_faqs' );

function projectslug_render_accordion_faqs( $content ): string
{
	$processor = new WP_HTML_Tag_Processor( $content );

	// Bail early if there's no Accordion block with the `.is-faqs` class.
	if (
		! $processor->next_tag( [ 'class_name' => 'wp-block-accordion' ] )
		|| ! $processor->has_class( 'is-faqs' )
	) {
		return $processor->get_updated_html();
	}

	// Add attributes to wrapping accordion block.
	$processor->set_attribute( 'itemscope', true );
	$processor->set_attribute( 'itemtype', 'https://schema.org/FAQPage' );

	// Loop through accordion items and add attributes.
	while ( $processor->next_tag( [ 'class_name' => 'wp-block-accordion-item' ] ) ) {
		$processor->set_attribute( 'itemscope', true );
		$processor->set_attribute( 'itemprop', 'mainEntity' );
		$processor->set_attribute( 'itemtype', 'https://schema.org/Question' );

		// Add attributes to the title element.
		if ( $processor->next_tag( [ 'class_name' => 'wp-block-accordion-heading__toggle-title' ] ) ) {
			$processor->set_attribute( 'itemprop', 'name' );
		}

		// Add attributes to the panel.
		if ( $processor->next_tag( [ 'class_name' => 'wp-block-accordion-panel' ] ) ) {
			$processor->set_attribute( 'itemscope', true );
			$processor->set_attribute( 'itemprop', 'acceptedAnswer' );
			$processor->set_attribute( 'itemtype', 'https://schema.org/Answer' );

			// Add attribute to first paragraph.
			if ( $processor->next_tag( 'p' ) ) {
				$processor->set_attribute( 'itemprop', 'text' );
			}
		}
	}

	return $processor->get_updated_html();
}
