=== BrevWoo ===
Contributors:      alecrust
Tags:              marketing, automation, sendinblue, brevo, woocommerce
Requires at least: 4.6
Tested up to:      6.7
Stable tag:        1.0.10
Requires PHP:      7.4
License:           GPL-2.0-or-later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html

Add WooCommerce customers to Brevo the simple way.

== Description ==

Improve and simplify your marketing automation by connecting WooCommerce purchases to [Brevo](https://www.brevo.com/).

**Set default Brevo lists that customers are added to when they buy a product, and/or set lists for specific products.**

This plugin can help with Brevo automations by reliably adding customers to Brevo lists based on the products they purchase.

Features include:

* Add customers to specific Brevo lists based on products they buy
* Add customers to default Brevo lists when they buy any product
* Select when during checkout the customer is added to Brevo
* Contacts created in Brevo include [transactional attributes](https://help.brevo.com/hc/en-us/articles/10635646979218-Create-and-manage-transactional-attributes)
* Optional logging included to help debug any issues

This plugin is not affiliated with Brevo. It's open source, and contributions are welcome [on GitHub](https://github.com/AlecRust/brevwoo).

== Installation ==

1. Install and activate the plugin
2. Set your Brevo API key at **Settings > BrevWoo**
3. Select any default Brevo lists on the same settings page
4. Edit a product and select some Brevo lists in the "BrevWoo" sidebar panel
5. Customers who buy this product will be added to the selected Brevo lists

== Frequently Asked Questions ==

= What is Brevo? =

[Brevo](https://www.brevo.com/) (formerly Sendinblue) is a marketing automation platform. This plugin adds customers to Brevo when they purchase your WooCommerce products.

= Where do I get a Brevo API key? =

From the "SMTP & API" section of your Brevo account settings, in the "API Keys" tab. It's recommended to create a new API key for this plugin e.g. you could label it "BrevWoo".

Once you have an API key, enter it in the plugin settings at **Settings > BrevWoo**.

View the [official Brevo documentation](https://developers.brevo.com/docs/getting-started#quick-start) for more information.

= Can I add customers to multiple lists? =

Yes. You can select any number of default lists in the plugin settings, and also select any number of product-specific lists in each edit product page.

= If I rename a list in Brevo, will this plugin still work? =

Yes, the plugin uses the list ID, not the list name, so renaming a list in Brevo will not affect the plugin.

= Doesn't Brevo already have a WooCommerce plugin? =

Yes, Brevo has an [official WooCommerce plugin](https://wordpress.org/plugins/woocommerce-sendinblue-newsletter-subscription/) as well as a [main plugin](https://wordpress.org/plugins/mailin/). BrevWoo is a complimentary plugin that provides a simple set of features including adding customers to product-specific lists.

You may or may not need any of the three plugins, check the features of each to see which best suits your needs. Having BrevWoo installed alongside the official Brevo plugins should not cause any conflicts.

== Screenshots ==

1. Configure global settings and default lists.
2. Select product-specific lists on the edit product page.
3. View optional debug entries in WooCommerce logs.

== Changelog ==

= 1.0.10 - 2024-09-15 =

* Update "WC tested up to" version
* Update WordPress "Tested up to" to 6.7
* Bump Node dependencies
* Bump Composer dependencies
* Merge pull request #4 from AlecRust/renovate/getbrevo-brevo-php-2.x-lockfile
* Update dependency getbrevo/brevo-php to v2.0.2

= 1.0.9 - 2024-07-06 =

* Update WordPress "Tested up to" to 6.6
* Update "WC tested up to" to 9
* Bump Composer dependencies
* Bump Node dependencies

= 1.0.8 - 2024-05-11 =

* Bump Composer dependencies
* Bump Node dependencies
* Tidy README
* Remove Node script to simplify build process

= 1.0.7 - 2024-04-20 =

* Bump @wordpress npm packages
* Bump Composer packages
* Fix workflow path
* Fix workflow name
* Add weekly "Tested up to" CI check

= 1.0.6 - 2024-04-12 =

* Bump release-it version
* Tidy README
* Don't activate WooCommerce by default in plugin demo

= 1.0.5 - 2024-04-11 =

* Simplify features list
* Improve plugin icon and banner
* Improve screenshot descriptions

= 1.0.4 - 2024-04-11 =

* Fix error submitting form when "Default Brevo lists" is not rendered

= 1.0.3 - 2024-04-11 =

* Fix changelogs
* Remove --latest from git-cliff command
* Add missing release to changelogs
* Adjust deploy workflow permissions

= 1.0.2 - 2024-04-11 =

* Improve plugin icon
* Fix old dev commits in changelog
* Remove "v" prefix from git-cliff commands

= 1.0.1 - 2024-04-11 =

* Improve tag deletion SVN commit message

= 1.0.0 - 2024-04-11 =

* Adjust changelog.txt formatting
* Tidy for initial release
