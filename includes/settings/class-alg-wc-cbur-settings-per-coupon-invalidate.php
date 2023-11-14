<?php
/**
 * Coupon by User Role for WooCommerce - Settings - Per Coupon - Invalidate
 *
 * @version 2.0.0
 * @since   2.0.0
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_CBUR_Settings_Per_Coupon_Invalidate' ) ) :

class Alg_WC_CBUR_Settings_Per_Coupon_Invalidate extends Alg_WC_CBUR_Settings_Per_Coupon {

	/**
	 * Constructor.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function __construct() {
		$this->id    = 'alg_wc_coupon_by_user_role_invalidate';
		$this->title = __( 'Invalidate by user role', 'coupon-by-user-role-for-woocommerce' );
		$this->icon  = 'e038';
		parent::__construct();
		add_action( 'admin_footer', array( $this, 'select_all_button_script' ) );
	}

	/**
	 * select_all_button_script.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function select_all_button_script() {
		if ( $this->is_coupon_admin_add_edit_page() ) {
			alg_wc_coupon_by_user_role()->core->add_admin_script( 'p' );
		}
	}

	/**
	 * get_options.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 *
	 * @todo    (dev) `wc_help_tip`: better design?
	 */
	function get_options() {
		return array(
			array(
				'label'      => __( 'Invalidate for roles', 'coupon-by-user-role-for-woocommerce' ),
				'name'       => 'wpjup_wc_coupon_by_user_role_invalid',
				'default'    => array(),
				'type'       => 'select',
				'multiple'   => true,
				'options'    => alg_wc_coupon_by_user_role()->core->get_user_roles_options(),
				'desc'       => alg_wc_coupon_by_user_role()->core->get_select_all_buttons( 'margin-top: 3px;' ) .
					wc_help_tip( __( 'Invalidate coupon for selected user roles.', 'coupon-by-user-role-for-woocommerce' ) ),
			),
			array(
				'label'      => __( 'Exceptions', 'coupon-by-user-role-for-woocommerce' ),
				'name'       => 'alg_wc_coupon_by_user_role_invalid_exceptions',
				'default'    => array(),
				'type'       => 'select',
				'multiple'   => true,
				'options'    => alg_wc_coupon_by_user_role()->core->get_user_roles_options(),
				'desc'       => alg_wc_coupon_by_user_role()->core->get_select_all_buttons( 'margin-top: 3px;' ) .
					wc_help_tip( __( 'This is useful if user can have multiple roles at once on your site.', 'coupon-by-user-role-for-woocommerce' ) ),
			),
		);
	}

}

endif;

return new Alg_WC_CBUR_Settings_Per_Coupon_Invalidate();
