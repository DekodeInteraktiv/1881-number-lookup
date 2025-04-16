/**
 * WordPress dependencies
 */
import { tag as icon } from '@wordpress/icons';
import { registerBlockType } from '@wordpress/blocks';

/**
 * Internal dependencies
 */
import edit from './edit';
import metadata from './block.json';

// import './style.css';

registerBlockType(metadata.name, {
	icon,
	edit,
});

/**
 * External dependencies
 * /
import { registerBlockType } from '@wordpress/blocks';
import { Icon, box } from '@wordpress/icons';

/**
 * Internal dependencies
 * /
import { Edit, Save } from './edit';
import metadata from './block.json';

registerBlockType( metadata, {
	icon: {
		src: <Icon icon={ box } />,
	},
	edit: Edit,
	save: Save,
} );
*/
