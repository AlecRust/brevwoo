<?php
/**
 * Core plugin class.
 *
 * Defines admin-specific and public-facing site hooks.
 *
 * Also maintains the unique identifier and current version of the plugin.
 *
 * @link       https://www.alecrust.com/
 * @package    BrevWoo
 * @subpackage BrevWoo/includes
 * @author     Alec Rust (https://www.alecrust.com/)
 */
class BrevWoo
{
  /**
   * Unique identifier of this plugin.
   *
   * @access protected
   * @var    string    $plugin_name    The string used to uniquely identify this plugin.
   */
  protected $plugin_name;

  /**
   * Current version of the plugin.
   *
   * @access protected
   * @var    string    $version    The current version of the plugin.
   */
  protected $version;

  /**
   * Define core plugin functionality.
   *
   */
  public function __construct()
  {
    if (defined('BREVWOO_VERSION')) {
      $this->version = BREVWOO_VERSION;
    } else {
      $this->version = '1.0.0';
    }
    $this->plugin_name = 'brevwoo';
  }

  /**
   * Initialize the plugin.
   */
  public function init()
  {
    $this->load_dependencies();
    $this->define_admin_hooks();
  }

  /**
   * Load required plugin dependencies.
   *
   * @access private
   */
  private function load_dependencies()
  {
    /**
     * Define all actions that occur in the admin area.
     */
    require_once plugin_dir_path(dirname(__FILE__)) . 'includes/admin.php';
  }

  /**
   * Register hooks related to the admin area functionality.
   *
   * @access private
   */
  private function define_admin_hooks()
  {
    $plugin_admin = new BrevWooAdmin(
      $this->get_plugin_name(),
      $this->get_version()
    );

    // Enqueue admin styles
    // add_action('admin_enqueue_scripts', [$plugin_admin, 'enqueueStyles']);

    // Enqueue admin scripts
    // add_action('admin_enqueue_scripts', [$plugin_admin, 'enqueueScripts']);

    // Admin settings page
    add_action('admin_init', [$plugin_admin, 'settings_page_init']);

    // Admin menu item
    add_action('admin_menu', [$plugin_admin, 'add_menu_item']);

    // Plugin settings link on "Plugins" page
    add_filter('plugin_action_links_brevwoo/brevwoo.php', [
      $plugin_admin,
      'add_settings_link',
    ]);

    // Add "BrevWoo" panel to edit product page
    add_action('add_meta_boxes', [$plugin_admin, 'add_brevwoo_panel']);

    // Set the selected Brevo list when a product is saved
    add_action('save_post_product', [$plugin_admin, 'save_product_meta']);

    // Add product purchaser to Brevo list when order is completed
    add_action('woocommerce_order_status_completed', [
      $plugin_admin,
      'process_wc_product_purchase',
    ]);
  }

  /**
   * Utility returning plugin name uniquely identifying it within the context of WordPress.
   *
   * @return string    The name of the plugin.
   */
  public function get_plugin_name()
  {
    return $this->plugin_name;
  }

  /**
   * Utility returning the version number of the plugin.
   *
   * @return string    The version number of the plugin.
   */
  public function get_version()
  {
    return $this->version;
  }
}
