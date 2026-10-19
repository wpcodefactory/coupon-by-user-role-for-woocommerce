<?php
/**
 * Coupon by User Role for WooCommerce - Core Class
 *
 * @version 2.3.0
 * @since   1.0.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_Coupon_By_User_Role
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_Coupon_By_User_Role_Core' ) ) :

	/**
	 * Alg_WC_Coupon_By_User_Role_Core class.
	 *
	 * @version 2.3.0
	 * @since   1.0.0
	 */
	class Alg_WC_Coupon_By_User_Role_Core {

		/**
		 * Coupon error code.
		 *
		 * @since 2.1.0
		 *
		 * @var int
		 */
		public $coupon_error_code;

		/**
		 * Constructor.
		 *
		 * @version 2.3.0
		 * @since   1.0.0
		 *
		 * @todo (dev) Move `require_once plugin_dir_path( __FILE__ ) . 'settings/class-alg-wc-cbur-settings-per-coupon.php'` to another class/file.
		 * @todo (dev) Split into, e.g., `Alg_WC_CBUR_Availability` and `Alg_WC_CBUR_Amounts`?
		 * @todo (dev) Rename file and class (`cbur`)?
		 * @todo (dev) Init all options in constructor.
		 * @todo (dev) Use another error code (instead of `10000`) || maybe add option for customizable code.
		 * @todo (desc) Add FAQ and Screenshots sections to readme.txt.
		 */
		public function __construct() {
			if ( is_admin() ) {
				require_once plugin_dir_path( __FILE__ ) . 'settings/class-alg-wc-cbur-settings-per-coupon.php';
			}

			// Amounts.
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

			// Availability.
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
					2
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
		 * Settings per coupon amounts.
		 *
		 * @version 2.2.0
		 * @since   2.2.0
		 */
		public function settings_per_coupon_amounts() {
			require_once plugin_dir_path( __FILE__ ) . 'settings/class-alg-wc-cbur-settings-per-coupon-amounts.php';
		}

		/**
		 * Settings per coupon invalidate.
		 *
		 * @version 2.2.0
		 * @since   2.2.0
		 */
		public function settings_per_coupon_invalidate() {
			require_once plugin_dir_path( __FILE__ ) . 'settings/class-alg-wc-cbur-settings-per-coupon-invalidate.php';
		}

		/**
		 * Amount by user role.
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 *
		 * @param float  $amount The coupon amount.
		 * @param object $coupon The coupon object.
		 *
		 * @return float The modified coupon amount.
		 *
		 * @todo (feature) `woocommerce_coupon_get_discount_type`.
		 */
		public function amount_by_user_role( $amount, $coupon ) {
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
		 * Add admin script.
		 *
		 * @version 2.3.0
		 * @since   1.4.0
		 *
		 * @param string $e The element.
		 */
		public function add_admin_script( $e ) {
			wp_register_script(
				'alg-wc-cbur-admin',
				false,
				array( 'jquery' ),
				ALG_WC_COUPON_BY_USER_ROLE_VERSION,
				true
			);

			wp_enqueue_script( 'alg-wc-cbur-admin' );

			wp_add_inline_script(
				'alg-wc-cbur-admin',
				'jQuery(document).ready(function () {
					jQuery(".alg-wc-cbur-select-all").click(function (event) {
						event.preventDefault();
						jQuery(this)
							.closest("' . esc_js( $e ) . '")
							.find("select.chosen_select")
							.select2("destroy")
							.find("option")
							.prop("selected", "selected")
							.end()
							.select2();
						return false;
					});
					jQuery(".alg-wc-cbur-deselect-all").click(function (event) {
						event.preventDefault();
						jQuery(this)
							.closest("' . esc_js( $e ) . '")
							.find("select.chosen_select")
							.val("")
							.change();
						return false;
					});
				});'
			);
		}

		/**
		 * Get select all buttons.
		 *
		 * @version 2.0.0
		 * @since   1.4.0
		 *
		 * @param string $style The inline style for the buttons.
		 *
		 * @return string The HTML for the select all and deselect all buttons.
		 */
		public function get_select_all_buttons( $style = '' ) {
			return (
				'<a style="' . $style . '" href="#" class="button alg-wc-cbur-select-all">' .
					__( 'Select all', 'coupon-by-user-role-for-woocommerce' ) .
				'</a>' .
				' ' .
				'<a style="' . $style . '" href="#" class="button alg-wc-cbur-deselect-all">' .
					__( 'Deselect all', 'coupon-by-user-role-for-woocommerce' ) .
				'</a>'
			);
		}

		/**
		 * Language shortcode.
		 *
		 * @version 2.3.0
		 * @since   1.1.0
		 *
		 * @param array  $atts    The shortcode attributes.
		 * @param string $content The content between the shortcode tags.
		 *
		 * @return string The translated content based on the current language.
		 */
		public function language_shortcode( $atts, $content = '' ) {
			// E.g.: `[alg_wc_cbur_translate lang="DE" lang_text="Message for DE" not_lang_text="Message for other languages"]`.
			if (
				isset( $atts['lang_text'], $atts['not_lang_text'] ) &&
				! empty( $atts['lang'] )
			) {
				return (
					(
						! defined( 'ICL_LANGUAGE_CODE' ) ||
						! in_array(
							strtolower( ICL_LANGUAGE_CODE ),
							array_map( 'trim', explode( ',', strtolower( $atts['lang'] ) ) ),
							true
						)
					) ?
					wp_kses_post( $atts['not_lang_text'] ) :
					wp_kses_post( $atts['lang_text'] )
				);
			}

			// E.g.: `[alg_wc_cbur_translate lang="DE"]Message for DE[/alg_wc_cbur_translate][alg_wc_cbur_translate lang="NL"]Message for NL[/alg_wc_cbur_translate][alg_wc_cbur_translate not_lang="DE,NL"]Message for other languages[/alg_wc_cbur_translate]`.
			return (
				(
					(
						! empty( $atts['lang'] ) &&
						(
							! defined( 'ICL_LANGUAGE_CODE' ) ||
							! in_array(
								strtolower( ICL_LANGUAGE_CODE ),
								array_map( 'trim', explode( ',', strtolower( $atts['lang'] ) ) ),
								true
							)
						)
					) ||
					(
						! empty( $atts['not_lang'] ) &&
						(
							defined( 'ICL_LANGUAGE_CODE' ) &&
							in_array(
								strtolower( ICL_LANGUAGE_CODE ),
								array_map( 'trim', explode( ',', strtolower( $atts['not_lang'] ) ) ),
								true
							)
						)
					)
				) ?
				'' :
				wp_kses_post( $content )
			);
		}

		/**
		 * Is current user role.
		 *
		 * @version 1.4.0
		 * @since   1.3.0
		 *
		 * @param array $roles_to_check The user roles to check.
		 * @param array $exceptions     The user roles to exclude.
		 *
		 * @return bool True if the current user has one of the roles to check and none of the exceptions, false otherwise.
		 */
		public function is_current_user_role( $roles_to_check, $exceptions = array() ) {
			$user_roles               = $this->get_current_user_roles();
			$intersect_roles_to_check = array_intersect( $user_roles, $roles_to_check );
			$intersect_exceptions     = array_intersect( $user_roles, $exceptions );

			return (
				! empty( $intersect_roles_to_check ) &&
				empty( $intersect_exceptions )
			);
		}

		/**
		 * Get current user roles.
		 *
		 * @version 1.3.0
		 * @since   1.0.0
		 */
		public function get_current_user_roles() {
			$user = wp_get_current_user();
			return (
				! empty( $user->roles ) && is_array( $user->roles ) ?
				array_map( array( $this, 'handle_guest_role' ), $user->roles ) :
				array( 'guest' )
			);
		}

		/**
		 * Handle guest role.
		 *
		 * @version 2.3.0
		 * @since   1.1.0
		 *
		 * @param string $role The user role to handle.
		 *
		 * @return string The user role or 'guest' if the role is empty.
		 */
		public function handle_guest_role( $role ) {
			return ( '' !== (string) $role ? $role : 'guest' );
		}

		/**
		 * Get user roles options.
		 *
		 * @version 1.3.0
		 * @since   1.0.0
		 *
		 * @todo (dev) `super_admin` (Super Admin).
		 */
		public function get_user_roles_options() {
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
		 * Coupons enabled.
		 *
		 * @version 1.4.0
		 * @since   1.0.0
		 *
		 * @param bool $is_enabled Whether coupons are enabled.
		 *
		 * @return bool Whether coupons are enabled for the current user role.
		 */
		public function coupons_enabled( $is_enabled ) {
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
		 * Add invalid user roles per coupon.
		 *
		 * @version 1.4.0
		 * @since   1.3.0
		 *
		 * @param array     $invalid_user_roles Array of invalid user roles for the coupon.
		 * @param WC_Coupon $coupon             The coupon object.
		 * @param string    $type               Type of invalid user roles (default: '').
		 *
		 * @return array Modified array of invalid user roles.
		 *
		 * @todo (feature) `array_merge` vs return only `$invalid_user_roles_per_coupon`?
		 * @todo (dev) Rename to, e.g., `add_invalid_user_roles_and_exceptions_per_coupon`?
		 */
		public function add_invalid_user_roles_per_coupon( $invalid_user_roles, $coupon, $type = '' ) {
			$key = (
				'' === $type ?
				'_wpjup_wc_coupon_by_user_role_invalid' :
				'_alg_wc_coupon_by_user_role_invalid_exceptions'
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
		 * Coupon valid.
		 *
		 * @version 2.3.0
		 * @since   1.0.0
		 *
		 * @param bool      $valid  Whether the coupon is valid.
		 * @param WC_Coupon $coupon The coupon object.
		 *
		 * @return bool Whether the coupon is valid.
		 *
		 * @throws \Exception If the coupon is not valid for the current user's role.
		 */
		public function coupon_valid( $valid, $coupon ) {
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
				throw new \Exception(
					wp_kses_post( $this->get_coupon_not_valid_message( $coupon ) ),
					(int) $this->coupon_error_code
				);
			}

			return $valid;
		}

		/**
		 * Coupon not valid message.
		 *
		 * @version 2.3.0
		 * @since   1.0.0
		 *
		 * @param string    $message The original message.
		 * @param string    $code    The error code.
		 * @param WC_Coupon $coupon  The coupon object.
		 *
		 * @return string Modified message.
		 */
		public function coupon_not_valid_message( $message, $code, $coupon ) {
			return (
				(int) $this->coupon_error_code === (int) $code ?
				$this->get_coupon_not_valid_message( $coupon ) :
				$message
			);
		}

		/**
		 * Get coupon not valid message.
		 *
		 * @version 1.3.0
		 * @since   1.3.0
		 *
		 * @param WC_Coupon $coupon The coupon object.
		 *
		 * @return string The "coupon not valid" message.
		 *
		 * @todo (dev) Add more placeholders?
		 */
		public function get_coupon_not_valid_message( $coupon ) {
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
		 * Get pro message.
		 *
		 * @version 2.0.0
		 * @since   2.0.0
		 *
		 * @return string The pro message.
		 */
		public function get_pro_msg() {
			return (
				'<p style="background-color:white;padding:10px;">' .
					'<span class="dashicons dashicons-info" style="color:red;"></span> ' .
					sprintf(
						'This plugin version includes "standard" user roles only: %s.',
						'<strong>' .
							implode(
								'</strong>, <strong>',
								alg_wc_coupon_by_user_role()->core->get_user_roles_options()
							) .
						'</strong>'
					) . ' ' .
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

return new Alg_WC_Coupon_By_User_Role_Core();
