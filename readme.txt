=== BrevWoo ===
Contributors:      alecrust
Tags:              marketing, automation, sendinblue, brevo, woocommerce
Requires at least: 4.6
Tested up to:      6.5
Stable tag:        0.0.6
Requires PHP:      7.0
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
* Useful debug logging added to [Activity Log](https://wordpress.org/plugins/aryo-activity-log/) plugin if installed

This plugin is open source and contributions are welcome [on GitHub](https://github.com/AlecRust/brevwoo).

== Installation ==

1. Install and activate the plugin
2. Set your Brevo API key at **Settings > BrevWoo**
3. Edit a product and select some Brevo lists in the "BrevWoo" sidebar panel
4. Customers who buy this product will be added to the selected Brevo lists

== Frequently Asked Questions ==

= What is Brevo? =

[Brevo](https://www.brevo.com/) (formerly Sendinblue) is a marketing automation platform. This plugin adds customers to Brevo when they purchase your WooCommerce products.

= Where do I get a Brevo API key? =

You can create a Brevo API key for this plugin in the "SMTP & API" section of your Brevo account settings, in the "API Keys" tab.

View the [official Brevo documentation](https://developers.brevo.com/docs/getting-started#quick-start) for more information.

== Screenshots ==

1. Set your Brevo API key in the plugin settings.
2. Select Brevo lists in the edit product sidebar.

== Changelog ==
