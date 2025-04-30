import { useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

import './view.css';

export const Edit = ({ attributes, setAttributes }) => {
	const blockProps = useBlockProps();
	const paragraphText = window.wcSettings['checkout-block-1881-lookup_data'].description_text;
	const inputLabel = window.wcSettings['checkout-block-1881-lookup_data'].lookup_label;

	return (
		<div {...blockProps}>
			<div className="woo1881-lookup block-checkout" id="woo1881-lookup">
				<p className="woo1881-description">{paragraphText}</p>
				<div className="woo1881-input-container wc-block-components-text-input">
					<label htmlFor="woo1881-phone-lookup">{inputLabel}</label>
					<input
						type="tel"
						value=""
						id="woo1881-phone-lookup"
						className="woo1881-lookup-input"
						autocapitalize="characters"
						autocomplete="tel"
						aria-label={inputLabel}
						aria-invalid="false"
						disabled="disabled"
					/>
				</div>
			</div>
		</div>
	);
};
