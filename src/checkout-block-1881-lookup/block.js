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
import ErrorIcon from './erroricon';

const { registerCheckoutBlock } = wc.blocksCheckout;

import './view.css';

const Block = () => {
	const [phone, setPhone] = useState('');
	const [debouncedPhone, setDebouncedPhone] = useState(''); // eslint-disable-line
	const [optionsData, setOptionsData] = useState([]);
	const [autocompleteVisible, setAutocompleteVisible] = useState(false);
	const [delayedValidation, setDelayedValidation] = useState(false);
	const [showNoResultsMessage, setShowNoResultsMessage] = useState(false);

	// Block settings.
	const keyUpDelayTime = window.wcSettings['checkout-block-1881-lookup_data'].keyup_delay_ms;
	const phoneValidLengths = window.wcSettings['checkout-block-1881-lookup_data'].valid_phone_lengths;
	const paragraphText = window.wcSettings['checkout-block-1881-lookup_data'].description_text;
	const inputLabel = window.wcSettings['checkout-block-1881-lookup_data'].lookup_label;
	const logo1881 = window.wcSettings['checkout-block-1881-lookup_data'].logo_1881_svg;
	const noResultsText = window.wcSettings['checkout-block-1881-lookup_data'].no_results_text;

	// Debounce input ("buffer", aka don't trigger request on every keystroke).
	useEffect(() => {
		const delayInputTimeoutId = setTimeout(() => {
			setDebouncedPhone(phone);
			if (phoneValidLengths.includes(phone.length)) {
				lookupPhoneNumber();
			}
		}, keyUpDelayTime);
		return () => clearTimeout(delayInputTimeoutId);
	}, [phone, keyUpDelayTime]); // eslint-disable-line react-hooks/exhaustive-deps

	const { CART_STORE_KEY } = window.wc.wcBlocksData;
	const { setBillingAddress, setShippingAddress } = dispatch(CART_STORE_KEY);

	const { VALIDATION_STORE_KEY } = window.wc.wcBlocksData;
	const { showAllValidationErrors } = dispatch(VALIDATION_STORE_KEY);

	const setAddresses = (contactInfo) => {
		if (contactInfo) {
			const shippingAddress = {
				first_name: contactInfo.first_name ?? '',
				last_name: contactInfo.last_name ?? '',
				address_1: contactInfo.shipping_address.street_address ?? '',
				city: contactInfo.shipping_address.city ?? '',
				postcode: contactInfo.shipping_address.zip ?? '',
				email: contactInfo.email ?? '',
				phone,
			};
			if (contactInfo.type === 'Company') {
				shippingAddress.company = contactInfo.company_name ?? '';
			}
			const billingAddress = {
				first_name: contactInfo.first_name ?? '',
				last_name: contactInfo.last_name ?? '',
				address_1: contactInfo.billing_address.street_address ?? '',
				city: contactInfo.billing_address.city ?? '',
				postcode: contactInfo.billing_address.zip ?? '',
				phone,
			};
			if (contactInfo.type === 'Company') {
				billingAddress.company = contactInfo.company_name ?? '';
			}

			// Update Woo addresses.
			setShippingAddress(shippingAddress);
			setBillingAddress(billingAddress);

			// Update state to trigger a delayed validation check.
			setDelayedValidation(true);
		}
	};

	const resetCheckoutFields = () => {
		const emptyAddress = {
			first_name: '',
			last_name: '',
			address_1: '',
			city: '',
			postcode: '',
			email: '',
			phone: '',
			company: '',
		};
		setShippingAddress(emptyAddress);
		setBillingAddress(emptyAddress);
	};

	const clickedAutocompleteItem = (index) => {
		setAddresses(optionsData[index]);
		setAutocompleteVisible(false);
	};

	const lookupPhoneNumber = () => {
		setAutocompleteVisible(false);
		resetCheckoutFields();
		setShowNoResultsMessage(false);

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
				} else if (data.search_result.length === 0) {
					setShowNoResultsMessage(true);
				}
			});
	};

	const inputChangeEvent = (e) => {
		const formattedPhone = e.target.value.replace(/\D/g, '');
		setPhone(formattedPhone);
		setAutocompleteVisible(false);
		setShowNoResultsMessage(false);
	};

	/***
	 * Trigger validation errors if any, after autofilling address from 1881.
	 * Unfortunately at the time of making this, there is no reliable hook or event for when the addresses was indeed changed.
	 * Woo does call dispatchCheckoutEvent() but it is not quite working. So only way atm is to use a timeout.
	 */
	useEffect(() => {
		if (delayedValidation) {
			setTimeout(() => {
				showAllValidationErrors();
			}, 500);
		}
		setDelayedValidation(false);
	}, [delayedValidation, showAllValidationErrors]);

	let inputContainerClasses = 'dm1881-input-container wc-block-components-text-input';
	if (phone.length > 0) {
		inputContainerClasses += ' is-active';
	}

	return (
		<div className="dm1881-lookup block-checkout" id="dm1881-lookup">
			<p className="dm1881-description">{paragraphText}</p>
			<div className="dm1881-logo-input-container">
				<div className="dm1881-logo" dangerouslySetInnerHTML={{ __html: logo1881 }}></div>
				<div className={inputContainerClasses}>
					<label htmlFor="dm1881-phone-lookup">{inputLabel}</label>
					<input
						type="tel"
						onChange={inputChangeEvent}
						value={phone}
						id="dm1881-phone-lookup"
						className="dm1881-lookup-input"
						autoCapitalize="characters"
						autoComplete="tel"
						aria-label={inputLabel}
						aria-invalid="false"
					/>
					{showNoResultsMessage && (
						<div className="wc-block-components-validation-error" role="alert">
							<p>
								<ErrorIcon />
								<span>{noResultsText}</span>
							</p>
						</div>
					)}
					{autocompleteVisible && (
						<div className="dm1881-autocomplete-container">
							{optionsData.map((x, index) => (
								<div // eslint-disable-line
									className="dm1881-autocomplete-item"
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
		</div>
	);
};

const options = {
	metadata,
	component: Block,
};

registerCheckoutBlock(options);
