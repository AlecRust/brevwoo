<?php

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Brevo\Api\Configuration;
use Brevo\Api\Api\ContactsApi;
use GuzzleHttp\Client;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
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
            "<a href=\"$url\">" . __('Settings', 'brevwoo') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    /**
     * Return settings page.
     */
    public function renderSettingsPage()
    {
        ?>
        <div class="wrap">
            <h2><?php echo __('BrevWoo', 'brevwoo'); ?></h2>
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
     * Fetched account details could be rendered with e.g. esc_html($result->getEmail())
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
            $result = $apiInstance->getAccount();
            echo '<div class="notice notice-success notice-alt">';
            echo '<p><strong>' .
                __('Connected to Brevo API', 'brevwoo') .
                '</strong></p>';
            echo '</div>';
        } catch (Exception $e) {
            echo '<div class="notice notice-error notice-alt">';
            echo '<p><strong>' .
                __('Could not connect to Brevo API', 'brevwoo') .
                '</strong></p>';
            echo '<p>' . esc_html($e->getMessage()) . '</p>';
            echo '</div>';
        }
    }

    /**
     * Explanation copy on settings page (between heading and fields).
     */
    public function renderSettingsDescription()
    {
        echo '<p>' .
            __(
                'Connect BrevWoo to your Brevo account using your Brevo API key.',
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
            '<input type="password" id="%s" name="%s" value="%s" class="regular-text">',
            esc_attr($field_id),
            esc_attr($name),
            esc_attr($value)
        );

        printf(
            '<p class="description">%s</p>',
            // translators: %s is a link to the Brevo API key documentation
            sprintf(
                __(
                    'Enter your Brevo API key. %s for more information.',
                    'brevwoo'
                ),
                '<a href="https://developers.brevo.com/docs/getting-started#quick-start" target="_blank">' .
                    __('See docs', 'brevwoo') .
                    '</a>'
            )
        );
    }

    /**
     * Add BrevWoo panel to edit product page.
     */
    public function addEditProductPanel()
    {
        add_meta_box(
            'brevwoo_options',
            __('BrevWoo', 'brevwoo'),
            [$this, 'renderEditProductPanelContent'],
            'product',
            'side', // Where the box should show ('normal', 'side', 'advanced')
            'default'
        );
    }

    /**
     * Contents of edit product page BrevWoo panel.
     * @SuppressWarnings(PHPMD.MissingImport)
     */
    public function renderEditProductPanelContent($post)
    {
        $brevo_list_id = get_post_meta($post->ID, 'brevo_list_id', true);
        $brevo_api_key = get_option('brevwoo_brevo_api_key', '');

        if (empty($brevo_api_key)) {
            printf(
                '<p>%s</p>',
                // translators: %s is a link to the BrevWoo settings page
                sprintf(
                    __('Please %s on the BrevWoo settings page.', 'brevwoo'),
                    '<a href="' .
                        esc_url(
                            add_query_arg(
                                'page',
                                $this->plugin_name,
                                get_admin_url() . 'options-general.php'
                            )
                        ) .
                        '">' .
                        __('enter your Brevo API key', 'brevwoo') .
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
            $result = $apiInstance->getLists(50, 0);
            $lists = ['' => 'Select a Brevo list'];
            foreach ($result['lists'] as $list) {
                $lists[$list['id']] = '#' . $list['id'] . ' ' . $list['name'];
            }

            echo '<div class="options_group">';
            echo '<p class="form-field brevo_list_id_field">';
            echo '<label for="brevo_list_id">' .
                __('Add purchaser to Brevo list', 'brevwoo') .
                '</label>';
            echo '<select id="brevo_list_id" name="brevo_list_id" class="select short">';
            foreach ($lists as $id => $name) {
                echo '<option value="' .
                    esc_attr($id) .
                    '"' .
                    selected($brevo_list_id, $id, false) .
                    '>' .
                    esc_html($name) .
                    '</option>';
            }
            echo '</select>';
            echo '</p>';
            echo '</div>';
        } catch (Exception $e) {
            echo '<p>' .
                // translators: %s is the error message
                sprintf(
                    __('Error fetching Brevo lists: %s', 'brevwoo'),
                    esc_html($e->getMessage())
                ) .
                '</p>';
        }
    }

    /**
     * Save selected Brevo list to product meta.
     */
    public function saveProductMeta($post_id)
    {
        if (isset($_POST['brevo_list_id'])) {
            update_post_meta(
                $post_id,
                'brevo_list_id',
                sanitize_text_field($_POST['brevo_list_id'])
            );
        }
    }

    /**
     * Catch the product purchase event and add the purchaser to the Brevo list.
     */
    public function processWcProductPurchase($order_id)
    {
        $order = wc_get_order($order_id);

        foreach ($order->get_items() as $item) {
            $product_id = $item->get_product_id();
            $brevo_list_id = get_post_meta($product_id, 'brevo_list_id', true);

            if (!empty($brevo_list_id)) {
                $this->addOrUpdateBrevoContact($order, [$brevo_list_id]);
            }
        }
    }

    /**
     * Add or update a Brevo contact.
     * https://developers.brevo.com/reference/createcontact
     * @SuppressWarnings(PHPMD.MissingImport)
     */
    public function addOrUpdateBrevoContact($order, $listIds)
    {
        $brevo_api_key = get_option('brevwoo_brevo_api_key', '');

        if ($brevo_api_key) {
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
                $result = $apiInstance->createContact($createContact);
                // Log the result for debugging purposes
                // error_log(print_r($result, true));
            } catch (Exception $e) {
                error_log(
                    'Error creating or updating Brevo contact: ' .
                        $e->getMessage()
                );
            }
        }
    }
}
