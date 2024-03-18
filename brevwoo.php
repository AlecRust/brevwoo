<?php
// phpcs:disable
/**
 * Plugin Name:       BrevWoo
 * Plugin URI:        http://github.com/AlecRust/brevwoo
 * GitHub Plugin URI: AlecRust/brevwoo
 * Requires Plugins:  woocommerce
 * Description:       Simple and reliable WooCommerce to Brevo integration.
 * Version:           0.0.5
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

require plugin_dir_path(__FILE__) . 'vendor/autoload.php';

// Abort if this file is called directly.
if (!defined('WPINC')) {
    die();
}

/**
 * Plugin version.
 */
define('BREVWOO_VERSION', '0.0.5');

/**
 * Load core plugin class defining all hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/core.php';

/**
 * Begin plugin execution.
 * @SuppressWarnings(PHPMD.MissingImport)
 */
function run_brevwoo()
{
    $plugin = new BrevWoo();
    $plugin->init();
}
run_brevwoo();
// phpcs:enable
