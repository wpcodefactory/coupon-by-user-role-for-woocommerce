<?php
/*
Plugin Name: Coupon by User Role for WooCommerce
Plugin URI: https://wpfactory.com/item/coupon-by-user-role-for-woocommerce/
Description: WooCommerce coupons by user roles.
Version: 2.2.1
Author: WPFactory
Author URI: https://wpfactory.com
Requires at least: 4.4
Text Domain: coupon-by-user-role-for-woocommerce
Domain Path: /langs
WC tested up to: 10.1
Requires Plugins: woocommerce
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

defined( 'ABSPATH' ) || exit;

if ( 'coupon-by-user-role-for-woocommerce.php' === basename( __FILE__ ) ) {
	/**
	 * Check if Pro plugin version is activated.
	 *
	 * @version 2.1.0
	 * @since   1.4.0
	 */
	$plugin = 'coupon-by-user-role-for-woocommerce-pro/coupon-by-user-role-for-woocommerce-pro.php';
	if (
		in_array( $plugin, (array) get_option( 'active_plugins', array() ), true ) ||
		(
			is_multisite() &&
			array_key_exists( $plugin, (array) get_site_option( 'active_sitewide_plugins', array() ) )
		)
	) {
		defined( 'ALG_WC_COUPON_BY_USER_ROLE_FILE_FREE' ) || define( 'ALG_WC_COUPON_BY_USER_ROLE_FILE_FREE', __FILE__ );
		return;
	}
}

defined( 'ALG_WC_COUPON_BY_USER_ROLE_VERSION' ) || define( 'ALG_WC_COUPON_BY_USER_ROLE_VERSION', '2.2.1' );

defined( 'ALG_WC_COUPON_BY_USER_ROLE_FILE' ) || define( 'ALG_WC_COUPON_BY_USER_ROLE_FILE', __FILE__ );

require_once plugin_dir_path( __FILE__ ) . 'includes/class-alg-wc-coupon-by-user-role.php';

if ( ! function_exists( 'alg_wc_coupon_by_user_role' ) ) {
	/**
	 * Returns the main instance of Alg_WC_Coupon_by_User_Role to prevent the need to use globals.
	 *
	 * @version 1.1.0
	 * @since   1.0.0
	 */
	function alg_wc_coupon_by_user_role() {
		return Alg_WC_Coupon_by_User_Role::instance();
	}
}

add_action( 'plugins_loaded', 'alg_wc_coupon_by_user_role' );
