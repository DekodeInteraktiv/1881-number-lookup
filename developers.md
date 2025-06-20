# 1881 Number Lookup Developer documentation

The plugin provides the following filters for developers to adjust settings or extend functionality:

* `dm1881_contacts_formatted` (array): The final parsed 1881 hits before this is sent to frontend script to autofill or display autocomplete.
* `dm1881_contacts_from_lookup` (array): Filter applied on 1881's immediate phone lookup results, before being parsed for frontend.
* `dm1881_validate_phone_number_before_search` (string): Allow to validate or modify the phone number before performing 1881 lookup.
* `dm1881_phone_valid_lengths` (int array): Define an array of ints with phone number lengths. Only when entered phone number is one of these lengths, a 1881 lookup is performed. Default `[ 5, 8 ]`.
* `dm1881_keyup_delay_ms` (int): Amount of ms to wait while typing in the phone field, before a lookup is performed.
* `dm1881_autocomplete_address_truncate_length` (int): Define the maximum number of characters to display in autocomplete list ("..." excluded)
* `dm1881_cache_phone_lookup_time` (int): Define the transient's expiration time for each phone number.
* `dm1881_api_request_headers` (array): HTTP headers used for sending requests to 1881 API.
* `dm1881_legacy_checkout_output_hook` (string): The WooCommerce template hook to output the phone lookup section in legacy checkout.
* `dm1881_script_localized_variables_legacy` (array): Variables being localized to the frontend script for legacy checkout.
* `dm1881_legacy_checkout_html` (string): Full HTML output for 1881 phone lookup section in legacy checkout.
* `dm1881_settings_defaults` (array): Default settings for admin settings page.

The plugin provides the following CSS variables for developers to adjust styling:

* `--dm1881-autocomplete-maxheight`: Max height for autocomplete container. If the number of results exceeds this, a scrollbar is displayed.
* `--dm1881-autocomplete-background`: Background color for the autocomplete container.
* `--dm1881-autocomplete-border-color`: Border color for the autocomplete container.
* `--dm1881-autocomplete-text-color`: Text color on autocomplete items.
* `--dm1881-autocomplete-hover-text-color`: Text color on hover autocomplete item.
* `--dm1881-error-color`: Text and icon fill color for the error message when search returned no hits.
