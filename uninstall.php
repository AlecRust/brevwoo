<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @link    https://www.alecrust.com/
 * @package BrevWoo
 */

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
  exit();
}

// Delete the plugin options from the wp_options table
delete_option('brevwoo_brevo_api_key');
