/**
 * Editor controls for Accordion block FAQ Schema.
 *
 * Adds a toggle to enable/disable FAQ schema.org microdata on accordion blocks.
 *
 * @package upa25
 */

import { addFilter } from '@wordpress/hooks';
import { createHigherOrderComponent } from '@wordpress/compose';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

/**
 * Add custom attributes to the accordion block.
 *
 * @param {Object} settings Block settings.
 * @param {string} name     Block name.
 * @return {Object} Modified settings.
 */
function addFaqAttributes( settings, name ) {
	if ( name !== 'core/accordion' ) {
		return settings;
	}

	return {
		...settings,
		attributes: {
			...settings.attributes,
			isFaqs: {
				type: 'boolean',
				default: false,
			},
		},
	};
}

addFilter(
	'blocks.registerBlockType',
	'upa25/accordion-faq-attributes',
	addFaqAttributes
);

/**
 * Add FAQ toggle control to the accordion block inspector.
 */
const withFaqControls = createHigherOrderComponent( ( BlockEdit ) => {
	return ( props ) => {
		if ( props.name !== 'core/accordion' ) {
			return <BlockEdit { ...props } />;
		}

		const { attributes, setAttributes } = props;
		const { isFaqs, className } = attributes;

		/**
		 * Toggle the FAQ schema functionality.
		 *
		 * @param {boolean} value New toggle value.
		 */
		const toggleFaqs = ( value ) => {
			// Update the isFaqs attribute
			setAttributes( { isFaqs: value } );

			// Update the className to include/exclude is-faqs
			const classes = className ? className.split( ' ' ) : [];
			const filteredClasses = classes.filter( ( c ) => c !== 'is-faqs' );

			if ( value ) {
				filteredClasses.push( 'is-faqs' );
			}

			setAttributes( {
				className: filteredClasses.join( ' ' ).trim() || undefined,
			} );
		};

		// Sync isFaqs attribute with className on initial load
		const hasFaqClass = className?.includes( 'is-faqs' );
		if ( hasFaqClass !== isFaqs ) {
			// Defer to avoid state update during render
			setTimeout( () => {
				setAttributes( { isFaqs: hasFaqClass } );
			}, 0 );
		}

		return (
			<>
				<BlockEdit { ...props } />
				<InspectorControls>
					<PanelBody
						title={ __( 'FAQ Schema', 'upa25' ) }
						initialOpen={ true }
					>
						<ToggleControl
							__nextHasNoMarginBottom
							label={ __( 'Enable FAQ Schema', 'upa25' ) }
							help={ __(
								'Adds schema.org FAQPage microdata for better SEO. Use this when the accordion contains frequently asked questions.',
								'upa25'
							) }
							checked={ isFaqs }
							onChange={ toggleFaqs }
						/>
					</PanelBody>
				</InspectorControls>
			</>
		);
	};
}, 'withFaqControls' );

addFilter(
	'editor.BlockEdit',
	'upa25/accordion-faq-controls',
	withFaqControls
);
