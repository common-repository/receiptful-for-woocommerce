<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/**
 * Trigger to update cart path.
 *
 * A trigger to update the slug of the current cart page is updated by the user.
 *
 * @since 1.3.6
 */
function receiptful_maybe_update_cart_path_on_slug_change( $post_ID, $post, $update ) {
	if ( $update && $post_ID === wc_get_page_id( 'cart' ) ) {
		CM_Commerce()->api->update_store_cart_path();
	}
}
add_action( 'save_post_page', 'receiptful_maybe_update_cart_path_on_slug_change', 10, 3 );

// Trigger update store cart path when option changes.
add_action( 'update_option_woocommerce_cart_page_id', array( CM_Commerce()->api, 'update_store_cart_path' ), 10, 3 );


if ( ! function_exists( 'wc_get_coupon_by_code' ) ) {

	/**
	 * Coupon by code.
	 *
	 * Get the coupon ID by the coupon code.
	 *
	 * @param	string		$coupon_code	Code that is used as coupon code.
	 * @return	int|bool					WP_Post ID if coupon is found, otherwise False.
	 */
	function wc_get_coupon_by_code( $coupon_code ) {

		global $wpdb;

		$coupon_id = $wpdb->get_var( $wpdb->prepare( apply_filters( 'woocommerce_coupon_code_query', "
			SELECT ID
			FROM $wpdb->posts
			WHERE post_title = %s
			AND post_type = 'shop_coupon'
			AND post_status = 'publish'
		" ), $coupon_code ) );

		if ( ! $coupon_id ) {
			return false;
		} else {
			return $coupon_id;
		}

	}

}


if ( ! function_exists( 'wc_get_random_products' ) ) {

	/**
	 * Random products.
	 *
	 * Get random WC product IDs.
	 *
	 * @param	int		$limit	Number of products to return
	 * @return	array			List of random product IDs.
	 */
	function wc_get_random_products( $limit = 2 ) {

		$product_args = array(
			'fields'			=> 'ids',
			'post_type'			=> 'product',
			'post_status'		=> 'publish',
			'posts_per_page'	=> $limit,
			'orderby'			=> 'rand',
			'meta_query'		=> array(
				array(
					'meta_key'	=> '_thumbnail_id',
					'compare'	=> 'EXISTS',
				),
			)
		);
		$products = get_posts( $product_args );

		return $products;

	}

}


/**
 * Clear unused, expired coupons.
 *
 * Clear the *CM Commerce* coupons that are expired / unused for at least
 *
 * @since 1.2.2
 */
function receiptful_clear_unused_coupons() {

	$expired_coupons = new WP_Query( array(
		'post_type' => 'shop_coupon',
		'fields' => 'ids',
		'posts_per_page' => 1000,
		'meta_query' => array(
			array(
				'key' => 'receiptful_coupon',
				'compare' => '=',
				'value' => 'yes',
			),
			array(
				'key' => 'expiry_date',
				'compare' => '<',
				'type' => 'DATE',
				'value' => date_i18n( 'Y-m-d', strtotime( '-7 days' ) ),
			),
			array(
				'key' => 'usage_count',
				'compare' => '=',
				'value' => '0',
			),
		),
	) );

	// Trash expired coupons
	foreach ( $expired_coupons->get_posts() as $post_id ) {
		wp_trash_post( $post_id );
	}

}


/**
 * Add CM Commerce version endpoint.
 *
 * Adds a simple CM Commerce version check endpoint, allowing
 * to check if CM Commerce is active and which version.
 *
 * @since 1.2.5
 */
function receiptful_add_active_endpoint() {

	if ( isset( $_GET['receiptful_version'] ) ) {
		wp_send_json( array(
			'version' 		=> CM_Commerce()->version,
			'publicUserKey' => CM_Commerce()->api->get_public_user_key(),
			'debug' 		=> (bool) get_option( 'receiptful_debug_mode_enabled', false ),
		) );
	}

}
if ( isset( $_GET['receiptful_version'] ) ) {
	add_action( 'init', 'receiptful_add_active_endpoint' );
}


/**
 * Get available widget locations.
 *
 * Get a list of the available widget locations.
 *
 * @since 1.5.0
 *
 * @return mixed|void|null
 */
function receiptful_get_available_widget_locations() {
	$locations = array(
		array(
			'title'   => 'Product page',
			'type'    => 'option-group',
			'options' => array(
				'woocommerce_single_product_summary:5' => array(
					'title'    => __( 'Title', 'conversio-for-woocommerce' ),
					'hook'     => 'woocommerce_single_product_summary',
					'priority' => 5,
				),
				'woocommerce_single_product_summary:10' => array(
					'title'    => __( 'Price', 'conversio-for-woocommerce' ),
					'hook'     => 'woocommerce_single_product_summary',
					'priority' => 10,
				),
				'woocommerce_single_product_summary:20' => array(
					'title'    => __( 'Excerpt', 'conversio-for-woocommerce' ),
					'hook'     => 'woocommerce_single_product_summary',
					'priority' => 20,
				),
				'woocommerce_single_product_summary:30' => array(
					'title'    => __( 'Add to cart button', 'conversio-for-woocommerce' ),
					'hook'     => 'woocommerce_single_product_summary',
					'priority' => 30,
				),
				'woocommerce_single_product_summary:40' => array(
					'title'    => __( 'Metadata', 'conversio-for-woocommerce' ),
					'hook'     => 'woocommerce_single_product_summary',
					'priority' => 40,
				),
				'woocommerce_single_product_summary:50' => array(
					'title'    => __( 'Sharing block', 'conversio-for-woocommerce' ),
					'hook'     => 'woocommerce_single_product_summary',
					'priority' => 50,
				),
				'woocommerce_after_single_product_summary:5' => array(
					'title'    => __( 'Product summary', 'conversio-for-woocommerce' ),
					'hook'     => 'woocommerce_after_single_product_summary',
					'priority' => 5,
				),
				'woocommerce_after_single_product_summary:10' => array(
					'title'    => __( 'Product tabs', 'conversio-for-woocommerce' ),
					'hook'     => 'woocommerce_after_single_product_summary',
					'priority' => 10,
				),
				'woocommerce_after_single_product_summary:15' => array(
					'title'    => __( 'Upsell', 'conversio-for-woocommerce' ),
					'hook'     => 'woocommerce_after_single_product_summary',
					'priority' => 15,
				),
				'woocommerce_after_single_product_summary:20' => array(
					'title'    => __( 'Related products', 'conversio-for-woocommerce' ),
					'hook'     => 'woocommerce_after_single_product_summary',
					'priority' => 20,
				),
			),
		),
		array(
			'title'   => 'Category page',
			'type'    => 'option-group',
			'options' => array(
				'woocommerce_before_shop_loop_item_title:10' => array(
					'title'    => __( 'Image', 'conversio-for-woocommerce' ),
					'hook'     => 'woocommerce_before_shop_loop_item_title',
					'priority' => 10,
				),
				'woocommerce_shop_loop_item_title:10' => array(
					'title'    => __( 'Product title', 'conversio-for-woocommerce' ),
					'hook'     => 'woocommerce_shop_loop_item_title',
					'priority' => 10,
				),
				'woocommerce_after_shop_loop_item_title:10' => array(
					'title'    => __( 'Price', 'conversio-for-woocommerce' ),
					'hook'     => 'woocommerce_after_shop_loop_item_title',
					'priority' => 10,
				),
				'woocommerce_after_shop_loop_item:10' => array(
					'title'    => __( 'Add to cart', 'conversio-for-woocommerce' ),
					'hook'     => 'woocommerce_after_shop_loop_item',
					'priority' => 10,
				),
			),
		),
		array(
			'title'   => 'Other',
			'type'    => 'option-group',
			'options' => array(
				'woocommerce_after_single_product:5' => array(
					'title'    => __( 'After product page content', 'conversio-for-woocommerce' ),
					'hook'     => 'woocommerce_after_single_product',
					'priority' => 5,
				),
				'woocommerce_after_main_content:5' => array(
					'title'    => __( 'Category page content', 'conversio-for-woocommerce' ),
					'hook'     => 'woocommerce_after_main_content',
					'priority' => 5,
				),
				'wp_footer' => array(
					'title'    => __( 'After all pages content', 'conversio-for-woocommerce' ),
					'hook'     => 'wp_footer',
					'priority' => null,
				),
				'custom' => array(
					'title'    => __( 'Custom', 'conversio-for-woocommerce' ),
					'hook'     => 'custom',
					'priority' => null,
				),
				'tab' => array(
					'title'    => __( 'New tab', 'conversio-for-woocommerce' ),
					'hook'     => 'tab',
					'priority' => null,
				),
			),
		),
	);

	return apply_filters( 'receiptful_available_widget_locations', $locations );
}
