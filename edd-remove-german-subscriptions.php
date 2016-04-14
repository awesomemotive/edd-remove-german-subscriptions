<?php
/*
 * Plugin Name: Easy Digital Downloads - Remove Subscriptions for German Customers
 * Description: Removes subscription flags so that German customers are not shown an error when purchasing through PayPal
 * Author: Easy Digital Downloads
 * Version: 1.0
 */

class EDD_Remove_German_Subscriptions {

	public function __construct() {
		$this->init();
	}

	public function init() {
		add_action( 'plugins_loaded', array( $this, 'filters' ) );
	}

	public function filters() {
		add_filter( 'edd_add_to_cart_item', array( $this, 'remove_subscription_flags' ), 99999 );
	}

	public function remove_subscription_flags( $cart_item ) {

		if( ! empty( $cart_item['options']['recurring'] ) ) {

			if( $this->is_in_germany() ) {

				unset( $cart_item['options']['recurring'] );

			}
		}

		return $cart_item;
	}

	public function is_in_germany() {

		$ret = false;

		$api = wp_remote_get( 'http://ipinfo.io/' . edd_get_ip() . '/country' );

		if( ! is_wp_error( $api ) ) {

			$response = wp_remote_retrieve_body( $api );

			try {

				// decode response
				$country_code = strtoupper( trim( $response ) );
				if( 'DE' === $country_code ) {
					$ret = true;
				}

			} catch( Exception $e ) {

			}

		}

		return $ret;
	}
}

$edd_rgs = new EDD_Remove_German_Subscriptions;
unset( $edd_rgs );