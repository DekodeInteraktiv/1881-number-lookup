jQuery(function($) {
	const woo1881wrapper = $('.woo1881-lookup.legacy-checkout');
	if (!woo1881wrapper || !window.Woo1881) {
		return;
	}
	const checkoutForm = $('.woocommerce-checkout');
	let phoneNumber;
	let autocompleteResults = [];

	// Helper method to "buffer" keyup event on input (aka don't try to send request on every single keystroke).
	const keyupDelay = (fn, ms) => {
		let timer = 0
		return function(...args) {
			clearTimeout(timer)
			timer = setTimeout(fn.bind(this, ...args), ms || 0)
		}
	};

	// Delete autocomplete HTML, if exists.
	const clearAutocomplete = () => {
		const autocompleteContainer = woo1881wrapper.find('.woo1881-autocomplete-container');
		if ( autocompleteContainer.length > 0 ) {
			autocompleteContainer.remove();
		}
	};

	// Generate HTML with autocomplete results.
	const createAutoomplete = () => {
		const autocompleteDOM = document.createElement('div');
		autocompleteDOM.classList.add('woo1881-autocomplete-container');
		for (let i = 0; i < autocompleteResults.length; i++) {
			const itemDOM = document.createElement('div');
			itemDOM.classList.add('woo1881-autocomplete-item');
			itemDOM.dataset.resultIndex = i;
			itemDOM.textContent = autocompleteResults[i].autocomplete_display;
			autocompleteDOM.appendChild(itemDOM);
		}
		woo1881wrapper.find('.woo1881-input-container').append(autocompleteDOM);
	};

	// Perform REST request to look up a phone number.
	const lookupPhoneNumber = () => {
		clearAutocomplete();
		autocompleteResults = [];

		const url = `${window.Woo1881.phone_lookup_rest}?phone=${phoneNumber}`;
		fetch(url, {
			method: 'GET',
		}).then((response) => {
			return response.json();
		}).then((data) => {
			if (data.success && data.search_result.length > 0) {
				autocompleteResults = data.search_result;
				if (autocompleteResults.length == 1) {
					fillInInfoAndUpdate(autocompleteResults[0]);
				} else {
					createAutoomplete();
				}
			}
		});
	};

	// Fill in all checkout inputs with values.
	const fillInInfoAndUpdate = (contactInfo) => {
		// Billing.
		const inputBillingFirstName = checkoutForm.find('input[name="billing_first_name"]');
		if (inputBillingFirstName.length > 0) {
			inputBillingFirstName.val(contactInfo.first_name);
		}
		const inputBillingLastName = checkoutForm.find('input[name="billing_last_name"]');
		if (inputBillingLastName.length > 0) {
			inputBillingLastName.val(contactInfo.last_name);
		}
		const inputBillingAddress1 = checkoutForm.find('input[name="billing_address_1"]');
		if (inputBillingAddress1.length > 0) {
			inputBillingAddress1.val(contactInfo.billing_address.street_address);
		}
		const inputBillingPostcode = checkoutForm.find('input[name="billing_postcode"]');
		if (inputBillingPostcode.length > 0) {
			inputBillingPostcode.val(contactInfo.billing_address.zip);
		}
		const inputBillingCity = checkoutForm.find('input[name="billing_city"]');
		if (inputBillingCity.length > 0) {
			inputBillingCity.val(contactInfo.billing_address.city);
		}
		const inputBillingPhone = checkoutForm.find('input[name="billing_phone"]');
		if (inputBillingPhone.length > 0) {
			inputBillingPhone.val(phoneNumber);
		}
		const inputBillingEmail = checkoutForm.find('input[name="billing_email"]');
		if (inputBillingEmail.length > 0) {
			inputBillingEmail.val(contactInfo.email);
		}

		// Shipping.
		const inputShippingFirstName = checkoutForm.find('input[name="shipping_first_name"]');
		if (inputShippingFirstName.length > 0) {
			inputShippingFirstName.val(contactInfo.first_name);
		}
		const inputShippingLastName = checkoutForm.find('input[name="shipping_last_name"]');
		if (inputShippingLastName.length > 0) {
			inputShippingLastName.val(contactInfo.last_name);
		}
		const inputShippingAddress1 = checkoutForm.find('input[name="shipping_address_1"]');
		if (inputShippingAddress1.length > 0) {
			inputShippingAddress1.val(contactInfo.shipping_address.street_address);
		}
		const inputShippingPostcode = checkoutForm.find('input[name="shipping_postcode"]');
		if (inputShippingPostcode.length > 0) {
			inputShippingPostcode.val(contactInfo.shipping_address.zip);
		}
		const inputShippingCity = checkoutForm.find('input[name="shipping_city"]');
		if (inputShippingCity.length > 0) {
			inputShippingCity.val(contactInfo.shipping_address.city);
		}

		// Company.
		if (contactInfo.type == 'Company') {
			const inputBillingCompany = checkoutForm.find('input[name="billing_company"]');
			if (inputBillingCompany.length > 0) {
				inputBillingCompany.val(contactInfo.company_name);
			}
			const inputShippingCompany = checkoutForm.find('input[name="shipping_company"]');
			if (inputShippingCompany.length > 0) {
				inputShippingCompany.val(contactInfo.company_name);
			}
		}

		// Trigger checkout updated event.
		$(document.body).trigger('update_checkout');
	};

	$(document).on('click', '.woo1881-autocomplete-item', function() {
		const hitIndex = parseInt($(this).data('resultIndex'));
		if (typeof autocompleteResults[hitIndex] !== 'undefined') {
			fillInInfoAndUpdate(autocompleteResults[hitIndex]);
			clearAutocomplete();  // Remove autocomplete ("close" dropdown).
		}
	});

	// Detect keyup on phone number input with delay, perform search if minimum 8 numbers.
	$(document).on('keyup', '#woo1881-phone-lookup', keyupDelay(function() {
		phoneNumber = $(this).val().replace(/\D/g, '');  // Ensure numbers only.
		if (phoneNumber.length >= 8) {
			lookupPhoneNumber();
		}
	}, window.Woo1881.keyup_delay_ms));
});
