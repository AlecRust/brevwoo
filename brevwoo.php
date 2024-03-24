<?php
/**
 * Plugin Name:       BrevWoo
 * Plugin URI:        http://github.com/AlecRust/brevwoo
 * GitHub Plugin URI: AlecRust/brevwoo
 * Description:       Add WooCommerce customers to Brevo the simple way.
 * Version:           0.0.9
 * Author:            Alec Rust
 * Author URI:        https://www.alecrust.com/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       brevwoo
 *
 * @package BrevWoo
 * @author  Alec Rust
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
define( 'BREVWOO_VERSION', '0.0.9' );

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
