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
     * @var BrevWooApiClient|null
     */
    protected $apiClient;

    /**
     * Initialize the class and set its properties.
     *
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->initializeApiClient();
    }

    /**
     * Initialize the API client
     * @SuppressWarnings(PHPMD.MissingImport)
     */
    private function initializeApiClient()
    {
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
        $url = esc_url($this->getPluginSettingsUrl());
        $settings_link = "<a href=\"$url\">" . esc_html__('Settings', 'brevwoo') . '</a>';
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
            <?php if ($this->apiClient) {
                $this->renderBrevoStatusNotice();
            } ?>
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

        // Add "Default Brevo lists" setting field
        add_settings_field(
            'brevwoo_default_lists', // HTML id
            __('Default Brevo lists', 'brevwoo'), // field title
            [$this, 'renderDefaultListsInput'], // callback
            'brevwoo-admin', // page
            'brevwoo_setting_section', // section
            [
                'id' => 'brevwoo_default_lists',
                'option_name' => 'brevwoo_default_lists',
            ]
        );

        // Register "Default Brevo lists" setting
        register_setting(
            'brevwoo_option_group', // settings group name
            'brevwoo_default_lists', // option name
            [
                'default' => [],
                'sanitize_callback' => [$this, 'sanitizeListsInput'],
            ]
        );

        // Add "Add to Brevo trigger" setting field
        add_settings_field(
            'brevwoo_order_status', // HTML id
            __('Add to Brevo trigger', 'brevwoo'), // field title
            [$this, 'renderOrderStatusInput'], // callback
            'brevwoo-admin', // page
            'brevwoo_setting_section', // section
            [
                'id' => 'brevwoo_order_status',
                'option_name' => 'brevwoo_order_status',
            ]
        );

        // Register "Add to Brevo trigger" setting
        register_setting(
            'brevwoo_option_group', // settings group name
            'brevwoo_order_status', // option name
            [
                'default' => 'completed',
                'sanitize_callback' => [$this, 'sanitizeOrderStatusInput'],
            ]
        );
    }

    /**
     * Render main description on plugin settings page.
     */
    public function renderSettingsDescription()
    {
        printf(
            '<p>%s. <a href="%s" target="_blank">%s</a> %s.</p>',
            esc_html__('Control the global settings for BrevWoo below', 'brevwoo'),
            esc_url(admin_url('edit.php?post_type=product')),
            esc_html__('Edit a WooCommerce product', 'brevwoo'),
            esc_html__('to set product-specific lists', 'brevwoo')
        );
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
                autocomplete="off">',
            esc_attr($field_id),
            esc_attr($name),
            esc_attr($value),
            esc_html__('e.g. xkeysib-XXXXXXXXXX', 'brevwoo')
        );

        printf(
            '<p class="description">%s <a href="https://app.brevo.com/settings/keys/api" target="_blank">%s</a>.</p>',
            esc_html__('Set an API key created for BrevWoo in your', 'brevwoo'),
            esc_html__('Brevo account', 'brevwoo')
        );
        printf(
            '<p class="description"><a href="https://developers.brevo.com/docs/getting-started#quick-start"
            target="_blank">%s</a></p>',
            esc_html__('Brevo API key documentation', 'brevwoo')
        );
    }

    /**
     * Render the "Default Brevo lists" field.
     */
    public function renderDefaultListsInput($val)
    {
        $field_id = $val['id'];
        $name = $val['option_name'];
        $default_brevo_lists = get_option($name, []);
        $brevo_api_key = get_option('brevwoo_brevo_api_key', '');

        if (empty($brevo_api_key)) {
            printf(
                '<p class="description">%s</p>',
                esc_html__('Set an API key to load lists.', 'brevwoo')
            );
            return;
        }

        if (!$this->apiClient) {
            printf('<p class="description">%s</p>', esc_html__('Could not load lists', 'brevwoo'));
            return;
        }

        try {
            $listsResult = $this->apiClient->getLists();
            $allFoldersResult = $this->apiClient->getFolders();
            $this->renderSelectListsInput(
                $field_id, // HTML ID and name attribute
                $default_brevo_lists, // Currently selected list IDs
                $listsResult['lists'], // All Brevo lists from API
                $allFoldersResult['folders'], // All Brevo folders from API
                __('Disabled (product-specific lists only)', 'brevwoo') // Disabled option label
            );
            printf(
                '<p class="description">%s</p>',
                esc_html__(
                    'Select the Brevo lists customers who buy any product will be added to.',
                    'brevwoo'
                )
            );
            printf(
                '<p class="description brevwoo-select-lists-help-note">' .
                    '<span class="dashicons dashicons-info-outline" style="font-size: 19px;"></span> ' .
                    '%s <kbd>Cmd</kbd> %s <kbd>Ctrl</kbd> %s</p>',
                esc_html__('Hold ', 'brevwoo'),
                esc_html__('or ', 'brevwoo'),
                esc_html__('to select multiple lists or deselect lists', 'brevwoo')
            );
        } catch (Exception $e) {
            printf('<p class="description">%s</p>', esc_html__('Could not load lists', 'brevwoo'));
        }
    }

    /**
     * Sanitize lists multi-select input.
     */
    public function sanitizeListsInput($input)
    {
        // Ensure $input is an array
        $input = (array) $input;

        // Sanitize each element in the array
        $input = array_map('intval', $input);

        // Strip out any non-list IDs (including 'Disabled' option)
        $input = array_filter($input);

        // Re-index the array
        return array_values($input);
    }

    /**
     * Render "Add to Brevo trigger" select input.
     */
    public function renderOrderStatusInput($val)
    {
        $field_id = $val['id'];
        $name = $val['option_name'];
        $value = get_option($name, 'completed');

        $options = [
            'completed' => esc_html__('Completed', 'brevwoo'),
            'processing' => esc_html__('Processing', 'brevwoo'),
            'pending' => esc_html__('Pending', 'brevwoo'),
        ];

        echo '<select id="' . esc_attr($field_id) . '" name="' . esc_attr($name) . '">';
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

        printf(
            '<p class="description">%s</p>',
            esc_html__('Select which WooCommerce order status adds a customer to Brevo.', 'brevwoo')
        );
    }

    /**
     * Sanitize "Add to Brevo trigger" select input.
     */
    public function sanitizeOrderStatusInput($input)
    {
        $valid = ['completed', 'processing', 'pending'];
        if (in_array($input, $valid, true)) {
            return $input;
        }
        return 'completed';
    }

    /**
     * Add BrevWoo panel to edit product page sidebar.
     */
    public function addEditProductPanel()
    {
        add_meta_box(
            'brevwoo',
            __('BrevWoo', 'brevwoo'),
            [$this, 'renderEditProductPanel'],
            'product',
            'side',
            'default'
        );
    }

    /**
     * Render edit product page BrevWoo panel.
     */
    public function renderEditProductPanel($post)
    {
        $product_lists = get_post_meta($post->ID, '_brevwoo_product_lists', true);

        // Initialize as empty array if the option is not yet set
        if (!is_array($product_lists)) {
            $product_lists = [];
        }

        if (!$this->apiClient) {
            printf(
                '<p>%s</p>',
                sprintf(
                    // translators: %s is a link to the BrevWoo settings page
                    esc_html__('Enter a Brevo API key in the %s to load lists.', 'brevwoo'),
                    '<a href="' .
                        esc_url($this->getPluginSettingsUrl()) .
                        '">' .
                        esc_html__('BrevWoo settings', 'brevwoo') .
                        '</a>'
                )
            );
            return;
        }

        try {
            $listsResult = $this->apiClient->getLists();
            $allFoldersResult = $this->apiClient->getFolders();
            printf(
                '<p class="howto">%s' .
                    '<span class="woocommerce-help-tip" style="margin-bottom: 2px;" data-tip="%s"></span>' .
                    '</p>',
                esc_html__(
                    'Select Brevo lists below to add customers to when they buy this product.',
                    'brevwoo'
                ),
                esc_attr__('Hold Cmd or Ctrl to select multiple lists or deselect lists', 'brevwoo')
            );
            printf(
                '<label for="brevwoo_product_lists" class="hidden">%s</label>',
                esc_html__('Product Brevo Lists', 'brevwoo')
            );
            $this->renderSelectListsInput(
                'brevwoo_product_lists', // HTML ID and name attribute
                $product_lists, // Currently selected list IDs
                $listsResult['lists'], // All Brevo lists from API
                $allFoldersResult['folders'], // All Brevo folders from API
                __('Disabled (default lists only)', 'brevwoo') // Disabled option label
            );
            printf(
                '<p class="howto">%s</p>',
                sprintf(
                    // translators: %s is a link to the BrevWoo settings page
                    esc_html__('Select default lists in %s.', 'brevwoo'),
                    '<a href="' .
                        esc_url($this->getPluginSettingsUrl()) .
                        '">' .
                        esc_html__('BrevWoo settings', 'brevwoo') .
                        '</a>'
                )
            );
        } catch (Exception $e) {
            $message =
                '<p><strong>' .
                esc_html__('Error fetching Brevo lists', 'brevwoo') .
                '</strong></p><p>' .
                esc_html($e->getMessage()) .
                '</p>';
            wp_admin_notice($message, [
                'type' => 'error',
                'paragraph_wrap' => false,
                'additional_classes' => ['inline'],
            ]);
        }
    }

    /**
     * Save selected Brevo lists to product meta.
     */
    public function saveSelectedLists($post_id)
    {
        // Verify nonce, user permission, and return early if checks fail
        $nonce_valid =
            isset($_POST['_wpnonce']) &&
            wp_verify_nonce($_POST['_wpnonce'], 'update-post_' . $post_id);
        $can_edit = current_user_can('edit_product', $post_id);
        if (!$nonce_valid || !$can_edit) {
            return;
        }

        if (isset($_POST['brevwoo_product_lists'])) {
            // Sanitize the input
            $product_lists = $this->sanitizeListsInput($_POST['brevwoo_product_lists']);

            // Save the list of IDs as a meta entry, overwriting any existing value
            update_post_meta($post_id, '_brevwoo_product_lists', $product_lists);
        }
    }

    /**
     * Get the WooCommerce hook to use for adding customer to Brevo lists.
     *
     * @return string Hook name
     */
    public function getWcCheckoutHook()
    {
        $order_status = get_option('brevwoo_order_status', 'completed');

        switch ($order_status) {
            case 'processing':
                return 'woocommerce_order_status_processing';
            case 'pending':
                return 'woocommerce_order_status_pending';
            case 'completed':
            default:
                return 'woocommerce_order_status_completed';
        }
    }

    /**
     * Enqueue custom admin styles.
     */
    public function enqueueAdminStyles()
    {
        wp_enqueue_style(
            'brevwoo-admin-styles',
            plugin_dir_url(__FILE__) . '../style.css',
            [], // no stylesheet dependencies
            $this->version // include plugin version in query string
        );
    }

    /**
     * Display admin notice if WooCommerce is not active.
     */
    public function renderRequiredPluginNotice()
    {
        if (!is_plugin_active('woocommerce/woocommerce.php')) {
            $message =
                '<p><strong>' .
                esc_html__(
                    'BrevWoo requires WooCommerce to be installed and active. You can download ',
                    'brevwoo'
                ) .
                '<a href="https://woocommerce.com/" target="_blank">' .
                esc_html__('WooCommerce', 'brevwoo') .
                '</a>' .
                esc_html__(' here.', 'brevwoo') .
                '</strong></p>';
            wp_admin_notice($message, [
                'type' => 'error',
                'paragraph_wrap' => false,
            ]);
        }
    }

    /**
     * Add the WooCommerce customer to the product's Brevo lists.
     */
    public function processWcOrder($order_id)
    {
        $order = wc_get_order($order_id);
        $default_lists = array_map('intval', get_option('brevwoo_default_lists', []));

        foreach ($order->get_items() as $item) {
            $product_id = $item->get_product_id();
            $product_lists = array_map(
                'intval',
                get_post_meta($product_id, '_brevwoo_product_lists', true)
            );
            $combined_lists = array_unique(array_merge($default_lists, $product_lists));

            if (!empty($combined_lists)) {
                $this->createBrevoContact($order, $combined_lists);
            }
        }
    }

    /**
     * Render a multiple select dropdown for Brevo lists, grouped by folder.
     *
     * @param string $fieldId The HTML id and name attribute for the input.
     * @param array $selectedLists An array of the currently selected list IDs.
     * @param array $allLists Brevo API lists response.
     * @param array $allFolders Brevo API folders response.
     * @param string $disabledLabel The label for the disabled (default) option.
     */
    private function renderSelectListsInput(
        $fieldId,
        $selectedLists,
        $allLists,
        $allFolders,
        $disabledLabel
    ) {
        // Create a map of folder IDs to folder names
        $folderMap = [];
        foreach ($allFolders as $folder) {
            $folderMap[$folder['id']] = $folder['name'];
        }

        // Group lists by folder
        $groupedLists = [];
        foreach ($allLists as $list) {
            $groupedLists[$list['folderId']][] = $list;
        }

        // Start the select element
        echo '<select id="' .
            esc_attr($fieldId) .
            '" name="' .
            esc_attr($fieldId) .
            '[]" class="brevwoo-select-lists-input" multiple>';

        // The initial disabled/default option
        echo '<option value=""' .
            (empty($selectedLists) ? ' selected' : '') .
            '>' .
            esc_html($disabledLabel) .
            '</option>';

        // Render grouped lists
        foreach ($groupedLists as $folderId => $lists) {
            $folderName = isset($folderMap[$folderId])
                ? $folderMap[$folderId]
                : __('No folder', 'brevwoo');
            echo '<optgroup label="' . esc_attr($folderName) . '">';
            foreach ($lists as $list) {
                $selected = in_array($list['id'], $selectedLists) ? ' selected' : '';
                echo '<option value="' .
                    esc_attr($list['id']) .
                    '"' .
                    esc_attr($selected) .
                    '>' .
                    esc_html('#' . $list['id'] . ' ' . $list['name']) .
                    '</option>';
            }
            echo '</optgroup>';
        }
        echo '</select>';
    }

    /**
     * Return the URL for the plugin settings page.
     */
    private function getPluginSettingsUrl()
    {
        return add_query_arg('page', $this->plugin_name, get_admin_url() . 'options-general.php');
    }

    /**
     * Create or update a Brevo contact, including the list IDs to add them to.
     * @SuppressWarnings(PHPMD.MissingImport)
     */
    private function createBrevoContact($order, $list_ids)
    {
        if (!$this->apiClient) {
            error_log('BrevWoo: Brevo API key not set, cannot add contact to Brevo');
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
            'listIds' => $list_ids,
        ]);

        try {
            $this->apiClient->createContact($createContact);
            $this->logContactCreated($email, $list_ids, strval($order_id));
        } catch (Exception $e) {
            error_log('BrevWoo: Error creating Brevo contact: ' . $e->getMessage());
        }
    }

    /**
     * Render Brevo API connection status notice.
     */
    private function renderBrevoStatusNotice()
    {
        try {
            $this->apiClient->getAccount();
            $message =
                '<p><strong>' .
                esc_html__('Successfully connected to Brevo', 'brevwoo') .
                '</strong></p>';
            wp_admin_notice($message, [
                'type' => 'success',
                'paragraph_wrap' => false,
                'additional_classes' => ['notice-alt'],
            ]);
        } catch (Exception $e) {
            $message =
                '<p><strong>' .
                esc_html__('Error connecting to Brevo', 'brevwoo') .
                '</strong></p><p>' .
                esc_html($e->getMessage()) .
                '</p>';
            wp_admin_notice($message, [
                'type' => 'error',
                'paragraph_wrap' => false,
            ]);
        }
    }

    /**
     * Log Brevo contact add to Activity Log plugin (if installed).
     */
    private function logContactCreated($email, $list_ids, $order_id)
    {
        if (!function_exists('aal_insert_log')) {
            return;
        }

        $log_message = sprintf(
            '%s added to Brevo lists %s via order #%s',
            $email,
            implode(
                ', ',
                array_map(function ($list_id) {
                    return '#' . $list_id;
                }, $list_ids)
            ),
            $order_id
        );

        aal_insert_log([
            'action' => 'complete',
            'object_type' => 'Users',
            'object_subtype' => 'BrevWoo',
            'object_name' => $log_message,
        ]);
    }
}
