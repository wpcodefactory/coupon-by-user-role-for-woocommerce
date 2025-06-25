<?php
/**
 * Coupon by User Role for WooCommerce - Core Class
 *
 * @version 2.2.0
 * @since   1.0.0
 *
 * @author  Algoritmika Ltd.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Coupon_by_User_Role_Core' ) ) :

class Alg_WC_Coupon_by_User_Role_Core {

	/**
	 * coupon_error_code.
	 *
	 * @since 2.1.0
	 */
	public $coupon_error_code;

	/**
	 * Constructor.
	 *
	 * @version 2.2.0
	 * @since   1.0.0
	 *
	 * @todo    (dev) move `require_once plugin_dir_path( __FILE__ ) . 'settings/class-alg-wc-cbur-settings-per-coupon.php'` to another class/file
	 * @todo    (dev) split into, e.g., `Alg_WC_CBUR_Availability` and `Alg_WC_CBUR_Amounts`?
	 * @todo    (dev) rename file and class (`cbur`)?
	 * @todo    (dev) init all options in constructor
	 * @todo    (dev) use another error code (instead of `10000`) || maybe add option for customizable code
	 * @todo    (desc) add FAQ and Screenshots sections to readme.txt
	 */
	function __construct() {

		if ( is_admin() ) {
			require_once plugin_dir_path( __FILE__ ) . 'settings/class-alg-wc-cbur-settings-per-coupon.php';
		}

		// Amounts
		if (
			'yes' === get_option( 'alg_wc_coupon_by_user_role_amount_section_enabled', 'yes' ) &&
			'yes' === get_option( 'alg_wc_coupon_by_user_role_amount_per_coupon', 'no' )
		) {
			if ( is_admin() ) {
				add_action(
					'admin_init',
					array( $this, 'settings_per_coupon_amounts' )
				);
			}
			add_filter(
				'woocommerce_coupon_get_amount',
				array( $this, 'amount_by_user_role' ),
				PHP_INT_MAX,
				2
			);
		}

		// Availability
		if ( 'yes' === get_option( 'wpjup_wc_coupon_by_user_role_plugin_enabled', 'yes' ) ) {
			$this->coupon_error_code = 10000;
			add_filter(
				'woocommerce_coupons_enabled',
				array( $this, 'coupons_enabled' ),
				PHP_INT_MAX,
				1
			);
			add_filter(
				'woocommerce_coupon_is_valid',
				array( $this, 'coupon_valid' ),
				PHP_INT_MAX,
				3
			);
			add_filter(
				'woocommerce_coupon_error',
				array( $this, 'coupon_not_valid_message' ),
				PHP_INT_MAX,
				3
			);
			if ( 'yes' === get_option( 'wpjup_wc_coupon_by_user_role_invalid_per_coupon', 'no' ) ) {
				if ( is_admin() ) {
					add_action(
						'admin_init',
						array( $this, 'settings_per_coupon_invalidate' )
					);
				}
				add_filter(
					'alg_wc_cbur_invalid_user_roles',
					array( $this, 'add_invalid_user_roles_per_coupon' ),
					10,
					3
				);
			}
			add_shortcode(
				'alg_wc_cbur_translate',
				array( $this, 'language_shortcode' )
			);
		}

	}

	/**
	 * settings_per_coupon_amounts.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	function settings_per_coupon_amounts() {
		require_once plugin_dir_path( __FILE__ ) . 'settings/class-alg-wc-cbur-settings-per-coupon-amounts.php';
	}

	/**
	 * settings_per_coupon_invalidate.
	 *
	 * @version 2.2.0
	 * @since   2.2.0
	 */
	function settings_per_coupon_invalidate() {
		require_once plugin_dir_path( __FILE__ ) . 'settings/class-alg-wc-cbur-settings-per-coupon-invalidate.php';
	}

	/**
	 * amount_by_user_role.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 *
	 * @todo    (feature) `woocommerce_coupon_get_discount_type`
	 */
	function amount_by_user_role( $amount, $coupon ) {
		$data = get_post_meta( $coupon->get_id(), '_alg_wc_coupon_by_user_role_amount', true );
		if ( ! empty( $data ) ) {
			foreach ( $this->get_current_user_roles() as $user_role ) {
				if ( isset( $data[ $user_role ] ) && '' !== $data[ $user_role ] ) {
					return $data[ $user_role ];
				}
			}
		}
		return $amount;
	}

	/**
	 * add_admin_script.
	 *
	 * @version 2.2.0
	 * @since   1.4.0
	 *
	 * @todo    (dev) move this to a separate js file
	 */
	function add_admin_script( $e ) {
		?>
		<script>
			jQuery( document ).ready( function() {
				jQuery( '.alg-wc-cbur-select-all' ).click( function( event ) {
					event.preventDefault();
					jQuery( this )
						.closest( '<?php echo esc_html( $e ); ?>' )
						.find( 'select.chosen_select' )
						.select2( 'destroy' )
						.find( 'option' )
						.prop( 'selected', 'selected' )
						.end()
						.select2();
					return false;
				} );
				jQuery( '.alg-wc-cbur-deselect-all' ).click( function( event ) {
					event.preventDefault();
					jQuery( this )
						.closest( '<?php echo esc_html( $e ); ?>' )
						.find( 'select.chosen_select' )
						.val( '' )
						.change();
					return false;
				} );
			} );
		</script>
		<?php
	}

	/**
	 * get_select_all_buttons.
	 *
	 * @version 2.0.0
	 * @since   1.4.0
	 */
	function get_select_all_buttons( $style = '' ) {
		return (
			'<a style="' . $style . '" href="#" class="button alg-wc-cbur-select-all">' .
				__( 'Select all', 'coupon-by-user-role-for-woocommerce' ) .
			'</a>' . ' ' .
			'<a style="' . $style . '" href="#" class="button alg-wc-cbur-deselect-all">' .
				__( 'Deselect all', 'coupon-by-user-role-for-woocommerce' ) .
			'</a>'
		);
	}

	/**
	 * language_shortcode.
	 *
	 * @version 2.2.0
	 * @since   1.1.0
	 */
	function language_shortcode( $atts, $content = '' ) {
		// E.g.: `[alg_wc_cbur_translate lang="DE" lang_text="Message for DE" not_lang_text="Message for other languages"]`
		if (
			isset( $atts['lang_text'], $atts['not_lang_text'] ) &&
			! empty( $atts['lang'] )
		) {
			return (
				(
					! defined( 'ICL_LANGUAGE_CODE' ) ||
					! in_array(
						strtolower( ICL_LANGUAGE_CODE ),
						array_map( 'trim', explode( ',', strtolower( $atts['lang'] ) ) )
					)
				) ?
				wp_kses_post( $atts['not_lang_text'] ) :
				wp_kses_post( $atts['lang_text'] )
			);
		}
		// E.g.: `[alg_wc_cbur_translate lang="DE"]Message for DE[/alg_wc_cbur_translate][alg_wc_cbur_translate lang="NL"]Message for NL[/alg_wc_cbur_translate][alg_wc_cbur_translate not_lang="DE,NL"]Message for other languages[/alg_wc_cbur_translate]`
		return (
			(
				(
					! empty( $atts['lang'] ) &&
					(
						! defined( 'ICL_LANGUAGE_CODE' ) ||
						! in_array(
							strtolower( ICL_LANGUAGE_CODE ),
							array_map( 'trim', explode( ',', strtolower( $atts['lang'] ) ) )
						)
					)
				) ||
				(
					! empty( $atts['not_lang'] ) &&
					(
						defined( 'ICL_LANGUAGE_CODE' ) &&
						in_array(
							strtolower( ICL_LANGUAGE_CODE ),
							array_map( 'trim', explode( ',', strtolower( $atts['not_lang'] ) ) )
						)
					)
				)
			) ?
			'' :
			wp_kses_post( $content )
		);
	}

	/**
	 * is_current_user_role.
	 *
	 * @version 1.4.0
	 * @since   1.3.0
	 */
	function is_current_user_role( $roles_to_check, $exceptions = array() ) {
		$user_roles               = $this->get_current_user_roles();
		$intersect_roles_to_check = array_intersect( $user_roles, $roles_to_check );
		$intersect_exceptions     = array_intersect( $user_roles, $exceptions );
		return ( ! empty( $intersect_roles_to_check ) && empty( $intersect_exceptions ) );
	}

	/**
	 * get_current_user_roles.
	 *
	 * @version 1.3.0
	 * @since   1.0.0
	 */
	function get_current_user_roles() {
		$user = wp_get_current_user();
		return (
			! empty( $user->roles ) && is_array( $user->roles ) ?
			array_map( array( $this, 'handle_guest_role' ), $user->roles ) :
			array( 'guest' )
		);
	}

	/**
	 * handle_guest_role.
	 *
	 * @version 1.1.0
	 * @since   1.1.0
	 */
	function handle_guest_role( $role ) {
		return ( '' != $role ? $role : 'guest' );
	}

	/**
	 * get_user_roles_options.
	 *
	 * @version 1.3.0
	 * @since   1.0.0
	 *
	 * @todo    (dev) `super_admin` (Super Admin)
	 */
	function get_user_roles_options() {
		return apply_filters(
			'alg_wc_cbur_user_roles',
				array(
				'guest'         => __( 'Guest', 'coupon-by-user-role-for-woocommerce' ),
				'administrator' => __( 'Administrator', 'coupon-by-user-role-for-woocommerce' ),
				'editor'        => __( 'Editor', 'coupon-by-user-role-for-woocommerce' ),
				'author'        => __( 'Author', 'coupon-by-user-role-for-woocommerce' ),
				'contributor'   => __( 'Contributor', 'coupon-by-user-role-for-woocommerce' ),
				'subscriber'    => __( 'Subscriber', 'coupon-by-user-role-for-woocommerce' ),
				'customer'      => __( 'Customer', 'coupon-by-user-role-for-woocommerce' ),
				'shop_manager'  => __( 'Shop manager', 'coupon-by-user-role-for-woocommerce' ),
			)
		);
	}

	/**
	 * coupons_enabled.
	 *
	 * @version 1.4.0
	 * @since   1.0.0
	 */
	function coupons_enabled( $is_enabled ) {
		$disabled_user_roles = get_option( 'wpjup_wc_coupon_by_user_role_disabled', array() );
		$disabled_user_roles = apply_filters(
			'alg_wc_cbur_disabled_user_roles',
			( ! empty( $disabled_user_roles ) ? $disabled_user_roles : array() )
		);
		$exceptions = get_option( 'alg_wc_coupon_by_user_role_disabled_exceptions', array() );
		if (
			! empty( $disabled_user_roles ) &&
			$this->is_current_user_role( $disabled_user_roles, $exceptions )
		) {
			return false;
		}
		return $is_enabled;
	}

	/**
	 * add_invalid_user_roles_per_coupon.
	 *
	 * @version 1.4.0
	 * @since   1.3.0
	 *
	 * @todo    (feature) `array_merge` vs return only `$invalid_user_roles_per_coupon`?
	 * @todo    (dev) rename to, e.g., `add_invalid_user_roles_and_exceptions_per_coupon`?
	 */
	function add_invalid_user_roles_per_coupon( $invalid_user_roles, $coupon, $type = '' ) {
		$key = (
			'' === $type ?
			'_' . 'wpjup_wc_coupon_by_user_role_invalid' :
			'_' . 'alg_wc_coupon_by_user_role_invalid_exceptions'
		);
		$invalid_user_roles_per_coupon = get_post_meta( $coupon->get_id(), $key, true );
		if ( ! empty( $invalid_user_roles_per_coupon ) ) {
			$invalid_user_roles = array_merge(
				$invalid_user_roles,
				$invalid_user_roles_per_coupon
			);
		}
		return $invalid_user_roles;
	}

	/**
	 * coupon_valid.
	 *
	 * @version 2.2.0
	 * @since   1.0.0
	 */
	function coupon_valid( $valid, $coupon, $discounts ) {
		$invalid_user_roles = get_option( 'wpjup_wc_coupon_by_user_role_invalid', array() );
		$invalid_user_roles = apply_filters(
			'alg_wc_cbur_invalid_user_roles',
			( ! empty( $invalid_user_roles ) ? $invalid_user_roles : array() ),
			$coupon
		);
		$exceptions = get_option( 'alg_wc_coupon_by_user_role_invalid_exceptions', array() );
		$exceptions = apply_filters(
			'alg_wc_cbur_invalid_user_roles',
			$exceptions,
			$coupon,
			'exceptions'
		);
		if (
			! empty( $invalid_user_roles ) &&
			$this->is_current_user_role( $invalid_user_roles, $exceptions )
		) {
			throw new Exception(
				wp_kses_post( $this->get_coupon_not_valid_message( $coupon ) ),
				esc_html( $this->coupon_error_code )
			);
			return false;
		}
		return $valid;
	}

	/**
	 * coupon_not_valid_message.
	 *
	 * @version 1.3.0
	 * @since   1.0.0
	 */
	function coupon_not_valid_message( $message, $code, $coupon ) {
		return (
			$this->coupon_error_code == $code ?
			$this->get_coupon_not_valid_message( $coupon ) :
			$message
		);
	}

	/**
	 * get_coupon_not_valid_message.
	 *
	 * @version 1.3.0
	 * @since   1.3.0
	 *
	 * @todo    (dev) add more placeholders?
	 */
	function get_coupon_not_valid_message( $coupon ) {
		$template = get_option(
			'wpjup_wc_coupon_by_user_role_invalid_message',
			__( 'Coupon is not valid for your user role.', 'coupon-by-user-role-for-woocommerce' )
		);
		$placeholders = array(
			'%coupon_code%'   => $coupon->get_code(),
			'%coupon_amount%' => $coupon->get_amount(),
		);
		return do_shortcode(
			str_replace(
				array_keys( $placeholders ),
				$placeholders,
				$template
			)
		);
	}

	/**
	 * get_pro_msg.
	 *
	 * @version 2.0.0
	 * @since   2.0.0
	 */
	function get_pro_msg() {
		return (
			'<p style="background-color:white;padding:10px;">' . '<span class="dashicons dashicons-info" style="color:red;"></span> ' .
				sprintf(
					'This plugin version includes "standard" user roles only: %s.',
					'<strong>' .
						implode(
							'</strong>, <strong>',
							alg_wc_coupon_by_user_role()->core->get_user_roles_options()
						) .
					'</strong>' ) . ' ' .
				sprintf(
					'If you need all your site\'s custom user roles to be included, you\'ll need %s plugin version.',
					'<a target="_blank" href="https://wpfactory.com/item/coupon-by-user-role-for-woocommerce/">' .
						'Coupon by User Role for WooCommerce Pro' .
					'</a>'
				) .
			'</p>'
		);
	}

}

endif;

return new Alg_WC_Coupon_by_User_Role_Core();
