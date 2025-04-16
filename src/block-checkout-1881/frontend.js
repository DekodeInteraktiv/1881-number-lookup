/**
 * Internal dependencies
 */
import '../frontend.css';
import { useSelect } from '@wordpress/data';

// TODO: Add class 'is-active' to container div when input has some value (to fix label).

//import { registerCheckoutFilters } from '@woocommerce/blocks-checkout';  // does not exist, can't resolve...

/*
const goddamnit = () => {
	console.log('???');

	const { CHECKOUT_STORE_KEY } = window.wc.wcBlocksData;
	console.log(CHECKOUT_STORE_KEY);

	// UseSelect does not work outside React component...!
	const checkoutStore = useSelect( ( select ) => select( CHECKOUT_STORE_KEY ));
	console.log(checkoutStore);
}*/

jQuery(function($) {
	if ('undefined' === typeof wc || ! wc.blocksCheckout ) {
		return;
	}

	//const checkoutStore = select(CHECKOUT_STORE_KEY);
	//goddamnit();  // Does not work outside React component.

	//console.log(wc);
	//console.log(wc?.blocksCheckout);

	/*
	const { extensionCartUpdate } = wc.blocksCheckout;
	//const { setExtensionData } = wc.blocksCheckout.checkoutExtensionData;  // Does not exist

	console.log(extensionCartUpdate);
	//console.log(setExtensionData);
	*/

	/*console.log('wcBlocksData');
	console.log(wc.wcBlocksData);*/

	/*const { checkoutStore } = wc.wcBlocksData;
	console.log(checkoutStore);
	//const customerId = checkoutStore.getCustomerId();  // Does not work.
	*/

	// Listener to action (see if it triggers).
	wp.hooks.addAction(
		'experimental__woocommerce_blocks-checkout-set-billing-address',
		'plugin/loool',
		() => {
			console.log('The billing address was changed!');
		}
	);

	$(document).on('click', '#woo1881-lookup', function() {
		console.log('clicked woo1881');

		// Does not work, there's something in block that restores the previous saved value, even though I replace the value!
		$('#shipping-first_name').attr('value', 'Heeyo').val('Heeyo').parent('div').addClass('is-active');

		$('#shipping-postcode').attr('value', '1000').val('1000').parent('div').addClass('is-active');

		console.log('triggering action for set billing');
		wp.hooks.doAction('experimental__woocommerce_blocks-checkout-set-billing-address');

		/*
		$('.wc-block-components-order-summary__content').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});

		$(this).attr( 'disabled', true );

		// This works! See block-checkout.php: woocommerce_blocks_loaded action!
		// However, this is updating CART - and I need to trigger update to the contact info (to update address, name etc.)
		extensionCartUpdate(
			{
				namespace: 'test_with_cart_update',
				data: {
					phone: '12345678',
				},
			}
		)
		.then(() => {
			console.log('hide button button maybe/loading spinner');
		})
		.finally(() => {
			$('.wc-block-components-order-summary__content').unblock();
			console.log('final result');
		});*/
	});
});

/**
 * WordPress dependencies
 * /
import domReady from '@wordpress/dom-ready';

jQuery(function($) {
	if ('undefined' === typeof wc || !wc.blocksCheckout ) {
		return;
	}

	console.log(wc);
	console.log(wc?.blocksCheckout);

	const { extensionCartUpdate } = wc.blocksCheckout;

	console.log(extensionCartUpdate);
	/*
	// This does not work
	const woo188Container = document.querySelector('#woo1881-lookup');
	console.log(woo188Container);
	if ( ! woo188Container ) {
		console.log('not found!');
		return;
	}* /

});

domReady(() => {
	console.log(window.Woo1881);

	const url = `${window.Woo1881.ajax_url}?action=woo1881_get_search_results&phone=111`;
	fetch(url, {
		method: 'POST',
	}).then((response) => {
		return response.json();
	}).then((data) => {
		console.log('received from ajax');
		console.log(data);
	});
});
 */
