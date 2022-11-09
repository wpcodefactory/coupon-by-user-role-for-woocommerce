<?php
/**
 * Coupon by User Role for WooCommerce - Settings
 *
 * @version 2.0.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_CBUR_Settings' ) ) :

class Alg_WC_CBUR_Settings extends WC_Settings_Page {

	/**
	 * Constructor.
	 *
	 * @version 2.0.0
	 * @since   1.0.0
	 */
	function __construct() {
		$this->id    = 'alg_wc_coupon_by_user_role';
		$this->label = __( 'Coupon by User Role', 'coupon-by-user-role-for-woocommerce' );
		parent::__construct();
		// Scripts
		add_action( 'admin_footer', array( $this, 'select_all_button_script' ) );
		// Sections
		require_once( 'class-alg-wc-cbur-settings-section.php' );
		require_once( 'class-alg-wc-cbur-settings-availability.php' );
		require_once( 'class-alg-wc-cbur-settings-amounts.php' );
	}

	/**
	 * select_all_button_script.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function select_all_button_script() {
		global $pagenow;
		if ( 'admin.php' === $pagenow && isset( $_GET['page'], $_GET['tab'] ) && 'wc-settings' === wc_clean( $_GET['page'] ) && 'alg_wc_coupon_by_user_role' === wc_clean( $_GET['tab'] ) ) {
			alg_wc_coupon_by_user_role()->core->add_admin_script( 'td' );
		}
	}

	/**
	 * get_settings.
	 *
	 * @version 1.2.0
	 * @since   1.0.0
	 */
	function get_settings() {
		global $current_section;
		return array_merge( apply_filters( 'woocommerce_get_settings_' . $this->id . '_' . $current_section, array() ), array(
			array(
				'title'     => __( 'Reset Settings', 'coupon-by-user-role-for-woocommerce' ),
				'type'      => 'title',
				'id'        => $this->id . '_' . $current_section . '_reset_options',
			),
			array(
				'title'     => __( 'Reset section settings', 'coupon-by-user-role-for-woocommerce' ),
				'desc'      => '<strong>' . __( 'Reset', 'coupon-by-user-role-for-woocommerce' ) . '</strong>',
				'desc_tip'  => __( 'Check the box and save changes to reset.', 'coupon-by-user-role-for-woocommerce' ),
				'id'        => $this->id . '_' . $current_section . '_reset',
				'default'   => 'no',
				'type'      => 'checkbox',
			),
			array(
				'type'      => 'sectionend',
				'id'        => $this->id . '_' . $current_section . '_reset_options',
			),
		) );
	}

	/**
	 * maybe_reset_settings.
	 *
	 * @version 1.1.0
	 * @since   1.0.0
	 */
	function maybe_reset_settings() {
		global $current_section;
		if ( 'yes' === get_option( $this->id . '_' . $current_section . '_reset', 'no' ) ) {
			foreach ( $this->get_settings() as $value ) {
				if ( isset( $value['id'] ) ) {
					$id = explode( '[', $value['id'] );
					delete_option( $id[0] );
				}
			}
			add_action( 'admin_notices', array( $this, 'admin_notice_settings_reset' ) );
		}
	}


	/**
	 * admin_notice_settings_reset.
	 *
	 * @version 1.1.0
	 * @since   1.1.0
	 */
	function admin_notice_settings_reset() {
		echo '<div class="notice notice-warning is-dismissible"><p><strong>' .
			__( 'Your settings have been reset.', 'coupon-by-user-role-for-woocommerce' ) . '</strong></p></div>';
	}

	/**
	 * Save settings.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function save() {
		parent::save();
		$this->maybe_reset_settings();
	}

}

endif;

return new Alg_WC_CBUR_Settings();
