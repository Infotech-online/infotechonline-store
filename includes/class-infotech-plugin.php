<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Evitar acceso directo
}

final class Infotech_Plugin {

    private static $instance;

    public static function instance() {
        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Infotech_Plugin ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {

        if ( Infotech_Plugin_Dependencies::is_woocommerce_active() ) {

			$this->includes();
            $this->init();

		} else {

			require_once ABSPATH . '/wp-admin/includes/plugin.php';
			deactivate_plugins( plugin_basename( INFOTECH_PLUGIN_FILE ) );
			add_action( 'admin_notices', array( $this, 'admin_notices' ), 15 );
		}
    }

    public function includes() {
        
        // Cargar todas las clases necesarias
        require_once INFOTECH_ABSPATH . 'includes/class-infotech-plugin-admin.php';
        // require_once plugin_dir_path( __FILE__ ) . 'public/class-infotech-plugin-public.php';
        require_once INFOTECH_ABSPATH . 'includes/shortcodes/class-infotech-plugin-pdf-shortcode.php';
        require_once INFOTECH_ABSPATH . 'includes/shortcodes/class-infotech-plugin-price-quote-shortcode.php';
        require_once INFOTECH_ABSPATH . 'includes/gateways/class-infotech-plugin-credit-payment.php';
		require_once INFOTECH_ABSPATH . 'includes/gateways/class-infotech-plugin-wallet-payment.php';
		// Frontend
		require_once INFOTECH_ABSPATH . 'includes/class-infotech-plugin-frontend.php';

        // Inicializar las clases
        new Infotech_Plugin_Admin();
        // new Mi_Plugin_Public();
        new Infotech_Plugin_Pdf_Shortcode();
        new Infotech_Plugin_Price_Quote_Shortcode();
        new WC_Infotech_Credit_Payment();
    }

    public function init() {
        
    }

    public function get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
		if ( $args && is_array( $args ) ) {
			extract( $args ); // phpcs:ignore
		}
		$located = $this->locate_template( $template_name, $template_path, $default_path );
		include $located;
	}

	/**
	 * Locate template file
	 *
	 * @param string $template_name template_name.
	 * @param string $template_path template_path.
	 * @param string $default_path default_path.
	 * @return string
	 */
	public function locate_template( $template_name, $template_path = '', $default_path = '' ) {
		$default_path = apply_filters( 'infotech_plugin_template_path', $default_path );
		if ( ! $template_path ) {
			$template_path = 'infotech-plugin';
		}
		if ( ! $default_path ) {
			$default_path = INFOTECH_ABSPATH . 'templates/';
		}
		// Look within passed path within the theme - this is priority.
		$template = locate_template( array( trailingslashit( $template_path ) . $template_name, $template_name ) );
		// Add support of third perty plugin.
		$template = apply_filters( 'infotech_plugin_locate_template', $template, $template_name, $template_path, $default_path );
		// Get default template.
		if ( ! $template ) {
			$template = $default_path . $template_name;
		}
		return $template;
	}

    /**
	 * Display admin notice
	 */
	public function admin_notices() {
		?>
		<div class="error">
			<p>
				<?php echo esc_html_e( 'TeraWallet plugin requires', 'woo-wallet' ); ?> 
				<a href="https://wordpress.org/plugins/woocommerce/">WooCommerce</a> <?php echo esc_html_e( 'plugins to be active!', 'woo-wallet' ); ?>
			</p>
		</div>
		<?php
	}
}