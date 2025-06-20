/* global jQuery */

jQuery(function ($) {
	const dm1881wrapper = $('.dm1881-lookup.legacy-checkout');
	if (!dm1881wrapper || !window.DM1881) {
		return;
	}
	const checkoutForm = $('.woocommerce-checkout');
	const dm1881errorContainer = dm1881wrapper.find('.dm1881-no-results');
	let phoneNumber;
	let autocompleteResults = [];

	const phoneValidLengths = window.DM1881.valid_phone_lengths;

	// Helper method to "buffer" keyup event on input (aka don't try to send request on every single keystroke).
	const keyupDelay = (fn, ms) => {
		let timer = 0;
		return function (...args) {
			clearTimeout(timer);
			timer = setTimeout(fn.bind(this, ...args), ms || 0);
		};
	};

	// Delete autocomplete HTML, if exists.
	const clearAutocomplete = () => {
		const autocompleteContainer = dm1881wrapper.find('.dm1881-autocomplete-container');
		if (autocompleteContainer.length > 0) {
			autocompleteContainer.remove();
		}
	};

	// Generate HTML with autocomplete results.
	const createAutoomplete = () => {
		const autocompleteDOM = document.createElement('div');
		autocompleteDOM.classList.add('dm1881-autocomplete-container');
		for (let i = 0; i < autocompleteResults.length; i++) {
			const itemDOM = document.createElement('div');
			itemDOM.classList.add('dm1881-autocomplete-item');
			itemDOM.dataset.resultIndex = i;
			itemDOM.textContent = autocompleteResults[i].autocomplete_display;
			autocompleteDOM.appendChild(itemDOM);
		}
		dm1881wrapper.find('.dm1881-input-container').append(autocompleteDOM);
	};

	// Perform REST request to look up a phone number.
	const lookupPhoneNumber = () => {
		clearAutocomplete();
		autocompleteResults = [];
		fillInInfoAndUpdate({}, false);
		dm1881errorContainer.hide();

		const url = `${window.DM1881.phone_lookup_rest}?phone=${phoneNumber}`;
		fetch(url, {
			method: 'GET',
		})
			.then((response) => {
				return response.json();
			})
			.then((data) => {
				if (data.success && data.search_result.length > 0) {
					autocompleteResults = data.search_result;
					if (autocompleteResults.length === 1) {
						fillInInfoAndUpdate(autocompleteResults[0], true);
					} else {
						createAutoomplete();
					}
				} else if (data.search_result.length === 0) {
					dm1881errorContainer.show();
				}
			});
	};

	// Fill in all checkout inputs with values.
	const fillInInfoAndUpdate = (contactInfo, triggerValidate) => {
		// Billing.
		const inputBillingFirstName = checkoutForm.find('input[name="billing_first_name"]');
		if (inputBillingFirstName.length > 0) {
			inputBillingFirstName.val(contactInfo.first_name ?? '');
		}
		const inputBillingLastName = checkoutForm.find('input[name="billing_last_name"]');
		if (inputBillingLastName.length > 0) {
			inputBillingLastName.val(contactInfo.last_name ?? '');
		}
		const inputBillingAddress1 = checkoutForm.find('input[name="billing_address_1"]');
		if (inputBillingAddress1.length > 0) {
			inputBillingAddress1.val(
				contactInfo.billing_address && contactInfo.billing_address.street_address
					? contactInfo.billing_address.street_address
					: ''
			);
		}
		const inputBillingPostcode = checkoutForm.find('input[name="billing_postcode"]');
		if (inputBillingPostcode.length > 0) {
			inputBillingPostcode.val(
				contactInfo.billing_address && contactInfo.billing_address.zip ? contactInfo.billing_address.zip : ''
			);
		}
		const inputBillingCity = checkoutForm.find('input[name="billing_city"]');
		if (inputBillingCity.length > 0) {
			inputBillingCity.val(
				contactInfo.billing_address && contactInfo.billing_address.city ? contactInfo.billing_address.city : ''
			);
		}
		const inputBillingPhone = checkoutForm.find('input[name="billing_phone"]');
		if (inputBillingPhone.length > 0) {
			inputBillingPhone.val(phoneNumber);
		}
		const inputBillingEmail = checkoutForm.find('input[name="billing_email"]');
		if (inputBillingEmail.length > 0) {
			inputBillingEmail.val(contactInfo.email ?? '');
		}

		// Shipping.
		const inputShippingFirstName = checkoutForm.find('input[name="shipping_first_name"]');
		if (inputShippingFirstName.length > 0) {
			inputShippingFirstName.val(contactInfo.first_name ?? '');
		}
		const inputShippingLastName = checkoutForm.find('input[name="shipping_last_name"]');
		if (inputShippingLastName.length > 0) {
			inputShippingLastName.val(contactInfo.last_name ?? '');
		}
		const inputShippingAddress1 = checkoutForm.find('input[name="shipping_address_1"]');
		if (inputShippingAddress1.length > 0) {
			inputShippingAddress1.val(
				contactInfo.shipping_address && contactInfo.shipping_address.street_address
					? contactInfo.shipping_address.street_address
					: ''
			);
		}
		const inputShippingPostcode = checkoutForm.find('input[name="shipping_postcode"]');
		if (inputShippingPostcode.length > 0) {
			inputShippingPostcode.val(
				contactInfo.shipping_address && contactInfo.shipping_address.zip ? contactInfo.shipping_address.zip : ''
			);
		}
		const inputShippingCity = checkoutForm.find('input[name="shipping_city"]');
		if (inputShippingCity.length > 0) {
			inputShippingCity.val(
				contactInfo.shipping_address && contactInfo.shipping_address.city
					? contactInfo.shipping_address.city
					: ''
			);
		}

		// Company.
		if (contactInfo.type === 'Company') {
			const inputBillingCompany = checkoutForm.find('input[name="billing_company"]');
			if (inputBillingCompany.length > 0) {
				inputBillingCompany.val(contactInfo.company_name ?? '');
			}
			const inputShippingCompany = checkoutForm.find('input[name="shipping_company"]');
			if (inputShippingCompany.length > 0) {
				inputShippingCompany.val(contactInfo.company_name ?? '');
			}
		}

		// Trigger checkout updated event.
		$(document.body).trigger('update_checkout');

		if (triggerValidate) {
			// If 1881 did not provide some of the required data (e.g. hidden address),
			// then visually show user that fields must be filled in by triggering classes.
			const requiredEmptyFields = document.querySelectorAll('.form-row.validate-required');
			requiredEmptyFields.forEach((requiredField) => {
				const inputField = requiredField.querySelector('input[type="text"]', 'input[type="email"]');
				if (inputField && inputField.value === '') {
					requiredField.classList.add('woocommerce-invalid');
					requiredField.classList.add('woocommerce-invalid-required-field');
				}
			});
		}
	};

	$(document).on('click', '.dm1881-autocomplete-item', function () {
		const hitIndex = parseInt($(this).data('resultIndex'));
		if (typeof autocompleteResults[hitIndex] !== 'undefined') {
			fillInInfoAndUpdate(autocompleteResults[hitIndex], true);
			clearAutocomplete(); // Remove autocomplete ("close" dropdown).
		}
	});

	// Detect keyup on phone number input with delay, perform search if valid length.
	$(document).on(
		'keyup',
		'#dm1881-phone-lookup',
		keyupDelay(function () {
			phoneNumber = $(this).val().replace(/\D/g, ''); // Ensure numbers only.
			if (phoneValidLengths.includes(phoneNumber.length)) {
				lookupPhoneNumber();
			} else {
				clearAutocomplete(); // Remove autocomplete ("close" dropdown).
				dm1881errorContainer.hide();
			}
		}, window.DM1881.keyup_delay_ms)
	);
});
