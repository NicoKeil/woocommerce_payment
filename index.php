<?php
/*
Plugin Name: Debit - WooCommerce Gateway
Plugin URI: https://www.gruposlant.com.ar/
Description: Extends WooCommerce by Adding the Debit Gateway.
Version: 1.0
Author: Keil Nicolas, Debit
Author URI: https://www.gruposlant.com.ar/
*/

// Include our Gateway Class and register Payment Gateway with WooCommerce
add_action( 'plugins_loaded', 'woocmmerce_debit', 0 );
function woocmmerce_debit() {
	// If the parent WC_Payment_Gateway class doesn't exist
	// it means WooCommerce is not installed on the site
	// so do nothing
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) return;
	
	// If we made it this far, then include our Gateway Class
	include_once( 'debit_de_best_way.php' );

	// Now that we have successfully included our class,
	// Lets add it too WooCommerce
	add_filter( 'woocommerce_payment_gateways', 'Debit_payment' );
	function Debit_payment( $methods ) {
		$methods[] = 'Debit_payment';
		return $methods;
	}
}

// Add custom action links
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'debit_action_links' );
function debit_action_links( $links ) {
	$plugin_links = array(
		'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout' ) . '">' . __( 'Settings', 'Debit_payment' ) . '</a>',
	);

	// Merge our new link with the default ones
	return array_merge( $plugin_links, $links );	
}