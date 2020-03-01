<?php
/*
 * Plugin Name: WooCommerce Product Manual Sorting Performance Improvement
 * Description: Improves performance when manually sorting products in Woocommerce
 * Author:      Hayden Whiteman
 * Author URI:  https://github.com/haydenw
 * License:     MIT
*/

if (!defined('ABSPATH'))
	exit; // Exit if accessed directly

remove_action('wp_ajax_woocommerce_product_ordering', [WC_AJAX::class, 'product_ordering']);
add_action('wp_ajax_woocommerce_product_ordering', 'product_ordering');

function product_ordering() {
	global $wpdb;

	if ( ! current_user_can( 'edit_products' ) || empty( $_POST['id'] ) ) {
		wp_die( -1 );
	}

	$sorting_id  = absint( $_POST['id'] );
	$previd      = absint( isset( $_POST['previd'] ) ? $_POST['previd'] : 0 );
	$nextid      = absint( isset( $_POST['nextid'] ) ? $_POST['nextid'] : 0 );
	$menu_orders = wp_list_pluck( $wpdb->get_results( "SELECT ID, menu_order FROM {$wpdb->posts} WHERE post_type = 'product' ORDER BY menu_order ASC, post_title ASC" ), 'menu_order', 'ID' );
	$index       = 0;

	$seen_nextid = false;

	if ( ! ( $nextid === 0 || $previd === 0 ) ) {
		foreach ( $menu_orders as $id => $menu_order ) {
			$id = absint( $id );

			if ( $sorting_id === $id ) {
				continue;
			} elseif ( $nextid === $id ) {
				$index ++;
				$seen_nextid = true;
			}
			$index ++;

			if ( $menu_orders[ $id ] != $index ) {
				$menu_orders[ $id ] = $index;
				$wpdb->update( $wpdb->posts, array( 'menu_order' => $index ), array( 'ID' => $id ) );

				/**
				 * When a single product has gotten it's ordering updated.
				 * $id The product ID
				 * $index The new menu order
				*/
				do_action( 'woocommerce_after_single_product_ordering', $id, $index );
			} elseif ( $seen_nextid ) {
				break;
			}
		}
	}

	if ( isset( $menu_orders[ $previd ] ) ) {
		$menu_orders[ $sorting_id ] = $menu_orders[ $previd ] + 1;
	} elseif ( isset( $menu_orders[ $nextid ] ) ) {
		$menu_orders[ $sorting_id ] = $menu_orders[ $nextid ] - 1;
	} else {
		$menu_orders[ $sorting_id ] = 0;
	}

	$wpdb->update( $wpdb->posts, array( 'menu_order' => $menu_orders[ $sorting_id ] ), array( 'ID' => $sorting_id ) );

	WC_Post_Data::delete_product_query_transients();

	do_action( 'woocommerce_after_product_ordering', $sorting_id, $menu_orders );
	wp_send_json( $menu_orders );
}