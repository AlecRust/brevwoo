=== BrevWoo ===
Contributors:      alecrust
Tags:              marketing, automation, sendinblue, brevo, woocommerce
Requires at least: 4.6
Tested up to:      6.5
Stable tag:        1.0.1
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

= 1.0.1 - 2024-04-11 =

* Improve tag deletion SVN commit message

= 1.0.0 - 2024-04-11 =

* Adjust changelog.txt formatting
* Tidy for initial release
* Tweak order in .distignore
* Bump dev version
* Refactor to avoid ignoring WPCS error
* Bump dev version
* Fix stable tag in output readme.txt
* Prefix public function
* Refactor saving of product lists
* Include composer.json in plugin dist
* Improve consistency of checking for WooCommerce
* Add plugin Blueprint file
* Sort changelogs consistently
* Add changelog.txt file required by WooCommerce extensions
* Use latest version of all development Composer packages
* Bump npm dependencies
* Use git-cliff for changelog building
* Set PHP version to build Composer dependencies with
* Tidy
* Bump dev version
* Bump Composer dependencies
* Improve how product meta is saved
* Declare compatibility with WC High-Performance Order Storage
* Tidy
* Remove "GitHub Plugin URI"
* Bump dev version
* Adhere to WC coding standards
* Remove @access tags
* Bump dev version
* Bump Composer dependencies
* Refactor to adhere to WordPress Coding Standards
* Bump dev version
* Improve screenshots
* Log to WC, add new debug logging option
* Consistently use printf() for paragraphs
* Improve plugin settings page description
* Improve input height on mobile
* Tidy
* Improve FAQs
* Improve plugin banner
* Improve functions order
* Bump Composer packages
* Bump @wordpress/env version
* Bump dev version
* Improve errors, remove required attributes
* Use wp_admin_notice for error alerts
* Improve naming
* Improve tooltip copy
* Improve plugin description
* Display help below "Default Brevo lists" field
* Tidy comments
* Bump dev version
* Group list options by folder name
* Add more plugins to development environment
* Remove WooCommerce plugin dependency for now
* Set WooCommerce as plugin dependency
* Improve how API status notice is rendered
* Bump Composer dependencies
* Improve order of functions
* Add function to get settings page URL
* Refactor to render both select fields from one function
* Bump dev version
* Add required attributes to new plugin settings fields
* Remove storefront theme from dev environment
* Delete WooCommerce product meta on uninstall
* Improve how list IDs are saved against products
* Update README
* Increase PHPStan level
* Improve FAQ
* Set more Composer options
* Use standard plugin description in Composer config
* Fix key name
* Fix lint
* Add initializeApiClient() function
* Sanitize list IDs as integers before save
* Increase max line length
* Add "Default Brevo lists" plugin option
* Log contact creation to Activity Log plugin if installed
* Improve copy
* Improve copy
* Improve alert copy
* Simplify product meta saving and fetching
* Persist "None" as the default option
* Tidy copy
* Bump dev version
* Fix lint
* Improve post meta saving security
* Bump @wordpress/env version
* Add Storefront WooCommerce theme to development themes
* Delete brevwoo_order_status_trigger on plugin uninstall
* Improve function name
* Improve function name
* Tidy copy
* Add README notice
* Fix Plugin Check errors
* Upgrade getbrevo/brevo-php to v2.0
* Add plugin option "Add to Brevo trigger"
* Display admin notice if WooCommerce is not active
* Improve @link comments
* Update screenshot
* Add help tooltip above multiple select input
* Add license to Composer config
* Add PHPStan
* Tidy copy
* Tidy copy
* Improve README
* Improve README
* Bump dev version
* Tidy README
* Improve plugin images and description
* Tidy comments
* Simplify GrumPHP config
* Fix line length
* Simplify security when saving selected lists
* Make Brevo API key input a required field
* Simplify edit product page panel ID
* Improve links under Brevo API key input
* Namespace custom product meta option
* Fix lint
* Bump development version
* Fix disabling of behavior by selecting "None (disabled)"
* Fix error when loading new product page
* Add logo icons and cover image
* Disable autocomplete on Brevo API key input
* Fix lint
* Create a dedicated class for Brevo API interactions
* Add support for selecting multiple Brevo lists
* Add missing translators comment
* Improve input description
* Improve error notices
* Improve security of saving product meta
* Improve HTML escaping
* Simplify Renovate config
* Stop formatting package-lock.json with Prettier
* Merge pull request #1 from AlecRust/renovate/squizlabs-php_codesniffer-3.x
* Update dependency squizlabs/php_codesniffer to ^3.9.0
* Disable Composer dev dependency Renovate updates
* Add Renovate config
* Improve screenshots and copy
* Fix location of translators comments
* Remove mention of Git Updater
* Improve README
* Add translators comments
* Add Plugin Check to development plugins
* Use 4 space formatting, fix lint
* Initial commit
