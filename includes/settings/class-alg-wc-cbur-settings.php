<?php
/**
 * Coupon by User Role for WooCommerce - Settings
 *
 * @version 2.3.0
 * @since   1.0.0
 *
 * @author WPFactory
 *
 * @package WPFactory\WC_Coupon_By_User_Role\Settings
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Alg_WC_CBUR_Settings' ) ) :

	/**
	 * Alg_WC_CBUR_Settings class.
	 *
	 * @version 2.3.0
	 * @since   1.0.0
	 */
	class Alg_WC_CBUR_Settings extends WC_Settings_Page {

		/**
		 * Constructor.
		 *
		 * @version 2.3.0
		 * @since   1.0.0
		 */
		public function __construct() {
			$this->id    = 'alg_wc_coupon_by_user_role';
			$this->label = __( 'Coupon by User Role', 'coupon-by-user-role-for-woocommerce' );
			parent::__construct();

			// Scripts.
			add_action( 'admin_enqueue_scripts', array( $this, 'select_all_button_script' ) );

			// Sections.
			require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-cbur-settings-section.php';
			require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-cbur-settings-availability.php';
			require_once plugin_dir_path( __FILE__ ) . 'class-alg-wc-cbur-settings-amounts.php';
		}

		/**
		 * Select all button script.
		 *
		 * @version 2.2.0
		 * @since   2.0.0
		 */
		public function select_all_button_script() {
			global $pagenow;
			if (
				'admin.php' === $pagenow &&
				isset( $_GET['page'], $_GET['tab'] ) && // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				'wc-settings' === wc_clean( wp_unslash( $_GET['page'] ) ) && // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended
				'alg_wc_coupon_by_user_role' === wc_clean( wp_unslash( $_GET['tab'] ) ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended
			) {
				alg_wc_coupon_by_user_role()->core->add_admin_script( 'td' );
			}
		}

		/**
		 * Get settings.
		 *
		 * @version 1.2.0
		 * @since   1.0.0
		 */
		public function get_settings() {
			global $current_section;
			return array_merge(
				apply_filters( 'woocommerce_get_settings_' . $this->id . '_' . $current_section, array() ), // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
				array(
					array(
						'title' => __( 'Reset Settings', 'coupon-by-user-role-for-woocommerce' ),
						'type'  => 'title',
						'id'    => $this->id . '_' . $current_section . '_reset_options',
					),
					array(
						'title'    => __( 'Reset section settings', 'coupon-by-user-role-for-woocommerce' ),
						'desc'     => '<strong>' . __( 'Reset', 'coupon-by-user-role-for-woocommerce' ) . '</strong>',
						'desc_tip' => __( 'Check the box and save changes to reset.', 'coupon-by-user-role-for-woocommerce' ),
						'id'       => $this->id . '_' . $current_section . '_reset',
						'default'  => 'no',
						'type'     => 'checkbox',
					),
					array(
						'type' => 'sectionend',
						'id'   => $this->id . '_' . $current_section . '_reset_options',
					),
				)
			);
		}

		/**
		 * Maybe reset settings.
		 *
		 * @version 1.1.0
		 * @since   1.0.0
		 */
		public function maybe_reset_settings() {
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
		 * Admin notice settings reset.
		 *
		 * @version 2.1.0
		 * @since   1.1.0
		 */
		public function admin_notice_settings_reset() {
			echo '<div class="notice notice-warning is-dismissible"><p><strong>' .
				esc_html__( 'Your settings have been reset.', 'coupon-by-user-role-for-woocommerce' ) .
			'</strong></p></div>';
		}

		/**
		 * Save settings.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function save() {
			parent::save();
			$this->maybe_reset_settings();
		}
	}

endif;

return new Alg_WC_CBUR_Settings();
