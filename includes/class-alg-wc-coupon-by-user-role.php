<?php
/**
 * Coupon by User Role for WooCommerce - Main Class
 *
 * @version 2.0.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Coupon_by_User_Role' ) ) :

final class Alg_WC_Coupon_by_User_Role {

	/**
	 * Plugin version.
	 *
	 * @var   string
	 * @since 1.0.0
	 */
	public $version = ALG_WC_COUPON_BY_USER_ROLE_VERSION;

	/**
	 * @var   Alg_WC_Coupon_by_User_Role The single instance of the class
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main Alg_WC_Coupon_by_User_Role Instance
	 *
	 * Ensures only one instance of Alg_WC_Coupon_by_User_Role is loaded or can be loaded.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 *
	 * @static
	 * @return  Alg_WC_Coupon_by_User_Role - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Alg_WC_Coupon_by_User_Role Constructor.
	 *
	 * @version 1.4.0
	 * @since   1.0.0
	 *
	 * @access  public
	 *
	 * @todo    [next] (dev) rename file and class (`cbur`)?
	 */
	function __construct() {

		// Check for active WooCommerce plugin
		if ( ! function_exists( 'WC' ) ) {
			return;
		}

		// Set up localisation
		add_action( 'init', array( $this, 'localize' ) );

		// Pro
		if ( 'coupon-by-user-role-for-woocommerce-pro.php' === basename( ALG_WC_COUPON_BY_USER_ROLE_FILE ) ) {
			require_once( 'pro/class-alg-wc-coupon-by-user-role-pro.php' );
		}

		// Include required files
		$this->includes();

		// Admin
		if ( is_admin() ) {
			$this->admin();
		}
	}

	/**
	 * localize.
	 *
	 * @version 1.4.0
	 * @since   1.3.0
	 */
	function localize() {
		load_plugin_textdomain( 'coupon-by-user-role-for-woocommerce', false, dirname( plugin_basename( ALG_WC_COUPON_BY_USER_ROLE_FILE ) ) . '/langs/' );
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 *
	 * @version 1.4.0
	 * @since   1.0.0
	 */
	function includes() {
		$this->core = require_once( 'class-alg-wc-coupon-by-user-role-core.php' );
	}

	/**
	 * admin.
	 *
	 * @version 1.4.0
	 * @since   1.1.0
	 */
	function admin() {
		// Action links
		add_filter( 'plugin_action_links_' . plugin_basename( ALG_WC_COUPON_BY_USER_ROLE_FILE ), array( $this, 'action_links' ) );
		// Settings
		add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_woocommerce_settings_tab' ) );
		// Version update
		if ( get_option( 'wpjup_wc_coupon_by_user_role_version', '' ) !== $this->version ) {
			add_action( 'admin_init', array( $this, 'version_update' ) );
		}
	}

	/**
	 * Show action links on the plugin screen.
	 *
	 * @version 1.4.0
	 * @since   1.0.0
	 *
	 * @param   mixed $links
	 * @return  array
	 */
	function action_links( $links ) {
		$custom_links = array();
		$custom_links[] = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_coupon_by_user_role' ) . '">' . __( 'Settings', 'woocommerce' ) . '</a>';
		if ( 'coupon-by-user-role-for-woocommerce.php' === basename( ALG_WC_COUPON_BY_USER_ROLE_FILE ) ) {
			$custom_links[] = '<a target="_blank" style="font-weight: bold; color: green;" href="https://wpfactory.com/item/coupon-by-user-role-for-woocommerce/">' .
				__( 'Go Pro', 'coupon-by-user-role-for-woocommerce' ) . '</a>';
		}
		return array_merge( $custom_links, $links );
	}

	/**
	 * Add Coupon by User Role settings tab to WooCommerce settings.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 */
	function add_woocommerce_settings_tab( $settings ) {
		$settings[] = require_once( 'settings/class-alg-wc-cbur-settings.php' );
		return $settings;
	}

	/**
	 * version_update.
	 *
	 * @version 1.1.0
	 * @since   1.1.0
	 */
	function version_update() {
		update_option( 'wpjup_wc_coupon_by_user_role_version', $this->version );
	}

	/**
	 * Get the plugin url.
	 *
	 * @version 1.4.0
	 * @since   1.0.0
	 *
	 * @return  string
	 */
	function plugin_url() {
		return untrailingslashit( plugin_dir_url( ALG_WC_COUPON_BY_USER_ROLE_FILE ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @version 1.4.0
	 * @since   1.0.0
	 *
	 * @return  string
	 */
	function plugin_path() {
		return untrailingslashit( plugin_dir_path( ALG_WC_COUPON_BY_USER_ROLE_FILE ) );
	}

}

endif;
