<?php

if (! defined('ABSPATH')) {
    exit;
}

final class InfotechWallet {

    public function __construct() {

		if ( Woo_Wallet_Dependencies::is_woocommerce_active() ) {
			$this->includes();
			$this->init_hooks();
			do_action( 'woo_wallet_loaded' );

		} else {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';
			deactivate_plugins( plugin_basename( INFOTECH_WALLET_PLUGIN_FILE ) );
			add_action( 'admin_notices', array( $this, 'admin_notices' ), 15 );
		}
	}

    private function is_request( $type ) {
		switch ( $type ) {
			case 'admin':
				return is_admin();
			case 'ajax':
				return defined( 'DOING_AJAX' );
			case 'cron':
				return defined( 'DOING_CRON' );
			case 'frontend':
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}

    public function includes() {
		
        /*
        include_once INFOTECH_WALLET_ABSPATH . 'includes/helper/woo-wallet-util.php';
		include_once INFOTECH_WALLET_ABSPATH . 'includes/helper/woo-wallet-update-functions.php';
		include_once INFOTECH_WALLET_ABSPATH . 'includes/class-woo-wallet-install.php';

		include_once INFOTECH_WALLET_ABSPATH . 'includes/class-woo-wallet-wallet.php';
		$this->wallet = new Woo_Wallet_Wallet();*/

		if ( $this->is_request( 'admin' ) ) {

			include_once INFOTECH_WALLET_ABSPATH . 'includes/class-infotech-wallet-admin.php';
		}
        /*
		if ( $this->is_request( 'frontend' ) ) {
			include_once INFOTECH_WALLET_ABSPATH . 'includes/class-woo-wallet-frontend.php';
		}*/
		if ( $this->is_request( 'ajax' ) ) {
			include_once INFOTECH_WALLET_ABSPATH . 'includes/class-woo-wallet-ajax.php';
		}

        // Public
        require_once INFOTECH_WALLET_ABSPATH . 'includes/public/payment-gateways/credit-method.php';
        require_once INFOTECH_WALLET_ABSPATH . 'includes/public/shortcodes.php';

        // Helpers
        require_once INFOTECH_WALLET_ABSPATH . 'includes/helpers/roles.php';
        require_once INFOTECH_WALLET_ABSPATH . 'includes/helpers/database-actions.php';
    }

    public function plugin_url() {
		return untrailingslashit( plugins_url( '/', INFOTECH_WALLET_PLUGIN_FILE ) );
	}

    /**
	 * WooCommerce email loader
	 *
	 * @param array $emails emails.
	 * @return array
	 */
	public function woocommerce_email_classes( $emails ) {
		$emails['Woo_Wallet_Email_New_Transaction']    = include INFOTECH_WALLET_ABSPATH . 'includes/emails/class-woo-wallet-email-new-transaction.php';
		$emails['Woo_Wallet_Email_Low_Wallet_Balance'] = include INFOTECH_WALLET_ABSPATH . 'includes/emails/class-woo-wallet-email-low-wallet-balance.php';
		return $emails;
	}

    /**
	 * Load template
	 *
	 * @param string $template_name Tempate Name.
	 * @param array  $args args.
	 * @param string $template_path Template Path.
	 * @param string $default_path Default path.
	 */
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

		$default_path = apply_filters( 'woo_wallet_template_path', $default_path );
		
        if ( ! $template_path ) {
			$template_path = 'woo-wallet';
		}

		if ( ! $default_path ) {
			$default_path = INFOTECH_WALLET_ABSPATH . 'templates/';
		}
		
        // Look within passed path within the theme - this is priority.
		$template = locate_template( array( trailingslashit( $template_path ) . $template_name, $template_name ) );
		
        // Add support of third perty plugin.
		$template = apply_filters( 'woo_wallet_locate_template', $template, $template_name, $template_path, $default_path );
		
        // Get default template.
		if ( ! $template ) {
			$template = $default_path . $template_name;
		}

		return $template;
	}

    public function admin_notices() {
		?>
		<div class="error">
			<p>
				<?php echo esc_html_e( 'InfotechWallet plugin requires', 'woo-wallet' ); ?> 
				<a href="https://wordpress.org/plugins/woocommerce/">WooCommerce</a> <?php echo esc_html_e( 'plugins to be active!', 'woo-wallet' ); ?>
			</p>
		</div>
		<?php
	}

}