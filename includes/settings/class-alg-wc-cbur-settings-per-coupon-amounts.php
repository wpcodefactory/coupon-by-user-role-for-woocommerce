<?php
/**
 * Coupon by User Role for WooCommerce - Settings - Per Coupon - Amounts
 *
 * @version 2.0.0
 * @since   2.0.0
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_CBUR_Settings_Per_Coupon_Amounts' ) ) :

class Alg_WC_CBUR_Settings_Per_Coupon_Amounts extends Alg_WC_CBUR_Settings_Per_Coupon {

	/**
	 * Constructor.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 *
	 * @todo    (dev) rename `alg-wc-coupon-by-user-role-amounts` to `alg-wc-cbur-amounts` (same in `invalidate`)?
	 */
	function __construct() {

		$this->id    = 'alg_wc_coupon_by_user_role_amounts';
		$this->title = __( 'Amount by user role', 'coupon-by-user-role-for-woocommerce' );
		$this->icon  = 'e03a';

		parent::__construct();

	}

	/**
	 * get_options.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 *
	 * @todo    (feature) add option to reduce the number of roles
	 * @todo    (dev) rename `alg_wc_coupon_by_user_role_amount` to `alg_wc_cbur_amount`?
	 */
	function get_options() {
		$options = array();
		foreach ( alg_wc_coupon_by_user_role()->core->get_user_roles_options() as $role => $name ) {
			$options = array_merge( $options, array(
				array(
					'label'      => $name,
					'name'       => 'alg_wc_coupon_by_user_role_amount',
					'key'        => $role,
					'default'    => '',
					'type'       => 'text',
					'desc'       => (
						__( 'Can be zero.', 'coupon-by-user-role-for-woocommerce' ) . ' ' .
						__( 'Ignored if empty.', 'coupon-by-user-role-for-woocommerce' )
					),
				),
			) );
		}
		return $options;
	}

}

endif;

return new Alg_WC_CBUR_Settings_Per_Coupon_Amounts();
