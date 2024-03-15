<?php
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
/**
 * Admin-specific functionality of the plugin.
 *
 * @link       https://github.com/AlecRust/brevwoo
 * @package    BrevWoo
 * @subpackage BrevWoo/includes
 * @author     Alec Rust (https://www.alecrust.com/)
 */

class BrevWooAdmin
{
    /**
     * The ID of this plugin.
     *
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * API client for Brevo.
     *
     * @var BrevWooApiClient
     */
    protected $apiClient;

    /**
     * Initialize the class and set its properties.
     * @SuppressWarnings(PHPMD.MissingImport)
     *
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;

        // Initialize the API client
        $brevo_api_key = get_option('brevwoo_brevo_api_key', '');
        if (!empty($brevo_api_key)) {
            require_once plugin_dir_path(__FILE__) . 'api.php';
            $this->apiClient = new BrevWooApiClient($brevo_api_key);
        }
    }

    /**
     * Register settings page in admin.
     */
    public function addMenuItem()
    {
        add_options_page(
            __('BrevWoo', 'brevwoo'), // page_title
            __('BrevWoo', 'brevwoo'), // menu_title
            'manage_options', // capability
            $this->plugin_name, // menu_slug
            [$this, 'renderSettingsPage'] // callback
        );
    }

    /**
     * Add link to plugin settings on Plugins page.
     */
    public function addSettingsLink($links)
    {
        $url = esc_url(
            add_query_arg(
                'page',
                $this->plugin_name,
                get_admin_url() . 'options-general.php'
            )
        );
        $settings_link =
            "<a href=\"$url\">" . esc_html__('Settings', 'brevwoo') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    /**
     * Render settings page.
     */
    public function renderSettingsPage()
    {
        ?>
        <div class="wrap">
            <h2><?php esc_html_e('BrevWoo', 'brevwoo'); ?></h2>
            <?php $this->renderBrevoStatusNotice(); ?>
            <form action="options.php" method="post">
                <?php
                settings_fields('brevwoo_option_group');
                do_settings_sections('brevwoo-admin');
                submit_button();?>
            </form>
        </div>
        <?php
    }

    /**
     * Register settings page options in admin.
     */
    public function settingsPageInit()
    {
        // Add settings section
        add_settings_section(
            'brevwoo_setting_section', // HTML id
            __('Settings', 'brevwoo'), // title
            [$this, 'renderSettingsDescription'], // callback
            'brevwoo-admin' // page
        );

        // Add "Brevo API key" setting field
        add_settings_field(
            'brevwoo_brevo_api_key', // HTML id
            __('Brevo API key', 'brevwoo'), // field title
            [$this, 'renderApiKeyInput'], // callback
            'brevwoo-admin', // page
            'brevwoo_setting_section', // section
            [
                'id' => 'brevwoo_brevo_api_key',
                'option_name' => 'brevwoo_brevo_api_key',
            ]
        );

        // Register "Brevo API key" setting
        register_setting(
            'brevwoo_option_group', // settings group name
            'brevwoo_brevo_api_key', // option name
            [
                'default' => '',
                'sanitize_callback' => 'sanitize_text_field',
            ]
        );

        // Add "Add to Brevo trigger" setting field
        add_settings_field(
            'brevwoo_order_status_trigger', // HTML id
            __('Add to Brevo trigger', 'brevwoo'), // field title
            [$this, 'renderOrderStatusTriggerInput'], // callback
            'brevwoo-admin', // page
            'brevwoo_setting_section', // section
            [
                'id' => 'brevwoo_order_status_trigger',
                'option_name' => 'brevwoo_order_status_trigger',
            ]
        );

        // Register "Add to Brevo trigger" setting
        register_setting(
            'brevwoo_option_group', // settings group name
            'brevwoo_order_status_trigger', // option name
            [
                'default' => 'completed',
                'sanitize_callback' => [
                    $this,
                    'sanitizeOrderStatusTriggerInput',
                ],
            ]
        );
    }

    /**
     * Render Brevo API connection status notice.
     */
    private function renderBrevoStatusNotice()
    {
        if (!$this->apiClient) {
            return;
        }

        try {
            $this->apiClient->getAccount();
            echo '<div class="notice notice-success notice-alt">
                <p><strong>' .
                esc_html__('Successfully connected to Brevo', 'brevwoo') .
                '</strong></p>
              </div>';
        } catch (Exception $e) {
            echo '<div class="notice notice-error notice-alt">
                <p><strong>' .
                esc_html__('Could not connect to Brevo', 'brevwoo') .
                '</strong></p>
                <p>' .
                esc_html($e->getMessage()) .
                '</p>
              </div>';
        }
    }

    /**
     * Render main description on plugin settings page.
     */
    public function renderSettingsDescription()
    {
        echo '<p>' .
            esc_html__(
                'Provide a Brevo API key below to connect BrevWoo to your Brevo account.',
                'brevwoo'
            ) .
            '</p>';
    }

    /**
     * Render the "Brevo API key" field.
     */
    public function renderApiKeyInput($val)
    {
        $field_id = $val['id'];
        $name = $val['option_name'];
        $value = get_option($name, '');

        printf(
            '<input type="password"
                id="%s"
                name="%s"
                value="%s"
                placeholder="%s"
                class="regular-text"
                autocomplete="off"
                required>',
            esc_attr($field_id),
            esc_attr($name),
            esc_attr($value),
            esc_html__('e.g. xkeysib-XXXXXXXXXX', 'brevwoo')
        );

        printf(
            '<p class="description">%s <a href="https://app.brevo.com/settings/keys/api" target="_blank">%s</a>.</p>',
            esc_html__('Create an API key for BrevWoo in your', 'brevwoo'),
            esc_html__('Brevo account', 'brevwoo')
        );
        printf(
            '<p class="description"><a href="https://developers.brevo.com/docs/getting-started#quick-start"
            target="_blank">%s</a></p>',
            esc_html__('Read the Brevo API key guide', 'brevwoo')
        );
    }

    /**
     * Sanitize "Add to Brevo trigger" select input.
     */
    public function sanitizeOrderStatusTriggerInput($input)
    {
        $valid = ['completed', 'processing', 'pending'];
        if (in_array($input, $valid, true)) {
            return $input;
        }
        return 'completed';
    }

    /**
     * Render "Add to Brevo trigger" select input.
     */
    public function renderOrderStatusTriggerInput($val)
    {
        $field_id = $val['id'];
        $name = $val['option_name'];
        $value = get_option($name, 'completed');

        $options = [
            'completed' => esc_html__('Completed', 'brevwoo'),
            'processing' => esc_html__('Processing', 'brevwoo'),
            'pending' => esc_html__('Pending', 'brevwoo'),
        ];

        echo '<select id="' .
            esc_attr($field_id) .
            '" name="' .
            esc_attr($name) .
            '">';
        foreach ($options as $key => $label) {
            echo '<option value="' .
                esc_attr($key) .
                '"' .
                ($value === $key ? ' selected' : '') .
                '>' .
                esc_html($label) .
                '</option>';
        }
        echo '</select>';

        echo '<p class="description">' .
            esc_html__(
                'Select which WooCommerce order status adds the customer to Brevo.',
                'brevwoo'
            ) .
            '</p>';
    }

    /**
     * Add BrevWoo panel to edit product page sidebar.
     */
    public function addEditProductPanel()
    {
        add_meta_box(
            'brevwoo',
            __('BrevWoo', 'brevwoo'),
            [$this, 'renderEditProductPanelContent'],
            'product',
            'side',
            'default'
        );
    }

    /**
     * Render edit product page BrevWoo panel.
     */
    public function renderEditProductPanelContent($post)
    {
        $brevo_list_ids = get_post_meta(
            $post->ID,
            'brevwoo_brevo_list_ids',
            false
        );

        if (!$this->apiClient) {
            printf(
                '<p>%s</p>',
                sprintf(
                    // translators: %s is a link to the BrevWoo settings page
                    esc_html__(
                        'Enter a Brevo API key on the %s to load your lists.',
                        'brevwoo'
                    ),
                    '<a href="' .
                        esc_url(
                            add_query_arg(
                                'page',
                                $this->plugin_name,
                                get_admin_url() . 'options-general.php'
                            )
                        ) .
                        '">' .
                        esc_html__('BrevWoo settings page', 'brevwoo') .
                        '</a>'
                )
            );
            return;
        }

        try {
            $result = $this->apiClient->getLists();
            $lists = ['' => esc_html__('None (disabled)', 'brevwoo')];
            foreach ($result['lists'] as $list) {
                $lists[$list['id']] = '#' . $list['id'] . ' ' . $list['name'];
            }

            echo '<p class="howto">' .
                esc_html__(
                    'Select the Brevo lists to add customers to when they purchase this product.',
                    'brevwoo'
                ) .
                '<span class="woocommerce-help-tip" style="margin-bottom: 1px;" data-tip="' .
                esc_attr__(
                    'Use your keyboard to select multiple lists e.g. hold "command" on Mac.',
                    'brevwoo'
                ) .
                '"></span>' .
                '</p>';
            echo '<label for="brevwoo_brevo_list_ids" class="hidden">' .
                esc_html__('Brevo Lists', 'brevwoo') .
                '</label>';
            echo '<select id="brevwoo_brevo_list_ids"
                        name="brevwoo_brevo_list_ids[]"
                        style="min-height: 100px; width: 100%;"
                        multiple>';
            foreach ($lists as $id => $name) {
                $selected =
                    (empty($brevo_list_ids) && $id === '') ||
                    in_array($id, $brevo_list_ids)
                        ? ' selected'
                        : '';
                echo '<option value="' .
                    esc_attr($id) .
                    '"' .
                    esc_attr($selected) .
                    '>' .
                    esc_html($name) .
                    '</option>';
            }
            echo '</select>';
        } catch (Exception $e) {
            echo '<div class="notice notice-error notice-alt inline">
                <p><strong>' .
                esc_html__('Error fetching Brevo lists', 'brevwoo') .
                '</strong></p>
                <p>' .
                esc_html($e->getMessage()) .
                '</p>
              </div>';
        }
    }

    /**
     * Save selected Brevo lists to product meta.
     */
    public function saveSelectedLists($post_id)
    {
        // Verify nonce, user permission, and return early if checks fail
        $nonceIsValid =
            isset($_POST['_wpnonce']) &&
            wp_verify_nonce($_POST['_wpnonce'], 'update-post_' . $post_id);
        $userCanEdit = current_user_can('edit_product', $post_id);
        if (!$nonceIsValid || !$userCanEdit) {
            return;
        }

        if (isset($_POST['brevwoo_brevo_list_ids'])) {
            // Sanitize the options
            $brevo_list_ids = array_map(
                'sanitize_text_field',
                $_POST['brevwoo_brevo_list_ids']
            );

            // Remove 'None' option to prevent it being saved
            $brevo_list_ids = array_filter($brevo_list_ids, function ($value) {
                return $value !== '';
            });

            // Delete all current lists (user may be deselecting all)
            delete_post_meta($post_id, 'brevwoo_brevo_list_ids');

            // If any valid lists are selected, save them
            if (!empty($brevo_list_ids)) {
                foreach ($brevo_list_ids as $list_id) {
                    add_post_meta($post_id, 'brevwoo_brevo_list_ids', $list_id);
                }
            }
        }
    }

    /**
     * Add the WooCommerce customer to the product's Brevo lists.
     */
    public function processWcOrder($order_id)
    {
        $order = wc_get_order($order_id);

        foreach ($order->get_items() as $item) {
            $product_id = $item->get_product_id();
            $brevo_list_ids = get_post_meta(
                $product_id,
                'brevwoo_brevo_list_ids',
                false
            );

            if (!empty($brevo_list_ids)) {
                $this->addOrUpdateBrevoContact($order, $brevo_list_ids);
            }
        }
    }

    /**
     * Create or update a Brevo contact.
     * @SuppressWarnings(PHPMD.MissingImport)
     */
    public function addOrUpdateBrevoContact($order, $listIds)
    {
        if (!$this->apiClient) {
            error_log(
                'BrevWoo: Brevo API key not set, cannot add contact to Brevo'
            );
            return;
        }

        // Collect order details
        $email = $order->get_billing_email();
        $first_name = $order->get_billing_first_name();
        $last_name = $order->get_billing_last_name();
        $order_id = $order->get_id();
        $order_total = $order->get_total();
        $order_date = $order->get_date_created()->date('d-m-Y');

        // Create the contact object
        $createContact = new Brevo\Client\Model\CreateContact([
            'email' => $email,
            'updateEnabled' => true,
            'attributes' => [
                'FIRSTNAME' => $first_name,
                'LASTNAME' => $last_name,
                'ORDER_ID' => strval($order_id),
                'ORDER_PRICE' => $order_total,
                'ORDER_DATE' => $order_date,
            ],
            'listIds' => array_map('intval', $listIds), // Ensure listIds are integers
        ]);

        try {
            $this->apiClient->createOrUpdateContact($createContact);
        } catch (Exception $e) {
            error_log(
                'BrevWoo: Error creating or updating Brevo contact: ' .
                    $e->getMessage()
            );
        }
    }

    /**
     * Display admin notice if WooCommerce is not active.
     */
    public function checkRequiredPlugins()
    {
        if (!is_plugin_active('woocommerce/woocommerce.php')) {
            echo '<div class="error"><p><strong>' .
                esc_html__(
                    'BrevWoo requires WooCommerce to be installed and active. You can download ',
                    'brevwoo'
                ) .
                '<a href="https://woocommerce.com/" target="_blank">' .
                esc_html__('WooCommerce', 'brevwoo') .
                '</a>' .
                esc_html__(' here.', 'brevwoo') .
                '</strong></p></div>';
        }
    }

    /**
     * Get the WooCommerce hook to use for adding customer to Brevo lists.
     *
     * @return string Hook name
     */
    public function getWcCheckoutHook()
    {
        $order_status_trigger = get_option(
            'brevwoo_order_status_trigger',
            'completed'
        );

        switch ($order_status_trigger) {
            case 'processing':
                return 'woocommerce_order_status_processing';
            case 'pending':
                return 'woocommerce_order_status_pending';
            case 'completed':
            default:
                return 'woocommerce_order_status_completed';
        }
    }
}
