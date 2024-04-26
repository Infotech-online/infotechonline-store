<?

class Infotech_Wallet_Admin {

    public function __construct() {

        add_action('admin_page', array($this, 'admin_page'));

    }


    public function admin_page() {

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

    

}

Infotech_Wallet_Admin::instance();