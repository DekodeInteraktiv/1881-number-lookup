/* global wc */
/**
 * WordPress dependencies
 */
import { dispatch } from '@wordpress/data';

/**
 * External dependencies
 */
import { useState, useEffect } from 'react';

/**
 * Internal dependencies
 */
import metadata from './block.json';

const { registerCheckoutBlock } = wc.blocksCheckout;

import './view.css';

const Block = () => {
	const [phone, setPhone] = useState('');
	const [debouncedPhone, setDebouncedPhone] = useState(''); // eslint-disable-line
	const [optionsData, setOptionsData] = useState([]);
	const [autocompleteVisible, setAutocompleteVisible] = useState(false);

	// Block settings.
	const keyUpDelayTime = window.wcSettings['checkout-block-1881-lookup_data'].keyup_delay_ms ?? 500;
	const paragraphText = window.wcSettings['checkout-block-1881-lookup_data'].description_text;
	const inputLabel = window.wcSettings['checkout-block-1881-lookup_data'].lookup_label;

	// Debounce input ("buffer", aka don't trigger request on every keystroke).
	useEffect(() => {
		const delayInputTimeoutId = setTimeout(() => {
			setDebouncedPhone(phone);
			if (phone.length >= 8) {
				lookupPhoneNumber();
			}
		}, keyUpDelayTime);
		return () => clearTimeout(delayInputTimeoutId);
	}, [phone, keyUpDelayTime]); // eslint-disable-line react-hooks/exhaustive-deps

	const { CART_STORE_KEY } = window.wc.wcBlocksData;
	const { setBillingAddress, setShippingAddress } = dispatch(CART_STORE_KEY);

	const setAddresses = (contactInfo) => {
		if (contactInfo) {
			const shippingAddress = {
				first_name: contactInfo.first_name,
				last_name: contactInfo.last_name,
				address_1: contactInfo.shipping_address.street_address,
				city: contactInfo.shipping_address.city,
				postcode: contactInfo.shipping_address.zip,
				email: contactInfo.email,
				phone,
			};
			if (contactInfo.type === 'Company') {
				shippingAddress.company = contactInfo.company_name;
			}
			const billingAddress = {
				first_name: contactInfo.first_name,
				last_name: contactInfo.last_name,
				address_1: contactInfo.billing_address.street_address,
				city: contactInfo.billing_address.city,
				postcode: contactInfo.billing_address.zip,
				phone,
			};
			if (contactInfo.type === 'Company') {
				billingAddress.company = contactInfo.company_name;
			}

			// Update Woo addresses.
			setShippingAddress(shippingAddress);
			setBillingAddress(billingAddress);
		}
	};

	const clickedAutocompleteItem = (index) => {
		setAddresses(optionsData[index]);
		setAutocompleteVisible(false);
	};

	const lookupPhoneNumber = () => {
		setAutocompleteVisible(false);

		const url = `${window.wcSettings['checkout-block-1881-lookup_data'].phone_lookup_rest}?phone=${phone}`;
		fetch(url, {
			method: 'GET',
		})
			.then((response) => {
				return response.json();
			})
			.then((data) => {
				if (data.success && data.search_result.length > 0) {
					setOptionsData(data.search_result);
					if (data.search_result.length === 1) {
						setAddresses(data.search_result[0]);
					} else {
						setAutocompleteVisible(true);
					}
				}
			});
	};

	const inputChangeEvent = (e) => {
		const formattedPhone = e.target.value.replace(/\D/g, '');
		setPhone(formattedPhone);
		setAutocompleteVisible(false);
	};

	let inputContainerClasses = 'woo1881-input-container wc-block-components-text-input';
	if (phone.length > 0) {
		inputContainerClasses += ' is-active';
	}

	return (
		<div className="woo1881-lookup block-checkout" id="woo1881-lookup">
			<p className="woo1881-description">{paragraphText}</p>
			<div className={inputContainerClasses}>
				<label htmlFor="woo1881-phone-lookup">{inputLabel}</label>
				<input
					type="tel"
					onChange={inputChangeEvent}
					value={phone}
					id="woo1881-phone-lookup"
					className="woo1881-lookup-input"
					autoCapitalize="characters"
					autoComplete="tel"
					aria-label={inputLabel}
					aria-invalid="false"
				/>
				{autocompleteVisible && (
					<div className="woo1881-autocomplete-container">
						{optionsData.map((x, index) => (
							<div // eslint-disable-line
								className="woo1881-autocomplete-item"
								onClick={() => clickedAutocompleteItem(index)}
								key={index}
							>
								{x.autocomplete_display}
							</div>
						))}
					</div>
				)}
			</div>
		</div>
	);
};

const options = {
	metadata,
	component: Block,
};

registerCheckoutBlock(options);
