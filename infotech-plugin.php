<?php
/*
Plugin name: Infotech Plugin
Description: Este plugin añade el metodo de pago con credito directo a Infotech.
Version: 0.0.1
Author: Juan Gallego
*/

// Get WooCommerce functions ( Production )

if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    require_once('srv/htdocs/wp-content/plugins/woocommerce/woocommerce.php');
}

// Libraries

// Public
require_once plugin_dir_path(__FILE__) . 'includes/public/payment-gateways/credit-method.php';
require_once plugin_dir_path(__FILE__) . 'includes/public/shortcodes.php';
require_once plugin_dir_path(__FILE__) . 'includes/public/quotations/quote-button.php';

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

add_action("admin_menu", "InfotechPayment");

// Hook to save the new payment method
add_filter('woocommerce_payment_gateways', 'agregar_metodo_pago_personalizado');

function agregar_metodo_pago_personalizado($gateways) {
    $gateways[] = 'WC_Metodo_Pago_Personalizado';
    return $gateways;
}

function InfotechPayment() {
    add_menu_page(
        "Infotech Payment",
        "Infotech Payment",
        "manage_options",
        plugin_dir_path(__FILE__).'includes/public/pages/credit-payment.php',
        null
    );
    
    add_submenu_page(
        plugin_dir_path(__FILE__).'includes/public/pages/credit-payment.php',
        "Pagos por Convenio",
        "Pagos por Convenio",
        "manage_options",
        plugin_dir_path(__FILE__).'includes/public/pages/convenio-payment.php'
    );

    add_submenu_page(
        plugin_dir_path(__FILE__).'includes/public/pages/credit-payment.php',
        "Cotizaciones",
        "Cotizaciones",
        "manage_options",
        plugin_dir_path(__FILE__).'includes/public/pages/quotes.php'
    );

}