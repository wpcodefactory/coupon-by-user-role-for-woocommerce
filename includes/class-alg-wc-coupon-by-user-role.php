<?php
/**
 * Coupon by User Role for WooCommerce - Main Class
 *
 * @version 2.2.0
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
	 * core.
	 *
	 * @since 2.1.0
	 */
	public $core;

	/**
	 * @var   Alg_WC_Coupon_by_User_Role The single instance of the class
	 * @since 1.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main Alg_WC_Coupon_by_User_Role Instance.
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
	 * @version 2.2.0
	 * @since   1.0.0
	 *
	 * @access  public
	 *
	 * @todo    (dev) rename file and class (`cbur`)?
	 */
	function __construct() {

		// Check for active WooCommerce plugin
		if ( ! function_exists( 'WC' ) ) {
			return;
		}

		// Load libs
		if ( is_admin() ) {
			require_once plugin_dir_path( ALG_WC_COUPON_BY_USER_ROLE_FILE ) . 'vendor/autoload.php';
		}

		// Set up localisation
		add_action( 'init', array( $this, 'localize' ) );

		// Declare compatibility with custom order tables for WooCommerce
		add_action( 'before_woocommerce_init', array( $this, 'wc_declare_compatibility' ) );

		// Pro
		if ( 'coupon-by-user-role-for-woocommerce-pro.php' === basename( ALG_WC_COUPON_BY_USER_ROLE_FILE ) ) {
			require_once plugin_dir_path( __FILE__ ) . 'pro/class-alg-wc-coupon-by-user-role-pro.php';
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
		load_plugin_textdomain(
			'coupon-by-user-role-for-woocommerce',
			false,
			dirname( plugin_basename( ALG_WC_COUPON_BY_USER_ROLE_FILE ) ) . '/langs/'
		);
	}

	/**
	 * wc_declare_compatibility.
	 *
	 * @version 2.1.0
	 * @since   2.1.0
	 *
	 * @see     https://developer.woocommerce.com/docs/features/high-performance-order-storage/recipe-book/
	 */
	function wc_declare_compatibility() {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			$files = (
				defined( 'ALG_WC_COUPON_BY_USER_ROLE_FILE_FREE' ) ?
				array( ALG_WC_COUPON_BY_USER_ROLE_FILE, ALG_WC_COUPON_BY_USER_ROLE_FILE_FREE ) :
				array( ALG_WC_COUPON_BY_USER_ROLE_FILE )
			);
			foreach ( $files as $file ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
					'custom_order_tables',
					$file,
					true
				);
			}
		}
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 *
	 * @version 2.2.0
	 * @since   1.0.0
	 */
	function includes() {
		$this->core = require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-coupon-by-user-role-core.php';
	}

	/**
	 * admin.
	 *
	 * @version 2.2.0
	 * @since   1.1.0
	 */
	function admin() {

		// Action links
		add_filter(
			'plugin_action_links_' . plugin_basename( ALG_WC_COUPON_BY_USER_ROLE_FILE ),
			array( $this, 'action_links' )
		);

		// "Recommendations" page
		add_action( 'init', array( $this, 'add_cross_selling_library' ) );

		// WC Settings tab as WPFactory submenu item
		add_action( 'init', array( $this, 'move_wc_settings_tab_to_wpfactory_menu' ) );

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
	 * @version 2.2.0
	 * @since   1.0.0
	 *
	 * @param   mixed $links
	 * @return  array
	 */
	function action_links( $links ) {
		$custom_links = array();

		$custom_links[] = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_wc_coupon_by_user_role' ) . '">' .
			__( 'Settings', 'coupon-by-user-role-for-woocommerce' ) .
		'</a>';

		if ( 'coupon-by-user-role-for-woocommerce.php' === basename( ALG_WC_COUPON_BY_USER_ROLE_FILE ) ) {
			$custom_links[] = '<a target="_blank" style="font-weight: bold; color: green;" href="https://wpfactory.com/item/coupon-by-user-role-for-woocommerce/">' .
				__( 'Go Pro', 'coupon-by-user-role-for-woocommerce' ) .
			'</a>';
		}

		return array_merge( $custom_links, $links );
	}

	/**
	 * add_cross_selling_library.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	function add_cross_selling_library() {

		if ( ! class_exists( '\WPFactory\WPFactory_Cross_Selling\WPFactory_Cross_Selling' ) ) {
			return;
		}

		$cross_selling = new \WPFactory\WPFactory_Cross_Selling\WPFactory_Cross_Selling();
		$cross_selling->setup( array( 'plugin_file_path' => ALG_WC_COUPON_BY_USER_ROLE_FILE ) );
		$cross_selling->init();

	}

	/**
	 * move_wc_settings_tab_to_wpfactory_menu.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	function move_wc_settings_tab_to_wpfactory_menu() {

		if ( ! class_exists( '\WPFactory\WPFactory_Admin_Menu\WPFactory_Admin_Menu' ) ) {
			return;
		}

		$wpfactory_admin_menu = \WPFactory\WPFactory_Admin_Menu\WPFactory_Admin_Menu::get_instance();

		if ( ! method_exists( $wpfactory_admin_menu, 'move_wc_settings_tab_to_wpfactory_menu' ) ) {
			return;
		}

		$wpfactory_admin_menu->move_wc_settings_tab_to_wpfactory_menu( array(
			'wc_settings_tab_id' => 'alg_wc_coupon_by_user_role',
			'menu_title'         => __( 'Coupon by User Role', 'coupon-by-user-role-for-woocommerce' ),
			'page_title'         => __( 'Coupons & Discount by User Role: WooCommerce Role Pricing', 'coupon-by-user-role-for-woocommerce' ),
			'plugin_icon'        => array(
				'get_url_method'    => 'wporg_plugins_api',
				'wporg_plugin_slug' => 'coupon-by-user-role-for-woocommerce',
			),
		) );

	}

	/**
	 * Add Coupon by User Role settings tab to WooCommerce settings.
	 *
	 * @version 2.2.0
	 * @since   1.0.0
	 */
	function add_woocommerce_settings_tab( $settings ) {
		$settings[] = require_once plugin_dir_path( __FILE__ ) . 'settings/class-alg-wc-cbur-settings.php';
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
