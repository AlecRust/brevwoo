=== BrevWoo ===
Contributors:      alecrust
Tags:              marketing, automation, sendinblue, brevo, woocommerce
Requires at least: 4.6
Tested up to:      6.5
Stable tag:        0.0.2
Requires PHP:      7.0
License:           GPL-2.0-or-later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html

Add customers who purchase a specific WooCommerce product to your Brevo lists.

== Description ==

Adds a panel to the WooCommerce edit product page where you can select from your Brevo lists. When a customer completes purchase of that product, they will be added to the selected Brevo lists.

This is useful for example to simplify Brevo automations. With the purchase of a given product tied to a specific Brevo list, a Brevo automation can use the [Contact added to list](https://help.brevo.com/hc/en-us/articles/15476926804370-New-automation-editor-BETA-Set-up-a-welcome-email-automation#h_01HGJTQFMDH72F17QCMG7DC8GY) trigger to start a sequence based on the product purchased.

Features include:

* Simpler and more reliable at adding customer to lists than the JavaScript Brevo Tracker
* Creates (or updates) a Brevo contact using the customer's email address and first/last name
* Adds [transactional attributes](https://help.brevo.com/hc/en-us/articles/10635646979218-Create-and-manage-transactional-attributes) to the Brevo contact including the order ID and price
* Uses the [official PHP client](https://github.com/getbrevo/brevo-php) to interact with the Brevo API

This plugin is open source and contributions are welcome [on GitHub](https://github.com/AlecRust/brevwoo).

== Installation ==

1. Install and activate the plugin
2. Set your Brevo API key at **Settings > BrevWoo**
3. Edit a product and select from your Brevo lists in the "BrevWoo" sidebar panel
4. When a customer purchases the product, they will be added to the selected Brevo lists

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
