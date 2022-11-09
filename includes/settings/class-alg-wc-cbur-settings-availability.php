<?php
/**
 * Coupon by User Role for WooCommerce - Availability Section Settings
 *
 * @version 2.0.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_CBUR_Settings_Availability' ) ) :

class Alg_WC_CBUR_Settings_Availability extends Alg_WC_CBUR_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		$this->id   = '';
		$this->desc = __( 'Availability', 'coupon-by-user-role-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 *
	 * @todo    [next] (dev) rearrange settings, e.g. "All Coupons: Disable", "All Coupons: Invalidate"
	 * @todo    [next] Message: desc: you can use HTML and shortcodes (e.g. `[alg_wc_cbur_translate]`) here
	 */
	function get_settings() {

		$plugin_settings = array(
			array(
				'title'    => __( 'Coupon Availability by User Role', 'coupon-by-user-role-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_coupon_by_user_role_plugin_options',
			),
			array(
				'title'    => __( 'Availability by user role', 'coupon-by-user-role-for-woocommerce' ),
				'desc'     => '<strong>' . __( 'Enable section', 'coupon-by-user-role-for-woocommerce' ) . '</strong>',
				'id'       => 'wpjup_wc_coupon_by_user_role_plugin_enabled', // mislabeled, should be `..._availability_section_enabled`
				'default'  => 'yes',
				'type'     => 'checkbox',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_coupon_by_user_role_plugin_options',
			),
		);

		$all_coupons_settings = array(
			array(
				'title'    => __( 'All Coupons', 'coupon-by-user-role-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_coupon_by_user_role_all_coupons_options',
			),
			array(
				'title'    => __( 'Disable all coupons for selected user roles', 'coupon-by-user-role-for-woocommerce' ),
				'desc_tip' => __( 'This will disable all coupons for selected user roles. Coupons will be disabled completely, including coupon code input field on the cart and checkout pages.', 'coupon-by-user-role-for-woocommerce' ),
				'type'     => 'multiselect',
				'class'    => 'chosen_select',
				'default'  => array(),
				'id'       => 'wpjup_wc_coupon_by_user_role_disabled',
				'options'  => alg_wc_coupon_by_user_role()->core->get_user_roles_options(),
				'desc'     => alg_wc_coupon_by_user_role()->core->get_select_all_buttons(),
			),
			array(
				'desc_tip' => __( 'Exceptions.', 'coupon-by-user-role-for-woocommerce' ) . ' ' .
					__( 'This is useful if user can have multiple roles at once on your site.', 'coupon-by-user-role-for-woocommerce' ),
				'type'     => 'multiselect',
				'class'    => 'chosen_select',
				'default'  => array(),
				'id'       => 'alg_wc_coupon_by_user_role_disabled_exceptions',
				'options'  => alg_wc_coupon_by_user_role()->core->get_user_roles_options(),
				'desc'     => alg_wc_coupon_by_user_role()->core->get_select_all_buttons(),
			),
			array(
				'title'    => __( 'Invalidate all coupons for selected user roles', 'coupon-by-user-role-for-woocommerce' ),
				'desc_tip' => __( 'This will invalidate all coupons for selected user roles. Coupon code input field will still be available on the cart and checkout pages.', 'coupon-by-user-role-for-woocommerce' ),
				'type'     => 'multiselect',
				'class'    => 'chosen_select',
				'default'  => array(),
				'id'       => 'wpjup_wc_coupon_by_user_role_invalid',
				'options'  => alg_wc_coupon_by_user_role()->core->get_user_roles_options(),
				'desc'     => alg_wc_coupon_by_user_role()->core->get_select_all_buttons(),
			),
			array(
				'desc_tip' => __( 'Exceptions.', 'coupon-by-user-role-for-woocommerce' ) . ' ' .
					__( 'This is useful if user can have multiple roles at once on your site.', 'coupon-by-user-role-for-woocommerce' ),
				'type'     => 'multiselect',
				'class'    => 'chosen_select',
				'default'  => array(),
				'id'       => 'alg_wc_coupon_by_user_role_invalid_exceptions',
				'options'  => alg_wc_coupon_by_user_role()->core->get_user_roles_options(),
				'desc'     => alg_wc_coupon_by_user_role()->core->get_select_all_buttons(),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_coupon_by_user_role_all_coupons_options',
			),
		);

		$per_coupon_settings = array(
			array(
				'title'    => __( 'Per Coupon', 'coupon-by-user-role-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_coupon_by_user_role_per_coupon_options',
			),
			array(
				'title'    => __( 'Invalidate per coupon', 'coupon-by-user-role-for-woocommerce' ),
				'desc'     => __( 'Enable', 'coupon-by-user-role-for-woocommerce' ),
				'desc_tip' => __( 'This will add "Invalidate by user role" tab to each coupon\'s admin edit page.', 'coupon-by-user-role-for-woocommerce' ),
				'type'     => 'checkbox',
				'default'  => 'no',
				'id'       => 'wpjup_wc_coupon_by_user_role_invalid_per_coupon',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_coupon_by_user_role_per_coupon_options',
			),
		);

		$message_settings = array(
			array(
				'title'    => __( 'Message', 'coupon-by-user-role-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_coupon_by_user_role_message_options',
			),
			array(
				'title'    => __( '"Coupon is not valid" message', 'coupon-by-user-role-for-woocommerce' ),
				'desc_tip' => __( 'Message that will be displayed for invalid coupons by user role.', 'coupon-by-user-role-for-woocommerce' ),
				'desc'     => sprintf( __( 'Available placeholders: %s.', 'coupon-by-user-role-for-woocommerce' ), '<code>' . implode( '</code>, <code>', array(
						'%coupon_code%',
						'%coupon_amount%',
					) ) . '</code>' ),
				'type'     => 'textarea',
				'default'  => __( 'Coupon is not valid for your user role.', 'coupon-by-user-role-for-woocommerce' ),
				'id'       => 'wpjup_wc_coupon_by_user_role_invalid_message',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_coupon_by_user_role_message_options',
			),
		);

		$notes = $this->get_notes();

		return array_merge( $plugin_settings, $all_coupons_settings, $per_coupon_settings, $message_settings, ( ! empty( $notes ) ? $notes : array() ) );
	}

}

endif;

return new Alg_WC_CBUR_Settings_Availability();
