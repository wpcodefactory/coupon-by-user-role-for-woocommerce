<?php
/**
 * Coupon by User Role for WooCommerce - Main Class
 *
 * @version 2.3.0
 * @since   1.0.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_Coupon_By_User_Role
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Coupon_By_User_Role' ) ) :

	/**
	 * Alg_WC_Coupon_By_User_Role class.
	 *
	 * @version 2.3.0
	 * @since   1.0.0
	 */
	final class Alg_WC_Coupon_By_User_Role {

		/**
		 * Plugin version.
		 *
		 * @since 1.0.0
		 *
		 * @var string
		 */
		public $version = ALG_WC_COUPON_BY_USER_ROLE_VERSION;

		/**
		 * Core.
		 *
		 * @since 2.1.0
		 *
		 * @var Alg_WC_Coupon_By_User_Role_Core
		 */
		public $core;

		/**
		 * The single instance of the class.
		 *
		 * @version 2.3.0
		 * @since   1.0.0
		 *
		 * @var Alg_WC_Coupon_By_User_Role
		 */
		protected static $instance = null;

		/**
		 * Main Alg_WC_Coupon_By_User_Role Instance.
		 *
		 * Ensures only one instance of Alg_WC_Coupon_By_User_Role is loaded or can be loaded.
		 *
		 * @version 2.3.0
		 * @since   1.0.0
		 *
		 * @static
		 *
		 * @return Alg_WC_Coupon_By_User_Role
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Alg_WC_Coupon_By_User_Role Constructor.
		 *
		 * @version 2.3.0
		 * @since   1.0.0
		 *
		 * @access  public
		 *
		 * @todo (dev) Rename file and class (`cbur`)?
		 */
		public function __construct() {
			// Check for active WooCommerce plugin.
			if ( ! function_exists( 'WC' ) ) {
				return;
			}

			// Load libs.
			if ( is_admin() ) {
				require_once plugin_dir_path( ALG_WC_COUPON_BY_USER_ROLE_FILE ) . 'vendor/autoload.php';
			}

			// Declare compatibility with custom order tables for WooCommerce.
			add_action( 'before_woocommerce_init', array( $this, 'wc_declare_compatibility' ) );

			// Pro.
			if ( 'coupon-by-user-role-for-woocommerce-pro.php' === basename( ALG_WC_COUPON_BY_USER_ROLE_FILE ) ) {
				require_once plugin_dir_path( __FILE__ ) . 'pro/class-alg-wc-coupon-by-user-role-pro.php';
			}

			// Include required files.
			$this->includes();

			// Admin.
			if ( is_admin() ) {
				$this->admin();
			}
		}

		/**
		 * WC declare compatibility.
		 *
		 * @version 2.1.0
		 * @since   2.1.0
		 *
		 * @see https://developer.woocommerce.com/docs/features/high-performance-order-storage/recipe-book/
		 */
		public function wc_declare_compatibility() {
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
		public function includes() {
			$this->core = require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-coupon-by-user-role-core.php';
		}

		/**
		 * Admin.
		 *
		 * @version 2.2.0
		 * @since   1.1.0
		 */
		public function admin() {
			// Action links.
			add_filter(
				'plugin_action_links_' . plugin_basename( ALG_WC_COUPON_BY_USER_ROLE_FILE ),
				array( $this, 'action_links' )
			);

			// "Recommendations" page.
			add_action( 'init', array( $this, 'add_cross_selling_library' ) );

			// WC Settings tab as WPFactory submenu item.
			add_action( 'init', array( $this, 'move_wc_settings_tab_to_wpfactory_menu' ) );

			// Settings.
			add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_woocommerce_settings_tab' ) );

			// Version update.
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
		 * @param mixed $links Plugin action links.
		 *
		 * @return array
		 */
		public function action_links( $links ) {
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
		 * Add cross selling library.
		 *
		 * @version 2.2.0
		 * @since   2.2.0
		 */
		public function add_cross_selling_library() {
			if ( ! class_exists( '\WPFactory\WPFactory_Cross_Selling\WPFactory_Cross_Selling' ) ) {
				return;
			}

			$cross_selling = new \WPFactory\WPFactory_Cross_Selling\WPFactory_Cross_Selling();
			$cross_selling->setup( array( 'plugin_file_path' => ALG_WC_COUPON_BY_USER_ROLE_FILE ) );
			$cross_selling->init();
		}

		/**
		 * Move WC settings tab to WPFactory menu.
		 *
		 * @version 2.2.0
		 * @since   2.2.0
		 */
		public function move_wc_settings_tab_to_wpfactory_menu() {
			if ( ! class_exists( '\WPFactory\WPFactory_Admin_Menu\WPFactory_Admin_Menu' ) ) {
				return;
			}

			$wpfactory_admin_menu = \WPFactory\WPFactory_Admin_Menu\WPFactory_Admin_Menu::get_instance();

			if ( ! method_exists( $wpfactory_admin_menu, 'move_wc_settings_tab_to_wpfactory_menu' ) ) {
				return;
			}

			$wpfactory_admin_menu->move_wc_settings_tab_to_wpfactory_menu(
				array(
					'wc_settings_tab_id' => 'alg_wc_coupon_by_user_role',
					'menu_title'         => __( 'Coupon by User Role', 'coupon-by-user-role-for-woocommerce' ),
					'page_title'         => __( 'Coupons & Discount by User Role: WooCommerce Role Pricing', 'coupon-by-user-role-for-woocommerce' ),
					'plugin_icon'        => array(
						'get_url_method'    => 'wporg_plugins_api',
						'wporg_plugin_slug' => 'coupon-by-user-role-for-woocommerce',
					),
				)
			);
		}

		/**
		 * Add Coupon by User Role settings tab to WooCommerce settings.
		 *
		 * @version 2.2.0
		 * @since   1.0.0
		 *
		 * @param array $settings WooCommerce settings array.
		 *
		 * @return array Modified WooCommerce settings array.
		 */
		public function add_woocommerce_settings_tab( $settings ) {
			$settings[] = require_once plugin_dir_path( __FILE__ ) . 'settings/class-alg-wc-cbur-settings.php';
			return $settings;
		}

		/**
		 * Version update.
		 *
		 * @version 1.1.0
		 * @since   1.1.0
		 */
		public function version_update() {
			update_option( 'wpjup_wc_coupon_by_user_role_version', $this->version );
		}

		/**
		 * Get the plugin url.
		 *
		 * @version 1.4.0
		 * @since   1.0.0
		 *
		 * @return string
		 */
		public function plugin_url() {
			return untrailingslashit( plugin_dir_url( ALG_WC_COUPON_BY_USER_ROLE_FILE ) );
		}

		/**
		 * Get the plugin path.
		 *
		 * @version 1.4.0
		 * @since   1.0.0
		 *
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( ALG_WC_COUPON_BY_USER_ROLE_FILE ) );
		}
	}

endif;
