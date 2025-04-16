/**
 * WordPress dependencies
 */
import { useBlockProps } from '@wordpress/block-editor';

const Edit = () => {
	const blockProps = useBlockProps();

	return (
		<div {...blockProps}>
			Heyoo
		</div>
	);
};

export default Edit;

/**
 * External dependencies
 * /
import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	RichText,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, SelectControl, Disabled } from '@wordpress/components';
import { getSetting } from '@woocommerce/settings';

/**
 * Internal dependencies
 * /
// import './style.scss';
// import { options } from './options';

// const { defaultShippingText } = getSetting( 'shipping-workshop_data', '' );

export const Edit = ( { attributes, setAttributes } ) => {
	const { description } = attributes;
	const blockProps = useBlockProps();

	const defaultDescription = __('Default loool', 'woo1881');

	return (
		<div { ...blockProps } style={ { display: 'block' } }>
			<InspectorControls>
				<PanelBody title={ __( 'Block options', 'shipping-workshop' ) }>
					Options for the block go here.
				</PanelBody>
			</InspectorControls>
			<div>
				<RichText
					value={ description || __('Heyo', 'woo1881') }
					onChange={ ( value ) => setAttributes( { description: value } ) }
				/>
			</div>
		</div>
	);
};

export const Save = ( { attributes } ) => {
	const { description } = attributes;
	return (
		<div { ...useBlockProps.save() }>
			<RichText.Content value={ description } />
		</div>
	);
};
*/