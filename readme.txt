=== 1881 Number Lookup ===
Contributors: dekode
Requires at least: 6.0
Tested up to: 6.8
Stable tag: 1.0.6
Requires PHP: 7.4
License: GPLv3 or later

Official extension for 1881 Number Lookup Checkout.

== Description ==

Make checkout faster and easier with automatic retrieval of name and address based on phone number.

With 1881 Number Lookup for WooCommerce Checkout, you can offer a more precise checkout experience that will result in a higher conversion rate. Customers only need to fill in their phone number at checkout, and the name and address will be automatically filled in.

1881 is Norway's original, largest, and most used directory service. 1881 is the leading provider of personal and business information and has been delivering information about phone numbers, names, and addresses for more than 140 years. You can find 1881 online, on mobile apps, via SMS, and by calling 1881. 1881 is among the most well-known brands in Norway, making 1881's directory service the natural place to search for names, numbers, maps, and addresses. With access to more than 7,350,000 contact points in the database, we ensure that you, as a user of Norway's largest information service, quickly and easily find the right answer to what you are looking for. The database is continuously updated with up to 125,000 daily updates.

== Screenshots ==
1. Admin settings
2. 1881 Lookup field in Block checkout
3. 1881 Lookup field in legacy checkout

== Installation ==
1. Install and activate the plugin via [WordPress.org plugins](https://wordpress.org/plugins/1881-number-lookup) or [GitHub](https://github.com/DekodeInteraktiv/1881-number-lookup).
2. Go to [www.api1881.no](https://www.api1881.no/) to create a user, then activate a FREE test product (provides 50 searches)
3. Go to Woocommerce > 1881 Number Lookup.
4. Paste your API key from [www.api1881.no/profile](https://www.api1881.no/profile) in the field for subscription key.
5. Click Save – and the plugin is ready for use at checkout.

== Frequently Asked Questions ==

= What does 1881 Number Lookup for WooCommerce Checkout do? =

This plugin allows your customers to fill in their phone number at checkout, and the plugin automatically retrieves the name and address from 1881 database.

= How does it work for the customer? =

When the customer enters their phone number at checkout, the plugin checks the 1881 database for information associated with that number. If there is a match, the first name, last name, address, postal code, city, and company name (if applicable) are automatically filled in the relevant fields. If there are multiple matches found on the phone number, an autocomplete list appears allowing the user to select the correct one. If there is no match, the customer must enter their details manually.

= Do I need an API key? =

Yes, to use the plugin, you must have a valid API key from 1881. You can obtain this key by visiting [www.api1881.no](https://www.api1881.no/) and entering into a commercial agreement.

= How do I know it works? =

After activation, go to your store and add a product to the cart. When you go to checkout you should see an additional field in the beginning of the form, with the 1881 logo, to look up a phone number. Enter your phone number here and the plugin will automatically retrieve the name and address from 1881 if the phone number is in the database.

= Is this GDPR compliant? =

Yes, the plugin is GDPR compliant. However, it is important that you, as the store administrator, inform your customers that information can be retrieved from 1881 when they provide their phone number. We recommend updating your privacy policy to comply with GDPR.

= What happens if the phone number is not found in 1881? =

If the phone number is not found in the 1881 system, no information will be filled in automatically, and the customer must manually enter their name and address. The plugin will always attempt to retrieve information based on the phone number, but there is no negative impact if there are no matches.

= Which fields are automatically filled in? =

The plugin automatically fills in the following fields in WooCommerce checkout:

* First name
* Last name
* Address 1 (street address)
* Postcode / zip
* City
* Phone number
* Email address
* Company name (if 1881 has registered the phone number as a company, and the field is active in checkout)

If the phone number is registered as a company in 1881, the following rules are followed for filling in the address:
* Shipping address will be used if set in 1881's database for company address.
* If 1881's database has an address in company's post address, this will be used as billing address. If not, the above address is used as billing.

= Are there any costs associated with use? =

Yes, 1881 typically charges per lookup when the plugin is used. You must enter into a commercial agreement with 1881 to obtain an API key. The plugin itself may be free or licensed, depending on the distribution model.

= Does the plugin work with other checkout solutions? =

Yes, 1881 Number Lookup for WooCommerce Checkout works with WooCommerce's standard checkout; both legacy checkout and Checkout block. If you use a highly customized checkout solution (e.g., One Page Checkout), it is recommended to perform some tests to ensure everything works as expected.

= Are there any options for developers to modify? =

Yes, the plugin comes with a number of hooks, filters, and CSS variables. Refer to the `developers.md` for more information.
