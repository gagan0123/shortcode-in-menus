<?php
/**
 * Plugin Name: Shortcode in Menus
 * Description: Allows you to add shortcodes in WordPress Navigation Menus
 * Plugin URI: http://wordpress.org/plugins/shortcode-in-menus/
 * Version: 3.5.1
 * Author: Gagan Deep Singh
 * Author URI: https://gagan0123.com
 * Text Domain: shortcode-in-menus
 * Domain Path: /languages
 *
 * @package Shortcode_In_Menus
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'SHORTCODE_IN_MENUS_PATH' ) ) {
	/**
	 * Path to the plugin directory.
	 *
	 * @since 3.2
	 */
	define( 'SHORTCODE_IN_MENUS_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
}
if ( ! defined( 'SHORTCODE_IN_MENUS_URL' ) ) {
	/**
	 * URL to the plugin directory.
	 *
	 * @since 3.2
	 */
	define( 'SHORTCODE_IN_MENUS_URL', trailingslashit( plugins_url( '', __FILE__ ) ) );
}
if ( ! defined( 'SHORTCODE_IN_MENUS_RES' ) ) {
	/**
	 * Resource version for busting cache.
	 *
	 * @since 3.5
	 */
	define( 'SHORTCODE_IN_MENUS_RES', 1.0 );
}
/**
 * The core plugin class
 */
require_once SHORTCODE_IN_MENUS_PATH . 'includes/class-shortcode-in-menus.php';

/**
 * Load the admin class if its the admin dashboard
 */
if ( is_admin() ) {
	require_once SHORTCODE_IN_MENUS_PATH . 'admin/class-shortcode-in-menus-admin.php';
	Shortcode_In_Menus_Admin::get_instance();
} else {
	Shortcode_In_Menus::get_instance();
}
