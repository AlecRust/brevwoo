<?php
/**
 * Core plugin class.
 *
 * @package    BrevWoo
 * @subpackage BrevWoo/includes
 * @link       https://github.com/AlecRust/brevwoo
 */

/**
 * The core plugin class.
 */
class BrevWoo {
	/**
	 * Unique identifier of this plugin.
	 *
	 * @var string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * Current version of the plugin.
	 *
	 * @var string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define core plugin functionality.
	 */
	public function __construct() {
		$this->version     = defined( 'BREVWOO_VERSION' ) ? BREVWOO_VERSION : '1.0.0';
		$this->plugin_name = 'brevwoo';
	}

	/**
	 * Initialize the plugin.
	 *
	 * @return void
	 */
	public function init() {
		$this->load_dependencies();
		$this->define_admin_hooks();
	}

	/**
	 * Load required plugin dependencies.
	 *
	 * @return void
	 */
	private function load_dependencies() {
		require_once plugin_dir_path( __FILE__ ) . 'class-brevwoo-admin.php';
	}

	/**
	 * Register hooks related to the admin area functionality.
	 *
	 * @SuppressWarnings(PHPMD.MissingImport)
	 *
	 * @return void
	 */
	private function define_admin_hooks() {
		$plugin_admin = new BrevWoo_Admin(
			$this->get_plugin_name(),
			$this->get_version()
		);

		// Admin custom styles.
		add_action(
			'admin_enqueue_scripts',
			array(
				$plugin_admin,
				'enqueue_admin_styles',
			)
		);

		// Display admin notice if WooCommerce is not active.
		add_action(
			'admin_notices',
			array(
				$plugin_admin,
				'render_required_plugin_notice',
			)
		);

		// Admin settings page.
		add_action( 'admin_init', array( $plugin_admin, 'settings_page_init' ) );

		// Admin menu item.
		add_action( 'admin_menu', array( $plugin_admin, 'add_menu_item' ) );

		// Plugin settings link on "Plugins" page.
		add_filter(
			'plugin_action_links_brevwoo/brevwoo.php',
			array(
				$plugin_admin,
				'add_settings_link',
			)
		);

		// Add "BrevWoo" panel to edit product page.
		add_action( 'add_meta_boxes', array( $plugin_admin, 'add_edit_product_panel' ) );

		// Save selected Brevo lists to a product when it's saved.
		add_action(
			'woocommerce_admin_process_product_object',
			array( $plugin_admin, 'save_product_lists' )
		);

		// Initialize WooCommerce logger.
		add_action( 'woocommerce_loaded', array( $plugin_admin, 'initialize_wc_logger' ) );

		// Add customer to Brevo lists when the (user defined) WC order status is reached.
		add_action(
			$plugin_admin->get_wc_checkout_hook(),
			array(
				$plugin_admin,
				'process_wc_order',
			)
		);
	}

	/**
	 * Utility returning plugin name uniquely identifying it.
	 *
	 * @return string The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Utility returning the version number of the plugin.
	 *
	 * @return string The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
