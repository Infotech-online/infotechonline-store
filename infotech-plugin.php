<?php
/*
Plugin name: Infotech Plugin
Description: Este plugin añade funcionalidades personalizadas para Infotech Online.
Version: 1.0.0
Author: Juan Carlos Gallego
*/

// Para mostrar los posibles errores en el plugin
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Obtener WooCommerce

if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    require_once('srv/htdocs/wp-content/plugins/woocommerce/woocommerce.php');
}

// Libraries

// Helpers
require_once plugin_dir_path(__FILE__) . 'includes/helpers/roles.php';
require_once plugin_dir_path(__FILE__) . 'includes/helpers/database-actions.php';

// Actions to activate plugin
register_activation_hook( __FILE__, 'activation_function' );

function activation_function() {
    // Create the quote tables
    create_quote_tables();
}

// Actions to deactivate plugin
register_deactivation_hook( __FILE__, 'deactivation_function' );

function deactivation_function() {
    
}

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define INFOTECH_PLUGIN_FILE.
if ( ! defined( 'INFOTECH_PLUGIN_FILE' ) ) {
	define( 'INFOTECH_PLUGIN_FILE', __FILE__ );
}

// Define INFOTECH_ABSPATH.
if ( ! defined( 'INFOTECH_ABSPATH' ) ) {
	define( 'INFOTECH_ABSPATH', dirname( INFOTECH_PLUGIN_FILE ) . '/' );
}

// include dependencies file.
if ( ! class_exists( 'Woo_Wallet_Dependencies' ) ) {
	include_once plugin_dir_path(__FILE__) . '/includes/class-infotech-plugin-dependencies.php';
}

// Incluir la clase principal.
if ( ! class_exists( 'Infotech_Plugin' ) ) {
	include_once plugin_dir_path(__FILE__) . '/includes/class-infotech-plugin.php';
}

function infotech_plugin() {
	return Infotech_Plugin::instance();
}

$GLOBALS['infotech_plugin'] = infotech_plugin();