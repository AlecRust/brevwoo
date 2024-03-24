<?php
/**
 * Runs when the plugin is uninstalled.
 *
 * @package BrevWoo
 * @link    https://github.com/AlecRust/brevwoo
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

// Delete main plugin options from the wp_options table.
delete_option( 'brevwoo_brevo_api_key' );
delete_option( 'brevwoo_default_lists' );
delete_option( 'brevwoo_order_status' );
delete_option( 'brevwoo_debug_logging' );

// Delete WooCommerce product meta.
if ( class_exists( 'WooCommerce' ) ) {
	$args     = array(
		'post_type'      => 'product',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	);
	$products = get_posts( $args );
	foreach ( $products as $product_id ) {
		delete_post_meta( $product_id, '_brevwoo_product_lists' );
	}
}
