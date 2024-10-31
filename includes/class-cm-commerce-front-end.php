<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class CM_Commerce_Front_End.
 *
 * Class to manage all front-end stuff.
 *
 * @class		CM_Commerce_Front_End
 * @since		1.1.4
 * @version		1.1.6
 * @author		Conversio
 */
class CM_Commerce_Front_End {


	/**
	 * Constructor.
	 *
	 * @since 1.1.4
	 */
	public function __construct() {

		// Track pageviews
		add_action( 'wp_footer', array( $this, 'page_tracking' ) );

		// CM Commerce search
		if ( get_option( 'receiptful_enable_search' ) == 'yes' ) {
			add_action( 'wp_footer', array( $this, 'js_init_search' ) );
		}

		// Delete user token
		add_action( 'woocommerce_thankyou', array( $this, 'reset_user_token_cookie' ) );

		// (maybe) Add marketing optin
		add_action( 'woocommerce_billing_fields', array( $this, 'add_marketing_optin' ), 10, 2 );
		// Store marketing optin text when agreed
		add_action( 'woocommerce_checkout_create_order', array( $this, 'store_order_marketing_optin_text' ), 10, 2 );

		// Store marketing optin value
		add_action( 'woocommerce_checkout_update_customer', array( $this, 'store_marketing_optin' ), 10, 2 );

		// Add CM Commerce widgets at hooks/actions
		add_action( 'init', array( $this, 'add_conversio_widget_hooks' ) );
	}


	/**
	 * Page tracking.
	 *
	 * Track the pageviews for better product recommendations.
	 *
	 * @since 1.1.6
	 */
	public function page_tracking() {

		$public_user_key 	= CM_Commerce()->api->get_public_user_key();
		$product_id = 'product' == get_post_type( get_the_ID() ) ? get_the_ID() : null;
		$customer = is_user_logged_in() ? get_current_user_id() : '';
		$cart = WC()->cart;
		$product_ids = $cart ? array_values( wp_list_pluck( $cart->get_cart(), 'product_id' ) ) : [];

		// Bail if public user key is empty/invalid
		if ( ! $public_user_key ) {
		    echo PHP_EOL . '<!-- ' . sprintf( __( "CM Commerce public user key not available, value: %s", 'conversio-for-woocommerce' ), var_export( $public_user_key, true ) ) . ' -->' . PHP_EOL;
			return false;
		}

		?><script type='text/javascript'>
			document.addEventListener('DOMContentLoaded', function(event) {
				if ( typeof Conversio !== 'undefined' ) {
					Conversio.init({
						user: '<?php echo esc_js( $public_user_key ); ?>',
						product: '<?php echo esc_js( $product_id ); ?>',
						cart: '<?php echo esc_js( implode( ',', $product_ids ) ); ?>',
						customer: '<?php echo esc_js( $customer ); ?>',
						recommend: <?php echo 'yes' == get_option( 'receiptful_enable_recommendations', false ) ? '1' : '0'; ?>,
						feedback: <?php echo 'yes' == get_option( 'receiptful_enable_feedback_widgets', false ) ? '1' : '0'; ?>
					});
				}
			});
		</script><?php
	}


	/**
	 * Search.
	 *
	 * Initialize the CM Commerce search feature.
	 *
	 * @since 1.2.3
	 */
	public function js_init_search() {

		$public_user_key = CM_Commerce()->api->get_public_user_key();

		// Bail if public user key is empty/invalid
		if ( ! $public_user_key ) {
			return false;
		}

		$search_field_selector = apply_filters( 'receiptful_search_selector', 'input[name=s]' );

		?><script type='text/javascript'>
			document.addEventListener('DOMContentLoaded', function(event) {
				if ( typeof ConversioSearch !== 'undefined' ) {
					ConversioSearch.init({
						user: '<?php echo esc_js( $public_user_key ); ?>',
						searchFieldSelector: '<?php echo esc_js( $search_field_selector ); ?>'
					});
				}
			});
		</script><?php

	}


	/**
	 * Delete user token.
	 *
	 * Delete the receiptful user token cookie after checkout. When deleted
	 * it will automatically re-generate a new one to track the new purchase flow.
	 *
	 * @since 1.1.6
	 */
	public function reset_user_token_cookie( $order_id ) {

		?><script type='text/javascript'>
			document.addEventListener('DOMContentLoaded', function(event) {
				if ( typeof ConversioCookies !== 'undefined' ) {
					new ConversioCookies().removeItem('receiptful-token', '/');
				}
			});
		</script><?php

	}


	/**
	 * Add marketing optin to checkout.
	 *
	 * Add a marketing optin based on the store settings this will be checked, unchecked or disabled.
	 * Won't show the optin when the customer has opted in before.
	 *
	 * @since 1.4.0
	 *
	 * @param array $fields List of existing fields/
	 * @param string $country Country.
	 * @return array List of modified fields.
	 */
	public function add_marketing_optin( $fields, $country ) {

		$email_priority = isset( $fields['billing_email']['priority'] ) ? $fields['billing_email']['priority'] : 110;
		$optin = get_option( 'receiptful_marketing_optin', 'unchecked' );
		$optin_text = get_option( 'receiptful_marketing_optin_text', __( 'Subscribe to marketing emails?', 'receiptful-for-woocommerce' ) );

		if ( $optin !== 'disabled' && WC()->customer && WC()->customer->get_meta( 'accepts_conversio_marketing' ) != true ) {
			$fields['billing_email_conversio_optin'] = array(
				'label'        => $optin_text,
				'required'     => false,
				'type'         => 'checkbox',
				'class'        => array( 'form-row-wide' ),
				'default'      => 'checked' === $optin,
				'priority'     => $email_priority + 5,
			);

		}

		return $fields;
	}


	/**
	 * Store marketing optin text.
	 *
	 * Store the text the user agreed to when (if they did).
	 *
	 * @since 1.4.0
	 *
	 * @param int $order_id Order ID.
	 * @param array $posted_data Posted data by the user from the checkout.
	 * @param WC_Order $order Order object.
	 */
	public function store_order_marketing_optin_text( $order, $data ) {
		if ( ! empty( $data['billing_email_conversio_optin'] ) ) {
			$order->add_meta_data( '_billing_email_conversio_optin_text', get_option( 'receiptful_marketing_optin_text', __( 'Subscribe to marketing emails?', 'receiptful-for-woocommerce' ) ) );
		}
	}


	/**
	 * Store marketing optin during order process.
	 *
	 * @since 1.4.0
	 *
	 * @param WC_Customer $customer Customer object.
	 * @param array $posted_data Posted data from checkout
	 */
	public function store_marketing_optin( $customer, $posted_data ) {
		if ( isset( $posted_data['billing_email_conversio_optin'] ) && $posted_data['billing_email_conversio_optin'] ) {
			$customer->update_meta_data( 'accepts_conversio_marketing', true );
		}
	}


	/**
	 * Add dynamic Conversio widget hooks.
	 *
	 * Add the dynamic hooks that the Conversio widgets use and ensure the widgets are output
	 * accordingly.
	 *
	 * @since 1.5.0
	 */
	public function add_conversio_widget_hooks() {
		$widgets = get_option( 'receiptful_widgets', array() );

		foreach ( $widgets as $widget ) {
			$hook_priority = explode( ':', $widget['location'] );
			$hook          = $widget['location'] === 'custom' ? $widget['hook'] : $hook_priority[0];
			$priority      = isset($widget['priority']) ? $widget['priority'] : null;

			if (!$priority) {
				$priority = isset($hook_priority[1]) ? $hook_priority[1] : 10;
			}

			if ( $hook === 'tab' ) {
				add_filter( 'woocommerce_product_tabs', function( $tabs ) use ( $widget ) {
					$key = sanitize_title( $widget['tab_name'] );
					$tabs[ $key ] = array(
						'title'    => $widget['tab_name'],
						'priority' => $widget['priority'],
						'callback' => function () use ( $widget ) {
							echo do_shortcode( $widget['shortcode'] );
						}
					);

					return $tabs;
				} );

			} else {
				add_filter( $hook, function ( $a ) use ( $widget ) {
					echo do_shortcode( $widget['shortcode'] );

					return $a;
				}, $priority );
			}
		}
	}


}
