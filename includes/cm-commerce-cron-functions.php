<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * CRON events.
 *
 * @author		Conversio
 * @version		1.1.1
 * @since		1.0.0
 */


/**
 * 15 minute interval.
 *
 * Add a 15 minute interval to the cron schedules.
 *
 * @since 1.0.0
 *
 * @param	array $schedules	List of current CRON schedules.
 * @return	array				List of modified CRON schedules.
 */
function cm_commerce_add_quarter_schedule( $schedules ) {

	$schedules['quarter_hour'] = array(
		'interval'	=> 60 * 15, // 60 seconds * 15 minutes
		'display'	=> __( 'Every quarter', 'conversio-for-woocommerce' ),
	);

	return $schedules;

}
add_filter( 'cron_schedules', 'cm_commerce_add_quarter_schedule' );


/**
 * Schedule events.
 *
 * Schedule the resend of receipts to fire every 15 minutes
 * Scheduled outside class because working with objects isn't
 * perfect while doing events.
 *
 * @since 1.0.0
 */
function cm_commerce_schedule_event() {

	// Resend queue
	if ( ! wp_next_scheduled( 'receiptful_check_resend' ) ) {
		wp_schedule_event( 1407110400, 'quarter_hour', 'receiptful_check_resend' ); // 1407110400 is 08 / 4 / 2014 @ 0:0:0 UTC
	}

	// Initial product sync
	if ( ! wp_next_scheduled( 'receiptful_initial_product_sync' ) && 1 != get_option( 'receiptful_completed_initial_product_sync', 0 ) ) {
		wp_schedule_event( 1407110400, 'quarter_hour', 'receiptful_initial_product_sync' ); // 1407110400 is 08 / 4 / 2014 @ 0:0:0 UTC
	} elseif ( wp_next_scheduled( 'receiptful_initial_product_sync' ) && 1 == get_option( 'receiptful_completed_initial_product_sync', 0 ) ) {
		// Remove CRON when we're done with it.
		wp_clear_scheduled_hook( 'receiptful_initial_product_sync' );
	}

	// Initial receipt sync
	if ( ! wp_next_scheduled( 'receiptful_initial_receipt_sync' ) && 1 != get_option( 'receiptful_completed_initial_receipt_sync', 0 ) ) {
		wp_schedule_event( 1407110400, 'quarter_hour', 'receiptful_initial_receipt_sync' ); // 1407110400 is 08 / 4 / 2014 @ 0:0:0 UTC
	} elseif ( wp_next_scheduled( 'receiptful_initial_receipt_sync' ) && 1 == get_option( 'receiptful_completed_initial_receipt_sync', 0 ) ) {
		wp_clear_scheduled_hook( 'receiptful_initial_receipt_sync' ); // Remove CRON when we're done with it.
	}

}
add_action( 'init', 'cm_commerce_schedule_event' );


/**
 * Resend queue.
 *
 * Function is called every 15 minutes by a CRON job.
 * This fires the resend of Receipts and data that should be synced.
 *
 * @since 1.0.0
 */
function cm_commerce_check_resend() {

	// Receipt queue
	CM_Commerce()->email->resend_queue();

	// Products queue
	CM_Commerce()->products->process_queue();

	// Orders queue
	CM_Commerce()->order->process_queue();

}
add_action( 'receiptful_check_resend', 'cm_commerce_check_resend' );


/**
 * Sync data.
 *
 * Sync data with the CM Commerce API, this contains products for now.
 * The products are synced with CM Commerce to give the best product recommendations.
 * This is a initial product sync, the process should be completed once.
 *
 * @since 1.1.1
 */
function cm_commerce_initial_product_sync() {

	$product_ids = get_posts( array(
		'fields'			=> 'ids',
		'posts_per_page'	=> '225',
		'post_type'			=> 'product',
		'has_password'		=> false,
		'post_status'		=> 'publish',
		'meta_query'		=> array(
			'relation' 		=> 'OR',
			// @since 1.2.5 - This is for a re-sync that should be initialised
			array(
				'key'		=> '_receiptful_last_update',
				'compare'	=> '<',
				'value'		=> strtotime( '2016-05-06' ),
			),
			array(
				array(
					'key'		=> '_receiptful_last_update',
					'compare'	=> 'NOT EXISTS',
					'value'		=> '',
				),
				array(
					'key'		=> '_visibility',
					'compare'	=> '!=',
					'value'		=> 'hidden',
				),
			),
		),
	) );

	// Update option so the system knows it should stop syncing
	if ( empty( $product_ids ) ) {
		update_option( 'receiptful_completed_initial_product_sync', 1 );
		return;
	}

	// Get product args
	$args = array();
	foreach ( $product_ids as $product_id ) {
		$args[] = CM_Commerce()->products->get_formatted_product( $product_id );
	}

	// Update products
	$response = CM_Commerce()->api->update_products( $args );

	// Process response
	if ( is_wp_error( $response ) ) {

		return false;

	} elseif ( in_array( $response['response']['code'], array( '400' ) ) ) {

		// Set empty update time, so its not retried at next CRON job
		foreach ( $product_ids as $product_id ) {
			update_post_meta( $product_id, '_receiptful_last_update', '' );
		}

	} elseif ( in_array( $response['response']['code'], array( '200', '202' ) ) ) { // Update only the ones without error - retry the ones with error

		$failed_ids = array();
		$body 		= json_decode( $response['body'], 1 );
		foreach ( $body['errors'] as $error ) {
			$failed_ids[] = isset( $error['error']['product_id'] ) ? $error['error']['product_id'] : null;
		}

		// Set empty update time, so its not retried at next CRON job
		foreach ( $product_ids as $product_id ) {
			if ( ! in_array( $product_id, $failed_ids ) ) {
				update_post_meta( $product_id, '_receiptful_last_update', time() );
			} else {
				update_post_meta( $product_id, '_receiptful_last_update', '' );
			}
		}

	} elseif ( in_array( $response['response']['code'], array( '401', '500', '503' ) ) ) { // Retry later - keep meta unset
	}

}
add_action( 'receiptful_initial_product_sync', 'cm_commerce_initial_product_sync' );


/**
 * Sync Receipt data.
 *
 * Sync data with the CM Commerce API, this function syncs previous receipts.
 * This is a initial receipt sync, the process should be completed once.
 *
 * @since 1.1.2
 */
function cm_commerce_initial_receipt_sync() {

	$order_ids = get_posts( array(
		'fields'			=> 'ids',
		'posts_per_page'	=> '225',
		'post_type'			=> 'shop_order',
		'post_status'		=> array( 'wc-completed' ),
		'meta_query'		=> array(
			'relation' => 'OR',
			array(
				'key'		=> '_receiptful_last_update',
				'compare'	=> 'NOT EXISTS',
				'value'		=> '',
			),
			// @since 1.1.9 - This is for a re-sync that should be initialised
			array(
				'key'		=> '_receiptful_last_update',
				'compare'	=> '<',
				'value'		=> strtotime( '2015-07-15' ),
			),
		),
	) );

	// Update option so the system knows it should stop syncing
	if ( empty( $order_ids ) ) {
		update_option( 'receiptful_completed_initial_receipt_sync', 1 );
		return null;
	}

	// Prepare receipt args
	$args = array();
	foreach ( $order_ids as $order_id ) {

		$order		= wc_get_order( $order_id );
		$items 		= WC()->mailer()->emails['Conversio_Email_Customer_Receipt']->api_args_get_items( $order );
		$subtotals 	= WC()->mailer()->emails['Conversio_Email_Customer_Receipt']->api_args_get_subtotals( $order );
		$order_args	= WC()->mailer()->emails['Conversio_Email_Customer_Receipt']->api_args_get_order_args( $order, $items, $subtotals, $related_products = array() );
		$order_args['status'] = $order->get_status();

		$args[] = $order_args;

	}

	// Update receipts
	$response = CM_Commerce()->api->upload_receipts( $args );

	// Process response
	if ( is_wp_error( $response ) ) {

		return false;

	} elseif ( in_array( $response['response']['code'], array( '400' ) ) ) {

		// Set empty update time, so its not retried at next CRON job
		foreach ( $order_ids as $order_id ) {
			update_post_meta( $order_id, '_receiptful_last_update', '' );
		}

	} elseif ( in_array( $response['response']['code'], array( '200', '202' ) ) ) { // Update only the ones without error - retry the ones with error

		$failed_ids = array();
		$body 		= json_decode( $response['body'], 1 );
		foreach ( $body['errors'] as $error ) {
			$failed_ids[] = isset( $error['error']['reference'] ) ? $error['error']['reference'] : null;
		}

		// Set empty update time, so its not retried at next CRON job
		foreach ( $order_ids as $order_id ) {
			if ( ! in_array( $order_id, $failed_ids ) ) {
				update_post_meta( $order_id, '_receiptful_last_update', time() );
			} else {
				update_post_meta( $order_id, '_receiptful_last_update', '' );
			}
		}

	} elseif ( in_array( $response['response']['code'], array( '401', '500', '503' ) ) ) { // Retry later - keep meta unset
	}

}
add_action( 'receiptful_initial_receipt_sync', 'cm_commerce_initial_receipt_sync' );
