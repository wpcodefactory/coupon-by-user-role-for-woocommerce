<?php
/*
Plugin Name: Coupon by User Role for WooCommerce
Plugin URI: https://wpfactory.com/item/coupon-by-user-role-for-woocommerce/
Description: WooCommerce coupons by user roles.
Version: 2.0.2-dev
Author: WPFactory
Author URI: https://wpfactory.com
Text Domain: coupon-by-user-role-for-woocommerce
Domain Path: /langs
WC tested up to: 7.1
*/

defined( 'ABSPATH' ) || exit;

if ( 'coupon-by-user-role-for-woocommerce.php' === basename( __FILE__ ) ) {
	/**
	 * Check if Pro plugin version is activated.
	 *
	 * @version 1.4.0
	 * @since   1.4.0
	 */
	$plugin = 'coupon-by-user-role-for-woocommerce-pro/coupon-by-user-role-for-woocommerce-pro.php';
	if (
		in_array( $plugin, (array) get_option( 'active_plugins', array() ), true ) ||
		( is_multisite() && array_key_exists( $plugin, (array) get_site_option( 'active_sitewide_plugins', array() ) ) )
	) {
		return;
	}
}

defined( 'ALG_WC_COUPON_BY_USER_ROLE_VERSION' ) || define( 'ALG_WC_COUPON_BY_USER_ROLE_VERSION', '2.0.2-dev-20221109-1725' );

defined( 'ALG_WC_COUPON_BY_USER_ROLE_FILE' ) || define( 'ALG_WC_COUPON_BY_USER_ROLE_FILE', __FILE__ );

require_once( 'includes/class-alg-wc-coupon-by-user-role.php' );

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
