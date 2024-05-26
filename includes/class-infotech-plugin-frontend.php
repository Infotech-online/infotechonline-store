<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Infotech_Plugin_Frontend' ) ) {
	/**
	 * Wallet Frontend class.
	 */
	class Infotech_Plugin_Frontend {

        protected static $_instance = null;

		/**
		 * Main instance
		 *
		 * @return class object
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

        public function __construct() {

            // Infotech Wallet
            add_filter( 'woocommerce_account_menu_items', array($this, 'infotech_wallet_account_menu_item') );
            add_action( 'init', array( $this, 'add_custom_endpoint' ) );
            add_action( 'woocommerce_account_wallet_endpoint', array( $this, 'infotech_wallet_account_menu_endpoint' ) );

            // add_action('woocommerce_review_order_before_payment', array($this, 'agregar_campo_seleccion_finalizar_compra'));

        }

        public function infotech_wallet_account_menu_item( $items ) {
            $items['wallet'] = 'Monedero';
            return $items;
        }

        public function add_custom_endpoint() {
            add_rewrite_endpoint( 'wallet', EP_PAGES ); // EP_PAGES or EP_ROOT
        }

        public function infotech_wallet_account_menu_endpoint() {
            
            // Incluir archivos JavaScript
            wp_enqueue_script( 'infotech-wallet-queries-script', INFOTECH_ABSPATH . 'assets/js/infotech_wallet/infotech-wallet-queries.js', array( 'jquery' ), '1.1.4', true );
            wp_enqueue_script( 'infotech-wallet-eventlisteners-script', INFOTECH_ABSPATH . 'assets/js/infotech_wallet/infotech-wallet-eventlisteners.js', array( 'jquery' ), '1.0.2', true );
            
            // Incluir archivos de Estilos
            wp_enqueue_style( 'infotech-wallet-menu-styles', INFOTECH_ABSPATH . 'assets/css/infotech_wallet/infotech-wallet-menu-styles.css', array(), '1.1.7'  );

            // Obtener la ruta del template
            $template_path = INFOTECH_ABSPATH . 'templates/infotech-wallet-menu-item.php';

            // Verificar si el template existe
            if ( file_exists( $template_path ) ) {
                // Cargar el template
                include $template_path;
            }
        }

        // Añadir campo de selección al finalizar la compra
        /*function agregar_campo_seleccion_finalizar_compra() {
            $current_user = wp_get_current_user();
            // $saldo_cartera = get_user_meta($current_user->ID, 'saldo_cartera', true); // Obtener saldo de la cartera del usuario
            ?>
            <div class="woocommerce-additional-fields">
                <p class="form-row">
                    <label for="usar_saldo_cartera"><?php _e('Usar saldo de cartera', 'woocommerce'); ?></label>
                    <select name="usar_saldo_cartera" id="usar_saldo_cartera">
                        <option value="0"><?php _e('No usar saldo de cartera', 'woocommerce'); ?></option>
                        <?php if (50000 > 0) : ?>
                            <option value="<?php echo 50000; ?>"><?php echo sprintf(__('Usar saldo de cartera (%s)', 'woocommerce'), wc_price(50000)); ?></option>
                        <?php endif; ?>
                    </select>
                </p>
            </div>
            <?php
        }*/
    }
}

Infotech_Plugin_Frontend::instance();