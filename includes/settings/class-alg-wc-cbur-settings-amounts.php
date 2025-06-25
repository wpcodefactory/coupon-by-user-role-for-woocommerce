<?php
/**
 * Coupon by User Role for WooCommerce - Amounts Section Settings
 *
 * @version 2.0.0
 * @since   2.0.0
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_CBUR_Settings_Amounts' ) ) :

class Alg_WC_CBUR_Settings_Amounts extends Alg_WC_CBUR_Settings_Section {

	/**
	 * Constructor.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function __construct() {
		$this->id   = 'amounts';
		$this->desc = __( 'Amounts', 'coupon-by-user-role-for-woocommerce' );
		parent::__construct();
	}

	/**
	 * get_settings.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 *
	 * @todo    (feature) "Amount" (i.e., globally, not per-coupon)
	 * @todo    (dev) rename `alg_wc_coupon_by_user_role_amount_per_coupon` to `alg_wc_cbur_amount_per_coupon`?
	 */
	function get_settings() {

		$section_settings = array(
			array(
				'title'    => __( 'Coupon Amount by User Role', 'coupon-by-user-role-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_coupon_by_user_role_amount_options',
			),
			array(
				'title'    => __( 'Amount by user role', 'coupon-by-user-role-for-woocommerce' ),
				'desc'     => '<strong>' . __( 'Enable section', 'coupon-by-user-role-for-woocommerce' ) . '</strong>',
				'id'       => 'alg_wc_coupon_by_user_role_amount_section_enabled',
				'type'     => 'checkbox',
				'default'  => 'yes',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_coupon_by_user_role_amount_options',
			),
		);

		$amount_per_coupon_settings = array(
			array(
				'title'    => __( 'Per Coupon', 'coupon-by-user-role-for-woocommerce' ),
				'type'     => 'title',
				'id'       => 'alg_wc_coupon_by_user_role_amount_per_coupon_options',
			),
			array(
				'title'    => __( 'Amount per coupon', 'coupon-by-user-role-for-woocommerce' ),
				'desc'     => __( 'Enable', 'coupon-by-user-role-for-woocommerce' ),
				'desc_tip' => __( 'This will add "Amount by user role" tab to each coupon\'s admin edit page.', 'coupon-by-user-role-for-woocommerce' ),
				'id'       => 'alg_wc_coupon_by_user_role_amount_per_coupon',
				'type'     => 'checkbox',
				'default'  => 'no',
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_coupon_by_user_role_amount_per_coupon_options',
			),
		);

		$notes = $this->get_notes();

		return array_merge(
			$section_settings,
			$amount_per_coupon_settings,
			( ! empty( $notes ) ? $notes : array() )
		);
	}

}

endif;

return new Alg_WC_CBUR_Settings_Amounts();
