<?php
class Infotech_Plugin_Admin {
    public function __construct() {
        add_action("admin_menu", array($this, 'add_admin_pages'));
    }

    public function add_admin_pages() {
        add_menu_page(
            "Infotech Payment",
            "Infotech Payment",
            "manage_options",
            INFOTECH_ABSPATH .'templates/admin/credit-payment.php',
            null
        );
        
        add_submenu_page(
            INFOTECH_ABSPATH .'templates/admin/credit-payment.php',
            "Pagos por Convenio",
            "Pagos por Convenio",
            "manage_options",
            INFOTECH_ABSPATH .'templates/admin/convenio-payment.php'
        );

        add_submenu_page(
            INFOTECH_ABSPATH .'templates/admin/credit-payment.php',
            "Cotizaciones",
            "Cotizaciones",
            "manage_options",
            INFOTECH_ABSPATH .'templates/admin/price-quotes.php'
        );
    }
}