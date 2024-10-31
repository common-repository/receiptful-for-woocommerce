<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class CM_Commerce_Admin.
 *
 * Admin class.
 *
 * @class		CM_Commerce_Admin
 * @version		1.0.0
 * @author		Conversio
 */
class CM_Commerce_Admin {


	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->init();
	}


	/**
	 * Class hooks.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		// Add admin_init hooks
		add_action( 'admin_init', array( $this, 'admin_init' ) );

		// Enqueue scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Add WC settings tab
		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'settings_tab' ), 60 );

		// Settings page contents
		add_action( 'woocommerce_settings_receiptful', array( $this, 'settings_page' ) );

		// Save settings page
		add_action( 'woocommerce_update_options_receiptful', array( $this, 'update_options' ) );

		// Add custom setting type
		add_action( 'woocommerce_admin_field_conversio_optin_notice', array( $this, 'conversio_optin_notice_field_type' ) );
		add_action( 'woocommerce_admin_field_conversio_widgets', array( $this, 'conversio_widgets_field_type' ) );

		// Custom setting type sanitize
		add_filter( 'woocommerce_admin_settings_sanitize_option_receiptful_widgets', array( $this, 'sanitize_conversio_widgets' ), 10, 3 );

		// Remove public key when API key gets changed (will be gotten automatically)
		add_action( 'update_option_receiptful_api_key', array( $this, 'delete_public_key' ), 10, 2 );

		// Add debug tool
		add_filter( 'woocommerce_debug_tools', array( $this, 'status_tools' ) );
		add_action( 'admin_init', array( $this, 'process_status_tools' ) );

		// Plugin activation message
		add_action( 'admin_notices', array( $this, 'plugin_activation' ) ) ;
	}


	/**
	 * Add actions on admin_init.
	 *
	 * Add actions that can only run on admin_init (infinite loop prevention).
	 */
	public function admin_init() {
		// Add the plugin page Settings and Docs links
		add_filter( 'plugin_action_links_' . plugin_basename( CM_Commerce()->file ), array( $this, 'plugin_action_links' ));
	}


	/**
	 * Enqueue scripts.
	 *
	 * @since 1.5.0
	 */
	public function enqueue_scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_style( 'cm-commerce-admin', plugins_url( '/assets/css/admin.min.css', CM_Commerce()->file ), array(), CM_Commerce()->version );

		wp_register_script( 'cm-commerce-admin', plugins_url( '/assets/js/admin' . $suffix . '.js', CM_Commerce()->file ), array( 'jquery' ), CM_Commerce()->version, true );

		// Only load scripts on relevant pages
		if ( isset( $_REQUEST['tab'] ) && in_array( $_REQUEST['tab'], array( 'receiptful' ) ) ) {
			wp_enqueue_style( 'cm-commerce-admin' );
			wp_enqueue_script( 'cm-commerce-admin' );
		}
	}

	/**
	 * Settings tab.
	 *
	 * Add a WooCommerce settings tab for the CM Commerce settings page.
	 *
	 * @since 1.0.0
	 *
	 * @param	array	$tabs	Array of default tabs used in WC.
	 * @return	array			All WC settings tabs including newly added.
	 */
	public function settings_tab( $tabs ) {

		$tabs['receiptful'] = 'CM Commerce';

		return $tabs;

	}


	/**
	 * Settings page array.
	 *
	 * Get settings page fields array.
	 *
	 * @since 1.0.0
	 *
	 * @return array List of settings for the settings page.
	 */
	public function get_settings() {

		$settings = apply_filters( 'woocommerce_receiptful_settings', array(

			array(
				'title'		=> 'CM Commerce',
				'type'		=> 'title',
				'desc'		=> '<strong>Previously Conversio</strong>. ' . sprintf( __( "To get started with CM Commerce, please add your API key (<a href='%s' target='_blank'>which you can find here</a>) and save the settings.", 'conversio-for-woocommerce' ), 'https://commerce.campaignmonitor.com/profile' ),
			),
			array(
				'title'		=> __( 'API Key', 'conversio-for-woocommerce' ),
				'desc'		=> '',
				'id'		=> 'receiptful_api_key',
				'default'	=> '',
				'type'		=> 'text',
				'autoload'	=> true,
			),
			array(
				'type'		=> 'sectionend',
			),
			array(
				'title'		=> '',
				'type'		=> 'title',
				'desc'		=> sprintf( __( "<a href='%s'>View Statistics</a>", 'conversio-for-woocommerce' ),	'https://commerce.campaignmonitor.com/dashboard' ),
				'id'		=> 'receiptful_links',
			),
			array(
				'title'   	=> __( 'Enable recommendations', 'conversio-for-woocommerce' ),
				'desc' 	  	=> sprintf( __( "Enable product recommendations. Requires to have set this up in the <a href='%s' target='_blank'>Recommendations section</a>.", 'conversio-for-woocommerce' ), 'https://commerce.campaignmonitor.com/recommendations/widgets' ),
				'id' 	  	=> 'receiptful_enable_recommendations',
				'default' 	=> 'no',
				'type' 	  	=> 'checkbox',
				'autoload'	=> true,
			),
			array(
				'title'   	=> __( 'Enable feedback widgets', 'conversio-for-woocommerce' ),
				'desc' 	  	=> sprintf( __( "Enable feedback widgets. Requires to have set this up in the <a href='%s' target='_blank'>Feedback section</a>.", 'conversio-for-woocommerce' ), 'https://commerce.campaignmonitor.com/feedback/widgets' ),
				'id' 	  	=> 'receiptful_enable_feedback_widgets',
				'default' 	=> 'no',
				'type' 	  	=> 'checkbox',
				'autoload'	=> true,
			),
			array(
				'title'   	=> __( 'Enable abandoned cart', 'conversio-for-woocommerce' ),
				'desc' 	  	=> __( "Enable the abandoned cart functionality.", 'conversio-for-woocommerce' ),
				'id' 	  	=> 'receiptful_enable_abandoned_cart',
				'default' 	=> 'no',
				'type' 	  	=> 'checkbox',
				'autoload'	=> true,
			),
			array(
				'title'   	=> __( 'Enable CM Commerce search', 'conversio-for-woocommerce' ),
				'desc' 	  	=> __( "Enable the CM Commerce search functionality.", 'conversio-for-woocommerce' ),
				'id' 	  	=> 'receiptful_enable_search',
				'default' 	=> 'no',
				'type' 	  	=> 'checkbox',
				'autoload'	=> true,
			),
			array(
				'title'   	=> __( 'Marketing Opt-in Checkbox During Checkout', 'receiptful-for-woocommerce' ),
				'desc' 	  	=> __( "", 'receiptful-for-woocommerce' ),
				'id' 	  	=> 'receiptful_marketing_optin',
				'default' 	=> 'unchecked',
				'type' 	  	=> 'radio',
				'options'   => array(
					'unchecked' => __( 'By default, customer does not agree to receive marketing emails (checkbox is not pre-checked)', 'receiptful-for-woocommerce' ),
					'checked' => __( 'By default, customer agrees to receive marketing emails (checkbox is pre-checked)', 'receiptful-for-woocommerce' ),
					'disabled' => __( 'Disable and hide this option (no checkbox is shown)', 'receiptful-for-woocommerce' ),
				),
				'autoload'	=> true,
			),
			array(
				'desc' 	  	=> '',
				'notices'    => array(
					'eu' => __( "GDPR laws within the EU say that this checkbox cannot be selected by default. We recommend you change this.", 'receiptful-for-woocommerce' ),
					'row' => __( "Selling to customers in the EU? GDPR laws within the EU say that this checkbox cannot be selected by default.", 'receiptful-for-woocommerce' ), // Rest of World
				),
				'id' 	  	=> 'receiptful_marketing_optin_notice',
				'type' 	  	=> 'conversio_optin_notice',
				'autoload'	=> true,
			),
			array(
				'title'   	=> __( 'Subscription text', 'receiptful-for-woocommerce' ),
				'desc' 	  	=> __( "", 'receiptful-for-woocommerce' ),
				'id' 	  	=> 'receiptful_marketing_optin_text',
				'default' 	=> __( 'Subscribe to marketing emails?', 'receiptful-for-woocommerce' ),
				'type' 	  	=> 'text',
				'autoload'	=> true,
			),
			array(
				'title'         => __( 'Email suppression', 'conversio-for-woocommerce' ),
				'desc'          => __( 'Suppress WooCommerce Processing order email', 'conversio-for-woocommerce' ),
				'id'            => 'receiptful_suppress_wc_processing_email',
				'default'       => 'yes',
				'type'          => 'checkbox',
				'autoload'      => true,
				'checkboxgroup' => 'start',
			),
			array(
				'desc'          => __( 'Suppress WooCommerce Completed order email', 'conversio-for-woocommerce' ),
				'id'            => 'receiptful_suppress_wc_completed_email',
				'default'       => 'yes',
				'type'          => 'checkbox',
				'autoload'      => true,
				'checkboxgroup' => 'end',
				'desc_tip'      => __( 'By default WooCommerce processing and completed emails are suppressed when using CM Commerce.', 'conversio-for-woocommerce' ),
			),

			array(
				'title' => 'Widgets',
				'type'  => 'title',
				'desc'  => __( '', 'conversio-for-woocommerce' ),
			),
			array(
				'title'         => __( 'Widgets', 'conversio-for-woocommerce' ),
				'desc'          => __( '', 'conversio-for-woocommerce' ),
				'id'            => 'receiptful_widgets',
				'type'          => 'conversio_widgets',
				'desc_tip'      => __( '', 'conversio-for-woocommerce' ),
				'value'         => wc_clean( get_option( 'receiptful_widgets', array() ) ),
			),
			array(
				'type'		=> 'sectionend',
			),

		) );

		return $settings;

	}


	/**
	 * Settings page content.
	 *
	 * Output settings page content via WooCommerce output_fields() method.
	 *
	 * @since 1.0.0
	 */
	public function settings_page() {

		WC_Admin_Settings::output_fields( $this->get_settings() );

	}


	/**
	 * Save settings.
	 *
	 * Save settings based on WooCommerce save_fields() method.
	 *
	 * @since 1.0.0
	 */
	public function update_options() {

		WC_Admin_Settings::save_fields( $this->get_settings() );

	}

	public function conversio_optin_notice_field_type( $value ) {
		$base_location_in_eu = in_array( WC()->countries->get_base_country(), WC()->countries->get_european_union_countries() );

		?><tr valign="top">
			<th scope="row" class="titledesc" style="padding: 0;">
				<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
			</th>
			<td class="forminp" style="padding: 0 10px;">
				<div class="gdpr-notices" style="margin: -20px 0 0; background: #fff; border-left: 4px solid #dc3232; box-shadow: 0 1px 1px 0 rgba( 0, 0, 0, 0.1 ); padding: 0 12px;"><?php
					foreach ( $value['notices'] as $k => $notice ) :
						?><p style="margin: 0.5em 0; padding: 9px 2px;" id="gdpr-notice-<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $notice ); ?></p><?php
					endforeach;
				?></div>

				<script type="text/javascript" charset="utf-8">
					(function() {
						document.querySelectorAll('[name=receiptful_marketing_optin]').forEach(function(el) {
							el.addEventListener('change', conversio_marketing_optin_option_change);
						});

						function conversio_marketing_optin_option_change() {
							document.querySelector('#gdpr-notice-eu').style.display = 'none';
							document.querySelector('#gdpr-notice-row').style.display = 'none';

							var value = document.querySelector('[name=receiptful_marketing_optin]:checked').value;
							if ( value == 'checked' && true == <?php echo esc_js( $base_location_in_eu ? 'true' : 'false' ); ?> ) {
								document.querySelector('#gdpr-notice-eu').style.display = 'block';
							} else if ( value == 'checked' ) {
								document.querySelector('#gdpr-notice-row').style.display = 'block';
							}
						}
						conversio_marketing_optin_option_change();
					})();
				</script>
			</td>
		</tr><?php

	}


	/**
	 * Custom field type.
	 *
	 * Output the HTML for hte custom widgets field type.
	 *
	 * @since 1.5.0
	 */
	public function conversio_widgets_field_type( $value ) {

		$widgets = wc_clean( $value['value'] );

		?><tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
			</th>
			<td class="" style="padding: 0 10px;">


				<div class="conversio-widgets-wrap">

					<div class="conversio-widgets-list-header">
						<span class="column column-shortcode"><?php _e( 'Shortcode', 'conversio-for-woocommerce' ); ?></span>
						<span class="column column-location"><?php _e( 'Add widget after', 'conversio-for-woocommerce' ); ?></span>
						<span class="column column-priority"><?php echo __( 'Priority', 'conversio-for-woocommerce' ) . wc_help_tip( __( 'The priority can be used to fine-tune at which order the widget will be displayed in comparison to other elements.', 'conversio-for-woocommerce' ) ); ?></span>
					</div>
					<div class="conversio-widgets-list"><?php
						foreach ( $widgets as $key => $widget ) :
							$this->conversio_widget_row( $key, $widget );
						endforeach;

						if ( empty( $widgets ) ) :
							$this->conversio_widget_row( 0, array() );
						endif;

					?></div>
					<div class="conversio-widgets-footer">
						<a href="javascript:void(0);" id="add-conversio-widget" class="button button-primary"><?php _e( '+ Add widget', 'conversio-for-woocommerce' ); ?></a>
					</div>
					<div class="conversio-widget-template" style="display: none;"><?php
						$this->conversio_widget_row( 9999, array() );
					?></div>
				</div>


			</td>
		</tr><?php

	}


	/**
	 * Singular CM Commerce widget row.
	 *
	 * @since 1.5.0
	 *
	 * @param int $i Index.
	 * @param array $widget Widget setting values.
	 */
	private function conversio_widget_row( $i, $widget ) {

		$widget = wp_parse_args( $widget, array(
			'shortcode' => '',
			'location'  => '',
			'priority'  => 10,
		) );
		$i = absint( $i );

		$locations = receiptful_get_available_widget_locations();
		$location = null;

		?><div class="conversio-widget">
			<div class="form-control column column-shortcode">
				<input type="text" name="receiptful_widgets[<?php echo $i; ?>][shortcode]" value="<?php echo esc_attr( $widget['shortcode'] ); ?>" placeholder="<?php _e( 'Insert your shortcode', 'conversio-for-woocommerce' ); ?>">
			</div>
			<div class="form-control column column-location">
				<select name="receiptful_widgets[<?php echo $i; ?>][location]" class="conversio-widget-location"><?php
					foreach ( $locations as $group ) :
						?><optgroup label='<?php echo esc_attr( $group['title'] ); ?>'><?php
							foreach ( $group['options'] as $key => $option ) :
								?><option value='<?php echo esc_attr( $key ); ?>' <?php selected( $key == $widget['location'] ); ?>><?php echo esc_attr( $option['title'] ); ?></option><?php

								if ( $key == $widget['location'] ) :
									$location = $option;
								endif;
							endforeach;
						?></optgroup><?php
					endforeach;
				?></select>
				<input
					type="text"
					class="conversio-widget-custom-hook"
					name="receiptful_widgets[<?php echo $i; ?>][hook]"
					value="<?php echo esc_attr( isset($widget['hook']) ? $widget['hook'] : '' ); ?>"
					placeholder="<?php _e( 'Custom hook', 'conversio-for-woocommerce' ); ?>"
					style="<?php echo $widget['location'] != 'custom' ? 'display: none;' : ''; ?>"
				>
				<input
					type="text"
					class="conversio-widget-tab-name"
					name="receiptful_widgets[<?php echo $i; ?>][tab_name]"
					value="<?php echo esc_attr( isset($widget['tab_name']) ? $widget['tab_name'] : '' ); ?>"
					placeholder="<?php _e( 'Tab name', 'conversio-for-woocommerce' ); ?>"
					style="<?php echo $widget['location'] != 'tab' ? 'display: none;' : ''; ?>"
				>
			</div>
			<div class="form-control column column-priority">
				<input
					type="checkbox"
					class="conversio-widget-custom-priority"
					value="<?php echo absint( $widget['priority'] ); ?>"
					<?php checked( ( isset($location['priority']) ? $location['priority'] : '' ) != $widget['priority'] ); ?>
				>
				<input
					type="number"
					class="conversio-widget-priority"
					name="receiptful_widgets[<?php echo $i; ?>][priority]"
					value="<?php echo absint( $widget['priority'] ); ?>"
					style="<?php echo ( isset($location['priority']) ? $location['priority'] : '' ) == $widget['priority'] ? 'display: none;' : ''; ?>"
				>
			</div>
			<div class="form-control column column-delete">
				<a href="javascript:void(0);" class="delete"><?php _e( 'Delete', 'conversio-for-woocommerce' ); ?></a>
			</div>
		</div><?php
	}


	/**
	 * Sanitize CM Commerce widgets.
	 *
	 * @since 1.5.0
	 *
	 * @param  mixed  $value     Pre-sanitized by Woo option value.
	 * @param  string $option    Option name.
	 * @param  mixed  $raw_value Raw option value.
	 * @return mixed             Sanitized option value.
	 */
	public function sanitize_conversio_widgets( $value, $option, $raw_value ) {
		unset( $value['9999'] );

		return $value;
	}


	/**
	 * Delete public key.
	 *
	 * Delete the public key when the API key gets updated.
	 *
	 * @since 1.1.4
	 */
	public function delete_public_key( $old_value, $value ) {

		delete_option( 'receiptful_public_user_key' );

	}


	/**
	 * Add product re-sync tool.
	 *
	 * Add a product re-sync tool to the System -> tools page to
	 * re-sync all products with Conversio.
	 *
	 * @since 1.1.12
	 * @since 1.1.13 - Add Receipt (order) sync.
	 * @since 1.3.4 - Add debug mode
	 *
	 * @param	array	$tools	List of existing tools.
	 * @return	array			List of modified tools.
	 */
	public function status_tools( $tools ) {

		$sync_queue = get_option( '_receiptful_queue', array( 'products' => array(), 'orders' => array() ) );
		$product_count_message = '';
		$order_count_message = '';
		if ( ! empty( $sync_queue['products'] ) ) {
			$product_count_message = '<strong>' . sprintf(  __( '%d products to be synced.', 'conversio-for-woocommerce' ), count( $sync_queue['products']) ) . '</strong>&nbsp;';
		}

		if ( ! empty( $sync_queue['orders'] ) ) {
			$order_count_message = '<strong>' . sprintf(  __( '%d orders to be synced.', 'conversio-for-woocommerce' ), count( $sync_queue['orders']) ) . '</strong>&nbsp;';
		}

		$tools['receiptful_product_sync'] = array(
			'name'		=> __( 'Synchronize products with CM Commerce', 'conversio-for-woocommerce' ),
			'button'	=> __( 'Synchronize', 'conversio-for-woocommerce' ),
			'desc'		=> $product_count_message . __( 'This will update all products in CM Commerce with all its latest data', 'conversio-for-woocommerce' ),
		);

		$tools['receiptful_receipt_sync'] = array(
			'name'		=> __( 'Synchronize receipts with CM Commerce', 'conversio-for-woocommerce' ),
			'button'	=> __( 'Synchronize', 'conversio-for-woocommerce' ),
			'desc'		=> $order_count_message . __( 'This will update all orders in CM Commerce with the latest data', 'conversio-for-woocommerce' ),
		);

		$tools['receiptful_clear_coupons'] = array(
			'name'		=> __( 'Clear unused, expired coupons created by CM Commerce', 'conversio-for-woocommerce' ),
			'button'	=> __( 'Trash expired coupons', 'conversio-for-woocommerce' ),
			'desc'		=> __( 'Only coupons that have been expired for more than 7 days will be trashed.', 'conversio-for-woocommerce' ),
		);

		$tools['receiptful_clear_coupons'] = array(
			'name'		=> __( 'Clear unused, expired coupons created by CM Commerce', 'conversio-for-woocommerce' ),
			'button'	=> __( 'Trash expired coupons', 'conversio-for-woocommerce' ),
			'desc'		=> __( 'Only coupons that have been expired for more than 7 days will be trashed.', 'conversio-for-woocommerce' ),
		);
		$tools['receiptful_clear_resend_queue'] = array(
			'name'		=> __( 'Clear resend queue', 'conversio-for-woocommerce' ),
			'button'	=> __( 'Clear queue', 'conversio-for-woocommerce' ),
			'desc'		=> __( 'Clear all the receipts on the CM Commerce resend queue.', 'conversio-for-woocommerce' ),
		);
		$debug_mode = get_option( 'receiptful_debug_mode_enabled', false );
		$debug_mode_status = $debug_mode == true ? __( 'Enabled', 'conversio-for-woocommerce' ) : __( 'Disabled', 'conversio-for-woocommerce' );
		$tools['receiptful_debug_mode'] = array(
			'name'		=> __( 'CM Commerce debug mode', 'conversio-for-woocommerce' ),
			'button'	=> $debug_mode ? __( 'Disable debug mode', 'conversio-for-woocommerce' ) : __( 'Enable debug mode', 'conversio-for-woocommerce' ),
			'desc'		=> sprintf( __( 'Enable debug mode to log all API calls being made. Debug mode is currently %s.', 'conversio-for-woocommerce' ), '<strong>' . $debug_mode_status . '</strong>' ),
		);

		return $tools;

	}


	/**
	 * Process re-sync action.
	 *
	 * Make sure that the status tool 'Conversio re-sync' is working.
	 *
	 * @since 1.1.12
	 * @since 1.1.13 - Add receipt sync handler.
	 */
	public function process_status_tools() {

		// Bail if action is not set
		if ( ! isset( $_GET['action'] ) ) {
			return;
		}

		// Bail if nonce is incorrect
		if ( ! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'debug_action' ) ) {
			return;
		}

		// Product sync
		if ( 'receiptful_product_sync' == $_GET['action'] ) {

			// Get all product IDs
			$product_ids = get_posts( array(
				'fields'			=> 'ids',
				'posts_per_page'	=> -1,
				'post_type'			=> 'product',
				'post_status'		=> 'any',
			) );
			$product_ids = array_map( 'absint', $product_ids );

			$queue = get_option( '_receiptful_queue', array() );
			foreach ( $product_ids as $product_id ) {
				$queue['products'][ $product_id ] = array( 'id' => $product_id, 'action' => 'update' );
			}
			update_option( '_receiptful_queue', $queue );

		}

		// Order sync
		if ( 'receiptful_receipt_sync' == $_GET['action'] ) {

			// Get all receipt IDs
			$order_ids = get_posts( array(
				'fields'			=> 'ids',
				'posts_per_page'	=> -1,
				'post_type'			=> 'shop_order',
				'post_status'		=> array( 'wc-completed', 'wc-processing' ),
			) );
			$order_ids = array_map( 'absint', $order_ids );

			$queue = get_option( '_receiptful_queue', array() );
			foreach ( $order_ids as $order_id ) {
				$queue['orders'][ $order_id ] = array( 'id' => $order_id, 'action' => 'upload' );
			}
			update_option( '_receiptful_queue', $queue );

		}

		// Clear coupons
		if ( 'receiptful_clear_coupons' == $_GET['action'] ) {
			receiptful_clear_unused_coupons();
		}

		// Clear resend queue
		if ( 'receiptful_clear_resend_queue' == $_GET['action'] ) {
			update_option( '_receiptful_resend_queue', '' );
		}

		// Toggle debug mode
		if ( 'receiptful_debug_mode' == $_GET['action'] ) {
			$debug_mode = get_option( 'receiptful_debug_mode_enabled', false );
			update_option( 'receiptful_debug_mode_enabled', !$debug_mode );
		}

		wp_redirect( esc_url_raw( admin_url( 'admin.php?page=wc-status&tab=tools' ) ) );
		die;

	}


	/**
	 * Plugin activation.
	 *
	 * Saves the version of the plugin to the database and displays an
	 * activation notice on where users can access the new options.
	 *
	 * @since 1.0.0
	 * @since 1.2.2 - Moved to admin class
	 */
	public function plugin_activation() {

		$api_key = get_option( 'receiptful_api_key' );
		if ( empty( $api_key ) ) {

			add_option( 'receiptful_woocommerce_version', CM_Commerce()->version );

			// admin.php?page=wc-settings&tab=receiptful
			$admin_url = admin_url( 'admin.php?page=wc-settings&tab=receiptful' );

			?><div class="updated">
				<p><?php
					printf( __( '%1$sConversio has been activated.%2$s Please %3$sclick here%4$s to add your API key & supercharge your receipts.', 'conversio-for-woocommerce' ), '<strong>', '</strong>', '<a href="' . esc_url( $admin_url ) . '">', '</a>' );
				?></p>
			</div><?php

		}

	}


	/**
	 * Plugin page link.
	 *
	 * Add a 'settings' link to the plugin on the plugins page.
	 *
	 * @since 1.0.0
	 * @since 1.2.2 - Moved to admin class
	 *
	 * @param 	array $links	List of existing plugin links.
	 * @return 	array			List of modified plugin links.
	 */
	public function plugin_action_links( $links ) {

		$links['settings'] = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=receiptful' ) . '">' . __( 'Settings', 'conversio-for-woocommerce' ) . '</a>';

		return $links;

	}


}
