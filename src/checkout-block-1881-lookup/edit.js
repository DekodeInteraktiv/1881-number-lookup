/**
 * WordPress dependencies
 */
import { useBlockProps } from '@wordpress/block-editor';

export const Edit = () => {
	const blockProps = useBlockProps();
	const paragraphText = window.wcSettings['checkout-block-1881-lookup_data'].description_text;
	const inputLabel = window.wcSettings['checkout-block-1881-lookup_data'].lookup_label;
	const logo1881 = window.wcSettings['checkout-block-1881-lookup_data'].logo_1881_svg;

	// Note: Woo blocks in editor are imported as iframe and thus importing CSS does not work. Therefore inline CSS.

	return (
		<div {...blockProps}>
			<div className="dm1881-lookup block-checkout" id="dm1881-lookup">
				<p className="dm1881-description">{paragraphText}</p>
				<div
					className="dm1881-logo-input-container"
					style={{
						alignItems: 'center',
						display: 'flex',
						flexDirection: 'row',
						gap: '16px',
					}}
				>
					<div
						className="dm1881-logo"
						dangerouslySetInnerHTML={{ __html: logo1881 }}
						style={{
							height: '32px',
							width: '32px',
						}}
					></div>
					<div
						className="dm1881-input-container wc-block-components-text-input"
						style={{
							flex: '1',
							marginTop: '0',
						}}
					>
						<label htmlFor="dm1881-phone-lookup">{inputLabel}</label>
						<input
							type="tel"
							value=""
							id="dm1881-phone-lookup"
							className="dm1881-lookup-input"
							autoCapitalize="characters"
							autoComplete="tel"
							aria-label={inputLabel}
							aria-invalid="false"
							disabled="disabled"
						/>
					</div>
				</div>
			</div>
		</div>
	);
};
