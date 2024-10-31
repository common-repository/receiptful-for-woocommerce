<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class CM_Commerce_Api.
 *
 * @class		CM_Commerce_Api
 * @version		1.0.0
 * @author		Conversio
 */
class CM_Commerce_Api {


	/**
	 * CM Commerce API key.
	 *
	 * @since 1.0.0
	 * @var $api_key
	 */
	public $api_key;


	/**
	 * URL for CM Commerce.
	 *
	 * @since 1.0.0
	 * @var $url
	 */
	public $url;


	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->url = getenv( 'CM_COMMERCE_URL' ) ?: 'https://commerce.campaignmonitor.com/api/v1';
		$this->api_key = get_option( 'receiptful_api_key' );
	}


	/**
	 * Send receipt.
	 *
	 * Send the CM Commerce receipt based on $args.
	 *
	 * @since 1.0.0
	 *
	 * @param	array	$args	API call arguments.
	 * @return	array			API response.
	 */
	public function receipt( $args = array() ) {

		$response = $this->api_call( '/receipts', $args );

		return $response;

	}


	/**
	 * Resend receipt.
	 *
	 * Resend the previously send CM Commerce receipt.
	 *
	 * @since 1.0.0
	 *
	 * @param	int				$receipt_id 	CM Commerce receipt ID, as retrieved from original API call.
	 * @return	array|WP_Error					WP_Error when the API call fails, otherwise the API response.
	 */
	public function resend_receipt( $receipt_id ) {

		$response = $this->api_call( '/receipts/' . $receipt_id . '/send' );

		return $response;

	}


	/**
	 * Update product.
	 *
	 * When a product is created/updated, send it to the Conversio API.
	 *
	 * @since 1.1.1
	 *
	 * @param	int				$product_id		Product ID to update in Conversio.
	 * @param	array			$args			Product arguments to update.
	 * @return	array|WP_Error					WP_Error when the API call fails, otherwise the API response.
	 */
	public function update_product( $product_id, $args ) {

		$response = $this->api_put( '/products/' . $product_id, $args );

		return $response;

	}


	/**
	 * Update products.
	 *
	 * When a product is created/updated, send it to the Conversio API.
	 *
	 * @since 1.1.1
	 *
	 * @param	array			$args	List of items arguments to update..
	 * @return	array|WP_Error			WP_Error when the API call fails, otherwise the API response.
	 */
	public function update_products( $args ) {

		$response = $this->api_call( '/products', $args );

		return $response;

	}


	/**
	 * Delete product.
	 *
	 * When a product is delete, also delete is from Conversio.
	 *
	 * @since 1.1.1
	 *
	 * @param	int				$product_id		Product ID to delete from Conversio.
	 * @return	array|WP_Error					WP_Error when the API call fails, otherwise the API response.
	 */
	public function delete_product( $product_id ) {

		$response = $this->api_delete( '/products/' . $product_id );

		return $response;

	}


	/**
	 * Upload receipts.
	 *
	 * Bulk upload old receipts to sync with Conversio. This ensures
	 * better quality recommendations for similar products.
	 *
	 * @since 1.1.2
	 *
	 * @param	int				$args	List of formatted receipts according the API specs.
	 * @return	array|WP_Error			WP_Error when the API call fails, otherwise the API response.
	 */
	public function upload_receipts( $args ) {

		$response = $this->api_call( '/receipts/bulk', $args );

		return $response;

	}


	/**
	 * Update cart.
	 *
	 * Send a update of the cart. When the cart is abandoned, Conversio will be able
	 * to send a cart abandoned email.
	 *
	 * @since 1.2.0
	 *
	 * @param	array			$args	List of arguments to pass to the endpoint.
	 * @return	array|WP_Error			WP_Error when the API call fails, otherwise the API response.
	 */
	public function post_cart_update( $args ) {

		$response = $this->api_call( '/abandoned-carts/', $args, array( 'blocking' => false ) );

		return $response;

	}


	/**
	 * Update cart URL.
	 *
	 * API call to make when the cart URL is updated.
	 *
	 * @since 1.3.6
	 *
	 * @return array|WP_Error API call response.
	 */
	public function update_store_cart_path() {

		$cart_path = trim( str_replace( home_url(), '', wc_get_cart_url() ), '\/' );

		$response = $this->api_call( '/users', array(
			'cartPath' => $cart_path,
		), array( 'method' => 'PUT' ) );

		return $response;

	}


	/**
	 * Get abandoned cart arguments.
	 *
	 * Get the abandoned cart arguments from the Conversio API (contains cart items).
	 *
	 * @since 1.2.0
	 *
	 * @param	string	$token	Abandoned cart token.
	 * @param	array	$args	List of arguments (unused).
	 * @return	mixed			False when API call is not valid. RAW API response otherwise.
	 */
	public function get_abandoned_cart( $token, $args = array() ) {

		$response = $this->api_get( '/abandoned-carts/' . $token, $args );

		if ( is_wp_error( $response ) || '200' != $response['response']['code'] ) {
			$cart = false;
		} else {
			$response_body 	= json_decode( $response['body'], 1 );
			$cart = $response_body;
		}

		return $cart;

	}


	/**
	 * Public user key.
	 *
	 * Get the current user key based on the API key used.
	 *
	 * @since 1.1.4
	 *
	 * @return array|WP_Error WP_Error when the API call fails, otherwise the API response.
	 */
	public function get_public_user_key() {

		$public_key = '';

		if ( ! $public_key = get_option( 'receiptful_public_user_key' ) ) {

			$response = $this->api_get( '/users/current' );

			if ( is_wp_error( $response ) || '200' != $response['response']['code'] ) {
				$public_key = '';
			} else {
				$response_body 	= json_decode( $response['body'], 1 );
				$public_key 	= isset( $response_body['publicKey'] ) ? $response_body['publicKey'] : '';
				update_option( 'receiptful_public_user_key', $public_key );
			}

		}

		return $public_key;

	}


	/**
	 * API GET.
	 *
	 * Send a GET request to the Conversio API call.
	 *
	 * @since 1.1.4
	 * @since 1.2.0 Add $request_args param.
	 *
	 * @param	string	$method				API method to call.
	 * @param	array	$args				Arguments to pass in the API call.
	 * @param	array	$request_args		List of arguments to override default request arguments.
	 * @return	array|WP_Error	$response	API response.
	 */
	protected function api_get( $method, $args = array(), $request_args = array() ) {

		if ( get_option( 'receiptful_debug_mode_enabled', false ) ) {
			CM_Commerce()->add_log( 'API GET request: ' . $method . PHP_EOL . 'Arguments: ' . print_r( $args, 1 ) );
		}

		$headers = array( 'Accept' => 'application/json', 'Content-Type' => 'application/json', 'X-ApiKey' => $this->api_key );

		$api_response = wp_remote_get( $this->url . $method, wp_parse_args( $request_args, array(
				'timeout'		=> 5,
				'redirection'	=> 5,
				'httpversion'	=> '1.0',
				'blocking'		=> true,
				'headers'		=> $headers,
				'body'			=> $args,
				'cookies'		=> array()
			)
		) );

		if ( is_wp_error( $api_response ) ) {
			$response = $api_response;
		} else {
			$response['response']	= $api_response['response'];
			$response['body']		= $api_response['body'];
		}

		if ( get_option( 'receiptful_debug_mode_enabled', false ) ) {
			CM_Commerce()->add_log( 'API GET response to: ' . $method . PHP_EOL . 'Response: ' . print_r( $response, 1 ) );
		}

		return $response;

	}


	/**
	 * API Call.
	 *
	 * Send a Conversio API call based on method and arguments.
	 *
	 * @since 1.0.0
	 * @since 1.2.0 Add $request_args param.
	 *
	 * @param	string	$method				API method to call.
	 * @param	array	$args				Arguments to pass in the API call.
	 * @param	array	$request_args		List of arguments to override default request arguments.
	 * @return	array|WP_Error	$response	API response.
	 */
	protected function api_call( $method, $args = array(), $request_args = array() ) {

		if ( get_option( 'receiptful_debug_mode_enabled', false ) ) {
			CM_Commerce()->add_log( 'API POST request: ' . $method . PHP_EOL . 'Arguments: ' . print_r( $args, 1 ) );
		}

		$headers = array(
			'Accept' => 'application/json',
			'Content-Type' => 'application/json',
			'X-ApiKey' => $this->api_key,
			'X-ConversioPluginVersion' => CM_Commerce()->version
		);

		$api_response = wp_remote_post( $this->url . $method, wp_parse_args( $request_args, array(
				'method'		=> 'POST',
				'timeout'		=> 5,
				'redirection'	=> 5,
				'httpversion'	=> '1.0',
				'blocking'		=> true,
				'headers'		=> $headers,
				'body'			=> json_encode( $args ),
				'cookies'		=> array()
			)
		) );

		if ( is_wp_error( $api_response ) ) {
			$response = $api_response;
		} else {
			$response['response']	= $api_response['response'];
			$response['body']		= $api_response['body'];
		}

		if ( get_option( 'receiptful_debug_mode_enabled', false ) ) {
			CM_Commerce()->add_log( 'API POST response to: ' . $method . PHP_EOL . 'Response: ' . print_r( $response, 1 ) );
		}

		return $response;

	}


	/**
	 * API PUT.
	 *
	 * Send a Conversio PUT API call based on method and arguments.
	 *
	 * @since 1.1.1
	 * @since 1.2.0 Add $request_args param.
	 *
	 * @param	string	$method				API method to call.
	 * @param	array	$args				Arguments to pass in the API call.
	 * @param	array	$request_args		List of arguments to override default request arguments.
	 * @return	array|WP_Error	$response	API response.
	 */
	protected function api_put( $method, $args = array(), $request_args = array() ) {

		if ( get_option( 'receiptful_debug_mode_enabled', false ) ) {
			CM_Commerce()->add_log( 'API PUT request: ' . $method . PHP_EOL . 'Arguments: ' . print_r( $args, 1 ) );
		}

		$headers = array(
			'Accept' => 'application/json',
			'Content-Type' => 'application/json',
			'X-ApiKey' => $this->api_key,
			'X-ConversioPluginVersion' => CM_Commerce()->version
		);

		$api_response = wp_remote_post( $this->url . $method, wp_parse_args( $request_args, array(
				'method'		=> 'PUT',
				'timeout'		=> 5,
				'redirection'	=> 5,
				'httpversion'	=> '1.0',
				'blocking'		=> true,
				'headers'		=> $headers,
				'body'			=> json_encode( $args ),
				'cookies'		=> array()
			)
		) );

		if ( is_wp_error( $api_response ) ) {
			$response = $api_response;
		} else {
			$response['response']	= $api_response['response'];
			$response['body']		= $api_response['body'];
		}

		if ( get_option( 'receiptful_debug_mode_enabled', false ) ) {
			CM_Commerce()->add_log( 'API PUT response to: ' . $method . PHP_EOL . 'Response: ' . print_r( $response, 1 ) );
		}

		return $response;

	}


	/**
	 * API DELETE.
	 *
	 * Send a Conversio DELETE API call based on method and arguments.
	 *
	 * @since 1.1.1
	 * @since 1.2.0 Add $request_args param.
	 *
	 * @param	string	$method				API method to call.
	 * @param	array	$request_args		List of arguments to override default request arguments.
	 * @return	array|WP_Error	$response	API response.
	 */
	protected function api_delete( $method, $request_args = array() ) {

		if ( get_option( 'receiptful_debug_mode_enabled', false ) ) {
			CM_Commerce()->add_log( 'API DELETE request: ' . $method . PHP_EOL . 'Arguments: ' . print_r( $request_args, 1 ) );
		}

		$headers = array(
			'Accept' => 'application/json',
			'Content-Type' => 'application/json',
			'X-ApiKey' => $this->api_key,
			'X-ConversioPluginVersion' => CM_Commerce()->version
		);

		$api_response = wp_remote_post( $this->url . $method, wp_parse_args( $request_args, array(
			'method'		=> 'DELETE',
			'timeout'		=> 5,
			'redirection'	=> 5,
			'httpversion'	=> '1.0',
			'blocking'		=> true,
			'headers'		=> $headers,
			'body'			=> array(),
			'cookies'		=> array()
		) ) );

		if ( is_wp_error( $api_response ) ) {
			$response = $api_response;
		} else {
			$response['response']	= $api_response['response'];
			$response['body']		= $api_response['body'];
		}

		if ( get_option( 'receiptful_debug_mode_enabled', false ) ) {
			CM_Commerce()->add_log( 'API PUT response to: ' . $method . PHP_EOL . 'Response: ' . print_r( $response, 1 ) );
		}

		return $response;

	}


}
