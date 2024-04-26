<?php
/*
Plugin name: Infotech Plugin V2
Description: Plugin requerido para funcionalidades externas de Infotech Online
Version: 0.1.1
Author: Juan Gallego
*/

function activation_function() {
    // Create the quote tables
    create_quote_tables();
}

// Hook to save the new payment method
add_filter('woocommerce_payment_gateways', 'agregar_metodo_pago_personalizado');

function agregar_metodo_pago_personalizado($gateways) {
    $gateways[] = 'WC_Metodo_Pago_Personalizado';
    return $gateways;
}

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define WOO_WALLET_PLUGIN_FILE.
if ( ! defined( 'INFOTECH_WALLET_PLUGIN_FILE' ) ) {
	define( 'INFOTECH_WALLET_PLUGIN_FILE', __FILE__ );
}

// Define WOO_WALLET_ABSPATH.
if ( ! defined( 'INFOTECH_WALLET_ABSPATH' ) ) {
	define( 'INFOTECH_WALLET_ABSPATH', dirname( INFOTECH_WALLET_PLUGIN_FILE ) . '/' );
}

// Define WOO_WALLET_PLUGIN_VERSION.
if ( ! defined( 'INFOTECH_WALLET_PLUGIN_VERSION' ) ) {
	define( 'INFOTECH_WALLET_PLUGIN_VERSION', '1.1.0' );
}

// include dependencies file.
if ( ! class_exists( 'infotech_Wallet_Dependencies' ) ) {
	include_once __DIR__ . '/includes/class-infotech-wallet-dependencies.php';
}

// Include the main class.
if ( ! class_exists( 'InfotechWallet' ) ) {
	include_once __DIR__ . '/includes/class-infotech-wallet.php';
}

/**
 * Returns the main instance of InfotechWallet.
 *
 * @since  1.1.0
 * @return InfotechWallet
 */
function infotech_wallet() {
	return InfotechWallet::instance();
}

$GLOBALS['infotech_wallet'] = infotech_wallet();
