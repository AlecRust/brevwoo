<?php
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
/**
 * Core plugin class.
 *
 * Defines the plugin name, version, and admin hooks.
 *
 * @link       https://github.com/AlecRust/brevwoo
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
        $this->version = defined('BREVWOO_VERSION') ? BREVWOO_VERSION : '1.0.0';
        $this->plugin_name = 'brevwoo';
    }

    /**
     * Initialize the plugin.
     */
    public function init()
    {
        $this->loadDependencies();
        $this->defineAdminHooks();
    }

    /**
     * Load required plugin dependencies.
     *
     * @access private
     */
    private function loadDependencies()
    {
        /**
         * Define all actions that occur in the admin area.
         */
        require_once plugin_dir_path(__FILE__) . 'admin.php';
    }

    /**
     * Register hooks related to the admin area functionality.
     * @SuppressWarnings(PHPMD.MissingImport)
     *
     * @access private
     */
    private function defineAdminHooks()
    {
        $plugin_admin = new BrevWooAdmin(
            $this->getPluginName(),
            $this->getVersion()
        );

        // Admin settings page
        add_action('admin_init', [$plugin_admin, 'settingsPageInit']);

        // Admin menu item
        add_action('admin_menu', [$plugin_admin, 'addMenuItem']);

        // Plugin settings link on "Plugins" page
        add_filter('plugin_action_links_brevwoo/brevwoo.php', [
            $plugin_admin,
            'addSettingsLink',
        ]);

        // Add "BrevWoo" panel to edit product page
        add_action('add_meta_boxes', [$plugin_admin, 'addEditProductPanel']);

        // Save selected Brevo lists to a product when it's saved
        add_action('save_post_product', [$plugin_admin, 'saveSelectedLists']);

        // Add customer to Brevo lists when order is completed
        add_action('woocommerce_order_status_completed', [
            $plugin_admin,
            'processWcOrderCompleted',
        ]);
    }

    /**
     * Utility returning plugin name uniquely identifying it within the context of WordPress.
     *
     * @return string    The name of the plugin.
     */
    public function getPluginName()
    {
        return $this->plugin_name;
    }

    /**
     * Utility returning the version number of the plugin.
     *
     * @return string    The version number of the plugin.
     */
    public function getVersion()
    {
        return $this->version;
    }
}
