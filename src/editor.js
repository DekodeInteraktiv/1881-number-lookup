document.addEventListener('DOMContentLoaded', function () {
	const { registerCheckoutFilters } = window.wc.blocksCheckout;

	// Allow our block as InnerBlock to any of WooCommerce Checkout blocks.
	const modifyAdditionalInnerBlockTypes = (
		defaultValue,
		extensions,
		args,
		validation
	) => {
		defaultValue.push('woo1881/checkout-1881-lookup');
		return defaultValue;
	};

	registerCheckoutFilters( 'woo1881-extension', {
		additionalCartCheckoutInnerBlockTypes: modifyAdditionalInnerBlockTypes,
	});
});
