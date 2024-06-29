<?php
/**
 * Plugin Name:       ReviewX
 * Plugin URI:        https://reviewx.io/
 * Description:       Advanced Multi-criteria Rating & Reviews for WooCommerce. Turn your customer reviews into sales by collecting and leveraging reviews, ratings with multiple criteria.
 * Version:           1.6.28
 * Author:            ReviewX
 * Author URI:        https://reviewx.io/ 
 * Text Domain:       reviewx
 * Domain Path:       /languages
 * @package     ReviewX
 * @author      ReviewX <support@reviewx.io>
 * @copyright   Copyright (C) 2024 ReviewX & JoulesLabs. All rights reserved.
 * @license     GPLv3 or later
 * @since       1.0.0
 */

require __DIR__ . '/vendor/autoload.php';

use JoulesLabs\Warehouse\Foundation\Bootstrap;


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

define( 'REVIEWX_PLUGIN_NAME', 'reviewx');
/**
 * This constant will be deprecated in version 1.6.30.
 */
define( 'PLUGIN_NAME', 'reviewx');
define( 'REVIEWX_VERSION', '1.6.28' );

define( 'REVIEWX_URL', plugins_url( '/', __FILE__ ) );
define( 'REVIEWX_ADMIN_URL', REVIEWX_URL . 'admin/' );
define( 'REVIEWX_PUBLIC_URL', REVIEWX_URL . 'public/' );
define( 'REVIEWX_FILE', __FILE__ );
define( 'REVIEWX_DIR', __DIR__ );
define( 'REVIEWX_BASENAME', plugin_basename( __FILE__ ) );
define( 'REVIEWX_ROOT_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'REVIEWX_ADMIN_DIR_PATH', REVIEWX_ROOT_DIR_PATH . 'admin/' );
define( 'REVIEWX_PUBLIC_PATH', REVIEWX_ROOT_DIR_PATH . 'public/' );
define( 'REVIEWX_PLUGIN_PATH', trailingslashit(plugin_dir_path(__FILE__)) );
define( 'REVIEWX_INCLUDE_PATH', REVIEWX_ROOT_DIR_PATH . 'includes/' );
define( 'REVIEWX_PARTIALS_PATH', REVIEWX_ROOT_DIR_PATH . 'partials/' );
define( 'REVIEWX_CUSTOMIZER_URL', REVIEWX_URL . 'app/Customizer/' );
define( 'REVIEWX_AUTOLOGIN_CODE_LENGTH', 32 );
define( 'REVIEWX_AUTOLOGIN_CODE_CHARACTERS', "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789" );
define( 'REVIEWX_AUTOLOGIN_VALUE_NAME', 'rx_autologin_code' );
define( 'REVIEWX_AUTOLOGIN_USER_META_KEY', 'rx_autologin_code' );
define( 'REVIEWX_AUTOLOGIN_STAGED_CODE_USER_META_KEY', 'rx_autologin_staged_code' );
define( 'REVIEWX_AUTOLOGIN_STAGED_CODE_NONCE_USER_META_KEY', 'rx_autologin_staged_code_nonce' );
define( 'REVIEWX_AUTOLOGIN_BIG_WEBSITE_THRESHOLD', 20 );
define( 'REVIEWX_GOOGLE_JSON_REVIEW', REVIEWX_ROOT_DIR_PATH . 'app/Controllers/Storefront/Modules/reviews.json' );

/**
 * rx-function.php require for load plugin internal function
 */
require_once ABSPATH . WPINC . "/class-phpass.php";
require_once ( REVIEWX_ROOT_DIR_PATH . 'includes/rx-functions.php' );

/**
 * The functions responsible for ReviewX customizer
 */
require_once REVIEWX_ROOT_DIR_PATH . 'app/Customizer/customizer.php';
require_once REVIEWX_ROOT_DIR_PATH . 'app/Customizer/defaults.php';

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require_once REVIEWX_ROOT_DIR_PATH . 'includes/class-rx.php';


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
if (! function_exists('run_reviewx')) {
    function run_reviewx() {
        $plugin = new ReviewX();
        $plugin->run();
    }
}
run_reviewx();

if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

Bootstrap::run(__FILE__);

add_action('plugins_loaded', function () {
    \ReviewX\Elementor\Classes\Starter::instance();
});
	/**
	 * Prevent any user who cannot 'edit_posts' (subscribers, customers etc) from accessing admin.
	 */
    function rx_roles_disable_woocommerce_admin_restrictions($prevent_access) {
		$prevent_access = false;

		// Do not interfere with admin-post or admin-ajax requests.
		$exempted_paths = array( 'admin-post.php', 'admin-ajax.php' );

		if (
			/**
			 * This filter is documented in ../wc-user-functions.php
			 *
			 * @since 3.6.0
			 */
			apply_filters( 'woocommerce_disable_admin_bar', true )
			&& isset( $_SERVER['SCRIPT_FILENAME'] )
			&& ! in_array( basename( sanitize_text_field( wp_unslash( $_SERVER['SCRIPT_FILENAME'] ) ) ), $exempted_paths, true )
		) {
			$has_cap     = false;
			$access_caps = array( 'edit_posts', 'manage_woocommerce', 'view_admin_dashboard','customer' );

			foreach ( $access_caps as $access_cap ) {
				if ( current_user_can( $access_cap ) ) {
					$has_cap = true;
					break;
				}
			}

			if ( ! $has_cap ) {
				$prevent_access = true;
			}
            return $prevent_access;
		}
	}
    add_filter('woocommerce_prevent_admin_access', 'rx_roles_disable_woocommerce_admin_restrictions', 20);