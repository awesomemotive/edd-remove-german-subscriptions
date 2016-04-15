<?php
/*
 * Plugin Name: Easy Digital Downloads - Remove Subscriptions for German Customers
 * Description: Removes subscription flags so that German customers are not shown an error when purchasing through PayPal
 * Author: Easy Digital Downloads
 * Version: 1.0
 */

if ( ! defined( 'EDD_NO_DE_PLUGIN_DIR' ) ) {
	define( 'EDD_NO_DE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

require EDD_NO_DE_PLUGIN_DIR . 'vendor/autoload.php';
use GeoIp2\Database\Reader;

class EDD_Remove_German_Subscriptions {

	public function __construct() {
		$this->init();
	}

	public function init() {
		add_action( 'plugins_loaded', array( $this, 'filters' ) );
	}

	public function filters() {
		add_filter( 'edd_add_to_cart_item', array( $this, 'remove_subscription_flags' ), 99999 );
		add_filter( 'edd_recurring_show_terms_on_cart_item', array( $this, 'remove_cart_terms' ), 99999, 2 );
	}

	public function remove_subscription_flags( $cart_item ) {

		if( ! empty( $cart_item['options']['recurring'] ) ) {

			if( $this->is_in_germany() ) {

				unset( $cart_item['options']['recurring'] );

			}
		}

		return $cart_item;
	}

	public function remove_cart_terms( $show_terms, $item ) {

		if( $this->is_in_germany() ) {
			$show_terms = false;
		}

		return $show_terms;
	}

	public function is_in_germany() {

		$ret = false;

		try {

			$ip_db_reader = new Reader( EDD_NO_DE_PLUGIN_DIR . 'vendor/GeoLite2-Country.mmdb' );
			$country_data = $ip_db_reader->country( edd_get_ip() );

			if ( 'DE' === strtoupper( $country_data->country->isoCode ) ) {
				$ret = true;
			}

		} catch( Exception $e ) {

		}

		return $ret;
	}
}

$edd_rgs = new EDD_Remove_German_Subscriptions;
unset( $edd_rgs );