=== Coupon by User Role for WooCommerce ===
Contributors: wpcodefactory, algoritmika, anbinder, karzin, omardabbas, kousikmukherjeeli
Tags: woocommerce, coupon, user role, woo commerce
Requires at least: 4.4
Tested up to: 6.6
Stable tag: 2.1.1
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

WooCommerce coupons by user roles.

== Description ==

**Coupon by User Role for WooCommerce** is a lightweight plugin that lets you:

* **Disable** or **invalidate** selected (or all) **coupons** for selected user role(s).
* Set **coupon amount** per user role.

### &#9989; Coupon Availability by User Role ###

**Disable all coupons for selected user roles** - This will disable all coupons for selected user roles. Coupons will be disabled completely, including coupon code input field on the cart and checkout pages.

**Invalidate selected (or all) coupons for selected user roles** - This will invalidate selected (or all) coupons for selected user roles. Coupon code input field will still be available on the cart and checkout pages. In case if user with "wrong" user role will try to apply the coupon, plugin will display customizable "Coupon is not valid for your user role" message on frontend.

### &#9989; Coupon Amount by User Role ###

Sets **coupon amount** per user role.

### &#127942; Premium Version ###

Free plugin version includes "standard" user roles only: Guest, Administrator, Editor, Author, Contributor, Subscriber, Customer, Shop manager. If you need all your site's custom user roles to be included, you'll need [Coupon by User Role for WooCommerce Pro](https://wpfactory.com/item/coupon-by-user-role-for-woocommerce/) plugin version.

### &#128472; Feedback ###

* We are open to your suggestions and feedback. Thank you for using or trying out one of our plugins!
* [Visit plugin site](https://wpfactory.com/item/coupon-by-user-role-for-woocommerce/).

### &#8505; More ###

* The plugin is **"High-Performance Order Storage (HPOS)"** compatible.

== Installation ==

1. Upload the entire plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Start by visiting plugin settings at "WooCommerce > Settings > Coupon by User Role".

== Changelog ==

= 2.1.1 - 30/07/2024 =
* WC tested up to: 9.1.
* Tested up to: 6.6.

= 2.1.0 - 14/11/2023 =
* Dev – "High-Performance Order Storage (HPOS)" compatibility.
* Dev - Code refactoring.
* WC tested up to: 8.2.
* Tested up to: 6.4.

= 2.0.4 - 24/09/2023 =
* WC tested up to: 8.1.
* Tested up to: 6.3.
* Plugin icon, banner updated.

= 2.0.3 - 18/06/2023 =
* WC tested up to: 7.8.
* Tested up to: 6.2.

= 2.0.2 - 14/11/2022 =
* WC tested up to: 7.1.
* Tested up to: 6.1.
* Readme.txt updated.
* Deploy script added.

= 2.0.1 - 13/04/2022 =
* Tested up to: 5.9.
* WC tested up to: 6.4.

= 2.0.0 - 25/08/2021 =
* Dev - "Amounts" section added.
* Dev - Per coupon settings redesigned. Now all options are in "Coupon data" tabs (instead of in meta boxes).
* Dev - General settings section renamed to "Availability". Settings descriptions updated.
* Dev - Code refactoring.
* WC tested up to: 5.6.

= 1.4.0 - 24/07/2021 =
* Dev - "Exceptions" options added (to "All Coupons: Disable", "All Coupons: Invalidate" and "Per coupon").
* Dev - Admin settings - "Select all" and "Deselect all" buttons added.
* Dev - Plugin is initialized on `plugins_loaded` action now.
* Dev - Code refactoring.
* WC tested up to: 5.5.
* Tested up to: 5.8.

= 1.3.0 - 25/02/2021 =
* Dev - Free plugin version released.
* Dev - Message - Placeholders added: `%coupon_code%`, `%coupon_amount%`.
* Dev - Meta Boxes - All input is sanitized now before saving.
* Dev - Localisation - `load_plugin_textdomain()` function moved to the `init` action.
* Dev - Admin settings descriptions updated.
* Dev - Code refactoring.
* WC tested up to: 5.0.
* Tested up to: 5.6.

= 1.2.0 - 26/07/2019 =
* Dev - Code refactoring.
* Dev - Admin settings descriptions updated.
* WC tested up to: 4.0.
* Tested up to: 5.3.

= 1.1.0 - 26/07/2019 =
* Dev - Now checking all user's roles (instead of only first role).
* Dev - Shortcodes are now processed in "Message"; language shortcode `[alg_wc_cbur_translate]` added (for WPML/Polylang).
* Dev - Code refactoring.
* WC tested up to: 3.6.
* Tested up to: 5.2.

= 1.0.0 - 11/06/2018 =
* Initial Release.

== Upgrade Notice ==

= 1.0.0 =
This is the first release of the plugin.
