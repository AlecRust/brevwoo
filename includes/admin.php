<?php

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Brevo\Api\Configuration;
use Brevo\Api\Api\ContactsApi;
use GuzzleHttp\Client;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks.
 *
 * @link       https://www.alecrust.com/
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
     * Initialize the class and set its properties.
     *
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
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
            <?php $this->renderBrevoConnectionStatus(); ?>
            <form method="post" action="options.php">
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
            'brevo_api_key', // HTML id
            __('Brevo API key', 'brevwoo'), // field title
            [$this, 'renderApiKeyInput'], // callback
            'brevwoo-admin', // page
            'brevwoo_setting_section', // section
            [
                'id' => 'brevo_api_key',
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
    }

    /**
     * Render Brevo API connection status notice.
     * https://developers.brevo.com/reference/getaccount
     * @SuppressWarnings(PHPMD.MissingImport)
     */
    private function renderBrevoConnectionStatus()
    {
        $brevo_api_key = get_option('brevwoo_brevo_api_key', '');
        if (empty($brevo_api_key)) {
            return;
        }

        $config = Brevo\Client\Configuration::getDefaultConfiguration()->setApiKey(
            'api-key',
            $brevo_api_key
        );
        $apiInstance = new Brevo\Client\Api\AccountApi(
            new GuzzleHttp\Client(),
            $config
        );

        try {
            $apiInstance->getAccount();
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
     * Explanation copy on settings page (between heading and fields).
     */
    public function renderSettingsDescription()
    {
        echo '<p>' .
            esc_html__(
                'Provide a Brevo API key below to connect BrevWoo to a Brevo account.',
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
            '<input type="password" id="%s" name="%s" value="%s" placeholder="%s" class="regular-text">',
            esc_attr($field_id),
            esc_attr($name),
            esc_attr($value),
            esc_html__('e.g. xkeysib-XXXXXXXXXX', 'brevwoo')
        );

        printf(
            '<p class="description">%s</p>',
            sprintf(
                // translators: 1: Link to the user's Brevo account, 2: Link to the Brevo documentation
                esc_html__(
                    'Create an API key for BrevWoo in your %1$s (%2$s).',
                    'brevwoo'
                ),
                '<a href="https://app.brevo.com/settings/keys/api" target="_blank">' .
                    esc_html__('Brevo account', 'brevwoo') .
                    '</a>',
                '<a href="https://developers.brevo.com/docs/getting-started#quick-start" target="_blank">' .
                    esc_html__('learn more', 'brevwoo') .
                    '</a>'
            )
        );
    }

    /**
     * Add BrevWoo panel to edit product page sidebar.
     */
    public function addEditProductPanel()
    {
        add_meta_box(
            'brevwoo_options',
            __('BrevWoo', 'brevwoo'),
            [$this, 'renderEditProductPanelContent'],
            'product',
            'side',
            'default'
        );
    }

    /**
     * Render edit product page BrevWoo panel.
     * https://developers.brevo.com/reference/getlists-1
     * @SuppressWarnings(PHPMD.MissingImport)
     */
    public function renderEditProductPanelContent($post)
    {
        $brevo_list_ids = get_post_meta($post->ID, 'brevo_list_ids', false);
        $brevo_api_key = get_option('brevwoo_brevo_api_key', '');

        if (empty($brevo_api_key)) {
            printf(
                '<p>%s</p>',
                sprintf(
                    // translators: %s is a link to the BrevWoo settings page
                    esc_html__(
                        'Please %s on the BrevWoo settings page.',
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
                        esc_html__('enter your Brevo API key', 'brevwoo') .
                        '</a>'
                )
            );
            return;
        }

        $config = Brevo\Client\Configuration::getDefaultConfiguration()->setApiKey(
            'api-key',
            $brevo_api_key
        );
        $apiInstance = new Brevo\Client\Api\ContactsApi(
            new \GuzzleHttp\Client(),
            $config
        );

        try {
            $limit = 50;
            $offset = 0;
            $result = $apiInstance->getLists($limit, $offset);
            $lists = ['' => esc_html__('None (disabled)', 'brevwoo')];
            foreach ($result['lists'] as $list) {
                $lists[$list['id']] = '#' . $list['id'] . ' ' . $list['name'];
            }

            echo '<p class="howto">' .
                esc_html__(
                    'Select the Brevo list(s) to add customers to when they purchase this product.',
                    'brevwoo'
                ) .
                '</p>';
            echo '<label for="brevo_list_ids" class="hidden">' .
                esc_html__('Brevo Lists', 'brevwoo') .
                '</label>';
            echo '<select id="brevo_list_ids"
                        name="brevo_list_ids[]"
                        style="min-height: 100px; width: 100%;"
                        multiple>';
            foreach ($lists as $id => $name) {
                $selected = in_array($id, $brevo_list_ids) ? ' selected' : '';
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
        if (!wp_verify_nonce($_POST['_wpnonce'], 'update-post_' . $post_id)) {
            return;
        }

        if (isset($_POST['brevo_list_ids'])) {
            $brevo_list_ids = array_map(
                'sanitize_text_field',
                $_POST['brevo_list_ids']
            );

            // Delete existing meta to avoid duplicates
            delete_post_meta($post_id, 'brevo_list_ids');

            // Add each new value separately to store them as an array
            foreach ($brevo_list_ids as $list_id) {
                add_post_meta($post_id, 'brevo_list_ids', $list_id);
            }
        }
    }

    /**
     * Add the WooCommerce customer to the product's Brevo lists.
     */
    public function processWcOrderCompleted($order_id)
    {
        $order = wc_get_order($order_id);

        foreach ($order->get_items() as $item) {
            $product_id = $item->get_product_id();
            $brevo_list_ids = get_post_meta(
                $product_id,
                'brevo_list_ids',
                false
            );

            if (!empty($brevo_list_ids)) {
                $this->addOrUpdateBrevoContact($order, $brevo_list_ids);
            }
        }
    }

    /**
     * Create or update a Brevo contact.
     * https://developers.brevo.com/reference/createcontact
     * @SuppressWarnings(PHPMD.MissingImport)
     */
    public function addOrUpdateBrevoContact($order, $listIds)
    {
        $brevo_api_key = get_option('brevwoo_brevo_api_key', '');
        if (empty($brevo_api_key)) {
            error_log(
                'BrevWoo: Brevo API key not set, cannot add contact to Brevo'
            );
            return;
        }

        $config = Brevo\Client\Configuration::getDefaultConfiguration()->setApiKey(
            'api-key',
            $brevo_api_key
        );
        $apiInstance = new Brevo\Client\Api\ContactsApi(
            new \GuzzleHttp\Client(),
            $config
        );

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
            $apiInstance->createContact($createContact);
        } catch (Exception $e) {
            error_log(
                'BrevWoo: Error creating or updating Brevo contact: ' .
                    $e->getMessage()
            );
        }
    }
}
