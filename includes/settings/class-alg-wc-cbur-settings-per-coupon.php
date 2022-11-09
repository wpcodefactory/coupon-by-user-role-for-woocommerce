<?php
/**
 * Coupon by User Role for WooCommerce - Per Coupon Settings
 *
 * @version 2.0.0
 * @since   1.1.0
 *
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_CBUR_Settings_Per_Coupon' ) ) :

class Alg_WC_CBUR_Settings_Per_Coupon {

	/**
	 * Constructor.
	 *
	 * @version 2.0.0
	 * @since   1.1.0
	 */
	function __construct() {
		add_action( 'woocommerce_coupon_data_tabs',    array( $this, 'add_tab' ), 10 );
		add_action( 'woocommerce_coupon_data_panels',  array( $this, 'add_options' ), 10, 2 );
		add_action( 'woocommerce_coupon_options_save', array( $this, 'save_options' ), 10, 2 );
		add_action( 'admin_head',                      array( $this, 'icon_css' ) );
	}

	/**
	 * is_coupon_admin_add_edit_page.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function is_coupon_admin_add_edit_page() {
		global $pagenow;
		return ( in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) && 'shop_coupon' === get_post_type() );
	}

	/**
	 * icon_css.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 *
	 * @see     https://rawgit.com/woothemes/woocommerce-icons/master/demo.html
	 *
	 * @todo    [next] (dev) better icons?
	 */
	function icon_css( $tabs ) {
		if ( $this->is_coupon_admin_add_edit_page() ) {
			echo '<style> .' . $this->id . '_options a::before { font-family: "WooCommerce" !important; content: "\\' . $this->icon . '" !important; } </style>';
		}
	}

	/**
	 * add_tab.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 *
	 * @see     https://github.com/woocommerce/woocommerce/blob/5.6.0/includes/admin/meta-boxes/class-wc-meta-box-coupon-data.php#L45
	 */
	function add_tab( $tabs ) {
		$tabs[ $this->id ] = array(
			'label'  => $this->title,
			'target' => $this->id . '_coupon_data',
			'class'  => $this->id . '_coupon_data',
		);
		return $tabs;
	}

	/**
	 * add_options.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 *
	 * @see     https://github.com/woocommerce/woocommerce/blob/5.6.0/includes/admin/meta-boxes/class-wc-meta-box-coupon-data.php#L344
	 * @see     https://github.com/woocommerce/woocommerce/blob/5.6.0/includes/admin/wc-meta-box-functions.php
	 *
	 * @todo    [next] `woocommerce_wp_text_input()`: `placeholder`, `description`, `desc_tip`
	 * @todo    [next] `woocommerce_wp_select()`: select all: better design?
	 * @todo    [next] Pro msg: better styling?
	 */
	function add_options( $coupon_id, $coupon ) {
		echo '<div id="' . $this->id . '_coupon_data" class="panel woocommerce_options_panel">' . '<div class="options_group">';
		foreach ( $this->get_options() as $option ) {
			$value = get_post_meta( $coupon_id, '_' . $option['name'], true );
			switch ( $option['type'] ) {
				case 'text':
					woocommerce_wp_text_input(
						array(
							'label'             => $option['label'],
							'id'                => $option['name'] . ( isset( $option['key'] ) ? '_' . $option['key'] : '' ),
							'name'              => $option['name'] . ( isset( $option['key'] ) ? '[' . $option['key'] . ']' : '' ),
							'data_type'         => 'percent' === $coupon->get_discount_type( 'edit' ) ? 'decimal' : 'price',
							'description'       => ( isset( $option['desc'] ) ? $option['desc'] : '' ),
							'desc_tip'          => true,
							'value'             => ( isset( $option['key'] ) ?
								( isset( $value[ $option['key'] ] ) ? $value[ $option['key'] ] : $option['default'] ) :
								( '' !== $value                     ? $value                   : $option['default'] ) ),
						)
					);
					break;
				case 'select':
					woocommerce_wp_select(
						array(
							'label'             => $option['label'],
							'id'                => $option['name'],
							'name'              => $option['name'] . ( ! empty( $option['multiple'] ) ? '[]' : '' ),
							'class'             => 'chosen_select short',
							'options'           => $option['options'],
							'custom_attributes' => ( ! empty( $option['multiple'] ) ? array( 'multiple' => 'multiple' ) : array() ),
							'description'       => ( isset( $option['desc'] ) ? $option['desc'] : '' ),
							'value'             => ( '' !== $value ? $value : $option['default'] ),
						)
					);
					break;
			}
		}
		echo apply_filters( 'alg_wc_cbur_settings', alg_wc_coupon_by_user_role()->core->get_pro_msg() );
		echo '</div>' . '</div>';
	}

	/**
	 * save_options.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 *
	 * @see     https://github.com/woocommerce/woocommerce/blob/5.6.0/includes/admin/meta-boxes/class-wc-meta-box-coupon-data.php#L391
	 */
	function save_options( $coupon_id, $coupon ) {
		$saved_options = array();
		foreach ( $this->get_options() as $option ) {
			if ( ! in_array( $option['name'], $saved_options ) ) {
				$value = ( isset( $_POST[ $option['name'] ] ) ? wc_clean( $_POST[ $option['name'] ] ) : $option['default'] );
				update_post_meta( $coupon_id, '_' . $option['name'], $value );
				$saved_options[] = $option['name'];
			}
		}
	}

}

endif;
