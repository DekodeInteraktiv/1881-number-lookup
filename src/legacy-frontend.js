jQuery(function($) {
	const woo1881wrapper = $('.woo1881-lookup.legacy-checkout');
	if (!woo1881wrapper || !window.Woo1881) {
		return;
	}
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

				// TODO: If only 1 hit, then just autofill instead of autocomplete?
				if (autocompleteResults.length > 0) {
					createAutoomplete();
				}
			}
		});
	};

	const fillInInfoAndUpdate = (contactInfo) => {
		console.log('fill in this:', contactInfo);
		// TODO: Fill in. Ref discussion with Vincent.
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
	}, window.Woo1881.keyup_delay));
});
