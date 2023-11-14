<?php
/**
 * Coupon by User Role for WooCommerce - Section Settings
 *
 * @version 2.1.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_CBUR_Settings_Section' ) ) :

class Alg_WC_CBUR_Settings_Section {

	/**
	 * id.
	 *
	 * @since 2.1.0
	 */
	public $id;

	/**
	 * desc.
	 *
	 * @since 2.1.0
	 */
	public $desc;

	/**
	 * Constructor.
	 *
	 * @version 1.1.0
	 * @since   1.0.0
	 */
	function __construct() {
		add_filter( 'woocommerce_get_sections_alg_wc_coupon_by_user_role',              array( $this, 'settings_section' ) );
		add_filter( 'woocommerce_get_settings_alg_wc_coupon_by_user_role_' . $this->id, array( $this, 'get_settings' ), PHP_INT_MAX );
	}

	/**
	 * settings_section.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function settings_section( $sections ) {
		$sections[ $this->id ] = $this->desc;
		return $sections;
	}

	/**
	 * get_notes.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function get_notes() {
		return apply_filters( 'alg_wc_cbur_settings', array(
			array(
				'title'    => '',
				'type'     => 'title',
				'id'       => 'alg_wc_coupon_by_user_role_notes',
				'desc'     => alg_wc_coupon_by_user_role()->core->get_pro_msg(),
			),
			array(
				'type'     => 'sectionend',
				'id'       => 'alg_wc_coupon_by_user_role_notes',
			),
		) );
	}

}

endif;
