=== BrevWoo ===
Contributors:      alecrust
Tags:              marketing, automation, sendinblue, brevo, woocommerce
Requires at least: 4.6
Tested up to:      6.5
Stable tag:        0.0.11
Requires PHP:      7.4
License:           GPL-2.0-or-later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html

Add WooCommerce customers to Brevo the simple way.

== Description ==

Improve and simplify your marketing automation by connecting WooCommerce purchases to [Brevo](https://www.brevo.com/).

Set default Brevo lists that customers are added to when they buy a product, and/or set lists for specific products.

This plugin can help with Brevo automations by reliably adding customers to Brevo lists based on the products they purchase.

Features include:

* Customer added to default Brevo lists selected in plugin settings
* Customer added to product-specific Brevo lists selected on edit product page
* Configuration of when during checkout a customer is added to Brevo
* Customer name and email attributes included in the created Brevo contact
* [Transactional attributes](https://help.brevo.com/hc/en-us/articles/10635646979218-Create-and-manage-transactional-attributes) included in the created Brevo contact
* Optional debug entries added to WooCommerce logs

This plugin is not affiliated with or endorsed by Brevo. It's open source, and contributions are welcome [on GitHub](https://github.com/AlecRust/brevwoo).

== Installation ==

1. Install and activate the plugin
2. Set your Brevo API key at **Settings > BrevWoo**
3. Edit a product and select some Brevo lists in the "BrevWoo" sidebar panel
4. Customers who buy this product will be added to the selected Brevo lists

== Frequently Asked Questions ==

= What is Brevo? =

[Brevo](https://www.brevo.com/) (formerly Sendinblue) is a marketing automation platform. This plugin adds customers to Brevo when they purchase your WooCommerce products.

= Where do I get a Brevo API key? =

From the "SMTP & API" section of your Brevo account settings, in the "API Keys" tab. It's recommended to create a new API key for this plugin e.g. you could label it "BrevWoo".

Once you have an API key, enter it in the plugin settings at **Settings > BrevWoo**.

View the [official Brevo documentation](https://developers.brevo.com/docs/getting-started#quick-start) for more information.

= Doesn't Brevo already have a WooCommerce plugin? =

Yes, Brevo has an [official WooCommerce plugin](https://wordpress.org/plugins/woocommerce-sendinblue-newsletter-subscription/) as well as a [main plugin](https://wordpress.org/plugins/mailin/). BrevWoo is a complimentary plugin that provides a simple set of features including adding customers to product-specific lists.

You may or may not need any of the three plugins, check the features of each to see which best suits your needs. Having BrevWoo installed alongside the official Brevo plugins should not cause any conflicts.

= Can I add customers to multiple lists? =

Yes. You can select any number of default lists in the plugin settings, and also select any number of product-specific lists in each edit product page.

= If I rename a list in Brevo, will this plugin still work? =

Yes, the plugin uses the list ID, not the list name, so renaming a list in Brevo will not affect the plugin.

== Screenshots ==

1. Set your Brevo API key in the plugin settings.
2. Select Brevo lists in the edit product sidebar.
3. Optional debug entries in WooCommerce logs.

== Changelog ==
