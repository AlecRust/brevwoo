<?php
/**
 * Plugin Name:       BrevWoo
 * Plugin URI:        http://github.com/AlecRust/brevwoo
 * Description:       Add WooCommerce customers to Brevo the simple way.
 * Version:           1.0.10
 * Author:            Alec Rust
 * Author URI:        https://www.alecrust.com/
 * Developer:         Alec Rust
 * Developer URI:     https://www.alecrust.com/
 * Text Domain:       brevwoo
 *
 * WC requires at least: 3.0.0
 * WC tested up to: 9.3.1
 *
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package BrevWoo
 * @link    https://github.com/AlecRust/brevwoo
 */

// Abort if this file is called directly.
if ( ! defined( 'WPINC' ) ) {
	die();
}

/**
 * Load Composer entry point.
 */
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

/**
 * Plugin version.
 */
define( 'BREVWOO_VERSION', '1.0.10' );

/**
 * Load core plugin class defining all hooks.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-brevwoo.php';


/**
 * Declare plugin compatibility with WC HPOS.
 * https://github.com/woocommerce/woocommerce/wiki/High-Performance-Order-Storage-Upgrade-Recipe-Book
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 *
 * @return void
 */
add_action(
	'before_woocommerce_init',
	function () {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
);

/**
 * Begin plugin execution.
 *
 * @return void
 * @SuppressWarnings(PHPMD.MissingImport)
 */
function brevwoo_init() {
	$plugin = new BrevWoo();
	$plugin->init();
}
brevwoo_init();
