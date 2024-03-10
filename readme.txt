=== BrevWoo ===
Contributors:      alecrust
Tags:              marketing, automation, sendinblue, brevo, woocommerce
Requires at least: 4.6
Tested up to:      6.5
Stable tag:        0.0.3
Requires PHP:      7.0
License:           GPL-2.0-or-later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html

Connect WooCommerce products to Brevo lists.

== Description ==

Improve your marketing automation by connecting WooCommerce products to [Brevo](https://www.brevo.com/) lists.

Each product can be connected to any number of Brevo lists. When a customer completes an order, they are added to the selected lists.

For example, use this plugin to create a Brevo automation based on customers who purchase a specific product.

Features include:

* Adds customer to selected Brevo lists for a given product when order is completed
* Includes customer email, first name, and last name in the Brevo contact
* Includes order ID, price and date as [transactional attributes](https://help.brevo.com/hc/en-us/articles/10635646979218-Create-and-manage-transactional-attributes)
* Simpler and more reliable than the [Brevo Tracker](https://developers.brevo.com/docs/getting-started-with-brevo-tracker) at what it does

This plugin is open source and contributions are welcome [on GitHub](https://github.com/AlecRust/brevwoo).

== Installation ==

1. Install and activate the plugin
2. Set your Brevo API key at **Settings > BrevWoo**
3. Edit a product and select some Brevo lists in the "BrevWoo" sidebar panel
4. Customers who purchase this product will be added to the selected Brevo lists

== Frequently Asked Questions ==

= What is Brevo? =

[Brevo](https://www.brevo.com/) (formerly Sendinblue) is a marketing automation platform. This plugin allows you to add a customer to Brevo lists when they purchase a specific WooCommerce product.

= Where do I get a Brevo API key? =

You can create a Brevo API key for this plugin in the "SMTP & API" section of your Brevo account settings, in the "API Keys" tab.

View the [official Brevo documentation](https://developers.brevo.com/docs/getting-started#quick-start) for more information.

== Screenshots ==

1. Set your Brevo API key in the plugin settings.
2. Select Brevo lists in the edit product sidebar.

== Changelog ==
