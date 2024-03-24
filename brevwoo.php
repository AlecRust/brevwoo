<?php
/**
 * Plugin Name:       BrevWoo
 * Plugin URI:        http://github.com/AlecRust/brevwoo
 * GitHub Plugin URI: AlecRust/brevwoo
 * Description:       Add WooCommerce customers to Brevo the simple way.
 * Version:           0.0.10
 * Author:            Alec Rust
 * Author URI:        https://www.alecrust.com/
 * Developer:         Alec Rust
 * Developer URI:     https://www.alecrust.com/
 * Text Domain:       brevwoo
 *
 * WC requires at least: 3.0.0
 * WC tested up to: 8.7.0
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
 * Load Composer autoloader.
 */
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

/**
 * Plugin version.
 */
define( 'BREVWOO_VERSION', '0.0.10' );

/**
 * Load core plugin class defining all hooks.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-brevwoo.php';

/**
 * Begin plugin execution.
 *
 * @return void
 * @SuppressWarnings(PHPMD.MissingImport)
 */
function run_brevwoo() {
	$plugin = new BrevWoo();
	$plugin->init();
}
run_brevwoo();
