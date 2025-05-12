/** Register Block Styles
 * --------------------------------------------- */
import { registerBlockStyle } from '@wordpress/blocks';

const blockStyles = [
	{
		block: 'core/button',
		styles: [
			{ name: 'brand', label: 'Brand' },
			{ name: 'base', label: 'Base' },
		],
	},
	{
		block: 'core/details',
		styles: [ { name: 'chevron', label: 'Chevron' } ],
	},
	{
		block: 'core/gallery',
		styles: [ { name: 'scale-effect', label: 'Scale Effect' } ],
	},
	{
		block: 'core/group',
		styles: [ { name: 'spotlight', label: 'Spotlight' } ],
	},
	{
		block: 'core/cover',
		styles: [ { name: 'blurred', label: 'Blurred' } ],
	},
	{
		block: 'core/image',
		styles: [ { name: 'picture-frame', label: 'Picture Frame' } ],
	},
	{
		block: 'core/list',
		styles: [
			{ name: 'checkmark', label: 'Checkmark' },
			{ name: 'crossmark', label: 'Crossmark' },
			{ name: 'crossmark-2', label: 'Crossmark 2 Red' },
			{ name: 'checkmark-2', label: 'Checkmark 2 Green' },
		],
	},
	{
		block: 'core/paragraph',
		styles: [
			{ name: 'indicator', label: 'Indicator' },
			{ name: 'overline', label: 'Overline' },
			{ name: 'checkmark', label: 'Checkmark' },
		],
	},
];

blockStyles.forEach( ( { block, styles } ) => {
	styles.forEach( ( { name, label } ) => {
		wp.blocks.registerBlockStyle( block, { name, label } );
	} );
} );
