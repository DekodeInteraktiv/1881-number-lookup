# WooCommerce 1881 Integration
Adds lookup of adress and contact information from 1881 in WooCommerce checkout.

The plugin supports both legacy (shortcode) checkout and WooCommerce blocks checkout. It will automatically detect which is in use.

## Requirements
* WooCommerce plugin, minimum v8.2
* Valid subscription to 1881 API

## Setup
Upon activating the plugin a settings page is added under the WooCommerce menu. In admin, head to WooCommerce > Woo 1881.

Enter your primary subscription key in the field for authentication. Follow the guide and link in the description for where to generate API key. Optionally you can edit the text displayed in checkout. Click Save.

Note that unless you enter a subscription key in settings, the lookup fields will not be added to checkout.

## Usage
With a subscription key entered in settings page, the checkout should automatically appear in checkout.

It adds a section with a phone field in the beginning of the checkout page. When entering a phone number (minimum 8 digits), it will perform a 1881 phone lookup.

If 1881 API returns with only 1 hit, the details are immediately filled in. If there's more than 1 hit, an autocomplete list will appear and upon clicking the right item, it will autofill the details for the chosen entry. If 1881 returned no hits, nothing will be filled in.

If 1881 API had missing data (e.g. a person chooses to hide address), then WooCommerce's validation is triggered, prompting the user to fill in the remaining required data themselves.

## Autofill fields and rules
The details to be filled in will be (if 1881 has this information):
* First name
* Last name
* Address 1 (street address)
* Postcode / zip
* City
* Phone number
* Email address
* Company name (if 1881 has registered the phone number as a company, and the field is active in checkout)

### Handling billing, shipping, and company
1881 API defines a type for each hit by phone number as either a person or a company.

If the phone number is registered as a person:
* Billing and shipping address are both filled in from 1881's "`geography.address`"

If the phone number is registered as a company:
* Shipping address are filled in from 1881's "`legalInformation.address`"
* If 1881's "`legalInformation.postAddress`" has an address, this is used as billing address. If not, the above address is used.

## Customization for developers
The plugin provides the following filters for developers to adjust settings or extend functionality:

* `woo1881_contacts_formatted` (array): The final parsed 1881 hits before this is sent to frontend script to autofill or display autocomplete.
* `woo1881_contacts_from_lookup` (array): Filter applied on 1881's immediate phone lookup results, before being parsed for frontend.
* `woo1881_validate_phone_number_before_search` (string): Allow to validate or modify the phone number before performing 1881 lookup.
* `woo1881_phone_valid_lengths` (int array): Define an array of ints with phone number lengths. Only when entered phone number is one of these lengths, a 1881 lookup is performed. Default `[ 5, 8 ]`.
* `woo1881_keyup_delay_ms` (int): Amount of ms to wait while typing in the phone field, before a lookup is performed.
* `woo1881_autocomplete_address_truncate_length` (int): Define the maximum number of characters to display in autocomplete list ("..." excluded)
* `woo1881_cache_phone_lookup_time` (int): Define the transient's expiration time for each phone number.
* `woo1881_api_request_headers` (array): HTTP headers used for sending requests to 1881 API.
* `woo1881_legacy_checkout_output_hook` (string): The WooCommerce template hook to output the phone lookup section in legacy checkout.
* `woo1881_script_localized_variables_legacy` (array): Variables being localized to the frontend script for legacy checkout.
* `woo1881_legacy_checkout_html` (string): Full HTML output for 1881 phone lookup section in legacy checkout.
* `woo1881_settings_defaults` (array): Default settings for admin settings page.

The plugin provides the following CSS variables for developers to adjust styling:

* `--woo1881-autocomplete-maxheight`: Max height for autocomplete container. If the number of results exceeds this, a scrollbar is displayed.
* `--woo1881-autocomplete-background`: Background color for the autocomplete container.
* `--woo1881-autocomplete-border-color`: Border color for the autocomplete container.
* `--woo1881-autocomplete-text-color`: Text color on autocomplete items.
* `--woo1881-autocomplete-hover-text-color`: Text color on hover autocomplete item.
