<?php
/**
 * Plugin Name: Coupon by User Role for WooCommerce
 * Plugin URI: https://wpfactory.com/item/coupon-by-user-role-for-woocommerce/
 * Description: WooCommerce coupons by user roles.
 * Version: 2.3.0
 * Author: WPFactory
 * Author URI: https://wpfactory.com
 * Requires at least: 4.5
 * Requires PHP: 7.4
 * Text Domain: coupon-by-user-role-for-woocommerce
 * Domain Path: /langs
 * WC tested up to: 11.2
 * Requires Plugins: woocommerce
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package WPFactory\WC_Coupon_By_User_Role
 */

defined( 'ABSPATH' ) || exit;

if ( 'coupon-by-user-role-for-woocommerce.php' === basename( __FILE__ ) ) {
	if ( ! function_exists( 'alg_wc_coupon_by_user_role_is_pro_activated' ) ) {
		/**
		 * Check if Pro plugin version is activated.
		 *
		 * @version 2.3.0
		 * @since   1.4.0
		 */
		function alg_wc_coupon_by_user_role_is_pro_activated() {
			$plugin = 'coupon-by-user-role-for-woocommerce-pro/coupon-by-user-role-for-woocommerce-pro.php';
			return (
				in_array( $plugin, (array) get_option( 'active_plugins', array() ), true ) ||
				(
					is_multisite() &&
					array_key_exists( $plugin, (array) get_site_option( 'active_sitewide_plugins', array() ) )
				)
			);
		}
	}

	if ( alg_wc_coupon_by_user_role_is_pro_activated() ) {
		defined( 'ALG_WC_COUPON_BY_USER_ROLE_FILE_FREE' ) || define( 'ALG_WC_COUPON_BY_USER_ROLE_FILE_FREE', __FILE__ );
		return;
	}
}

/**
 * Plugin version.
 */
defined( 'ALG_WC_COUPON_BY_USER_ROLE_VERSION' ) || define( 'ALG_WC_COUPON_BY_USER_ROLE_VERSION', '2.3.0' );

/**
 * Plugin file.
 */
defined( 'ALG_WC_COUPON_BY_USER_ROLE_FILE' ) || define( 'ALG_WC_COUPON_BY_USER_ROLE_FILE', __FILE__ );

/**
 * Include the main plugin class.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-alg-wc-coupon-by-user-role.php';

if ( ! function_exists( 'alg_wc_coupon_by_user_role' ) ) {
	/**
	 * Returns the main instance of Alg_WC_Coupon_By_User_Role to prevent the need to use globals.
	 *
	 * @version 2.3.0
	 * @since   1.0.0
	 */
	function alg_wc_coupon_by_user_role() {
		return Alg_WC_Coupon_By_User_Role::instance();
	}
}

/**
 * Initialize the plugin.
 */
add_action( 'plugins_loaded', 'alg_wc_coupon_by_user_role' );
