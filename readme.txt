=== BrevWoo ===
Contributors:      alecrust
Tags:              marketing, automation, sendinblue, brevo, woocommerce
Requires at least: 4.6
Tested up to:      6.5
Stable tag:        0.0.1
Requires PHP:      7.0
License:           GPL-2.0-or-later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html

Allows adding customers who purchase a specific WooCommerce product to a specific Brevo list.

== Description ==

Allows adding customers who purchase a specific WooCommerce product to a specific Brevo list.

This would be a great feature of the official [Brevo for WooCommerce](https://wordpress.org/plugins/woocommerce-sendinblue-newsletter-subscription/) plugin, but it is not currently supported. This plugin fills that gap.

Simply provide your Brevo API key in the BrevWoo settings page, and a dropdown will appear on the product edit page to select the Brevo list to add customers to.

When a customer purchases the product, they will be added to the selected Brevo list using the [official Brevo API client](https://github.com/getbrevo/brevo-php).

Features include:

* Interacts with the Brevo API using the [official PHP API client](https://github.com/getbrevo/brevo-php)
* Processes the WooCommerce order server-side to ensure the customer is reliably added to the list
* Creates (or updates) a Brevo contact using the customer's email address and first/last name
* Adds [transactional attributes](https://help.brevo.com/hc/en-us/articles/10635646979218-Create-and-manage-transactional-attributes) to the Brevo contact including the order ID and price

This plugin is open source and contributions are welcome [on GitHub](https://github.com/AlecRust/brevwoo).

== Installation ==

1. Install and activate the plugin
2. Set your Brevo API key at **Settings > BrevWoo**
3. Edit a WooCommerce product and set the Brevo list to add customers to at **Product data > General > Brevo List**
4. When a customer purchases the product, they will be added to the selected Brevo list

== Frequently Asked Questions ==

= What does this plugin do? =

This plugin adds a dropdown to the WooCommerce product edit page to select a Brevo list. When a customer purchases the product, they will be added to the selected Brevo list. The plugin must be configured with your Brevo API key at **Settings > BrevWoo**.

== Screenshots ==

1. Settings page to configure the Brevo API key.
2. Brevo list dropdown in edit product sidebar.

== Changelog ==
