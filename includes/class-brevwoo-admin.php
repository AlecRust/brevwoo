<?php
/**
 * Admin-specific functionality of the plugin.
 *
 * @package    BrevWoo
 * @subpackage BrevWoo/includes
 * @link       https://github.com/AlecRust/brevwoo
 */

/**
 * The admin-specific functionality of the plugin.
 */
class BrevWoo_Admin {

	/**
	 * The plugin ID.
	 *
	 * @var string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The plugin version.
	 *
	 * @var string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * API client for Brevo.
	 *
	 * @var BrevWoo_ApiClient|null
	 */
	protected $api_client;

	/**
	 * WooCommerce logger.
	 *
	 * @var object
	 */
	protected $wc_logger;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->initialize_api_client();
	}

	/**
	 * Initialize the API client
	 *
	 * @SuppressWarnings(PHPMD.MissingImport)
	 *
	 * @return void
	 */
	private function initialize_api_client() {
		$brevo_api_key = get_option( 'brevwoo_brevo_api_key', '' );
		if ( ! empty( $brevo_api_key ) ) {
			require_once plugin_dir_path( __FILE__ ) . 'class-brevwoo-apiclient.php';
			$this->api_client = new BrevWoo_ApiClient( $brevo_api_key );
		}
	}

	/**
	 * Initialize the WooCommerce logger.
	 *
	 * @return void
	 */
	public function initialize_wc_logger() {
		if ( ! function_exists( 'wc_get_logger' ) ) {
			return;
		}

		$this->wc_logger = wc_get_logger();
	}

	/**
	 * Register settings page in admin.
	 *
	 * @return void
	 */
	public function add_menu_item() {
		add_options_page(
			__( 'BrevWoo', 'brevwoo' ), // Display name.
			__( 'BrevWoo', 'brevwoo' ), // Menu name.
			'manage_options', // Capability.
			$this->plugin_name, // Menu slug.
			array( $this, 'render_settings_page' ) // Callback.
		);
	}

	/**
	 * Add link to plugin settings on Plugins page.
	 *
	 * @param array<string> $links Array of plugin action links.
	 * @return array<string> Modified array of plugin action links.
	 */
	public function add_settings_link( $links ) {
		$url           = esc_url( $this->get_plugin_settings_url() );
		$settings_link =
			"<a href=\"$url\">" . esc_html__( 'Settings', 'brevwoo' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}

	/**
	 * Render settings page.
	 *
	 * @return void
	 */
	public function render_settings_page() {
		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'BrevWoo', 'brevwoo' ); ?></h2>
			<?php
			if ( $this->api_client ) {
				$this->render_brevo_status_notice();
			}
			?>
			<form action="options.php" method="post">
				<?php
				settings_fields( 'brevwoo_option_group' );
				do_settings_sections( 'brevwoo-admin' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Register settings page options in admin.
	 *
	 * @return void
	 */
	public function settings_page_init() {
		// Add settings section.
		add_settings_section(
			'brevwoo_setting_section', // HTML id.
			__( 'Settings', 'brevwoo' ), // title.
			array( $this, 'render_settings_description' ), // callback.
			'brevwoo-admin' // page.
		);

		// Add "Brevo API key" setting field.
		add_settings_field(
			'brevwoo_brevo_api_key', // HTML id.
			__( 'Brevo API key', 'brevwoo' ), // field title.
			array( $this, 'render_api_key_input' ), // callback.
			'brevwoo-admin', // page.
			'brevwoo_setting_section', // section.
			array(
				'id'          => 'brevwoo_brevo_api_key',
				'option_name' => 'brevwoo_brevo_api_key',
			)
		);

		// Register "Brevo API key" setting.
		register_setting(
			'brevwoo_option_group', // settings group name.
			'brevwoo_brevo_api_key', // option name.
			array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		// Add "Default Brevo lists" setting field.
		add_settings_field(
			'brevwoo_default_lists', // HTML id.
			__( 'Default Brevo lists', 'brevwoo' ), // field title.
			array( $this, 'render_default_lists_input' ), // callback.
			'brevwoo-admin', // page.
			'brevwoo_setting_section', // section.
			array(
				'id'          => 'brevwoo_default_lists',
				'option_name' => 'brevwoo_default_lists',
			)
		);

		// Register "Default Brevo lists" setting.
		register_setting(
			'brevwoo_option_group', // settings group name.
			'brevwoo_default_lists', // option name.
			array(
				'default'           => array(),
				'sanitize_callback' => array( $this, 'sanitize_lists_input' ),
			)
		);

		// Add "Order status trigger" setting field.
		add_settings_field(
			'brevwoo_order_status', // HTML id.
			__( 'Order status trigger', 'brevwoo' ), // field title.
			array( $this, 'render_order_status_input' ), // callback.
			'brevwoo-admin', // page.
			'brevwoo_setting_section', // section.
			array(
				'id'          => 'brevwoo_order_status',
				'option_name' => 'brevwoo_order_status',
			)
		);

		// Register "Order status trigger" setting.
		register_setting(
			'brevwoo_option_group', // settings group name.
			'brevwoo_order_status', // option name.
			array(
				'default'           => 'completed',
				'sanitize_callback' => array( $this, 'sanitize_order_status_input' ),
			)
		);

		// Add "Debug logging" setting field.
		add_settings_field(
			'brevwoo_debug_logging', // HTML id.
			__( 'Debug logging', 'brevwoo' ), // field title.
			array( $this, 'render_debug_logging_input' ), // callback.
			'brevwoo-admin', // page.
			'brevwoo_setting_section', // section.
			array(
				'id'          => 'brevwoo_debug_logging',
				'option_name' => 'brevwoo_debug_logging',
			)
		);

		// Register "Debug logging" checkbox setting.
		register_setting(
			'brevwoo_option_group', // settings group name.
			'brevwoo_debug_logging', // option name.
			array(
				'default'           => '0',
				'sanitize_callback' => array( $this, 'sanitize_checkbox_input' ),
			)
		);
	}

	/**
	 * Render main description on plugin settings page.
	 *
	 * @return void
	 */
	public function render_settings_description() {
		printf(
			'<p>%s. <a href="%s" target="_blank">%s</a> %s.</p>',
			esc_html__(
				'Control the global settings for BrevWoo below',
				'brevwoo'
			),
			esc_url( admin_url( 'edit.php?post_type=product' ) ),
			esc_html__( 'Edit a WooCommerce product', 'brevwoo' ),
			esc_html__( 'to set product-specific lists', 'brevwoo' )
		);
	}

	/**
	 * Render the "Brevo API key" field.
	 *
	 * @param array<string> $val Field arguments.
	 * @return void
	 */
	public function render_api_key_input( $val ) {
		$field_id = $val['id'];
		$name     = $val['option_name'];
		$value    = get_option( $name, '' );

		printf(
			'<input type="password"
                id="%s"
                name="%s"
                value="%s"
                placeholder="%s"
                class="regular-text"
                autocomplete="off">',
			esc_attr( $field_id ),
			esc_attr( $name ),
			esc_attr( $value ),
			esc_html__( 'e.g. xkeysib-XXXXXXXXXX', 'brevwoo' )
		);

		printf(
			'<p class="description">%s <a href="https://app.brevo.com/settings/keys/api" target="_blank">%s</a>.</p>',
			esc_html__( 'Set an API key created for BrevWoo in your', 'brevwoo' ),
			esc_html__( 'Brevo account', 'brevwoo' )
		);
		printf(
			'<p class="description"><a href="https://developers.brevo.com/docs/getting-started#quick-start"
            target="_blank">%s</a></p>',
			esc_html__( 'Brevo API key documentation', 'brevwoo' )
		);
	}

	/**
	 * Render the "Default Brevo lists" field.
	 *
	 * @param array<string> $val Field arguments.
	 * @return void
	 */
	public function render_default_lists_input( $val ) {
		$field_id            = $val['id'];
		$name                = $val['option_name'];
		$default_brevo_lists = get_option( $name, array() );
		$brevo_api_key       = get_option( 'brevwoo_brevo_api_key', '' );

		if ( empty( $brevo_api_key ) ) {
			printf(
				'<p class="description">%s</p>',
				esc_html__( 'Set an API key to load lists.', 'brevwoo' )
			);
			return;
		}

		if ( ! $this->api_client ) {
			printf(
				'<p class="description">%s</p>',
				esc_html__( 'Could not load lists', 'brevwoo' )
			);
			return;
		}

		try {
			$all_lists_result   = $this->api_client->get_lists();
			$all_folders_result = $this->api_client->get_folders();
			$this->render_select_lists_input(
				$field_id, // HTML ID and name attribute.
				$default_brevo_lists, // Currently selected list IDs.
				$all_lists_result->getLists(), // All Brevo lists from API.
				$all_folders_result->getFolders(), // All Brevo folders from API.
				__( 'Disabled (product-specific lists only)', 'brevwoo' ) // Disabled option label.
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
				esc_html__( 'Hold ', 'brevwoo' ),
				esc_html__( 'or ', 'brevwoo' ),
				esc_html__(
					'to select multiple lists or deselect lists',
					'brevwoo'
				)
			);
		} catch ( Exception $e ) {
			printf(
				'<p class="description">%s</p>',
				esc_html__( 'Could not load lists', 'brevwoo' )
			);
		}
	}

	/**
	 * Sanitize lists multi-select input.
	 *
	 * @param array<string> $input The input from the form.
	 * @return array<int> Sanitized input.
	 */
	public function sanitize_lists_input( $input ) {
		$input = (array) $input;
		return array_values(
			array_filter( array_map( 'intval', $input ) )
		);
	}

	/**
	 * Render "Order status trigger" select input.
	 *
	 * @param array<string> $val Field arguments.
	 * @return void
	 */
	public function render_order_status_input( $val ) {
		$field_id = $val['id'];
		$name     = $val['option_name'];
		$value    = get_option( $name, 'completed' );

		$options = array(
			'completed'  => esc_html__( 'Completed', 'brevwoo' ),
			'processing' => esc_html__( 'Processing', 'brevwoo' ),
			'pending'    => esc_html__( 'Pending', 'brevwoo' ),
		);

		echo '<select id="' .
			esc_attr( $field_id ) .
			'" name="' .
			esc_attr( $name ) .
			'">';
		foreach ( $options as $key => $label ) {
			echo '<option value="' .
				esc_attr( $key ) .
				'"' .
				( $value === $key ? ' selected' : '' ) .
				'>' .
				esc_html( $label ) .
				'</option>';
		}
		echo '</select>';

		printf(
			'<p class="description">%s</p>',
			esc_html__(
				'Select which WooCommerce order status adds a customer to Brevo.',
				'brevwoo'
			)
		);
	}

	/**
	 * Sanitize "Order status trigger" select input.
	 *
	 * @param string $input The input from the form.
	 * @return string Sanitized input.
	 */
	public function sanitize_order_status_input( $input ) {
		$valid = array( 'completed', 'processing', 'pending' );
		if ( in_array( $input, $valid, true ) ) {
			return $input;
		}
		return 'completed';
	}

	/**
	 * Render "Debug logging" checkbox input.
	 *
	 * @param array<string> $val Field arguments.
	 * @return void
	 */
	public function render_debug_logging_input( $val ) {
		$field_id = $val['id'];
		$name     = $val['option_name'];
		$value    = get_option( $name, '0' );

		printf(
			'<label for="%s"><input type="checkbox" id="%s" name="%s" value="1" %s> %s</label>',
			esc_attr( $field_id ),
			esc_attr( $field_id ),
			esc_attr( $name ),
			checked( '1', $value, false ),
			esc_html__( 'Enable debug logging', 'brevwoo' )
		);

		$wc_logs_url = admin_url( 'admin.php?page=wc-status&tab=logs' );
		printf(
			'<p class="description">%s <a href="%s" target="_blank">%s</a> %s.</p>',
			esc_html__( 'Add to', 'brevwoo' ),
			esc_url( $wc_logs_url ),
			esc_html__( 'WooCommerce logs', 'brevwoo' ),
			esc_html__( 'when a customer is added to Brevo', 'brevwoo' )
		);
	}

	/**
	 * Sanitize checkbox input.
	 *
	 * @param string $input The input from the form.
	 * @return string Sanitized input.
	 */
	public function sanitize_checkbox_input( $input ) {
		return ! empty( $input ) ? '1' : '0';
	}

	/**
	 * Add BrevWoo panel to edit product page sidebar.
	 *
	 * @return void
	 */
	public function add_edit_product_panel() {
		add_meta_box(
			'brevwoo',
			__( 'BrevWoo', 'brevwoo' ),
			array( $this, 'render_edit_product_panel' ),
			'product',
			'side',
			'default'
		);
	}

	/**
	 * Render edit product page BrevWoo panel.
	 *
	 * @param WP_Post $post The current post object.
	 * @return void
	 */
	public function render_edit_product_panel( $post ) {
		$product_lists = get_post_meta(
			$post->ID,
			'_brevwoo_product_lists',
			true
		);

		// Initialize as empty array if the option is not yet set.
		if ( ! is_array( $product_lists ) ) {
			$product_lists = array();
		}

		if ( ! $this->api_client ) {
			printf(
				'<p>%s</p>',
				sprintf(
					// translators: %s is a link to the BrevWoo settings page.
					esc_html__(
						'Enter a Brevo API key in the %s to load lists.',
						'brevwoo'
					),
					'<a href="' .
						esc_url( $this->get_plugin_settings_url() ) .
						'">' .
						esc_html__( 'BrevWoo settings', 'brevwoo' ) .
						'</a>'
				)
			);
			return;
		}

		try {
			$all_lists_result   = $this->api_client->get_lists();
			$all_folders_result = $this->api_client->get_folders();
			wp_nonce_field(
				'brevwoo_edit_product_nonce_action',
				'brevwoo_edit_product_nonce'
			);
			printf(
				'<p class="howto">%s' .
					'<span class="woocommerce-help-tip" style="margin-bottom: 2px;" data-tip="%s"></span>' .
					'</p>',
				esc_html__(
					'Select Brevo lists below to add customers to when they buy this product.',
					'brevwoo'
				),
				esc_attr__(
					'Hold Cmd or Ctrl to select multiple lists or deselect lists',
					'brevwoo'
				)
			);
			printf(
				'<label for="brevwoo_product_lists" class="hidden">%s</label>',
				esc_html__( 'Product Brevo Lists', 'brevwoo' )
			);
			$this->render_select_lists_input(
				'brevwoo_product_lists', // HTML ID and name attribute.
				$product_lists, // Currently selected list IDs.
				$all_lists_result->getLists(), // All Brevo lists from API.
				$all_folders_result->getFolders(), // All Brevo folders from API.
				__( 'Disabled (default lists only)', 'brevwoo' ) // Disabled option label.
			);
			printf(
				'<p class="howto">%s</p>',
				sprintf(
					// translators: %s is a link to the BrevWoo settings page.
					esc_html__( 'Select default lists in %s.', 'brevwoo' ),
					'<a href="' .
						esc_url( $this->get_plugin_settings_url() ) .
						'">' .
						esc_html__( 'BrevWoo settings', 'brevwoo' ) .
						'</a>'
				)
			);
		} catch ( Exception $e ) {
			$message =
				'<p><strong>' .
				esc_html__( 'Error fetching Brevo lists', 'brevwoo' ) .
				'</strong></p><p>' .
				esc_html( $e->getMessage() ) .
				'</p>';
			wp_admin_notice(
				$message,
				array(
					'type'               => 'error',
					'paragraph_wrap'     => false,
					'additional_classes' => array( 'inline' ),
				)
			);
		}
	}

	/**
	 * Save selected Brevo lists to product meta.
	 *
	 * @param mixed $product The product being saved.
	 * @return void
	 */
	public function save_product_lists( $product ) {
		if (
			! isset( $_POST['brevwoo_edit_product_nonce'], $_POST['brevwoo_product_lists'] ) ||
			! wp_verify_nonce( sanitize_key( $_POST['brevwoo_edit_product_nonce'] ), 'brevwoo_edit_product_nonce_action' )
		) {
			return;
		}

		if ( is_array( $_POST['brevwoo_product_lists'] ) ) {
			// Sanitize here instead of using sanitize_lists_input to keep WPCS happy.
			$sanitized_lists = array_values(
				array_filter(
					array_map( 'intval', wp_unslash( $_POST['brevwoo_product_lists'] ) )
				)
			);
			$product->update_meta_data( '_brevwoo_product_lists', $sanitized_lists );
		}
	}

	/**
	 * Get the WooCommerce hook to use for adding customer to Brevo lists.
	 *
	 * @return string Hook name
	 */
	public function get_wc_checkout_hook() {
		$order_status = get_option( 'brevwoo_order_status', 'completed' );

		switch ( $order_status ) {
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
	 *
	 * @return void
	 */
	public function enqueue_admin_styles() {
		wp_enqueue_style(
			'brevwoo-admin-styles',
			plugin_dir_url( __FILE__ ) . '../style.css',
			array(), // no stylesheet dependencies.
			$this->version // include plugin version in query string.
		);
	}

	/**
	 * Display admin notice if WooCommerce is not active.
	 *
	 * @return void
	 */
	public function render_required_plugin_notice() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			$message =
				'<p><strong>' .
				esc_html__(
					'BrevWoo requires WooCommerce to be installed and active. You can download ',
					'brevwoo'
				) .
				'<a href="https://woocommerce.com/" target="_blank">' .
				esc_html__( 'WooCommerce', 'brevwoo' ) .
				'</a>' .
				esc_html__( ' here.', 'brevwoo' ) .
				'</strong></p>';
			wp_admin_notice(
				$message,
				array(
					'type'           => 'error',
					'paragraph_wrap' => false,
				)
			);
		}
	}

	/**
	 * Add the WooCommerce customer to the product's Brevo lists.
	 *
	 * @param int $order_id The ID of the order.
	 * @return void
	 */
	public function process_wc_order( $order_id ) {
		$order         = wc_get_order( $order_id );
		$default_lists = array_map(
			'intval',
			get_option( 'brevwoo_default_lists', array() )
		);

		foreach ( $order->get_items() as $item ) {
			$product_id     = $item->get_product_id();
			$product_lists  = array_map(
				'intval',
				get_post_meta( $product_id, '_brevwoo_product_lists', true )
			);
			$combined_lists = array_unique(
				array_merge( $default_lists, $product_lists )
			);

			if ( ! empty( $combined_lists ) ) {
				$this->create_brevo_contact( $order, $combined_lists );
			}
		}
	}

	/**
	 * Render a multiple select dropdown for Brevo lists, grouped by folder.
	 *
	 * @param string        $field_id The HTML id and name attribute for the input.
	 * @param array<int>    $selected_lists The list IDs to pre-select.
	 * @param array<object> $all_lists Brevo API lists response.
	 * @param array<object> $all_folders Brevo API folders response.
	 * @param string        $disabled_label The label for the disabled (default) option.
	 * @return void
	 */
	private function render_select_lists_input(
		$field_id,
		$selected_lists,
		$all_lists,
		$all_folders,
		$disabled_label
	) {
		// Create a map of folder IDs to folder names.
		$folder_map = array();
		foreach ( $all_folders as $folder ) {
			$folder_map[ $folder['id'] ] = $folder['name'];
		}

		// Group lists by folder.
		$grouped_lists = array();
		foreach ( $all_lists as $list ) {
			$grouped_lists[ $list['folderId'] ][] = $list;
		}

		// Start the select element.
		echo '<select id="' .
			esc_attr( $field_id ) .
			'" name="' .
			esc_attr( $field_id ) .
			'[]" class="brevwoo-select-lists-input" multiple>';

		// The initial disabled/default option.
		echo '<option value=""' .
			( empty( $selected_lists ) ? ' selected' : '' ) .
			'>' .
			esc_html( $disabled_label ) .
			'</option>';

		// Render grouped lists.
		foreach ( $grouped_lists as $folder_id => $lists ) {
			$folder_name = isset( $folder_map[ $folder_id ] )
				? $folder_map[ $folder_id ]
				: __( 'No folder', 'brevwoo' );
			echo '<optgroup label="' . esc_attr( $folder_name ) . '">';
			foreach ( $lists as $list ) {
				$selected = in_array( $list['id'], $selected_lists, true )
					? ' selected'
					: '';
				echo '<option value="' .
					esc_attr( $list['id'] ) .
					'"' .
					esc_attr( $selected ) .
					'>' .
					esc_html( '#' . $list['id'] . ' ' . $list['name'] ) .
					'</option>';
			}
			echo '</optgroup>';
		}
		echo '</select>';
	}

	/**
	 * Return the URL for the plugin settings page.
	 *
	 * @return string The URL for the plugin settings page.
	 */
	private function get_plugin_settings_url() {
		return add_query_arg(
			'page',
			$this->plugin_name,
			get_admin_url() . 'options-general.php'
		);
	}

	/**
	 * Create or update a Brevo contact, including the list IDs to add them to.
	 *
	 * @param mixed      $order The WooCommerce order object.
	 * @param array<int> $list_ids The list IDs to add the contact to.
	 * @return void
	 *
	 * @SuppressWarnings(PHPMD.MissingImport)
	 */
	private function create_brevo_contact( $order, $list_ids ) {
		// Get the brevwoo_debug_logging option.
		$debug_logging = get_option( 'brevwoo_debug_logging', '0' );

		if ( ! $this->api_client ) {
			$error_message = __(
				'API client not initialized, could not add contact to Brevo',
				'brevwoo'
			);
			error_log( 'BrevWoo: ' . $error_message ); // phpcs:ignore
			$this->wc_logger->error( $error_message );
			return;
		}

		// Collect order details.
		$email        = $order->get_billing_email();
		$first_name   = $order->get_billing_first_name();
		$last_name    = $order->get_billing_last_name();
		$order_id     = $order->get_id();
		$order_total  = $order->get_total();
		$order_date   = $order->get_date_created()->date( 'd-m-Y' );
		$order_status = $order->get_status();

		// Create the contact object.
		$brevo_contact = new Brevo\Client\Model\CreateContact(
			array(
				'email'         => $email,
				'updateEnabled' => true,
				'attributes'    => array(
					'FIRSTNAME'   => $first_name,
					'LASTNAME'    => $last_name,
					'ORDER_ID'    => strval( $order_id ),
					'ORDER_PRICE' => $order_total,
					'ORDER_DATE'  => $order_date,
				),
				'listIds'       => $list_ids,
			)
		);

		try {
			$this->api_client->create_contact( $brevo_contact );
			if ( $debug_logging ) {
				$this->log_contact_created(
					$email,
					$list_ids,
					strval( $order_id ),
					$order_status
				);
			}
		} catch ( Exception $e ) {
			$error_message =
				'Error creating Brevo contact: ' . $e->getMessage();
			error_log( 'BrevWoo: ' . $error_message ); // phpcs:ignore
			$this->wc_logger->error( $error_message );
		}
	}

	/**
	 * Render Brevo API connection status notice.
	 *
	 * @return void
	 */
	private function render_brevo_status_notice() {
		try {
			$this->api_client->get_account();
			$message =
				'<p><strong>' .
				esc_html__( 'Successfully connected to Brevo', 'brevwoo' ) .
				'</strong></p>';
			wp_admin_notice(
				$message,
				array(
					'type'               => 'success',
					'paragraph_wrap'     => false,
					'additional_classes' => array( 'notice-alt' ),
				)
			);
		} catch ( Exception $e ) {
			$message =
				'<p><strong>' .
				esc_html__( 'Error connecting to Brevo', 'brevwoo' ) .
				'</strong></p><p>' .
				esc_html( $e->getMessage() ) .
				'</p>';
			wp_admin_notice(
				$message,
				array(
					'type'           => 'error',
					'paragraph_wrap' => false,
				)
			);
		}
	}

	/**
	 * Log Brevo contact add to WooCommerce.
	 *
	 * @param string     $email The email address of the contact.
	 * @param array<int> $list_ids The list IDs the contact was added to.
	 * @param string     $order_id The ID of the order.
	 * @param string     $order_status The status of the order.
	 * @return void
	 */
	private function log_contact_created(
		$email,
		$list_ids,
		$order_id,
		$order_status
	) {
		$log_message = sprintf(
			'Order #%s %s status added %s to Brevo list %s',
			$order_id,
			$order_status,
			$email,
			implode(
				', ',
				array_map(
					function ( $list_id ) {
						return '#' . $list_id;
					},
					$list_ids
				)
			)
		);
		$this->wc_logger->info( $log_message );
	}
}
